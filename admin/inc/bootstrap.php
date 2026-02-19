<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/inc/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

const ADMIN_SESSION_KEY = 'admin_user';

function admin_base_path(): string
{
    global $basePath;
    return (isset($basePath) && is_string($basePath)) ? $basePath : '';
}

function admin_url(string $path = '/'): string
{
    $base = rtrim(admin_base_path(), '/');
    $suffix = '/' . ltrim($path, '/');
    if ($base === '') {
        return $suffix;
    }
    return $base . $suffix;
}

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

    $db->set_charset('utf8mb4');
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

function admin_csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return (string) $_SESSION['csrf_token'];
}

function admin_verify_csrf(?string $token): bool
{
    $current = $_SESSION['csrf_token'] ?? '';
    return is_string($token) && $current !== '' && hash_equals($current, $token);
}

function admin_set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function admin_get_flash(): ?array
{
    if (!isset($_SESSION['flash']) || !is_array($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function admin_current_user(): ?array
{
    $u = $_SESSION[ADMIN_SESSION_KEY] ?? null;
    return is_array($u) ? $u : null;
}

function admin_require_login(): void
{
    if (admin_current_user() !== null) {
        return;
    }
    header('Location: ' . admin_url('/admin/login'));
    exit;
}

function admin_attempt_login(string $email, string $password): bool
{
    if (!admin_table_exists('users')) {
        admin_set_flash('error', 'Tabel users belum tersedia.');
        return false;
    }

    $sql = "SELECT id, name, email, password_hash, is_active
            FROM users
            WHERE email = ? AND deleted_at IS NULL
            LIMIT 1";
    $stmt = admin_db()->prepare($sql);
    if (!$stmt) {
        admin_set_flash('error', 'Gagal menyiapkan autentikasi.');
        return false;
    }
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row || (int) $row['is_active'] !== 1) {
        return false;
    }

    if (!password_verify($password, (string) $row['password_hash'])) {
        return false;
    }

    $roles = [];
    if (admin_table_exists('user_roles') && admin_table_exists('roles')) {
        $roleSql = "SELECT r.code
                    FROM roles r
                    JOIN user_roles ur ON ur.role_id = r.id
                    WHERE ur.user_id = ?";
        $roleStmt = admin_db()->prepare($roleSql);
        if ($roleStmt) {
            $roleUserId = (int) $row['id'];
            $roleStmt->bind_param('i', $roleUserId);
            $roleStmt->execute();
            $roleRes = $roleStmt->get_result();
            while ($rr = $roleRes->fetch_assoc()) {
                $roles[] = (string) $rr['code'];
            }
            $roleStmt->close();
        }
    }

    $_SESSION[ADMIN_SESSION_KEY] = [
        'id' => (int) $row['id'],
        'name' => (string) $row['name'],
        'email' => (string) $row['email'],
        'roles' => $roles,
    ];

    $uid = (int) $row['id'];
    $update = admin_db()->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?");
    if ($update) {
        $update->bind_param('i', $uid);
        $update->execute();
        $update->close();
    }

    return true;
}

function admin_user_roles(): array
{
    $u = admin_current_user();
    if (!$u || !isset($u['roles']) || !is_array($u['roles'])) {
        return [];
    }
    return array_values(array_filter(array_map('strval', $u['roles'])));
}

function admin_has_any_role(array $wanted): bool
{
    $roles = admin_user_roles();
    if (!$roles) {
        return false;
    }
    foreach ($wanted as $w) {
        if (in_array((string) $w, $roles, true)) {
            return true;
        }
    }
    return false;
}

function admin_can_access_admin_panel(): bool
{
    return admin_has_any_role(['super_admin', 'editor', 'hr_admin']);
}

function admin_default_home_for_current_user(): string
{
    if (admin_can_access_admin_panel()) {
        return admin_url('/admin/');
    }
    if (admin_has_any_role(['instructor', 'employee', 'student'])) {
        return admin_url('/portal');
    }
    return admin_url('/portal');
}

function admin_logout(): void
{
    unset($_SESSION[ADMIN_SESSION_KEY]);
}

function admin_e(string $v): string
{
    return htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
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
