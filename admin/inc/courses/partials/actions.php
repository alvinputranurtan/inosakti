<?php
declare(strict_types=1);

require_once __DIR__ . '/actions/helpers.php';

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

require __DIR__ . '/actions/chapter.php';
require __DIR__ . '/actions/module.php';
require __DIR__ . '/actions/video_manager.php';
require __DIR__ . '/actions/course.php';

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
