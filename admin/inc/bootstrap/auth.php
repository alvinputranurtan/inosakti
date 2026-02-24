<?php
declare(strict_types=1);

function admin_remember_table_exists(): bool
{
    return admin_table_exists('auth_remember_tokens');
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
    header('Location: ' . admin_url('/login'));
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

