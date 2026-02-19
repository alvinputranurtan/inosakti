<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/layout.php';
admin_require_login();

if (!admin_table_exists('courses')) {
    admin_set_flash('error', 'Tabel courses belum tersedia.');
    header('Location: ' . admin_url('/admin/'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!admin_verify_csrf(is_string($token) ? $token : null)) {
        admin_set_flash('error', 'Token keamanan tidak valid.');
        header('Location: ' . admin_url('/admin/courses'));
        exit;
    }

    $id = (int) ($_POST['id'] ?? 0);
    $status = (string) ($_POST['status'] ?? '');
    $allowed = ['draft', 'published', 'archived'];
    if ($id > 0 && in_array($status, $allowed, true)) {
        $sql = "UPDATE courses SET status = ?, published_at = IF(?='published' AND published_at IS NULL, NOW(), published_at) WHERE id = ?";
        $stmt = admin_db()->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ssi', $status, $status, $id);
            $stmt->execute();
            $stmt->close();
            admin_set_flash('success', 'Status kursus berhasil diperbarui.');
        }
    }
    header('Location: ' . admin_url('/admin/courses'));
    exit;
}

$rows = [];
if (admin_table_exists('course_translations') && admin_table_exists('languages')) {
    $sql = "SELECT c.id, c.status, c.price, c.published_at, c.created_at,
                   COALESCE(ct.title, CONCAT('Course #', c.id)) AS title
            FROM courses c
            LEFT JOIN course_translations ct ON ct.course_id = c.id
            LEFT JOIN languages l ON l.id = ct.language_id AND l.code = 'id'
            WHERE c.deleted_at IS NULL
            GROUP BY c.id
            ORDER BY c.created_at DESC
            LIMIT 100";
} else {
    $sql = "SELECT c.id, c.status, c.price, c.published_at, c.created_at,
                   CONCAT('Course #', c.id) AS title
            FROM courses c
            WHERE c.deleted_at IS NULL
            ORDER BY c.created_at DESC
            LIMIT 100";
}
$result = admin_db()->query($sql);
if ($result) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
}

admin_render_start('Manajemen Kursus', 'courses');
?>
<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
  <div class="p-5 border-b border-slate-200">
    <h2 class="font-bold text-lg">Daftar Kursus (maks 100 terbaru)</h2>
    <p class="text-sm text-slate-500 mt-1">Update status publikasi kursus secara langsung.</p>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] tracking-wider">
      <tr>
        <th class="px-4 py-3 text-left">Judul</th>
        <th class="px-4 py-3 text-left">Harga</th>
        <th class="px-4 py-3 text-left">Published</th>
        <th class="px-4 py-3 text-left">Status</th>
        <th class="px-4 py-3 text-right">Aksi</th>
      </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
      <?php foreach ($rows as $r): ?>
        <tr>
          <td class="px-4 py-3 font-semibold text-slate-800"><?= admin_e((string) $r['title']) ?></td>
          <td class="px-4 py-3">Rp <?= number_format((float) $r['price'], 0, ',', '.') ?></td>
          <td class="px-4 py-3 text-slate-500"><?= admin_e((string) ($r['published_at'] ?? '-')) ?></td>
          <td class="px-4 py-3">
            <span class="px-2 py-1 rounded-full text-xs font-bold <?= $r['status'] === 'published' ? 'bg-emerald-100 text-emerald-700' : ($r['status'] === 'draft' ? 'bg-slate-100 text-slate-700' : 'bg-amber-100 text-amber-700') ?>">
              <?= admin_e((string) $r['status']) ?>
            </span>
          </td>
          <td class="px-4 py-3">
            <form method="post" class="flex justify-end gap-2">
              <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <select name="status" class="rounded-lg border-slate-300 text-sm">
                <?php foreach (['draft', 'published', 'archived'] as $s): ?>
                  <option value="<?= $s ?>" <?= $s === $r['status'] ? 'selected' : '' ?>><?= $s ?></option>
                <?php endforeach; ?>
              </select>
              <button class="px-3 py-1.5 bg-blue-800 text-white rounded-lg font-semibold">Simpan</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?>
        <tr><td colspan="5" class="px-4 py-5 text-center text-slate-500">Belum ada data courses.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php admin_render_end(); ?>
