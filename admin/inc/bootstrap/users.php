<?php
declare(strict_types=1);

function admin_current_user(): ?array
{
    $u = $_SESSION[ADMIN_SESSION_KEY] ?? null;
    return is_array($u) ? $u : null;
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

