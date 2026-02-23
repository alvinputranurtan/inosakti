<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/layout.php';
admin_require_login();
admin_require_admin_panel_access();

if (!admin_table_exists('users')) {
    admin_set_flash('error', 'Tabel users belum tersedia.');
    header('Location: ' . admin_url('/admin/'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!admin_verify_csrf(is_string($token) ? $token : null)) {
        admin_set_flash('error', 'Token keamanan tidak valid.');
        header('Location: ' . admin_url('/admin/users'));
        exit;
    }

    $id = (int) ($_POST['id'] ?? 0);
    $isActive = (int) ($_POST['is_active'] ?? 0);
    if ($id > 0) {
        $stmt = admin_db()->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('ii', $isActive, $id);
            $stmt->execute();
            $stmt->close();
            admin_set_flash('success', 'Status user berhasil diperbarui.');
        }
    }
    header('Location: ' . admin_url('/admin/users'));
    exit;
}

$rows = [];
if (admin_table_exists('user_roles') && admin_table_exists('roles')) {
    $sql = "SELECT u.id, u.name, u.email, u.is_active, u.last_login_at, u.created_at,
                   GROUP_CONCAT(r.name ORDER BY r.name SEPARATOR ', ') AS roles
            FROM users u
            LEFT JOIN user_roles ur ON ur.user_id = u.id
            LEFT JOIN roles r ON r.id = ur.role_id
            WHERE u.deleted_at IS NULL
            GROUP BY u.id
            ORDER BY u.created_at DESC
            LIMIT 100";
} else {
    $sql = "SELECT u.id, u.name, u.email, u.is_active, u.last_login_at, u.created_at,
                   NULL AS roles
            FROM users u
            WHERE u.deleted_at IS NULL
            ORDER BY u.created_at DESC
            LIMIT 100";
}
$result = admin_db()->query($sql);
if ($result) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
}

admin_render_start('Manajemen Pengguna', 'users');
?>
<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
  <div class="p-5 border-b border-slate-200">
    <h2 class="font-bold text-lg">Daftar User (maks 100 terbaru)</h2>
    <p class="text-sm text-slate-500 mt-1">Aktif/nonaktifkan akun admin/editor secara cepat.</p>
  </div>
  <div class="md:hidden p-4 space-y-3">
    <?php foreach ($rows as $r): ?>
      <div class="rounded-xl border border-slate-200 p-4">
        <div class="font-semibold text-slate-800"><?= admin_e((string) $r['name']) ?></div>
        <div class="mt-1 text-xs text-slate-600 break-all"><?= admin_e((string) $r['email']) ?></div>
        <div class="mt-2 text-xs text-slate-500">Role: <?= admin_e((string) ($r['roles'] ?: '-')) ?></div>
        <div class="text-xs text-slate-500">Last Login: <?= admin_e((string) ($r['last_login_at'] ?? '-')) ?></div>
        <div class="mt-2">
          <?php if ((int) $r['is_active'] === 1): ?>
            <span class="px-2 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">active</span>
          <?php else: ?>
            <span class="px-2 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">inactive</span>
          <?php endif; ?>
        </div>
        <form method="post" class="mt-3">
          <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
          <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
          <input type="hidden" name="is_active" value="<?= (int) $r['is_active'] === 1 ? 0 : 1 ?>">
          <button class="px-3 py-2 rounded-lg font-semibold w-full <?= (int) $r['is_active'] === 1 ? 'bg-amber-500 text-white' : 'bg-emerald-600 text-white' ?>">
            <?= (int) $r['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?>
          </button>
        </form>
      </div>
    <?php endforeach; ?>
    <?php if (!$rows): ?>
      <div class="text-center text-sm text-slate-500 py-4">Belum ada data users.</div>
    <?php endif; ?>
  </div>

  <div class="hidden md:block overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] tracking-wider">
      <tr>
        <th class="px-4 py-3 text-left">Nama</th>
        <th class="px-4 py-3 text-left">Email</th>
        <th class="px-4 py-3 text-left">Role</th>
        <th class="px-4 py-3 text-left">Last Login</th>
        <th class="px-4 py-3 text-left">Status</th>
        <th class="px-4 py-3 text-right">Aksi</th>
      </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
      <?php foreach ($rows as $r): ?>
        <tr>
          <td class="px-4 py-3 font-semibold text-slate-800"><?= admin_e((string) $r['name']) ?></td>
          <td class="px-4 py-3"><?= admin_e((string) $r['email']) ?></td>
          <td class="px-4 py-3 text-slate-600"><?= admin_e((string) ($r['roles'] ?: '-')) ?></td>
          <td class="px-4 py-3 text-slate-500"><?= admin_e((string) ($r['last_login_at'] ?? '-')) ?></td>
          <td class="px-4 py-3">
            <?php if ((int) $r['is_active'] === 1): ?>
              <span class="px-2 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">active</span>
            <?php else: ?>
              <span class="px-2 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">inactive</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3">
            <form method="post" class="flex flex-col sm:flex-row sm:justify-end gap-2">
              <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <input type="hidden" name="is_active" value="<?= (int) $r['is_active'] === 1 ? 0 : 1 ?>">
              <button class="px-3 py-1.5 rounded-lg font-semibold <?= (int) $r['is_active'] === 1 ? 'bg-amber-500 text-white' : 'bg-emerald-600 text-white' ?>">
                <?= (int) $r['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?>
              </button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?>
        <tr><td colspan="6" class="px-4 py-5 text-center text-slate-500">Belum ada data users.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php admin_render_end(); ?>
