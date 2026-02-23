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
const ADMIN_REMEMBER_COOKIE = 'inos_remember';
const ADMIN_REMEMBER_DAYS = 30;
const ADMIN_OTP_TTL_SECONDS = 600;
const ADMIN_OTP_MAX_ATTEMPTS = 5;

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

function admin_is_local_env(): bool
{
    $host = (string) ($_SERVER['HTTP_HOST'] ?? '');
    $hostNoPort = preg_replace('/:\d+$/', '', $host) ?: '';
    return in_array($hostNoPort, ['localhost', '127.0.0.1'], true);
}

function admin_is_https_request(): bool
{
    if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    return ((string) ($_SERVER['SERVER_PORT'] ?? '')) === '443';
}

function admin_set_cookie(string $name, string $value, int $expiresAt): void
{
    setcookie($name, $value, [
        'expires' => $expiresAt,
        'path' => '/',
        'secure' => admin_is_https_request(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function admin_clear_cookie(string $name): void
{
    admin_set_cookie($name, '', time() - 3600);
}

function admin_fetch_roles_for_user(int $userId): array
{
    $roles = [];
    if (admin_table_exists('user_roles') && admin_table_exists('roles')) {
        $roleSql = "SELECT r.code
                    FROM roles r
                    JOIN user_roles ur ON ur.role_id = r.id
                    WHERE ur.user_id = ?";
        $roleStmt = admin_db()->prepare($roleSql);
        if ($roleStmt) {
            $roleStmt->bind_param('i', $userId);
            $roleStmt->execute();
            $roleRes = $roleStmt->get_result();
            while ($rr = $roleRes->fetch_assoc()) {
                $roles[] = (string) $rr['code'];
            }
            $roleStmt->close();
        }
    }
    return $roles;
}

function admin_login_user(array $row, array $roles): void
{
    session_regenerate_id(true);
    $_SESSION[ADMIN_SESSION_KEY] = [
        'id' => (int) $row['id'],
        'name' => (string) $row['name'],
        'email' => (string) $row['email'],
        'roles' => $roles,
    ];
}

function admin_load_user_by_email(string $email): ?array
{
    if (!admin_table_exists('users')) {
        return null;
    }

    $hasDeletedAt = admin_table_has_column('users', 'deleted_at');
    $whereDeleted = $hasDeletedAt ? " AND deleted_at IS NULL" : '';
    $sql = "SELECT id, name, email, password_hash, is_active
            FROM users
            WHERE email = ?" . $whereDeleted . "
            LIMIT 1";
    $stmt = admin_db()->prepare($sql);
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return is_array($row) ? $row : null;
}

function admin_load_user_by_id(int $id): ?array
{
    if (!admin_table_exists('users')) {
        return null;
    }

    $hasDeletedAt = admin_table_has_column('users', 'deleted_at');
    $whereDeleted = $hasDeletedAt ? " AND deleted_at IS NULL" : '';
    $sql = "SELECT id, name, email, password_hash, is_active
            FROM users
            WHERE id = ?" . $whereDeleted . "
            LIMIT 1";
    $stmt = admin_db()->prepare($sql);
    if (!$stmt) {
        return null;
    }
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return is_array($row) ? $row : null;
}

function admin_update_last_login(int $userId): void
{
    $update = admin_db()->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?");
    if ($update) {
        $update->bind_param('i', $userId);
        $update->execute();
        $update->close();
    }
}

function admin_remember_table_exists(): bool
{
    return admin_table_exists('auth_remember_tokens');
}

function admin_otp_table_exists(): bool
{
    return admin_table_exists('auth_otp_codes');
}

function admin_otp_log_table_exists(): bool
{
    return admin_table_exists('auth_otp_logs');
}

function admin_log_otp_event(
    string $email,
    string $purpose,
    string $event,
    string $status,
    string $message,
    ?int $requestId = null,
    array $meta = []
): void {
    if (!admin_otp_log_table_exists()) {
        return;
    }

    $email = strtolower(trim($email));
    if ($email === '') {
        $email = '-';
    }
    $purpose = trim($purpose) !== '' ? trim($purpose) : '-';
    $event = trim($event) !== '' ? trim($event) : 'unknown';
    $status = trim($status) !== '' ? trim($status) : 'info';
    $message = trim($message);

    $ipAddress = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    if ($ipAddress === '') {
        $ipAddress = null;
    }
    $userAgent = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');
    if ($userAgent === '') {
        $userAgent = null;
    }

    $metaJson = null;
    if ($meta) {
        $encoded = json_encode($meta, JSON_UNESCAPED_SLASHES);
        if (is_string($encoded)) {
            $metaJson = $encoded;
        }
    }

    $sql = "INSERT INTO auth_otp_logs
            (request_id, email, purpose, event, status, message, ip_address, user_agent, meta_json, created_at)
            VALUES (NULLIF(?, 0), ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = admin_db()->prepare($sql);
    if (!$stmt) {
        return;
    }
    $requestIdInt = $requestId ?? 0;
    $stmt->bind_param(
        'issssssss',
        $requestIdInt,
        $email,
        $purpose,
        $event,
        $status,
        $message,
        $ipAddress,
        $userAgent,
        $metaJson
    );
    $stmt->execute();
    $stmt->close();
}

function admin_create_remember_token(int $userId): bool
{
    if (!admin_remember_table_exists()) {
        return false;
    }

    $selector = bin2hex(random_bytes(9));
    $validator = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $validator);
    $expiresAt = date('Y-m-d H:i:s', time() + (ADMIN_REMEMBER_DAYS * 86400));

    $sql = "INSERT INTO auth_remember_tokens (user_id, selector, token_hash, expires_at, created_at)
            VALUES (?, ?, ?, ?, NOW())";
    $stmt = admin_db()->prepare($sql);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('isss', $userId, $selector, $tokenHash, $expiresAt);
    $ok = $stmt->execute();
    $stmt->close();
    if (!$ok) {
        return false;
    }

    $_SESSION['remember_selector'] = $selector;
    admin_set_cookie(
        ADMIN_REMEMBER_COOKIE,
        $selector . ':' . $validator,
        time() + (ADMIN_REMEMBER_DAYS * 86400)
    );
    return true;
}

function admin_parse_remember_cookie(?string $cookieValue): ?array
{
    if (!is_string($cookieValue) || $cookieValue === '') {
        return null;
    }
    $parts = explode(':', $cookieValue, 2);
    if (count($parts) !== 2) {
        return null;
    }
    $selector = trim($parts[0]);
    $validator = trim($parts[1]);
    if ($selector === '' || $validator === '') {
        return null;
    }
    if (!ctype_xdigit($selector) || !ctype_xdigit($validator)) {
        return null;
    }
    return ['selector' => $selector, 'validator' => $validator];
}

function admin_revoke_remember_selector(?string $selector): void
{
    if (!is_string($selector) || $selector === '' || !admin_remember_table_exists()) {
        return;
    }
    $sql = "UPDATE auth_remember_tokens SET revoked_at = NOW() WHERE selector = ? AND revoked_at IS NULL";
    $stmt = admin_db()->prepare($sql);
    if (!$stmt) {
        return;
    }
    $stmt->bind_param('s', $selector);
    $stmt->execute();
    $stmt->close();
}

function admin_restore_session_from_remember_cookie(): void
{
    if (admin_current_user() !== null || !admin_remember_table_exists()) {
        return;
    }

    $parsed = admin_parse_remember_cookie((string) ($_COOKIE[ADMIN_REMEMBER_COOKIE] ?? ''));
    if (!$parsed) {
        return;
    }

    $selector = (string) $parsed['selector'];
    $validator = (string) $parsed['validator'];

    $sql = "SELECT user_id, token_hash
            FROM auth_remember_tokens
            WHERE selector = ? AND revoked_at IS NULL AND expires_at > NOW()
            LIMIT 1";
    $stmt = admin_db()->prepare($sql);
    if (!$stmt) {
        admin_clear_cookie(ADMIN_REMEMBER_COOKIE);
        return;
    }
    $stmt->bind_param('s', $selector);
    $stmt->execute();
    $tokenRow = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$tokenRow) {
        admin_clear_cookie(ADMIN_REMEMBER_COOKIE);
        return;
    }

    $actualHash = hash('sha256', $validator);
    if (!hash_equals((string) $tokenRow['token_hash'], $actualHash)) {
        admin_revoke_remember_selector($selector);
        admin_clear_cookie(ADMIN_REMEMBER_COOKIE);
        return;
    }

    $userId = (int) ($tokenRow['user_id'] ?? 0);
    if ($userId <= 0) {
        admin_revoke_remember_selector($selector);
        admin_clear_cookie(ADMIN_REMEMBER_COOKIE);
        return;
    }

    $user = admin_load_user_by_id($userId);
    if (!$user || (int) ($user['is_active'] ?? 0) !== 1) {
        admin_revoke_remember_selector($selector);
        admin_clear_cookie(ADMIN_REMEMBER_COOKIE);
        return;
    }

    $roles = admin_fetch_roles_for_user($userId);
    admin_login_user($user, $roles);
    admin_update_last_login($userId);

    admin_revoke_remember_selector($selector);
    admin_create_remember_token($userId);
}

function admin_bootstrap_auth(): void
{
    admin_restore_session_from_remember_cookie();
}

function admin_require_login(): void
{
    if (admin_current_user() !== null) {
        return;
    }
    header('Location: ' . admin_url('/admin/login'));
    exit;
}

function admin_attempt_login(string $email, string $password, bool $rememberMe = false): bool
{
    $email = strtolower(trim($email));
    if ($email === '') {
        return false;
    }

    if (!admin_table_exists('users')) {
        admin_set_flash('error', 'Tabel users belum tersedia.');
        return false;
    }

    $row = admin_load_user_by_email($email);
    if (!$row || (int) ($row['is_active'] ?? 0) !== 1) {
        return false;
    }

    if (!password_verify($password, (string) $row['password_hash'])) {
        return false;
    }

    $userId = (int) $row['id'];
    $roles = admin_fetch_roles_for_user($userId);
    admin_login_user($row, $roles);
    admin_update_last_login($userId);

    if ($rememberMe) {
        admin_create_remember_token($userId);
    }

    return true;
}

function admin_login_by_email(string $email, bool $rememberMe = false): bool
{
    $email = strtolower(trim($email));
    if ($email === '') {
        return false;
    }
    $row = admin_load_user_by_email($email);
    if (!$row || (int) ($row['is_active'] ?? 0) !== 1) {
        return false;
    }
    $userId = (int) $row['id'];
    $roles = admin_fetch_roles_for_user($userId);
    admin_login_user($row, $roles);
    admin_update_last_login($userId);
    if ($rememberMe) {
        admin_create_remember_token($userId);
    }
    return true;
}

function admin_validate_password(string $password): ?string
{
    if (strlen($password) < 8) {
        return 'Password minimal 8 karakter.';
    }
    return null;
}

function admin_env_bool(string $key, bool $default = false): bool
{
    $raw = inosakti_env_value($key);
    if ($raw === null) {
        return $default;
    }
    $val = strtolower(trim($raw));
    if ($val === '') {
        return $default;
    }
    return in_array($val, ['1', 'true', 'yes', 'on'], true);
}

function admin_mailer_autoload(): void
{
    static $loaded = false;
    if ($loaded) {
        return;
    }
    $autoload = dirname(__DIR__, 2) . '/vendor/autoload.php';
    if (is_file($autoload) && is_readable($autoload)) {
        require_once $autoload;
    }
    $loaded = true;
}

function admin_smtp_enabled(): bool
{
    return admin_env_bool('SMTP_ENABLED', false);
}

function admin_send_email_via_smtp(string $toEmail, string $subject, string $htmlBody): bool
{
    admin_mailer_autoload();
    if (!class_exists('\\PHPMailer\\PHPMailer\\PHPMailer')) {
        return false;
    }

    $host = trim((string) inosakti_env_value('SMTP_HOST', ''));
    $port = (int) inosakti_env_value('SMTP_PORT', '587');
    $username = trim((string) inosakti_env_value('SMTP_USERNAME', ''));
    $password = (string) inosakti_env_value('SMTP_PASSWORD', '');
    $fromEmail = trim((string) inosakti_env_value('SMTP_FROM_EMAIL', $username));
    $fromName = trim((string) inosakti_env_value('SMTP_FROM_NAME', 'InoSakti'));
    $secureMode = strtolower(trim((string) inosakti_env_value('SMTP_SECURE', 'tls')));
    $smtpAuth = admin_env_bool('SMTP_AUTH', true);
    $allowSelfSigned = admin_env_bool('SMTP_ALLOW_SELF_SIGNED', false);

    if ($host === '' || $fromEmail === '') {
        return false;
    }

    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $host;
        $mail->Port = $port > 0 ? $port : 587;
        $mail->SMTPAuth = $smtpAuth;
        if ($smtpAuth) {
            $mail->Username = $username;
            $mail->Password = $password;
        }

        if ($secureMode === 'ssl') {
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($secureMode === 'tls') {
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        } else {
            $mail->SMTPSecure = '';
            $mail->SMTPAutoTLS = false;
        }

        if ($allowSelfSigned) {
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
        }

        $mail->CharSet = 'UTF-8';
        $mail->setFrom($fromEmail, $fromName !== '' ? $fromName : 'InoSakti');
        $mail->addAddress($toEmail);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $htmlBody;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlBody));

        return $mail->send();
    } catch (\Throwable $e) {
        return false;
    }
}

function admin_send_email(string $toEmail, string $subject, string $htmlBody): bool
{
    if (admin_smtp_enabled()) {
        return admin_send_email_via_smtp($toEmail, $subject, $htmlBody);
    }

    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8',
        'From: InoSakti <no-reply@inosakti.com>',
    ];
    $ok = @mail($toEmail, $subject, $htmlBody, implode("\r\n", $headers));
    if ($ok) {
        return true;
    }
    if (admin_is_local_env()) {
        return true;
    }
    return false;
}

function admin_generate_otp(int $length = 6): string
{
    $max = (10 ** $length) - 1;
    return str_pad((string) random_int(0, $max), $length, '0', STR_PAD_LEFT);
}

function admin_send_otp_email(string $email, string $otp, string $purpose): bool
{
    $purposeLabel = $purpose === 'password_reset'
        ? 'Reset Password'
        : 'Verifikasi Pendaftaran Student';
    $subject = 'Kode OTP InoSakti - ' . $purposeLabel;
    $html = '<div style="font-family:Arial,sans-serif;line-height:1.5;color:#0f172a">'
        . '<h2 style="margin:0 0 8px">InoSakti Account Security</h2>'
        . '<p>Gunakan kode OTP berikut:</p>'
        . '<p style="font-size:28px;font-weight:700;letter-spacing:3px;margin:10px 0">' . admin_e($otp) . '</p>'
        . '<p>Kode berlaku selama 10 menit dan hanya bisa dipakai sekali.</p>'
        . '<p>Jika bukan Anda, abaikan email ini.</p>'
        . '</div>';

    $ok = admin_send_email($email, $subject, $html);
    if ($ok && admin_is_local_env()) {
        $_SESSION['dev_last_otp'] = [
            'email' => $email,
            'purpose' => $purpose,
            'otp' => $otp,
            'generated_at' => date('Y-m-d H:i:s'),
        ];
    }
    return $ok;
}

function admin_create_otp_request(string $email, string $purpose, array $payload = []): array
{
    if (!admin_otp_table_exists()) {
        admin_log_otp_event($email, $purpose, 'request', 'error', 'OTP table is not available.');
        return ['ok' => false, 'message' => 'Fitur OTP belum tersedia. Jalankan migration terbaru.'];
    }
    $email = strtolower(trim($email));
    if ($email === '') {
        admin_log_otp_event($email, $purpose, 'request', 'invalid', 'Email is empty.');
        return ['ok' => false, 'message' => 'Email wajib diisi.'];
    }

    $rateSql = "SELECT COUNT(*) AS cnt
                FROM auth_otp_codes
                WHERE email = ? AND purpose = ? AND created_at >= (NOW() - INTERVAL 10 MINUTE)";
    $rateStmt = admin_db()->prepare($rateSql);
    if (!$rateStmt) {
        admin_log_otp_event($email, $purpose, 'request', 'error', 'Failed preparing OTP rate-limit query.');
        return ['ok' => false, 'message' => 'Gagal menyiapkan OTP.'];
    }
    $rateStmt->bind_param('ss', $email, $purpose);
    $rateStmt->execute();
    $rateRow = $rateStmt->get_result()->fetch_assoc();
    $rateStmt->close();
    if ((int) ($rateRow['cnt'] ?? 0) >= 3) {
        admin_log_otp_event($email, $purpose, 'request', 'rate_limited', 'OTP request blocked by rate limiter.');
        return ['ok' => false, 'message' => 'Terlalu banyak permintaan OTP. Coba lagi beberapa menit lagi.'];
    }

    $otp = admin_generate_otp(6);
    $otpHash = hash('sha256', $otp);
    $payloadJson = json_encode($payload, JSON_UNESCAPED_SLASHES);
    if (!is_string($payloadJson)) {
        $payloadJson = '{}';
    }
    $expiresAt = date('Y-m-d H:i:s', time() + ADMIN_OTP_TTL_SECONDS);

    $insertSql = "INSERT INTO auth_otp_codes
                  (email, purpose, otp_hash, payload_json, attempt_count, expires_at, created_at)
                  VALUES (?, ?, ?, ?, 0, ?, NOW())";
    $insertStmt = admin_db()->prepare($insertSql);
    if (!$insertStmt) {
        admin_log_otp_event($email, $purpose, 'request', 'error', 'Failed preparing OTP insert query.');
        return ['ok' => false, 'message' => 'Gagal menyimpan OTP.'];
    }
    $insertStmt->bind_param('sssss', $email, $purpose, $otpHash, $payloadJson, $expiresAt);
    $ok = $insertStmt->execute();
    $requestId = (int) admin_db()->insert_id;
    $insertStmt->close();
    if (!$ok || $requestId <= 0) {
        admin_log_otp_event($email, $purpose, 'request', 'error', 'OTP row creation failed.');
        return ['ok' => false, 'message' => 'Gagal membuat OTP.'];
    }

    if (!admin_send_otp_email($email, $otp, $purpose)) {
        admin_log_otp_event($email, $purpose, 'delivery', 'failed', 'SMTP delivery failed.', $requestId);
        return ['ok' => false, 'message' => 'Gagal mengirim OTP ke email.'];
    }
    admin_log_otp_event($email, $purpose, 'delivery', 'sent', 'OTP delivery accepted by mail sender.', $requestId);

    $result = [
        'ok' => true,
        'message' => 'OTP sudah dikirim ke email Anda.',
        'request_id' => $requestId,
    ];
    if (admin_is_local_env() && !admin_smtp_enabled()) {
        $result['dev_otp'] = $otp;
    }
    return $result;
}

function admin_verify_otp_request(int $requestId, string $email, string $purpose, string $otp): array
{
    if (!admin_otp_table_exists()) {
        admin_log_otp_event($email, $purpose, 'verify', 'error', 'OTP table is not available.', $requestId);
        return ['ok' => false, 'message' => 'Fitur OTP belum tersedia.'];
    }
    if ($requestId <= 0) {
        admin_log_otp_event($email, $purpose, 'verify', 'invalid', 'Request ID is invalid.', $requestId);
        return ['ok' => false, 'message' => 'Request OTP tidak valid.'];
    }

    $email = strtolower(trim($email));
    $otp = trim($otp);
    if ($email === '' || $otp === '') {
        admin_log_otp_event($email, $purpose, 'verify', 'invalid', 'Email or OTP code is empty.', $requestId);
        return ['ok' => false, 'message' => 'Email dan OTP wajib diisi.'];
    }

    $sql = "SELECT id, email, purpose, otp_hash, payload_json, attempt_count, expires_at
            FROM auth_otp_codes
            WHERE id = ? AND email = ? AND purpose = ? AND consumed_at IS NULL
            LIMIT 1";
    $stmt = admin_db()->prepare($sql);
    if (!$stmt) {
        admin_log_otp_event($email, $purpose, 'verify', 'error', 'Failed preparing OTP verify query.', $requestId);
        return ['ok' => false, 'message' => 'Gagal memverifikasi OTP.'];
    }
    $stmt->bind_param('iss', $requestId, $email, $purpose);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        admin_log_otp_event($email, $purpose, 'verify', 'not_found', 'OTP request not found, consumed, or mismatched.', $requestId);
        return ['ok' => false, 'message' => 'OTP tidak ditemukan atau sudah dipakai.'];
    }

    if ((int) ($row['attempt_count'] ?? 0) >= ADMIN_OTP_MAX_ATTEMPTS) {
        admin_log_otp_event($email, $purpose, 'verify', 'blocked', 'OTP max attempts reached.', (int) $row['id']);
        return ['ok' => false, 'message' => 'OTP sudah melebihi batas percobaan.'];
    }

    $expiresAt = strtotime((string) ($row['expires_at'] ?? ''));
    if ($expiresAt === false || $expiresAt < time()) {
        admin_log_otp_event($email, $purpose, 'verify', 'expired', 'OTP code expired.', (int) $row['id']);
        return ['ok' => false, 'message' => 'OTP sudah kedaluwarsa.'];
    }

    $actual = hash('sha256', $otp);
    if (!hash_equals((string) $row['otp_hash'], $actual)) {
        $attemptSql = "UPDATE auth_otp_codes SET attempt_count = attempt_count + 1 WHERE id = ?";
        $attemptStmt = admin_db()->prepare($attemptSql);
        if ($attemptStmt) {
            $id = (int) $row['id'];
            $attemptStmt->bind_param('i', $id);
            $attemptStmt->execute();
            $attemptStmt->close();
        }
        admin_log_otp_event($email, $purpose, 'verify', 'invalid_code', 'OTP code mismatch.', (int) $row['id']);
        return ['ok' => false, 'message' => 'OTP tidak valid.'];
    }

    $touchSql = "UPDATE auth_otp_codes SET verified_at = NOW() WHERE id = ?";
    $touchStmt = admin_db()->prepare($touchSql);
    if ($touchStmt) {
        $id = (int) $row['id'];
        $touchStmt->bind_param('i', $id);
        $touchStmt->execute();
        $touchStmt->close();
    }
    admin_log_otp_event($email, $purpose, 'verify', 'verified', 'OTP code verified successfully.', (int) $row['id']);

    $payload = [];
    $payloadRaw = (string) ($row['payload_json'] ?? '{}');
    $decoded = json_decode($payloadRaw, true);
    if (is_array($decoded)) {
        $payload = $decoded;
    }

    return [
        'ok' => true,
        'message' => 'OTP valid.',
        'request_id' => (int) $row['id'],
        'payload' => $payload,
    ];
}

function admin_consume_otp_request(int $requestId): void
{
    if ($requestId <= 0 || !admin_otp_table_exists()) {
        return;
    }
    $sql = "UPDATE auth_otp_codes SET consumed_at = NOW() WHERE id = ? AND consumed_at IS NULL";
    $stmt = admin_db()->prepare($sql);
    if (!$stmt) {
        return;
    }
    $stmt->bind_param('i', $requestId);
    $stmt->execute();
    $stmt->close();
}

function admin_ensure_role_id(string $roleCode, string $roleName): ?int
{
    if (!admin_table_exists('roles')) {
        return null;
    }

    $selectSql = "SELECT id FROM roles WHERE code = ? LIMIT 1";
    $selectStmt = admin_db()->prepare($selectSql);
    if ($selectStmt) {
        $selectStmt->bind_param('s', $roleCode);
        $selectStmt->execute();
        $roleRow = $selectStmt->get_result()->fetch_assoc();
        $selectStmt->close();
        if ($roleRow) {
            return (int) $roleRow['id'];
        }
    }

    $insertSql = "INSERT INTO roles (code, name, created_at, updated_at) VALUES (?, ?, NOW(), NOW())";
    $insertStmt = admin_db()->prepare($insertSql);
    if (!$insertStmt) {
        return null;
    }
    $insertStmt->bind_param('ss', $roleCode, $roleName);
    $ok = $insertStmt->execute();
    $insertStmt->close();
    if (!$ok) {
        return null;
    }
    return (int) admin_db()->insert_id;
}

function admin_register_student_with_password_hash(string $name, string $email, string $passwordHash): array
{
    if (!admin_table_exists('users')) {
        return ['ok' => false, 'message' => 'Tabel users belum tersedia.'];
    }
    if (!admin_table_exists('user_roles') || !admin_table_exists('roles')) {
        return ['ok' => false, 'message' => 'Tabel role belum lengkap.'];
    }

    $email = strtolower(trim($email));
    $name = trim($name);
    if ($name === '' || $email === '' || $passwordHash === '') {
        return ['ok' => false, 'message' => 'Data akun belum lengkap.'];
    }

    $existing = admin_load_user_by_email($email);
    if ($existing) {
        return ['ok' => false, 'message' => 'Email sudah terdaftar.'];
    }

    admin_db()->begin_transaction();
    try {
        $insertUserSql = "INSERT INTO users (name, email, password_hash, is_active, created_at, updated_at)
                          VALUES (?, ?, ?, 1, NOW(), NOW())";
        $insertUserStmt = admin_db()->prepare($insertUserSql);
        if (!$insertUserStmt) {
            throw new RuntimeException('Gagal menyiapkan insert user.');
        }
        $insertUserStmt->bind_param('sss', $name, $email, $passwordHash);
        if (!$insertUserStmt->execute()) {
            $insertUserStmt->close();
            throw new RuntimeException('Gagal menyimpan user baru.');
        }
        $insertUserStmt->close();
        $userId = (int) admin_db()->insert_id;
        if ($userId <= 0) {
            throw new RuntimeException('User ID tidak valid.');
        }

        $studentRoleId = admin_ensure_role_id('student', 'Student');
        if ($studentRoleId === null || $studentRoleId <= 0) {
            throw new RuntimeException('Role student belum tersedia.');
        }

        $insertRoleSql = "INSERT INTO user_roles (user_id, role_id, created_at)
                          VALUES (?, ?, NOW())
                          ON DUPLICATE KEY UPDATE role_id = VALUES(role_id)";
        $insertRoleStmt = admin_db()->prepare($insertRoleSql);
        if (!$insertRoleStmt) {
            throw new RuntimeException('Gagal menyiapkan assignment role.');
        }
        $insertRoleStmt->bind_param('ii', $userId, $studentRoleId);
        if (!$insertRoleStmt->execute()) {
            $insertRoleStmt->close();
            throw new RuntimeException('Gagal assign role student.');
        }
        $insertRoleStmt->close();

        admin_db()->commit();
        return ['ok' => true, 'message' => 'Akun student berhasil dibuat.'];
    } catch (Throwable $e) {
        admin_db()->rollback();
        return ['ok' => false, 'message' => $e->getMessage()];
    }
}

function admin_password_reset_eligible_user(string $email): ?array
{
    $row = admin_load_user_by_email($email);
    if (!$row || (int) ($row['is_active'] ?? 0) !== 1) {
        return null;
    }
    return $row;
}

function admin_reset_password_by_email(string $email, string $newPassword): array
{
    $passwordError = admin_validate_password($newPassword);
    if ($passwordError !== null) {
        return ['ok' => false, 'message' => $passwordError];
    }

    $email = strtolower(trim($email));
    $eligible = admin_password_reset_eligible_user($email);
    if (!$eligible) {
        return ['ok' => false, 'message' => 'Akun tidak ditemukan atau tidak aktif.'];
    }

    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?";
    $stmt = admin_db()->prepare($sql);
    if (!$stmt) {
        return ['ok' => false, 'message' => 'Gagal menyiapkan reset password.'];
    }
    $userId = (int) $eligible['id'];
    $stmt->bind_param('si', $newHash, $userId);
    $ok = $stmt->execute();
    $stmt->close();
    if (!$ok) {
        return ['ok' => false, 'message' => 'Gagal mengubah password.'];
    }

    if (admin_remember_table_exists()) {
        $revokeSql = "UPDATE auth_remember_tokens SET revoked_at = NOW() WHERE user_id = ? AND revoked_at IS NULL";
        $revokeStmt = admin_db()->prepare($revokeSql);
        if ($revokeStmt) {
            $revokeStmt->bind_param('i', $userId);
            $revokeStmt->execute();
            $revokeStmt->close();
        }
    }

    return ['ok' => true, 'message' => 'Password berhasil diubah.'];
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

function admin_can_manage_courses(): bool
{
    return admin_has_any_role(['super_admin', 'instructor']);
}

function admin_can_login_admin_area(): bool
{
    return admin_can_access_admin_panel() || admin_can_manage_courses();
}

function admin_require_admin_panel_access(): void
{
    if (admin_can_access_admin_panel()) {
        return;
    }
    admin_set_flash('error', 'Akun ini tidak punya akses ke halaman admin tersebut.');
    header('Location: ' . admin_default_home_for_current_user());
    exit;
}

function admin_require_course_manager_access(): void
{
    if (admin_can_manage_courses()) {
        return;
    }
    admin_set_flash('error', 'Akun ini tidak punya izin kelola kursus.');
    header('Location: ' . admin_default_home_for_current_user());
    exit;
}

function admin_default_home_for_current_user(): string
{
    if (admin_can_manage_courses() && !admin_can_access_admin_panel()) {
        return admin_url('/admin/courses');
    }
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
    $selector = null;
    if (isset($_SESSION['remember_selector']) && is_string($_SESSION['remember_selector'])) {
        $selector = $_SESSION['remember_selector'];
    } else {
        $parsed = admin_parse_remember_cookie((string) ($_COOKIE[ADMIN_REMEMBER_COOKIE] ?? ''));
        $selector = is_array($parsed) ? (string) ($parsed['selector'] ?? '') : null;
    }
    admin_revoke_remember_selector($selector);
    admin_clear_cookie(ADMIN_REMEMBER_COOKIE);
    unset($_SESSION['remember_selector']);
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

admin_bootstrap_auth();
