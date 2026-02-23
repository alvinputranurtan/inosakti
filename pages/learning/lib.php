<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/config.php';

function learning_db(): ?mysqli
{
    static $db = null;
    if ($db instanceof mysqli) {
        return $db;
    }
    global $dbConfig;
    $db = @new mysqli(
        (string) ($dbConfig['host'] ?? 'localhost'),
        (string) ($dbConfig['user'] ?? ''),
        (string) ($dbConfig['pass'] ?? ''),
        (string) ($dbConfig['name'] ?? ''),
        (int) ($dbConfig['port'] ?? 3306)
    );
    if ($db->connect_errno) {
        return null;
    }
    inosakti_init_db_connection($db);
    return $db;
}

function learning_table_exists(string $table): bool
{
    static $cache = [];
    if (array_key_exists($table, $cache)) {
        return $cache[$table];
    }
    $db = learning_db();
    if (!$db) {
        $cache[$table] = false;
        return false;
    }
    $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?");
    if (!$stmt) {
        $cache[$table] = false;
        return false;
    }
    $stmt->bind_param('s', $table);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $cache[$table] = ((int) ($row['cnt'] ?? 0)) > 0;
    return $cache[$table];
}

function learning_language_id(): ?int
{
    $db = learning_db();
    if (!$db || !learning_table_exists('languages')) {
        return null;
    }
    $stmt = $db->prepare("SELECT id FROM languages WHERE code = 'id' LIMIT 1");
    if ($stmt) {
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if ($row) {
            return (int) $row['id'];
        }
    }
    $res = $db->query("SELECT id FROM languages ORDER BY id ASC LIMIT 1");
    if ($res) {
        $row = $res->fetch_assoc();
        if ($row) {
            return (int) $row['id'];
        }
    }
    return null;
}

function learning_fetch_published_courses(int $limit = 12): array
{
    $db = learning_db();
    if (
        !$db ||
        !learning_table_exists('courses') ||
        !learning_table_exists('course_translations')
    ) {
        return [];
    }
    $langId = learning_language_id();
    $sql = "SELECT c.id, c.level, c.price, c.featured_image, c.status,
                   COALESCE(ct.slug, CONCAT('course-', c.id)) AS slug,
                   COALESCE(ct.title, CONCAT('Course #', c.id)) AS title,
                   COALESCE(ct.short_description, '') AS short_description
            FROM courses c
            LEFT JOIN course_translations ct ON ct.course_id = c.id" . ($langId ? " AND ct.language_id = " . $langId : '') . "
            WHERE c.deleted_at IS NULL AND c.status = 'published'
            ORDER BY c.published_at DESC, c.id DESC
            LIMIT " . max(1, $limit);
    $res = $db->query($sql);
    if (!$res) {
        return [];
    }
    return $res->fetch_all(MYSQLI_ASSOC);
}

function learning_fetch_course_by_slug(string $slug): ?array
{
    $slug = trim($slug);
    if ($slug === '') {
        return null;
    }
    $db = learning_db();
    if (
        !$db ||
        !learning_table_exists('courses') ||
        !learning_table_exists('course_translations')
    ) {
        return null;
    }
    $langId = learning_language_id();
    $sql = "SELECT c.id, c.level, c.price, c.featured_image, c.status, c.published_at,
                   COALESCE(ct.slug, CONCAT('course-', c.id)) AS slug,
                   COALESCE(ct.title, CONCAT('Course #', c.id)) AS title,
                   COALESCE(ct.short_description, '') AS short_description,
                   COALESCE(ct.full_description, '') AS full_description
            FROM courses c
            LEFT JOIN course_translations ct ON ct.course_id = c.id" . ($langId ? " AND ct.language_id = " . $langId : '') . "
            WHERE c.deleted_at IS NULL AND ct.slug = ?
            LIMIT 1";
    $stmt = $db->prepare($sql);
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param('s', $slug);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return is_array($row) ? $row : null;
}

function learning_fetch_course_modules_with_lessons(int $courseId): array
{
    if ($courseId <= 0) {
        return [];
    }
    $db = learning_db();
    if (
        !$db ||
        !learning_table_exists('course_modules') ||
        !learning_table_exists('course_lessons')
    ) {
        return [];
    }

    $modules = [];
    $stmt = $db->prepare("SELECT id, module_order, title FROM course_modules WHERE course_id = ? ORDER BY module_order ASC");
    if (!$stmt) {
        return [];
    }
    $stmt->bind_param('i', $courseId);
    $stmt->execute();
    $moduleRows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($moduleRows as $module) {
        $moduleId = (int) ($module['id'] ?? 0);
        $lessonStmt = $db->prepare("SELECT id, lesson_order, title, lesson_type, content_url, content_body, duration_seconds, is_preview
                                    FROM course_lessons
                                    WHERE module_id = ?
                                    ORDER BY lesson_order ASC");
        $lessons = [];
        if ($lessonStmt) {
            $lessonStmt->bind_param('i', $moduleId);
            $lessonStmt->execute();
            $lessons = $lessonStmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $lessonStmt->close();
        }
        $module['lessons'] = $lessons;
        $modules[] = $module;
    }
    return $modules;
}

function learning_fetch_course_resources(int $courseId): array
{
    if ($courseId <= 0) {
        return [];
    }
    $db = learning_db();
    if (!$db || !learning_table_exists('course_resources')) {
        return [];
    }
    $stmt = $db->prepare("SELECT id, resource_order, title, resource_type, resource_url, description
                          FROM course_resources
                          WHERE course_id = ?
                          ORDER BY resource_order ASC");
    if (!$stmt) {
        return [];
    }
    $stmt->bind_param('i', $courseId);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function learning_decode_json_object(?string $raw): array
{
    if (!is_string($raw) || trim($raw) === '') {
        return [];
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

function learning_fetch_course_page_config(int $courseId, string $pageKey): ?array
{
    if ($courseId <= 0 || trim($pageKey) === '') {
        return null;
    }
    $db = learning_db();
    if (!$db || !learning_table_exists('course_page_configs')) {
        return null;
    }
    $stmt = $db->prepare("SELECT id, page_key, title, subtitle, description, content_html, layout_json
                          FROM course_page_configs
                          WHERE course_id = ? AND page_key = ?
                          LIMIT 1");
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param('is', $courseId, $pageKey);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!is_array($row)) {
        return null;
    }
    $row['layout'] = learning_decode_json_object((string) ($row['layout_json'] ?? ''));
    return $row;
}

function learning_fetch_course_media(int $courseId, ?string $mediaKind = null): array
{
    if ($courseId <= 0) {
        return [];
    }
    $db = learning_db();
    if (!$db || !learning_table_exists('course_media')) {
        return [];
    }
    if ($mediaKind !== null && trim($mediaKind) !== '') {
        $stmt = $db->prepare("SELECT id, media_order, title, media_kind, file_url, mime_type, description, meta_json
                              FROM course_media
                              WHERE course_id = ? AND media_kind = ?
                              ORDER BY media_order ASC");
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('is', $courseId, $mediaKind);
    } else {
        $stmt = $db->prepare("SELECT id, media_order, title, media_kind, file_url, mime_type, description, meta_json
                              FROM course_media
                              WHERE course_id = ?
                              ORDER BY media_order ASC");
        if (!$stmt) {
            return [];
        }
        $stmt->bind_param('i', $courseId);
    }
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    foreach ($rows as &$row) {
        $row['meta'] = learning_decode_json_object((string) ($row['meta_json'] ?? ''));
    }
    unset($row);
    return $rows;
}

function learning_fetch_course_assessment(int $courseId, string $assessmentKey): ?array
{
    if ($courseId <= 0 || trim($assessmentKey) === '') {
        return null;
    }
    $db = learning_db();
    if (!$db || !learning_table_exists('course_assessments')) {
        return null;
    }
    $stmt = $db->prepare("SELECT id, assessment_key, title, instruction_text, pass_score, layout_json
                          FROM course_assessments
                          WHERE course_id = ? AND assessment_key = ?
                          LIMIT 1");
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param('is', $courseId, $assessmentKey);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!is_array($row)) {
        return null;
    }
    $row['layout'] = learning_decode_json_object((string) ($row['layout_json'] ?? ''));
    return $row;
}

function learning_fetch_assessment_questions_with_options(int $assessmentId): array
{
    if ($assessmentId <= 0) {
        return [];
    }
    $db = learning_db();
    if (
        !$db ||
        !learning_table_exists('course_assessment_questions') ||
        !learning_table_exists('course_assessment_options')
    ) {
        return [];
    }
    $stmt = $db->prepare("SELECT id, question_order, question_type, question_text, answer_text, hint_text, layout_json
                          FROM course_assessment_questions
                          WHERE assessment_id = ?
                          ORDER BY question_order ASC");
    if (!$stmt) {
        return [];
    }
    $stmt->bind_param('i', $assessmentId);
    $stmt->execute();
    $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    foreach ($questions as &$question) {
        $question['layout'] = learning_decode_json_object((string) ($question['layout_json'] ?? ''));
        $question['options'] = [];
        $questionId = (int) ($question['id'] ?? 0);
        if ($questionId <= 0) {
            continue;
        }
        $optStmt = $db->prepare("SELECT id, option_order, option_text, is_correct
                                 FROM course_assessment_options
                                 WHERE question_id = ?
                                 ORDER BY option_order ASC");
        if (!$optStmt) {
            continue;
        }
        $optStmt->bind_param('i', $questionId);
        $optStmt->execute();
        $question['options'] = $optStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $optStmt->close();
    }
    unset($question);

    return $questions;
}

function learning_format_duration(?int $seconds): string
{
    $s = max(0, (int) $seconds);
    if ($s === 0) {
        return '-';
    }
    $m = intdiv($s, 60);
    $h = intdiv($m, 60);
    $rm = $m % 60;
    if ($h > 0) {
        return $h . 'j ' . $rm . 'm';
    }
    return $m . 'm';
}
