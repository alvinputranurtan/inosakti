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
