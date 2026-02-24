<?php
declare(strict_types=1);

if (!function_exists('courses_slugify')) {
    function courses_slugify(string $value): string
    {
        $value = trim(strtolower($value));
        if ($value === '') {
            return '';
        }
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
        return trim($value, '-');
    }
}

if (!function_exists('courses_save_uploaded_image')) {
    function courses_save_uploaded_image(string $fieldName = 'front_card_image_file'): ?string
    {
        if (!isset($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
            return null;
        }
        $file = $_FILES[$fieldName];
        $err = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($err === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($err !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload gambar gagal.');
        }
        $tmp = (string) ($file['tmp_name'] ?? '');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            throw new RuntimeException('File upload tidak valid.');
        }
        $img = @getimagesize($tmp);
        if (!$img || !isset($img['mime'])) {
            throw new RuntimeException('File harus berupa gambar.');
        }
        $mime = strtolower((string) $img['mime']);
        $extMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
        ];
        if (!isset($extMap[$mime])) {
            throw new RuntimeException('Format gambar didukung: jpg, png, webp, gif.');
        }
        $ext = $extMap[$mime];
        $uploadDirFs = dirname(__DIR__, 4) . '/assets/uploads/courses';
        if (!is_dir($uploadDirFs) && !@mkdir($uploadDirFs, 0775, true) && !is_dir($uploadDirFs)) {
            throw new RuntimeException('Gagal membuat folder upload gambar.');
        }
        $filename = 'course-front-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest = $uploadDirFs . '/' . $filename;
        if (!@move_uploaded_file($tmp, $dest)) {
            throw new RuntimeException('Gagal menyimpan gambar.');
        }
        return admin_url('/assets/uploads/courses/' . $filename);
    }
}

if (!function_exists('courses_save_uploaded_video')) {
    function courses_save_uploaded_video(string $fieldName = 'module_video_file'): ?string
    {
        if (!isset($_FILES[$fieldName]) || !is_array($_FILES[$fieldName])) {
            return null;
        }
        $file = $_FILES[$fieldName];
        $err = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($err === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($err !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload video gagal.');
        }
        $tmp = (string) ($file['tmp_name'] ?? '');
        if ($tmp === '' || !is_uploaded_file($tmp)) {
            throw new RuntimeException('File upload video tidak valid.');
        }
        $origName = (string) ($file['name'] ?? '');
        $ext = strtolower((string) pathinfo($origName, PATHINFO_EXTENSION));
        $allowedExt = ['mp4', 'webm', 'ogg'];
        if (!in_array($ext, $allowedExt, true)) {
            throw new RuntimeException('Format video didukung: .mp4, .webm, .ogg');
        }
        $mime = strtolower((string) (@mime_content_type($tmp) ?: ''));
        $allowedMime = ['video/mp4', 'video/webm', 'video/ogg', 'application/octet-stream'];
        if ($mime !== '' && !in_array($mime, $allowedMime, true)) {
            throw new RuntimeException('MIME video tidak didukung.');
        }
        $uploadDirFs = dirname(__DIR__, 4) . '/assets/uploads/courses/videos';
        if (!is_dir($uploadDirFs) && !@mkdir($uploadDirFs, 0775, true) && !is_dir($uploadDirFs)) {
            throw new RuntimeException('Gagal membuat folder upload video.');
        }
        $filename = 'course-video-' . date('YmdHis') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest = $uploadDirFs . '/' . $filename;
        if (!@move_uploaded_file($tmp, $dest)) {
            throw new RuntimeException('Gagal menyimpan video.');
        }
        return admin_url('/assets/uploads/courses/videos/' . $filename);
    }
}

if (!function_exists('courses_video_dir_fs')) {
    function courses_video_dir_fs(): string
    {
        return dirname(__DIR__, 4) . '/assets/uploads/courses/videos';
    }
}

if (!function_exists('courses_video_public_url')) {
    function courses_video_public_url(string $fileName): string
    {
        return admin_url('/assets/uploads/courses/videos/' . ltrim($fileName, '/'));
    }
}

if (!function_exists('courses_extract_video_filename')) {
    function courses_extract_video_filename(string $value): string
    {
        $candidate = basename(parse_url($value, PHP_URL_PATH) ?: $value);
        $candidate = trim($candidate);
        if ($candidate === '' || !preg_match('/^[A-Za-z0-9._-]+$/', $candidate)) {
            return '';
        }
        $ext = strtolower((string) pathinfo($candidate, PATHINFO_EXTENSION));
        if (!in_array($ext, ['mp4', 'webm', 'ogg'], true)) {
            return '';
        }
        return $candidate;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

$token = $_POST['csrf_token'] ?? '';
if (!admin_verify_csrf(is_string($token) ? $token : null)) {
    admin_set_flash('error', 'Token keamanan tidak valid.');
    header('Location: ' . admin_url('/admin/courses'));
    exit;
}

$action = (string) ($_POST['action'] ?? 'status_update');

if ($action === 'save_chapter_basic') {
    $courseId = (int) ($_POST['course_id'] ?? 0);
    $chapterId = (int) ($_POST['chapter_id'] ?? 0);
    $chapterTitle = trim((string) ($_POST['chapter_title'] ?? ''));
    $chapterOrder = max(1, (int) ($_POST['chapter_order'] ?? 1));
    if ($courseId <= 0 || $chapterId <= 0 || $chapterTitle === '') {
        admin_set_flash('error', 'Data chapter tidak lengkap.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=chapter:' . $chapterId));
        exit;
    }
    $db = admin_db();
    try {
        $db->begin_transaction();

        $currentStmt = $db->prepare("SELECT module_order FROM course_modules WHERE id = ? AND course_id = ? LIMIT 1");
        if (!$currentStmt) {
            throw new RuntimeException('Gagal membaca chapter saat ini.');
        }
        $currentStmt->bind_param('ii', $chapterId, $courseId);
        $currentStmt->execute();
        $currentRow = $currentStmt->get_result()->fetch_assoc();
        $currentStmt->close();
        if (!$currentRow) {
            throw new RuntimeException('Chapter tidak ditemukan.');
        }
        $currentOrder = (int) ($currentRow['module_order'] ?? 1);

        $maxStmt = $db->prepare("SELECT COALESCE(MAX(module_order), 0) AS max_order FROM course_modules WHERE course_id = ?");
        if (!$maxStmt) {
            throw new RuntimeException('Gagal membaca urutan chapter maksimum.');
        }
        $maxStmt->bind_param('i', $courseId);
        $maxStmt->execute();
        $maxRow = $maxStmt->get_result()->fetch_assoc();
        $maxStmt->close();
        $maxOrder = max(1, (int) ($maxRow['max_order'] ?? 1));

        if ($chapterOrder > $maxOrder) {
            $chapterOrder = $maxOrder;
        }

        // Lepaskan chapter aktif dulu agar pergeseran urutan tidak bentrok UNIQUE(course_id,module_order).
        $detachStmt = $db->prepare("UPDATE course_modules SET module_order = 0, updated_at = NOW() WHERE id = ? AND course_id = ?");
        if (!$detachStmt) {
            throw new RuntimeException('Gagal menyiapkan perpindahan chapter.');
        }
        $detachStmt->bind_param('ii', $chapterId, $courseId);
        if (!$detachStmt->execute()) {
            $detachStmt->close();
            throw new RuntimeException('Gagal memindahkan chapter sementara.');
        }
        $detachStmt->close();

        if ($chapterOrder < $currentOrder) {
            $shiftStmt = $db->prepare("UPDATE course_modules
                                       SET module_order = module_order + 1, updated_at = NOW()
                                       WHERE course_id = ? AND module_order >= ? AND module_order < ?");
            if (!$shiftStmt) {
                throw new RuntimeException('Gagal menyiapkan pergeseran chapter.');
            }
            $shiftStmt->bind_param('iii', $courseId, $chapterOrder, $currentOrder);
            if (!$shiftStmt->execute()) {
                $shiftStmt->close();
                throw new RuntimeException('Gagal menggeser chapter (naik).');
            }
            $shiftStmt->close();
        } elseif ($chapterOrder > $currentOrder) {
            $shiftStmt = $db->prepare("UPDATE course_modules
                                       SET module_order = module_order - 1, updated_at = NOW()
                                       WHERE course_id = ? AND module_order > ? AND module_order <= ?");
            if (!$shiftStmt) {
                throw new RuntimeException('Gagal menyiapkan pergeseran chapter.');
            }
            $shiftStmt->bind_param('iii', $courseId, $currentOrder, $chapterOrder);
            if (!$shiftStmt->execute()) {
                $shiftStmt->close();
                throw new RuntimeException('Gagal menggeser chapter (turun).');
            }
            $shiftStmt->close();
        }

        $finalStmt = $db->prepare("UPDATE course_modules
                                   SET title = ?, module_order = ?, updated_at = NOW()
                                   WHERE id = ? AND course_id = ?");
        if (!$finalStmt) {
            throw new RuntimeException('Gagal menyiapkan update chapter akhir.');
        }
        $finalStmt->bind_param('siii', $chapterTitle, $chapterOrder, $chapterId, $courseId);
        if (!$finalStmt->execute()) {
            $finalStmt->close();
            throw new RuntimeException('Gagal menyimpan chapter.');
        }
        $finalStmt->close();

        $db->commit();
        admin_set_flash('success', 'Chapter berhasil diperbarui.');
    } catch (Throwable $e) {
        $db->rollback();
        admin_set_flash('error', 'Gagal update chapter: ' . $e->getMessage());
    }
    header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=chapter:' . $chapterId));
    exit;
}

if ($action === 'add_chapter_basic') {
    $courseId = (int) ($_POST['course_id'] ?? 0);
    $chapterTitle = trim((string) ($_POST['chapter_title_new'] ?? ''));
    $chapterOrder = max(1, (int) ($_POST['chapter_order_new'] ?? 1));
    if ($courseId <= 0 || $chapterTitle === '') {
        admin_set_flash('error', 'Data chapter baru tidak lengkap.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=chapter'));
        exit;
    }
    $db = admin_db();
    $newId = 0;
    try {
        $db->begin_transaction();

        $maxStmt = $db->prepare("SELECT COALESCE(MAX(module_order), 0) AS max_order FROM course_modules WHERE course_id = ?");
        if (!$maxStmt) {
            throw new RuntimeException('Gagal membaca urutan chapter maksimum.');
        }
        $maxStmt->bind_param('i', $courseId);
        $maxStmt->execute();
        $maxRow = $maxStmt->get_result()->fetch_assoc();
        $maxStmt->close();
        $maxOrder = (int) ($maxRow['max_order'] ?? 0);

        $maxAllowed = $maxOrder + 1;
        if ($chapterOrder > $maxAllowed) {
            $chapterOrder = $maxAllowed;
        }

        if ($maxOrder > 0 && $chapterOrder <= $maxOrder) {
            // Dua tahap untuk menghindari bentrok UNIQUE(course_id,module_order) saat geser +1.
            $tmpOffset = 1000000;
            $phase1 = $db->prepare("UPDATE course_modules
                                    SET module_order = module_order + ?, updated_at = NOW()
                                    WHERE course_id = ? AND module_order >= ?");
            if (!$phase1) {
                throw new RuntimeException('Gagal menyiapkan shift chapter (fase 1).');
            }
            $phase1->bind_param('iii', $tmpOffset, $courseId, $chapterOrder);
            if (!$phase1->execute()) {
                $phase1->close();
                throw new RuntimeException('Gagal menggeser chapter (fase 1).');
            }
            $phase1->close();

            $threshold = $tmpOffset + $chapterOrder;
            $netShift = $tmpOffset - 1;
            $phase2 = $db->prepare("UPDATE course_modules
                                    SET module_order = module_order - ?, updated_at = NOW()
                                    WHERE course_id = ? AND module_order >= ?");
            if (!$phase2) {
                throw new RuntimeException('Gagal menyiapkan shift chapter (fase 2).');
            }
            $phase2->bind_param('iii', $netShift, $courseId, $threshold);
            if (!$phase2->execute()) {
                $phase2->close();
                throw new RuntimeException('Gagal menggeser chapter (fase 2).');
            }
            $phase2->close();
        }

        $insertStmt = $db->prepare("INSERT INTO course_modules (course_id, module_order, title, created_at, updated_at)
                                    VALUES (?, ?, ?, NOW(), NOW())");
        if (!$insertStmt) {
            throw new RuntimeException('Gagal menyiapkan tambah chapter.');
        }
        $insertStmt->bind_param('iis', $courseId, $chapterOrder, $chapterTitle);
        if (!$insertStmt->execute()) {
            $insertStmt->close();
            throw new RuntimeException('Gagal menambahkan chapter.');
        }
        $newId = (int) $db->insert_id;
        $insertStmt->close();

        $db->commit();
        admin_set_flash('success', 'Chapter baru berhasil ditambahkan.');
    } catch (Throwable $e) {
        $db->rollback();
        admin_set_flash('error', 'Gagal tambah chapter: ' . $e->getMessage());
    }
    $targetSection = $newId > 0 ? ('chapter:' . $newId) : 'chapter';
    header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=' . urlencode($targetSection)));
    exit;
}

if ($action === 'delete_chapter_basic') {
    $courseId = (int) ($_POST['course_id'] ?? 0);
    $chapterId = (int) ($_POST['chapter_id'] ?? 0);
    if ($courseId <= 0 || $chapterId <= 0) {
        admin_set_flash('error', 'Data chapter untuk hapus tidak lengkap.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=chapter'));
        exit;
    }

    $db = admin_db();
    try {
        $db->begin_transaction();

        $chapterStmt = $db->prepare("SELECT module_order, title
                                     FROM course_modules
                                     WHERE id = ? AND course_id = ?
                                     LIMIT 1");
        if (!$chapterStmt) {
            throw new RuntimeException('Gagal membaca data chapter.');
        }
        $chapterStmt->bind_param('ii', $chapterId, $courseId);
        $chapterStmt->execute();
        $chapterRow = $chapterStmt->get_result()->fetch_assoc();
        $chapterStmt->close();
        if (!$chapterRow) {
            throw new RuntimeException('Chapter tidak ditemukan.');
        }
        $deletedOrder = (int) ($chapterRow['module_order'] ?? 0);
        if ($deletedOrder <= 0) {
            throw new RuntimeException('Nomor urut chapter tidak valid.');
        }

        $deleteStmt = $db->prepare("DELETE FROM course_modules WHERE id = ? AND course_id = ?");
        if (!$deleteStmt) {
            throw new RuntimeException('Gagal menyiapkan penghapusan chapter.');
        }
        $deleteStmt->bind_param('ii', $chapterId, $courseId);
        if (!$deleteStmt->execute()) {
            $deleteStmt->close();
            throw new RuntimeException('Gagal menghapus chapter.');
        }
        $deleteStmt->close();

        // Rapikan urutan: semua chapter setelah posisi terhapus turun 1.
        $tmpOffset = 1000000;
        $phase1 = $db->prepare("UPDATE course_modules
                                SET module_order = module_order + ?, updated_at = NOW()
                                WHERE course_id = ? AND module_order > ?");
        if (!$phase1) {
            throw new RuntimeException('Gagal menyiapkan perapihan chapter (fase 1).');
        }
        $phase1->bind_param('iii', $tmpOffset, $courseId, $deletedOrder);
        if (!$phase1->execute()) {
            $phase1->close();
            throw new RuntimeException('Gagal merapikan chapter (fase 1).');
        }
        $phase1->close();

        $threshold = $tmpOffset + $deletedOrder;
        $netShift = $tmpOffset + 1;
        $phase2 = $db->prepare("UPDATE course_modules
                                SET module_order = module_order - ?, updated_at = NOW()
                                WHERE course_id = ? AND module_order > ?");
        if (!$phase2) {
            throw new RuntimeException('Gagal menyiapkan perapihan chapter (fase 2).');
        }
        $phase2->bind_param('iii', $netShift, $courseId, $threshold);
        if (!$phase2->execute()) {
            $phase2->close();
            throw new RuntimeException('Gagal merapikan chapter (fase 2).');
        }
        $phase2->close();

        $db->commit();
        admin_set_flash('success', 'Chapter berhasil dihapus.');
    } catch (Throwable $e) {
        $db->rollback();
        admin_set_flash('error', 'Gagal hapus chapter: ' . $e->getMessage());
    }

    header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=chapter'));
    exit;
}

if ($action === 'add_module_basic') {
    $courseId = (int) ($_POST['course_id'] ?? 0);
    $chapterId = (int) ($_POST['chapter_id'] ?? 0);
    $moduleTitle = trim((string) ($_POST['module_title_new'] ?? ''));
    $moduleOrder = max(1, (int) ($_POST['module_order_new'] ?? 1));
    $moduleKind = trim((string) ($_POST['module_kind_new'] ?? 'article'));
    $moduleDurationMinutes = max(0, (int) ($_POST['module_duration_minutes_new'] ?? 0));
    $moduleDurationSeconds = $moduleDurationMinutes * 60;
    $moduleIsPreview = isset($_POST['module_is_preview_new']) ? 1 : 0;

    if ($courseId <= 0 || $chapterId <= 0 || $moduleTitle === '') {
        admin_set_flash('error', 'Data modul baru tidak lengkap.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=chapter:' . $chapterId));
        exit;
    }

    $kindMap = [
        'article' => ['lesson_type' => 'article', 'lesson_variant' => 'article'],
        'video' => ['lesson_type' => 'video', 'lesson_variant' => 'video'],
        'quiz_multiple_choice' => ['lesson_type' => 'quiz', 'lesson_variant' => 'quiz_multiple_choice'],
        'quiz_essay' => ['lesson_type' => 'quiz', 'lesson_variant' => 'quiz_essay'],
        'quiz_submit_file' => ['lesson_type' => 'quiz', 'lesson_variant' => 'quiz_submit_file'],
        'test_multiple_choice' => ['lesson_type' => 'test', 'lesson_variant' => 'test_multiple_choice'],
        'test_essay' => ['lesson_type' => 'test', 'lesson_variant' => 'test_essay'],
        'test_submit_file' => ['lesson_type' => 'test', 'lesson_variant' => 'test_submit_file'],
    ];
    if (!isset($kindMap[$moduleKind])) {
        $moduleKind = 'article';
    }
    $targetType = (string) $kindMap[$moduleKind]['lesson_type'];
    $targetVariant = (string) $kindMap[$moduleKind]['lesson_variant'];

    $db = admin_db();
    $ownerStmt = $db->prepare("SELECT id FROM course_modules WHERE id = ? AND course_id = ? LIMIT 1");
    if (!$ownerStmt) {
        admin_set_flash('error', 'Gagal validasi chapter.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=chapter:' . $chapterId));
        exit;
    }
    $ownerStmt->bind_param('ii', $chapterId, $courseId);
    $ownerStmt->execute();
    $ownerRow = $ownerStmt->get_result()->fetch_assoc();
    $ownerStmt->close();
    if (!$ownerRow) {
        admin_set_flash('error', 'Chapter tidak ditemukan pada kursus ini.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=chapter:' . $chapterId));
        exit;
    }

    $hasLessonVariant = admin_table_has_column('course_lessons', 'lesson_variant');
    $newLessonId = 0;
    try {
        $db->begin_transaction();

        $maxStmt = $db->prepare("SELECT COALESCE(MAX(lesson_order), 0) AS max_order FROM course_lessons WHERE module_id = ?");
        if (!$maxStmt) {
            throw new RuntimeException('Gagal membaca urutan modul maksimum.');
        }
        $maxStmt->bind_param('i', $chapterId);
        $maxStmt->execute();
        $maxRow = $maxStmt->get_result()->fetch_assoc();
        $maxStmt->close();
        $maxOrder = (int) ($maxRow['max_order'] ?? 0);

        $maxAllowed = $maxOrder + 1;
        if ($moduleOrder > $maxAllowed) {
            $moduleOrder = $maxAllowed;
        }

        if ($maxOrder > 0 && $moduleOrder <= $maxOrder) {
            $tmpOffset = 1000000;
            $phase1 = $db->prepare("UPDATE course_lessons
                                    SET lesson_order = lesson_order + ?, updated_at = NOW()
                                    WHERE module_id = ? AND lesson_order >= ?");
            if (!$phase1) {
                throw new RuntimeException('Gagal menyiapkan shift modul (fase 1).');
            }
            $phase1->bind_param('iii', $tmpOffset, $chapterId, $moduleOrder);
            if (!$phase1->execute()) {
                $phase1->close();
                throw new RuntimeException('Gagal menggeser modul (fase 1).');
            }
            $phase1->close();

            $threshold = $tmpOffset + $moduleOrder;
            $netShift = $tmpOffset - 1;
            $phase2 = $db->prepare("UPDATE course_lessons
                                    SET lesson_order = lesson_order - ?, updated_at = NOW()
                                    WHERE module_id = ? AND lesson_order >= ?");
            if (!$phase2) {
                throw new RuntimeException('Gagal menyiapkan shift modul (fase 2).');
            }
            $phase2->bind_param('iii', $netShift, $chapterId, $threshold);
            if (!$phase2->execute()) {
                $phase2->close();
                throw new RuntimeException('Gagal menggeser modul (fase 2).');
            }
            $phase2->close();
        }

        if ($hasLessonVariant) {
            $insertStmt = $db->prepare("INSERT INTO course_lessons
                                        (module_id, lesson_order, title, lesson_type, lesson_variant, content_url, content_body, duration_seconds, is_preview, created_at, updated_at)
                                        VALUES (?, ?, ?, ?, ?, '', '', ?, ?, NOW(), NOW())");
            if (!$insertStmt) {
                throw new RuntimeException('Gagal menyiapkan tambah modul.');
            }
            $insertStmt->bind_param('iisssii', $chapterId, $moduleOrder, $moduleTitle, $targetType, $targetVariant, $moduleDurationSeconds, $moduleIsPreview);
        } else {
            $insertStmt = $db->prepare("INSERT INTO course_lessons
                                        (module_id, lesson_order, title, lesson_type, content_url, content_body, duration_seconds, is_preview, created_at, updated_at)
                                        VALUES (?, ?, ?, ?, '', '', ?, ?, NOW(), NOW())");
            if (!$insertStmt) {
                throw new RuntimeException('Gagal menyiapkan tambah modul.');
            }
            $insertStmt->bind_param('iissii', $chapterId, $moduleOrder, $moduleTitle, $targetType, $moduleDurationSeconds, $moduleIsPreview);
        }

        if (!$insertStmt->execute()) {
            $insertStmt->close();
            throw new RuntimeException('Gagal menambahkan modul.');
        }
        $newLessonId = (int) $db->insert_id;
        $insertStmt->close();

        $db->commit();
        admin_set_flash('success', 'Modul baru berhasil ditambahkan.');
    } catch (Throwable $e) {
        $db->rollback();
        admin_set_flash('error', 'Gagal tambah modul: ' . $e->getMessage());
    }

    $targetSection = $newLessonId > 0 ? ('module:' . $newLessonId) : ('chapter:' . $chapterId);
    header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=' . urlencode($targetSection)));
    exit;
}

if ($action === 'video_pick_from_directory') {
    $courseId = (int) ($_POST['course_id'] ?? 0);
    $moduleLessonId = (int) ($_POST['module_lesson_id'] ?? 0);
    $videoFileName = courses_extract_video_filename((string) ($_POST['video_directory_file'] ?? ''));
    if ($courseId <= 0 || $moduleLessonId <= 0 || $videoFileName === '') {
        admin_set_flash('error', 'Pilih file video dari directory terlebih dahulu.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }
    $videoPath = courses_video_dir_fs() . '/' . $videoFileName;
    if (!is_file($videoPath)) {
        admin_set_flash('error', 'File video tidak ditemukan di directory.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }
    $db = admin_db();
    $ownedStmt = $db->prepare("SELECT l.id
                               FROM course_lessons l
                               JOIN course_modules m ON m.id = l.module_id
                               WHERE l.id = ? AND m.course_id = ?
                               LIMIT 1");
    if (!$ownedStmt) {
        admin_set_flash('error', 'Gagal validasi modul.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }
    $ownedStmt->bind_param('ii', $moduleLessonId, $courseId);
    $ownedStmt->execute();
    $owned = $ownedStmt->get_result()->fetch_assoc();
    $ownedStmt->close();
    if (!$owned) {
        admin_set_flash('error', 'Modul tidak ditemukan pada kursus ini.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }
    $url = courses_video_public_url($videoFileName);
    $stmt = $db->prepare("UPDATE course_lessons SET content_url = ?, updated_at = NOW() WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param('si', $url, $moduleLessonId);
        $ok = $stmt->execute();
        $stmt->close();
        admin_set_flash($ok ? 'success' : 'error', $ok ? 'Video dari directory berhasil dipakai.' : 'Gagal mengaitkan video ke modul.');
    }
    header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
    exit;
}

if ($action === 'video_rename_file') {
    $courseId = (int) ($_POST['course_id'] ?? 0);
    $moduleLessonId = (int) ($_POST['module_lesson_id'] ?? 0);
    $oldFileName = courses_extract_video_filename((string) ($_POST['video_directory_file'] ?? ''));
    $newBaseName = trim((string) ($_POST['video_rename_to'] ?? ''));
    if ($courseId <= 0 || $moduleLessonId <= 0 || $oldFileName === '' || $newBaseName === '') {
        admin_set_flash('error', 'Data rename video tidak lengkap.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }
    $safeBase = preg_replace('/[^A-Za-z0-9._-]+/', '-', $newBaseName) ?? '';
    $safeBase = trim((string) $safeBase, '-_.');
    if ($safeBase === '') {
        admin_set_flash('error', 'Nama file baru tidak valid.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }
    $ext = strtolower((string) pathinfo($oldFileName, PATHINFO_EXTENSION));
    $newFileName = $safeBase . '.' . $ext;
    $newFileName = courses_extract_video_filename($newFileName);
    if ($newFileName === '') {
        admin_set_flash('error', 'Nama file baru tidak valid.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }
    $dir = courses_video_dir_fs();
    $oldPath = $dir . '/' . $oldFileName;
    $newPath = $dir . '/' . $newFileName;
    if (!is_file($oldPath)) {
        admin_set_flash('error', 'File video lama tidak ditemukan.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }
    if (is_file($newPath)) {
        admin_set_flash('error', 'Nama file tujuan sudah dipakai.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }
    if (!@rename($oldPath, $newPath)) {
        admin_set_flash('error', 'Gagal rename file video.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }
    $db = admin_db();
    $oldUrl = courses_video_public_url($oldFileName);
    $newUrl = courses_video_public_url($newFileName);
    $up = $db->prepare("UPDATE course_lessons SET content_url = ?, updated_at = NOW() WHERE content_url = ?");
    if ($up) {
        $up->bind_param('ss', $newUrl, $oldUrl);
        $up->execute();
        $up->close();
    }
    admin_set_flash('success', 'Video berhasil di-rename.');
    header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
    exit;
}

if ($action === 'video_delete_file') {
    $courseId = (int) ($_POST['course_id'] ?? 0);
    $moduleLessonId = (int) ($_POST['module_lesson_id'] ?? 0);
    $fileName = courses_extract_video_filename((string) ($_POST['video_directory_file'] ?? ''));
    if ($courseId <= 0 || $moduleLessonId <= 0 || $fileName === '') {
        admin_set_flash('error', 'Pilih file video yang akan dihapus.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }
    $path = courses_video_dir_fs() . '/' . $fileName;
    if (!is_file($path)) {
        admin_set_flash('error', 'File video tidak ditemukan.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }
    if (!@unlink($path)) {
        admin_set_flash('error', 'Gagal menghapus file video.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }
    $db = admin_db();
    $url = courses_video_public_url($fileName);
    $up = $db->prepare("UPDATE course_lessons SET content_url = '', updated_at = NOW() WHERE content_url = ?");
    if ($up) {
        $up->bind_param('s', $url);
        $up->execute();
        $up->close();
    }
    admin_set_flash('success', 'Video berhasil dihapus dari directory.');
    header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
    exit;
}

if ($action === 'save_module_basic') {
    $courseId = (int) ($_POST['course_id'] ?? 0);
    $moduleLessonId = (int) ($_POST['module_lesson_id'] ?? 0);
    $moduleTitle = trim((string) ($_POST['module_title'] ?? ''));
    $moduleKind = trim((string) ($_POST['module_kind'] ?? 'article'));
    $moduleArticleHtml = (string) ($_POST['module_article_html'] ?? '');
    $moduleVideoIntroText = (string) ($_POST['module_video_intro_text'] ?? '');
    $moduleVideoUrl = trim((string) ($_POST['module_video_url'] ?? ''));
    $moduleLessonOrder = max(1, (int) ($_POST['module_lesson_order'] ?? 1));
    $moduleDurationMinutes = max(0, (int) ($_POST['module_duration_minutes'] ?? 0));
    $moduleDurationSeconds = $moduleDurationMinutes * 60;
    $moduleIsPreview = isset($_POST['module_is_preview']) ? 1 : 0;
    if ($courseId <= 0 || $moduleLessonId <= 0 || $moduleTitle === '') {
        admin_set_flash('error', 'Data modul tidak lengkap.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }

    $kindMap = [
        'article' => ['lesson_type' => 'article', 'lesson_variant' => 'article'],
        'video' => ['lesson_type' => 'video', 'lesson_variant' => 'video'],
        'quiz_multiple_choice' => ['lesson_type' => 'quiz', 'lesson_variant' => 'quiz_multiple_choice'],
        'quiz_essay' => ['lesson_type' => 'quiz', 'lesson_variant' => 'quiz_essay'],
        'quiz_submit_file' => ['lesson_type' => 'quiz', 'lesson_variant' => 'quiz_submit_file'],
        'test_multiple_choice' => ['lesson_type' => 'test', 'lesson_variant' => 'test_multiple_choice'],
        'test_essay' => ['lesson_type' => 'test', 'lesson_variant' => 'test_essay'],
        'test_submit_file' => ['lesson_type' => 'test', 'lesson_variant' => 'test_submit_file'],
    ];
    if (!isset($kindMap[$moduleKind])) {
        $moduleKind = 'article';
    }
    $targetType = (string) $kindMap[$moduleKind]['lesson_type'];
    $targetVariant = (string) $kindMap[$moduleKind]['lesson_variant'];

    $db = admin_db();
    $ownedStmt = $db->prepare("SELECT l.id, l.module_id, l.lesson_order
                               FROM course_lessons l
                               JOIN course_modules m ON m.id = l.module_id
                               WHERE l.id = ? AND m.course_id = ?
                               LIMIT 1");
    if (!$ownedStmt) {
        admin_set_flash('error', 'Gagal validasi kepemilikan modul.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }
    $ownedStmt->bind_param('ii', $moduleLessonId, $courseId);
    $ownedStmt->execute();
    $owned = $ownedStmt->get_result()->fetch_assoc();
    $ownedStmt->close();
    if (!$owned) {
        admin_set_flash('error', 'Modul tidak ditemukan pada kursus ini.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }
    $parentModuleId = (int) ($owned['module_id'] ?? 0);
    $currentOrder = (int) ($owned['lesson_order'] ?? 1);
    if ($parentModuleId <= 0) {
        admin_set_flash('error', 'Parent chapter modul tidak valid.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }

    $maxStmt = $db->prepare("SELECT COALESCE(MAX(lesson_order), 0) AS max_order FROM course_lessons WHERE module_id = ?");
    if (!$maxStmt) {
        admin_set_flash('error', 'Gagal membaca urutan modul maksimum.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }
    $maxStmt->bind_param('i', $parentModuleId);
    $maxStmt->execute();
    $maxRow = $maxStmt->get_result()->fetch_assoc();
    $maxStmt->close();
    $maxOrder = max(1, (int) ($maxRow['max_order'] ?? 1));
    if ($moduleLessonOrder > $maxOrder) {
        $moduleLessonOrder = $maxOrder;
    }

    try {
        if ($targetType === 'video') {
            $uploadedVideoUrl = courses_save_uploaded_video('module_video_file');
            if (is_string($uploadedVideoUrl) && $uploadedVideoUrl !== '') {
                $moduleVideoUrl = $uploadedVideoUrl;
            }
        }

        $db->begin_transaction();

        $detachStmt = $db->prepare("UPDATE course_lessons
                                    SET lesson_order = 0, updated_at = NOW()
                                    WHERE id = ? AND module_id = ?");
        if (!$detachStmt) {
            throw new RuntimeException('Gagal menyiapkan perpindahan urutan modul.');
        }
        $detachStmt->bind_param('ii', $moduleLessonId, $parentModuleId);
        if (!$detachStmt->execute()) {
            $detachStmt->close();
            throw new RuntimeException('Gagal memindahkan modul sementara.');
        }
        $detachStmt->close();

        if ($moduleLessonOrder < $currentOrder) {
            $shiftStmt = $db->prepare("UPDATE course_lessons
                                       SET lesson_order = lesson_order + 1, updated_at = NOW()
                                       WHERE module_id = ? AND lesson_order >= ? AND lesson_order < ?");
            if (!$shiftStmt) {
                throw new RuntimeException('Gagal menyiapkan pergeseran modul.');
            }
            $shiftStmt->bind_param('iii', $parentModuleId, $moduleLessonOrder, $currentOrder);
            if (!$shiftStmt->execute()) {
                $shiftStmt->close();
                throw new RuntimeException('Gagal menggeser urutan modul (naik).');
            }
            $shiftStmt->close();
        } elseif ($moduleLessonOrder > $currentOrder) {
            $shiftStmt = $db->prepare("UPDATE course_lessons
                                       SET lesson_order = lesson_order - 1, updated_at = NOW()
                                       WHERE module_id = ? AND lesson_order > ? AND lesson_order <= ?");
            if (!$shiftStmt) {
                throw new RuntimeException('Gagal menyiapkan pergeseran modul.');
            }
            $shiftStmt->bind_param('iii', $parentModuleId, $currentOrder, $moduleLessonOrder);
            if (!$shiftStmt->execute()) {
                $shiftStmt->close();
                throw new RuntimeException('Gagal menggeser urutan modul (turun).');
            }
            $shiftStmt->close();
        }

        $hasLessonVariant = admin_table_has_column('course_lessons', 'lesson_variant');
        if ($hasLessonVariant) {
            if ($targetType === 'article') {
                $stmt = $db->prepare("UPDATE course_lessons
                                      SET title = ?, lesson_order = ?, lesson_type = ?, lesson_variant = ?, content_body = ?, duration_seconds = ?, is_preview = ?, updated_at = NOW()
                                      WHERE id = ?");
                if (!$stmt) {
                    throw new RuntimeException('Gagal menyiapkan simpan modul.');
                }
                $stmt->bind_param('sisssiii', $moduleTitle, $moduleLessonOrder, $targetType, $targetVariant, $moduleArticleHtml, $moduleDurationSeconds, $moduleIsPreview, $moduleLessonId);
            } else {
                $stmt = $db->prepare("UPDATE course_lessons
                                      SET title = ?, lesson_order = ?, lesson_type = ?, lesson_variant = ?, duration_seconds = ?, is_preview = ?, updated_at = NOW()
                                      WHERE id = ?");
                if (!$stmt) {
                    throw new RuntimeException('Gagal menyiapkan simpan modul.');
                }
                $stmt->bind_param('sissiii', $moduleTitle, $moduleLessonOrder, $targetType, $targetVariant, $moduleDurationSeconds, $moduleIsPreview, $moduleLessonId);
                if ($targetType === 'video') {
                    $stmt->close();
                    $stmt = $db->prepare("UPDATE course_lessons
                                          SET title = ?, lesson_order = ?, lesson_type = ?, lesson_variant = ?, content_body = ?, content_url = ?, duration_seconds = ?, is_preview = ?, updated_at = NOW()
                                          WHERE id = ?");
                    if (!$stmt) {
                        throw new RuntimeException('Gagal menyiapkan simpan modul video.');
                    }
                    $stmt->bind_param('sissssiii', $moduleTitle, $moduleLessonOrder, $targetType, $targetVariant, $moduleVideoIntroText, $moduleVideoUrl, $moduleDurationSeconds, $moduleIsPreview, $moduleLessonId);
                }
            }
        } else {
            if ($targetType === 'article') {
                $stmt = $db->prepare("UPDATE course_lessons
                                      SET title = ?, lesson_order = ?, lesson_type = ?, content_body = ?, duration_seconds = ?, is_preview = ?, updated_at = NOW()
                                      WHERE id = ?");
                if (!$stmt) {
                    throw new RuntimeException('Gagal menyiapkan simpan modul.');
                }
                $stmt->bind_param('sissiii', $moduleTitle, $moduleLessonOrder, $targetType, $moduleArticleHtml, $moduleDurationSeconds, $moduleIsPreview, $moduleLessonId);
            } else {
                $stmt = $db->prepare("UPDATE course_lessons
                                      SET title = ?, lesson_order = ?, lesson_type = ?, duration_seconds = ?, is_preview = ?, updated_at = NOW()
                                      WHERE id = ?");
                if (!$stmt) {
                    throw new RuntimeException('Gagal menyiapkan simpan modul.');
                }
                $stmt->bind_param('sisiii', $moduleTitle, $moduleLessonOrder, $targetType, $moduleDurationSeconds, $moduleIsPreview, $moduleLessonId);
                if ($targetType === 'video') {
                    $stmt->close();
                    $stmt = $db->prepare("UPDATE course_lessons
                                          SET title = ?, lesson_order = ?, lesson_type = ?, content_body = ?, content_url = ?, duration_seconds = ?, is_preview = ?, updated_at = NOW()
                                          WHERE id = ?");
                    if (!$stmt) {
                        throw new RuntimeException('Gagal menyiapkan simpan modul video.');
                    }
                    $stmt->bind_param('sisssiii', $moduleTitle, $moduleLessonOrder, $targetType, $moduleVideoIntroText, $moduleVideoUrl, $moduleDurationSeconds, $moduleIsPreview, $moduleLessonId);
                }
            }
        }

        $ok = $stmt->execute();
        $stmt->close();
        if (!$ok) {
            throw new RuntimeException('Gagal menyimpan modul.');
        }

        $db->commit();
        admin_set_flash('success', 'Modul berhasil diperbarui.');
    } catch (Throwable $e) {
        $db->rollback();
        admin_set_flash('error', 'Gagal menyimpan modul: ' . $e->getMessage());
    }

    header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
    exit;
}

if ($action === 'delete_module_basic') {
    $courseId = (int) ($_POST['course_id'] ?? 0);
    $moduleLessonId = (int) ($_POST['module_lesson_id'] ?? 0);
    if ($courseId <= 0 || $moduleLessonId <= 0) {
        admin_set_flash('error', 'Data modul untuk hapus tidak lengkap.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId));
        exit;
    }

    $db = admin_db();
    try {
        $db->begin_transaction();

        $findStmt = $db->prepare("SELECT l.module_id, l.lesson_order
                                  FROM course_lessons l
                                  JOIN course_modules m ON m.id = l.module_id
                                  WHERE l.id = ? AND m.course_id = ?
                                  LIMIT 1");
        if (!$findStmt) {
            throw new RuntimeException('Gagal membaca modul.');
        }
        $findStmt->bind_param('ii', $moduleLessonId, $courseId);
        $findStmt->execute();
        $row = $findStmt->get_result()->fetch_assoc();
        $findStmt->close();
        if (!$row) {
            throw new RuntimeException('Modul tidak ditemukan.');
        }
        $parentModuleId = (int) ($row['module_id'] ?? 0);
        $deletedOrder = (int) ($row['lesson_order'] ?? 0);
        if ($parentModuleId <= 0 || $deletedOrder <= 0) {
            throw new RuntimeException('Data urutan modul tidak valid.');
        }

        $deleteStmt = $db->prepare("DELETE FROM course_lessons WHERE id = ?");
        if (!$deleteStmt) {
            throw new RuntimeException('Gagal menyiapkan hapus modul.');
        }
        $deleteStmt->bind_param('i', $moduleLessonId);
        if (!$deleteStmt->execute()) {
            $deleteStmt->close();
            throw new RuntimeException('Gagal menghapus modul.');
        }
        $deleteStmt->close();

        $tmpOffset = 1000000;
        $phase1 = $db->prepare("UPDATE course_lessons
                                SET lesson_order = lesson_order + ?, updated_at = NOW()
                                WHERE module_id = ? AND lesson_order > ?");
        if (!$phase1) {
            throw new RuntimeException('Gagal menyiapkan perapihan modul (fase 1).');
        }
        $phase1->bind_param('iii', $tmpOffset, $parentModuleId, $deletedOrder);
        if (!$phase1->execute()) {
            $phase1->close();
            throw new RuntimeException('Gagal merapikan modul (fase 1).');
        }
        $phase1->close();

        $threshold = $tmpOffset + $deletedOrder;
        $netShift = $tmpOffset + 1;
        $phase2 = $db->prepare("UPDATE course_lessons
                                SET lesson_order = lesson_order - ?, updated_at = NOW()
                                WHERE module_id = ? AND lesson_order > ?");
        if (!$phase2) {
            throw new RuntimeException('Gagal menyiapkan perapihan modul (fase 2).');
        }
        $phase2->bind_param('iii', $netShift, $parentModuleId, $threshold);
        if (!$phase2->execute()) {
            $phase2->close();
            throw new RuntimeException('Gagal merapikan modul (fase 2).');
        }
        $phase2->close();

        $db->commit();
        admin_set_flash('success', 'Modul berhasil dihapus.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=chapter:' . $parentModuleId));
        exit;
    } catch (Throwable $e) {
        $db->rollback();
        admin_set_flash('error', 'Gagal hapus modul: ' . $e->getMessage());
        header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
        exit;
    }
}

if ($action === 'save_course_basic') {
    $id = (int) ($_POST['id'] ?? 0);
    $editSection = (string) ($_POST['edit_section'] ?? 'metadata');
    $heroBorderPreset = (string) ($_POST['hero_border_preset'] ?? 'border-white');
    $heroBgPreset = (string) ($_POST['hero_bg_preset'] ?? 'slate-cyan');
    $landingDescription = trim((string) ($_POST['landing_description'] ?? ''));
    $title = trim((string) ($_POST['title'] ?? ''));
    $slugInput = trim((string) ($_POST['slug'] ?? ''));
    $slug = courses_slugify($slugInput !== '' ? $slugInput : $title);
    $shortDescription = trim((string) ($_POST['short_description'] ?? ''));
    $authorName = trim((string) ($_POST['author_name'] ?? ''));
    $frontCardImage = trim((string) ($_POST['featured_image'] ?? ''));
    $uploadedFrontCardImage = courses_save_uploaded_image('front_card_image_file');
    if (is_string($uploadedFrontCardImage) && $uploadedFrontCardImage !== '') {
        $frontCardImage = $uploadedFrontCardImage;
    }
    $price = (float) ($_POST['price'] ?? 0);
    $status = (string) ($_POST['status'] ?? 'draft');
    $levelInput = (string) ($_POST['level_group'] ?? 'beginner');
    $levelCustom = trim((string) ($_POST['level_group_custom'] ?? ''));
    $levelAllowed = ['beginner', 'intermediate', 'advanced'];
    $isCustomGroup = $levelInput === 'custom';
    $level = in_array($levelInput, $levelAllowed, true) ? $levelInput : ($isCustomGroup ? 'group' : 'beginner');
    $courseGroupLabel = $isCustomGroup ? ($levelCustom !== '' ? $levelCustom : 'custom') : null;
    $hasCourseLevelGroupLabel = admin_table_has_column('courses', 'level_group_label');
    $allowed = ['draft', 'published', 'archived'];
    if ($title === '' || $slug === '') {
        admin_set_flash('error', 'Judul dan slug wajib diisi.');
        header('Location: ' . admin_url('/admin/courses' . ($id > 0 ? '?course_id=' . $id . '&edit_section=' . urlencode($editSection) : '?mode=create&edit_section=' . urlencode($editSection))));
        exit;
    }
    if (!in_array($status, $allowed, true)) {
        admin_set_flash('error', 'Status tidak valid.');
        header('Location: ' . admin_url('/admin/courses' . ($id > 0 ? '?course_id=' . $id . '&edit_section=' . urlencode($editSection) : '?mode=create&edit_section=' . urlencode($editSection))));
        exit;
    }

    if ($editSection === 'landing') {
        if ($id <= 0) {
            admin_set_flash('error', 'Simpan metadata kursus dulu sebelum mengatur landing page.');
            header('Location: ' . admin_url('/admin/courses?mode=create&edit_section=metadata'));
            exit;
        }
        if (!admin_table_exists('course_page_configs')) {
            admin_set_flash('error', 'Tabel course_page_configs belum tersedia. Jalankan migration 019.');
            header('Location: ' . admin_url('/admin/courses?course_id=' . $id . '&edit_section=landing'));
            exit;
        }
        $borderAllowed = ['border-white', 'border-cyan', 'border-amber', 'border-emerald'];
        $bgAllowed = ['slate-cyan', 'indigo-blue', 'emerald-teal', 'amber-rose'];
        $heroBorderPreset = in_array($heroBorderPreset, $borderAllowed, true) ? $heroBorderPreset : 'border-white';
        $heroBgPreset = in_array($heroBgPreset, $bgAllowed, true) ? $heroBgPreset : 'slate-cyan';
        $layoutJson = json_encode([
            'hero_border_preset' => $heroBorderPreset,
            'hero_bg_preset' => $heroBgPreset,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $stmt = admin_db()->prepare("SELECT id, title, subtitle, content_html
                                     FROM course_page_configs
                                     WHERE course_id = ? AND page_key = 'landing'
                                     LIMIT 1");
        $existing = null;
        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $existing = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        }

        if ($existing) {
            $up = admin_db()->prepare("UPDATE course_page_configs
                                       SET description = ?, layout_json = ?, updated_at = NOW()
                                       WHERE id = ?");
            if ($up) {
                $cfgId = (int) ($existing['id'] ?? 0);
                $up->bind_param('ssi', $landingDescription, $layoutJson, $cfgId);
                $up->execute();
                $up->close();
            }
        } else {
            $ins = admin_db()->prepare("INSERT INTO course_page_configs
                    (course_id, page_key, title, subtitle, description, content_html, layout_json, created_at, updated_at)
                    VALUES (?, 'landing', NULL, NULL, ?, NULL, ?, NOW(), NOW())");
            if ($ins) {
                $ins->bind_param('iss', $id, $landingDescription, $layoutJson);
                $ins->execute();
                $ins->close();
            }
        }

        admin_set_flash('success', 'Landing page berhasil diperbarui.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $id . '&edit_section=landing'));
        exit;
    }

    $langId = null;
    if (admin_table_exists('languages')) {
        $langRes = admin_db()->query("SELECT id FROM languages WHERE code='id' ORDER BY id ASC LIMIT 1");
        if ($langRes) {
            $langRow = $langRes->fetch_assoc();
            if ($langRow) {
                $langId = (int) ($langRow['id'] ?? 0);
            }
        }
    }
    if ($langId === null || $langId <= 0) {
        admin_set_flash('error', 'Bahasa default (id) tidak ditemukan.');
        header('Location: ' . admin_url('/admin/courses' . ($id > 0 ? '?course_id=' . $id . '&edit_section=' . urlencode($editSection) : '?mode=create&edit_section=' . urlencode($editSection))));
        exit;
    }

    admin_db()->begin_transaction();
    try {
        $instructorId = null;
        if (admin_table_exists('instructors') && $authorName !== '') {
            $findInstructor = admin_db()->prepare("SELECT id FROM instructors WHERE LOWER(TRIM(display_name)) = LOWER(TRIM(?)) LIMIT 1");
            if ($findInstructor) {
                $findInstructor->bind_param('s', $authorName);
                $findInstructor->execute();
                $rowInstructor = $findInstructor->get_result()->fetch_assoc();
                $findInstructor->close();
                if ($rowInstructor) {
                    $instructorId = (int) ($rowInstructor['id'] ?? 0);
                }
            }
            if (($instructorId ?? 0) > 0) {
            } else {
                $insInstructor = admin_db()->prepare("INSERT INTO instructors (display_name, bio) VALUES (?, NULL)");
                if ($insInstructor) {
                    $insInstructor->bind_param('s', $authorName);
                    $insInstructor->execute();
                    $instructorId = (int) admin_db()->insert_id;
                    $insInstructor->close();
                }
            }
        }

        if ($id > 0) {
            if (($instructorId ?? 0) > 0) {
                $sql = "UPDATE courses SET price = ?, status = ?, level = ?, featured_image = NULLIF(?, ''), instructor_id = ?, ";
                if ($hasCourseLevelGroupLabel) {
                    $sql .= "level_group_label = NULLIF(?, ''), ";
                }
                $sql .= "published_at = IF(?='published' AND published_at IS NULL, NOW(), published_at), updated_at = NOW() WHERE id = ?";
                $stmt = admin_db()->prepare($sql);
                if (!$stmt) {
                    throw new RuntimeException('Gagal menyiapkan update course.');
                }
                $groupBind = (string) ($courseGroupLabel ?? '');
                if ($hasCourseLevelGroupLabel) {
                    $stmt->bind_param('dsssissi', $price, $status, $level, $frontCardImage, $instructorId, $groupBind, $status, $id);
                } else {
                    $stmt->bind_param('dsssisi', $price, $status, $level, $frontCardImage, $instructorId, $status, $id);
                }
                $stmt->execute();
                $stmt->close();
            } else {
                $sql = "UPDATE courses SET price = ?, status = ?, level = ?, featured_image = NULLIF(?, ''), ";
                if ($hasCourseLevelGroupLabel) {
                    $sql .= "level_group_label = NULLIF(?, ''), ";
                }
                $sql .= "published_at = IF(?='published' AND published_at IS NULL, NOW(), published_at), updated_at = NOW() WHERE id = ?";
                $stmt = admin_db()->prepare($sql);
                if (!$stmt) {
                    throw new RuntimeException('Gagal menyiapkan update course.');
                }
                $groupBind = (string) ($courseGroupLabel ?? '');
                if ($hasCourseLevelGroupLabel) {
                    $stmt->bind_param('dsssssi', $price, $status, $level, $frontCardImage, $groupBind, $status, $id);
                } else {
                    $stmt->bind_param('dssssi', $price, $status, $level, $frontCardImage, $status, $id);
                }
                $stmt->execute();
                $stmt->close();
            }
        } else {
            if (($instructorId ?? 0) > 0) {
                $sql = "INSERT INTO courses (instructor_id, level, " . ($hasCourseLevelGroupLabel ? "level_group_label, " : "") . "price, status, featured_image, published_at, created_at, updated_at)
                        VALUES (?, ?, " . ($hasCourseLevelGroupLabel ? "NULLIF(?, ''), " : "") . "?, ?, NULLIF(?, ''), IF(?='published', NOW(), NULL), NOW(), NOW())";
                $stmt = admin_db()->prepare($sql);
                if (!$stmt) {
                    throw new RuntimeException('Gagal menyiapkan insert course.');
                }
                $groupBind = (string) ($courseGroupLabel ?? '');
                if ($hasCourseLevelGroupLabel) {
                    $stmt->bind_param('issdsss', $instructorId, $level, $groupBind, $price, $status, $frontCardImage, $status);
                } else {
                    $stmt->bind_param('isdsss', $instructorId, $level, $price, $status, $frontCardImage, $status);
                }
                $stmt->execute();
                $id = (int) admin_db()->insert_id;
                $stmt->close();
            } else {
                $sql = "INSERT INTO courses (level, " . ($hasCourseLevelGroupLabel ? "level_group_label, " : "") . "price, status, featured_image, published_at, created_at, updated_at)
                        VALUES (?, " . ($hasCourseLevelGroupLabel ? "NULLIF(?, ''), " : "") . "?, ?, NULLIF(?, ''), IF(?='published', NOW(), NULL), NOW(), NOW())";
                $stmt = admin_db()->prepare($sql);
                if (!$stmt) {
                    throw new RuntimeException('Gagal menyiapkan insert course.');
                }
                $groupBind = (string) ($courseGroupLabel ?? '');
                if ($hasCourseLevelGroupLabel) {
                    $stmt->bind_param('ssdsss', $level, $groupBind, $price, $status, $frontCardImage, $status);
                } else {
                    $stmt->bind_param('sdsss', $level, $price, $status, $frontCardImage, $status);
                }
                $stmt->execute();
                $id = (int) admin_db()->insert_id;
                $stmt->close();
            }
        }

        if (admin_table_exists('course_translations')) {
            $check = admin_db()->prepare("SELECT id FROM course_translations WHERE course_id = ? AND language_id = ? LIMIT 1");
            if (!$check) {
                throw new RuntimeException('Gagal cek translation.');
            }
            $check->bind_param('ii', $id, $langId);
            $check->execute();
            $existing = $check->get_result()->fetch_assoc();
            $check->close();

            if ($existing) {
                $translationId = (int) ($existing['id'] ?? 0);
                $up = admin_db()->prepare("UPDATE course_translations
                    SET slug = ?, title = ?, short_description = ?, updated_at = NOW()
                    WHERE id = ?");
                if (!$up) {
                    throw new RuntimeException('Gagal update translation.');
                }
                $up->bind_param('sssi', $slug, $title, $shortDescription, $translationId);
                $up->execute();
                $up->close();
            } else {
                $ins = admin_db()->prepare("INSERT INTO course_translations
                    (course_id, language_id, slug, title, short_description, full_description, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, '', NOW(), NOW())");
                if (!$ins) {
                    throw new RuntimeException('Gagal insert translation.');
                }
                $ins->bind_param('iisss', $id, $langId, $slug, $title, $shortDescription);
                $ins->execute();
                $ins->close();
            }
        }

        admin_db()->commit();
        admin_set_flash('success', 'Kursus berhasil disimpan.');
        header('Location: ' . admin_url('/admin/courses?course_id=' . $id . '&edit_section=' . urlencode($editSection)));
        exit;
    } catch (Throwable $e) {
        admin_db()->rollback();
        admin_set_flash('error', 'Gagal menyimpan kursus: ' . $e->getMessage());
        header('Location: ' . admin_url('/admin/courses' . ($id > 0 ? '?course_id=' . $id . '&edit_section=' . urlencode($editSection) : '?mode=create&edit_section=' . urlencode($editSection))));
        exit;
    }
}

$id = (int) ($_POST['id'] ?? 0);
$status = (string) ($_POST['status'] ?? '');
$allowed = ['draft', 'published', 'archived'];
if ($id > 0 && in_array($status, $allowed, true)) {
    $sql = "UPDATE courses SET status = ?, published_at = IF(?='published' AND published_at IS NULL, NOW(), published_at) WHERE id = ?";
    $stmt = admin_db()->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('ssi', $status, $status, $id);
        $stmt->execute();
        $stmt->close();
        admin_set_flash('success', 'Status kursus berhasil diperbarui.');
    }
}

header('Location: ' . admin_url('/admin/courses'));
exit;
