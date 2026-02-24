<?php
declare(strict_types=1);

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
