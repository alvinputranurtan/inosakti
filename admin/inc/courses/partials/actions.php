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

if ($action === 'save_module_basic') {
    $courseId = (int) ($_POST['course_id'] ?? 0);
    $moduleLessonId = (int) ($_POST['module_lesson_id'] ?? 0);
    $moduleKind = trim((string) ($_POST['module_kind'] ?? 'article'));
    if ($courseId <= 0 || $moduleLessonId <= 0) {
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
    $ownedStmt = $db->prepare("SELECT l.id
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

    $hasLessonVariant = admin_table_has_column('course_lessons', 'lesson_variant');
    if ($hasLessonVariant) {
        $stmt = $db->prepare("UPDATE course_lessons
                              SET lesson_type = ?, lesson_variant = ?, updated_at = NOW()
                              WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('ssi', $targetType, $targetVariant, $moduleLessonId);
            $ok = $stmt->execute();
            $stmt->close();
            if ($ok) {
                admin_set_flash('success', 'Jenis modul berhasil diperbarui.');
            } else {
                admin_set_flash('error', 'Gagal menyimpan jenis modul.');
            }
        }
    } else {
        $stmt = $db->prepare("UPDATE course_lessons
                              SET lesson_type = ?, updated_at = NOW()
                              WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('si', $targetType, $moduleLessonId);
            $ok = $stmt->execute();
            $stmt->close();
            if ($ok) {
                admin_set_flash('success', 'Jenis modul berhasil diperbarui.');
            } else {
                admin_set_flash('error', 'Gagal menyimpan jenis modul.');
            }
        }
    }

    header('Location: ' . admin_url('/admin/courses?course_id=' . $courseId . '&edit_section=module:' . $moduleLessonId));
    exit;
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
