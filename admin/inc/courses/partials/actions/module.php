<?php
declare(strict_types=1);

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
        'powerpoint' => ['lesson_type' => 'presentation', 'lesson_variant' => 'powerpoint'],
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
        'powerpoint' => ['lesson_type' => 'presentation', 'lesson_variant' => 'powerpoint'],
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
