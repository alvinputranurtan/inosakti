<?php
declare(strict_types=1);

function admin_db(): mysqli
{
    static $db = null;
    global $dbConfig;

    if ($db instanceof mysqli) {
        return $db;
    }

    $db = @new mysqli(
        (string) ($dbConfig['host'] ?? 'localhost'),
        (string) ($dbConfig['user'] ?? ''),
        (string) ($dbConfig['pass'] ?? ''),
        (string) ($dbConfig['name'] ?? ''),
        (int) ($dbConfig['port'] ?? 3306)
    );

    if ($db->connect_errno) {
        http_response_code(500);
        exit('Database connection failed.');
    }

    inosakti_init_db_connection($db);
    return $db;
}

function admin_table_exists(string $table): bool
{
    static $cache = [];
    if (array_key_exists($table, $cache)) {
        return $cache[$table];
    }

    $sql = "SELECT COUNT(*) AS cnt
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?";
    $stmt = admin_db()->prepare($sql);
    if (!$stmt) {
        $cache[$table] = false;
        return false;
    }
    $stmt->bind_param('s', $table);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $cache[$table] = ((int) ($res['cnt'] ?? 0)) > 0;
    return $cache[$table];
}

function admin_table_has_column(string $table, string $column): bool
{
    static $cache = [];
    $key = $table . '.' . $column;
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }

    $sql = "SELECT COUNT(*) AS cnt
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?";
    $stmt = admin_db()->prepare($sql);
    if (!$stmt) {
        $cache[$key] = false;
        return false;
    }
    $stmt->bind_param('ss', $table, $column);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    $cache[$key] = ((int) ($res['cnt'] ?? 0)) > 0;
    return $cache[$key];
}

function admin_count_or_zero(string $sql): int
{
    $res = admin_db()->query($sql);
    if (!$res) {
        return 0;
    }
    $row = $res->fetch_assoc();
    return (int) array_values($row ?: ['0'])[0];
}

