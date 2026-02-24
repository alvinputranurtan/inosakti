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

require_once __DIR__ . '/bootstrap/base.php';
require_once __DIR__ . '/bootstrap/db.php';
require_once __DIR__ . '/bootstrap/security.php';
require_once __DIR__ . '/bootstrap/users.php';
require_once __DIR__ . '/bootstrap/auth.php';
require_once __DIR__ . '/bootstrap/mail_otp.php';
require_once __DIR__ . '/bootstrap/registration.php';
require_once __DIR__ . '/bootstrap/roles.php';
require_once __DIR__ . '/bootstrap/logout.php';

admin_bootstrap_auth();
