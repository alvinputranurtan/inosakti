<?php
declare(strict_types=1);

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
