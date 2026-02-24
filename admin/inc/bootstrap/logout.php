<?php
declare(strict_types=1);

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

