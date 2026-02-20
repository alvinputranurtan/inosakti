<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/layout.php';
admin_require_login();

$stats = [
    'posts_published' => 0,
    'courses_published' => 0,
    'orders_pending' => 0,
    'users_active' => 0,
    'employees_active' => 0,
    'enrollments_active' => 0,
];

if (admin_table_exists('posts')) {
    $stats['posts_published'] = admin_count_or_zero("SELECT COUNT(*) FROM posts WHERE status='published' AND deleted_at IS NULL");
}
if (admin_table_exists('courses')) {
    $stats['courses_published'] = admin_count_or_zero("SELECT COUNT(*) FROM courses WHERE status='published' AND deleted_at IS NULL");
}
if (admin_table_exists('orders')) {
    $stats['orders_pending'] = admin_count_or_zero("SELECT COUNT(*) FROM orders WHERE status IN ('pending','paid','processing')");
}
if (admin_table_exists('users')) {
    $stats['users_active'] = admin_count_or_zero("SELECT COUNT(*) FROM users WHERE is_active=1 AND deleted_at IS NULL");
}
if (admin_table_exists('employees')) {
    $stats['employees_active'] = admin_count_or_zero("SELECT COUNT(*) FROM employees WHERE employment_status='active' AND deleted_at IS NULL");
}
if (admin_table_exists('enrollments')) {
    $stats['enrollments_active'] = admin_count_or_zero("SELECT COUNT(*) FROM enrollments WHERE status='active'");
}

$latestLogs = [];
if (admin_table_exists('audit_logs') && admin_table_exists('users')) {
    $sql = "SELECT a.action, a.entity_type, a.created_at, u.name
            FROM audit_logs a
            LEFT JOIN users u ON u.id = a.user_id
            ORDER BY a.created_at DESC
            LIMIT 8";
    $result = admin_db()->query($sql);
    if ($result) {
        $latestLogs = $result->fetch_all(MYSQLI_ASSOC);
    }
}

admin_render_start('Dashboard', 'dashboard');
?>

<div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-8">
  <div class="bg-white border border-slate-200 rounded-2xl p-5">
    <div class="text-xs uppercase text-slate-500 font-bold tracking-wider">Blog Published</div>
    <div class="text-3xl font-extrabold mt-2"><?= (int) $stats['posts_published'] ?></div>
  </div>
  <div class="bg-white border border-slate-200 rounded-2xl p-5">
    <div class="text-xs uppercase text-slate-500 font-bold tracking-wider">Kursus Published</div>
    <div class="text-3xl font-extrabold mt-2"><?= (int) $stats['courses_published'] ?></div>
  </div>
  <div class="bg-white border border-slate-200 rounded-2xl p-5">
    <div class="text-xs uppercase text-slate-500 font-bold tracking-wider">Order Aktif</div>
    <div class="text-3xl font-extrabold mt-2"><?= (int) $stats['orders_pending'] ?></div>
  </div>
  <div class="bg-white border border-slate-200 rounded-2xl p-5">
    <div class="text-xs uppercase text-slate-500 font-bold tracking-wider">Admin/User Aktif</div>
    <div class="text-3xl font-extrabold mt-2"><?= (int) $stats['users_active'] ?></div>
  </div>
  <div class="bg-white border border-slate-200 rounded-2xl p-5">
    <div class="text-xs uppercase text-slate-500 font-bold tracking-wider">Karyawan Aktif</div>
    <div class="text-3xl font-extrabold mt-2"><?= (int) $stats['employees_active'] ?></div>
  </div>
  <div class="bg-white border border-slate-200 rounded-2xl p-5">
    <div class="text-xs uppercase text-slate-500 font-bold tracking-wider">Enrollment Aktif</div>
    <div class="text-3xl font-extrabold mt-2"><?= (int) $stats['enrollments_active'] ?></div>
  </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
  <div class="bg-white border border-slate-200 rounded-2xl p-6">
    <h2 class="font-bold text-lg">Quick Access</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4 text-sm">
      <a class="rounded-xl border border-slate-200 p-3 hover:bg-slate-50 font-semibold" href="<?= admin_e(admin_url('/admin/posts')) ?>">Kelola Blog</a>
      <a class="rounded-xl border border-slate-200 p-3 hover:bg-slate-50 font-semibold" href="<?= admin_e(admin_url('/admin/courses')) ?>">Kelola Kursus</a>
      <a class="rounded-xl border border-slate-200 p-3 hover:bg-slate-50 font-semibold" href="<?= admin_e(admin_url('/admin/products')) ?>">Kelola Produk</a>
      <a class="rounded-xl border border-slate-200 p-3 hover:bg-slate-50 font-semibold" href="<?= admin_e(admin_url('/admin/orders')) ?>">Kelola Order</a>
      <a class="rounded-xl border border-slate-200 p-3 hover:bg-slate-50 font-semibold" href="<?= admin_e(admin_url('/admin/users')) ?>">Kelola Pengguna</a>
    </div>
  </div>

  <div class="bg-white border border-slate-200 rounded-2xl p-6">
    <h2 class="font-bold text-lg">Audit Log Terbaru</h2>
    <?php if (!$latestLogs): ?>
      <p class="mt-3 text-sm text-slate-500">Belum ada log aktivitas.</p>
    <?php else: ?>
      <ul class="mt-3 space-y-3 text-sm">
        <?php foreach ($latestLogs as $log): ?>
          <li class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-3 border-b border-slate-100 pb-2">
            <div>
              <span class="font-semibold"><?= admin_e((string) ($log['name'] ?? 'System')) ?></span>
              <span class="text-slate-500">- <?= admin_e((string) ($log['action'] ?? '')) ?> <?= admin_e((string) ($log['entity_type'] ?? '')) ?></span>
            </div>
            <div class="text-slate-400 whitespace-nowrap"><?= admin_e((string) ($log['created_at'] ?? '')) ?></div>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>
</div>

<?php admin_render_end(); ?>
