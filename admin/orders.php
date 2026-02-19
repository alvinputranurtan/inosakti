<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/layout.php';
admin_require_login();

if (!admin_table_exists('orders')) {
    admin_set_flash('error', 'Tabel orders belum tersedia.');
    header('Location: ' . admin_url('/admin/'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!admin_verify_csrf(is_string($token) ? $token : null)) {
        admin_set_flash('error', 'Token keamanan tidak valid.');
        header('Location: ' . admin_url('/admin/orders'));
        exit;
    }

    $id = (int) ($_POST['id'] ?? 0);
    $status = (string) ($_POST['status'] ?? '');
    $allowed = ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'];
    if ($id > 0 && in_array($status, $allowed, true)) {
        $stmt = admin_db()->prepare("UPDATE orders SET status = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('si', $status, $id);
            $stmt->execute();
            $stmt->close();
            admin_set_flash('success', 'Status order berhasil diperbarui.');
        }
    }
    header('Location: ' . admin_url('/admin/orders'));
    exit;
}

$rows = [];
if (admin_table_exists('customers')) {
    $sql = "SELECT o.id, o.order_number, o.status, o.grand_total, o.placed_at,
                   c.full_name AS customer_name
            FROM orders o
            JOIN customers c ON c.id = o.customer_id
            ORDER BY o.placed_at DESC
            LIMIT 100";
} else {
    $sql = "SELECT o.id, o.order_number, o.status, o.grand_total, o.placed_at,
                   CONCAT('Customer #', o.customer_id) AS customer_name
            FROM orders o
            ORDER BY o.placed_at DESC
            LIMIT 100";
}
$result = admin_db()->query($sql);
if ($result) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
}

admin_render_start('Manajemen Order', 'orders');
?>
<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
  <div class="p-5 border-b border-slate-200">
    <h2 class="font-bold text-lg">Daftar Order (maks 100 terbaru)</h2>
    <p class="text-sm text-slate-500 mt-1">Kelola status order e-commerce langsung dari dashboard.</p>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] tracking-wider">
      <tr>
        <th class="px-4 py-3 text-left">Order No</th>
        <th class="px-4 py-3 text-left">Customer</th>
        <th class="px-4 py-3 text-left">Total</th>
        <th class="px-4 py-3 text-left">Tanggal</th>
        <th class="px-4 py-3 text-left">Status</th>
        <th class="px-4 py-3 text-right">Aksi</th>
      </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
      <?php foreach ($rows as $r): ?>
        <tr>
          <td class="px-4 py-3 font-semibold text-blue-800"><?= admin_e((string) $r['order_number']) ?></td>
          <td class="px-4 py-3"><?= admin_e((string) $r['customer_name']) ?></td>
          <td class="px-4 py-3">Rp <?= number_format((float) $r['grand_total'], 0, ',', '.') ?></td>
          <td class="px-4 py-3 text-slate-500"><?= admin_e((string) $r['placed_at']) ?></td>
          <td class="px-4 py-3">
            <span class="px-2 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700"><?= admin_e((string) $r['status']) ?></span>
          </td>
          <td class="px-4 py-3">
            <form method="post" class="flex justify-end gap-2">
              <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <select name="status" class="rounded-lg border-slate-300 text-sm">
                <?php foreach (['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'] as $s): ?>
                  <option value="<?= $s ?>" <?= $s === $r['status'] ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
              </select>
              <button class="px-3 py-1.5 bg-blue-800 text-white rounded-lg font-semibold">Simpan</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?>
        <tr><td colspan="6" class="px-4 py-5 text-center text-slate-500">Belum ada data orders.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php admin_render_end(); ?>
