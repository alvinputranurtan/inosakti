<?php
declare(strict_types=1);

function admin_validate_password(string $password): ?string
{
    if (strlen($password) < 8) {
        return 'Password minimal 8 karakter.';
    }
    return null;
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

