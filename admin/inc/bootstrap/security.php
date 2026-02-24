<?php
declare(strict_types=1);

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

