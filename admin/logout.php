<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/bootstrap.php';
admin_logout();
header('Location: ' . admin_url('/admin/login'));
exit;
