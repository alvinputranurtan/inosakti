<?php
declare(strict_types=1);

admin_require_login();

if (!admin_table_exists('courses')) {
    admin_set_flash('error', 'Tabel courses belum tersedia.');
    header('Location: ' . admin_url('/admin/'));
    exit;
}
