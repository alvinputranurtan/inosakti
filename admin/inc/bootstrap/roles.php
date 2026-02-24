<?php
declare(strict_types=1);

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

