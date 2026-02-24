<?php
declare(strict_types=1);

$rows = [];
$hasTranslations = admin_table_exists('course_translations') && admin_table_exists('languages');
$hasCourseCategories = admin_table_exists('course_categories');
$hasInstructors = admin_table_exists('instructors');
$hasEnrollments = admin_table_exists('enrollments');
$hasCourseLevelGroupLabel = admin_table_has_column('courses', 'level_group_label');

if ($hasTranslations) {
    $sql = "SELECT c.id, c.status, c.price, c.published_at, c.created_at,
                   COALESCE(ct.title, CONCAT('Course #', c.id)) AS title,
                   COALESCE(ct.slug, '') AS slug,
                   " . ($hasCourseCategories ? "COALESCE(cc.name, '-')" : "'-'") . " AS category_name,
                   " . ($hasInstructors ? "COALESCE(i.display_name, '-')" : "'-'") . " AS author_name,
                   " . ($hasEnrollments ? "COALESCE(ec.student_count, 0)" : "0") . " AS student_count
            FROM courses c
            LEFT JOIN course_translations ct
              ON ct.course_id = c.id
             AND ct.language_id = (SELECT id FROM languages WHERE code='id' ORDER BY id ASC LIMIT 1)
            " . ($hasCourseCategories ? "LEFT JOIN course_categories cc ON cc.id = c.category_id" : "") . "
            " . ($hasInstructors ? "LEFT JOIN instructors i ON i.id = c.instructor_id" : "") . "
            " . ($hasEnrollments ? "LEFT JOIN (
                SELECT course_id, COUNT(*) AS student_count
                FROM enrollments
                WHERE status = 'active'
                GROUP BY course_id
            ) ec ON ec.course_id = c.id" : "") . "
            WHERE c.deleted_at IS NULL
            ORDER BY c.created_at DESC
            LIMIT 100";
} else {
    $sql = "SELECT c.id, c.status, c.price, c.published_at, c.created_at,
                   CONCAT('Course #', c.id) AS title,
                   '' AS slug,
                   " . ($hasCourseCategories ? "COALESCE(cc.name, '-')" : "'-'") . " AS category_name,
                   " . ($hasInstructors ? "COALESCE(i.display_name, '-')" : "'-'") . " AS author_name,
                   " . ($hasEnrollments ? "COALESCE(ec.student_count, 0)" : "0") . " AS student_count
            FROM courses c
            " . ($hasCourseCategories ? "LEFT JOIN course_categories cc ON cc.id = c.category_id" : "") . "
            " . ($hasInstructors ? "LEFT JOIN instructors i ON i.id = c.instructor_id" : "") . "
            " . ($hasEnrollments ? "LEFT JOIN (
                SELECT course_id, COUNT(*) AS student_count
                FROM enrollments
                WHERE status = 'active'
                GROUP BY course_id
            ) ec ON ec.course_id = c.id" : "") . "
            WHERE c.deleted_at IS NULL
            ORDER BY c.created_at DESC
            LIMIT 100";
}

$result = admin_db()->query($sql);
if ($result) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
}

$selectedCourseId = (int) ($_GET['course_id'] ?? 0);
$mode = (string) ($_GET['mode'] ?? '');
$isCreateMode = $mode === 'create';
$editingCourse = null;
$landingConfig = [
    'description' => '',
    'hero_border_preset' => 'border-white',
    'hero_bg_preset' => 'slate-cyan',
];
$moduleRows = [];
$selectedChapterId = 0;
$selectedChapter = null;
$selectedModuleLessonId = 0;
$selectedModuleLesson = null;
$videoDirectoryFiles = [];
$selectedModuleVideoFile = '';
$pptDirectoryFiles = [];
$selectedModulePptFile = '';
$editSections = [];
$selectedEditSection = (string) ($_GET['edit_section'] ?? 'metadata');

if ($selectedCourseId > 0) {
    if ($hasTranslations) {
        $sqlEdit = "SELECT c.id, c.price, c.status, c.level, " . ($hasCourseLevelGroupLabel ? "COALESCE(c.level_group_label,'')" : "''") . " AS level_group_label, c.featured_image,
                           COALESCE(ct.title, '') AS title,
                           COALESCE(ct.slug, '') AS slug,
                           COALESCE(ct.short_description, '') AS short_description
                    FROM courses c
                    LEFT JOIN course_translations ct
                      ON ct.course_id = c.id
                     AND ct.language_id = (SELECT id FROM languages WHERE code='id' ORDER BY id ASC LIMIT 1)
                    WHERE c.id = ?
                    LIMIT 1";
    } else {
        $sqlEdit = "SELECT c.id, c.price, c.status, c.level, " . ($hasCourseLevelGroupLabel ? "COALESCE(c.level_group_label,'')" : "''") . " AS level_group_label, c.featured_image, CONCAT('Course #', c.id) AS title, '' AS slug, '' AS short_description
                    FROM courses c
                    WHERE c.id = ?
                    LIMIT 1";
    }
    $stmt = admin_db()->prepare($sqlEdit);
    if ($stmt) {
        $stmt->bind_param('i', $selectedCourseId);
        $stmt->execute();
        $editingCourse = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }

    if ($editingCourse) {
        $editingCourse['author_name'] = '';
        if ($hasInstructors) {
            $stmt = admin_db()->prepare("SELECT i.display_name
                                         FROM courses c
                                         LEFT JOIN instructors i ON i.id = c.instructor_id
                                         WHERE c.id = ?
                                         LIMIT 1");
            if ($stmt) {
                $stmt->bind_param('i', $selectedCourseId);
                $stmt->execute();
                $authorRow = $stmt->get_result()->fetch_assoc();
                $stmt->close();
                $editingCourse['author_name'] = (string) ($authorRow['display_name'] ?? '');
            }
        }
    }

    if (admin_table_exists('course_page_configs')) {
        $stmt = admin_db()->prepare("SELECT description, layout_json
                                     FROM course_page_configs
                                     WHERE course_id = ? AND page_key = 'landing'
                                     LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $selectedCourseId);
            $stmt->execute();
            $rowCfg = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($rowCfg) {
                $landingConfig['description'] = (string) ($rowCfg['description'] ?? '');
                $layout = json_decode((string) ($rowCfg['layout_json'] ?? ''), true);
                if (is_array($layout)) {
                    $landingConfig['hero_border_preset'] = (string) ($layout['hero_border_preset'] ?? $landingConfig['hero_border_preset']);
                    $landingConfig['hero_bg_preset'] = (string) ($layout['hero_bg_preset'] ?? $landingConfig['hero_bg_preset']);
                }
            }
        }
    }

    $lessonRows = [];
    $stmt = admin_db()->prepare("SELECT id, module_order, title
                                 FROM course_modules
                                 WHERE course_id = ?
                                 ORDER BY module_order ASC");
    if ($stmt) {
        $stmt->bind_param('i', $selectedCourseId);
        $stmt->execute();
        $moduleRows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

    $hasLessonVariant = admin_table_has_column('course_lessons', 'lesson_variant');
    $variantSelect = $hasLessonVariant ? "COALESCE(l.lesson_variant, '') AS lesson_variant," : "'' AS lesson_variant,";
    $stmt = admin_db()->prepare("SELECT l.id, l.module_id, l.lesson_order, l.title, l.lesson_type, $variantSelect l.content_url, l.content_body, l.duration_seconds, l.is_preview, m.module_order
                                 FROM course_lessons l
                                 JOIN course_modules m ON m.id = l.module_id
                                 WHERE m.course_id = ?
                                 ORDER BY m.module_order ASC, l.lesson_order ASC");
    if ($stmt) {
        $stmt->bind_param('i', $selectedCourseId);
        $stmt->execute();
        $lessonRows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }

    $lessonsByModule = [];
    $lessonsById = [];
    foreach ($lessonRows as $lr) {
        $mid = (int) ($lr['module_id'] ?? 0);
        $lid = (int) ($lr['id'] ?? 0);
        if ($mid <= 0) {
            continue;
        }
        if (!isset($lessonsByModule[$mid])) {
            $lessonsByModule[$mid] = [];
        }
        $lessonsByModule[$mid][] = $lr;
        if ($lid > 0) {
            $lessonsById[$lid] = $lr;
        }
    }

    $editSections[] = ['value' => 'metadata', 'label' => 'Metadata'];
    $editSections[] = ['value' => 'front-card', 'label' => 'Front Card'];
    $editSections[] = ['value' => 'landing', 'label' => 'Landing Page'];
    foreach ($moduleRows as $m) {
        $moduleId = (int) ($m['id'] ?? 0);
        $chapterOrder = (int) ($m['module_order'] ?? 0);
        $chapterTitle = trim((string) ($m['title'] ?? ''));
        $editSections[] = [
            'value' => 'chapter:' . $moduleId,
            'label' => 'Chapter ' . $chapterOrder . ($chapterTitle !== '' ? ' - ' . $chapterTitle : ''),
        ];
        foreach (($lessonsByModule[$moduleId] ?? []) as $ls) {
            $lessonId = (int) ($ls['id'] ?? 0);
            $lessonOrder = (int) ($ls['lesson_order'] ?? 0);
            $lessonTitle = trim((string) ($ls['title'] ?? ''));
            $editSections[] = [
                'value' => 'module:' . $lessonId,
                'label' => 'Module ' . $chapterOrder . '.' . $lessonOrder . ($lessonTitle !== '' ? ' - ' . $lessonTitle : ''),
            ];
        }
    }
    $editSections[] = ['value' => 'summary', 'label' => 'Summary'];
    $editSections[] = ['value' => 'certificate', 'label' => 'Certificate'];

    if (strpos($selectedEditSection, 'chapter:') === 0) {
        $selectedChapterId = (int) substr($selectedEditSection, 8);
        foreach ($moduleRows as $m) {
            if ((int) ($m['id'] ?? 0) === $selectedChapterId) {
                $selectedChapter = $m;
                break;
            }
        }
        if (!$selectedChapter && $moduleRows) {
            $selectedChapter = $moduleRows[0];
            $selectedChapterId = (int) ($selectedChapter['id'] ?? 0);
            $selectedEditSection = 'chapter:' . $selectedChapterId;
        }
    } elseif (strpos($selectedEditSection, 'module:') === 0) {
        $selectedModuleLessonId = (int) substr($selectedEditSection, 7);
        if ($selectedModuleLessonId > 0 && isset($lessonsById[$selectedModuleLessonId])) {
            $selectedModuleLesson = $lessonsById[$selectedModuleLessonId];
        } elseif ($lessonRows) {
            $selectedModuleLesson = $lessonRows[0];
            $selectedModuleLessonId = (int) ($selectedModuleLesson['id'] ?? 0);
            $selectedEditSection = 'module:' . $selectedModuleLessonId;
        }
    }
}

$videoDirFs = dirname(__DIR__, 5) . '/assets/uploads/courses/videos';
if (is_dir($videoDirFs)) {
    $allFiles = @scandir($videoDirFs);
    if (is_array($allFiles)) {
        foreach ($allFiles as $f) {
            if (!is_string($f) || $f === '.' || $f === '..') {
                continue;
            }
            $ext = strtolower((string) pathinfo($f, PATHINFO_EXTENSION));
            if (!in_array($ext, ['mp4', 'webm', 'ogg'], true)) {
                continue;
            }
            $videoDirectoryFiles[] = $f;
        }
        natcasesort($videoDirectoryFiles);
        $videoDirectoryFiles = array_values($videoDirectoryFiles);
    }
}

$pptDirFs = dirname(__DIR__, 5) . '/assets/uploads/courses/presentations';
if (is_dir($pptDirFs)) {
    $allFiles = @scandir($pptDirFs);
    if (is_array($allFiles)) {
        foreach ($allFiles as $f) {
            if (!is_string($f) || $f === '.' || $f === '..') {
                continue;
            }
            $ext = strtolower((string) pathinfo($f, PATHINFO_EXTENSION));
            if (!in_array($ext, ['ppt', 'pptx', 'pdf'], true)) {
                continue;
            }
            $pptDirectoryFiles[] = $f;
        }
        natcasesort($pptDirectoryFiles);
        $pptDirectoryFiles = array_values($pptDirectoryFiles);
    }
}

if (is_array($selectedModuleLesson)) {
    $currentVideoUrl = trim((string) ($selectedModuleLesson['content_url'] ?? ''));
    if ($currentVideoUrl !== '') {
        $selectedModuleVideoFile = basename(parse_url($currentVideoUrl, PHP_URL_PATH) ?: $currentVideoUrl);
        $selectedModulePptFile = $selectedModuleVideoFile;
    }
}

if (!$editSections) {
    $editSections = [
        ['value' => 'metadata', 'label' => 'Metadata'],
        ['value' => 'front-card', 'label' => 'Front Card'],
        ['value' => 'landing', 'label' => 'Landing Page'],
        ['value' => 'summary', 'label' => 'Summary'],
        ['value' => 'certificate', 'label' => 'Certificate'],
    ];
}
