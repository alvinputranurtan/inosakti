<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/layout.php';
admin_require_login();

if (!admin_table_exists('posts')) {
    admin_set_flash('error', 'Tabel posts belum tersedia.');
    header('Location: ' . admin_url('/admin/'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!admin_verify_csrf(is_string($token) ? $token : null)) {
        admin_set_flash('error', 'Token keamanan tidak valid.');
        header('Location: ' . admin_url('/admin/posts'));
        exit;
    }

    $id = (int) ($_POST['id'] ?? 0);
    $status = (string) ($_POST['status'] ?? '');
    $allowed = ['draft', 'published', 'archived'];
    if ($id > 0 && in_array($status, $allowed, true)) {
        $sql = "UPDATE posts SET status = ?, published_at = IF(?='published' AND published_at IS NULL, NOW(), published_at) WHERE id = ?";
        $stmt = admin_db()->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('ssi', $status, $status, $id);
            $stmt->execute();
            $stmt->close();
            admin_set_flash('success', 'Status post berhasil diperbarui.');
        }
    }
    header('Location: ' . admin_url('/admin/posts'));
    exit;
}

$rows = [];
if (admin_table_exists('post_translations') && admin_table_exists('languages')) {
    $sql = "SELECT p.id, p.status, p.published_at, p.view_count, p.created_at, COALESCE(pt.title, CONCAT('Post #', p.id)) AS title
            FROM posts p
            LEFT JOIN post_translations pt ON pt.post_id = p.id
            LEFT JOIN languages l ON l.id = pt.language_id AND l.code = 'id'
            WHERE p.deleted_at IS NULL
            GROUP BY p.id
            ORDER BY p.created_at DESC
            LIMIT 100";
} else {
    $sql = "SELECT p.id, p.status, p.published_at, p.view_count, p.created_at, CONCAT('Post #', p.id) AS title
            FROM posts p
            WHERE p.deleted_at IS NULL
            ORDER BY p.created_at DESC
            LIMIT 100";
}
$result = admin_db()->query($sql);
if ($result) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
}

admin_render_start('Manajemen Blog Posts', 'posts');
?>

<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
  <div class="p-5 border-b border-slate-200">
    <h2 class="font-bold text-lg">Daftar Post (maks 100 terbaru)</h2>
    <p class="text-sm text-slate-500 mt-1">Aksi cepat: ubah status draft/published/archived.</p>
  </div>
  <div class="md:hidden p-4 space-y-3">
    <?php foreach ($rows as $r): ?>
      <div class="rounded-xl border border-slate-200 p-4">
        <div class="font-semibold text-slate-800"><?= admin_e((string) $r['title']) ?></div>
        <div class="mt-2 text-xs text-slate-500">Views: <?= (int) $r['view_count'] ?></div>
        <div class="text-xs text-slate-500">Published: <?= admin_e((string) ($r['published_at'] ?? '-')) ?></div>
        <div class="mt-2">
          <span class="px-2 py-1 rounded-full text-xs font-bold <?= $r['status'] === 'published' ? 'bg-emerald-100 text-emerald-700' : ($r['status'] === 'draft' ? 'bg-slate-100 text-slate-700' : 'bg-amber-100 text-amber-700') ?>">
            <?= admin_e((string) $r['status']) ?>
          </span>
        </div>
        <form method="post" class="mt-3 flex flex-col gap-2">
          <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
          <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
          <select name="status" class="rounded-lg border-slate-300 text-sm w-full">
            <?php foreach (['draft', 'published', 'archived'] as $s): ?>
              <option value="<?= $s ?>" <?= $s === $r['status'] ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
          </select>
          <button class="px-3 py-2 bg-blue-800 text-white rounded-lg font-semibold w-full">Simpan</button>
        </form>
      </div>
    <?php endforeach; ?>
    <?php if (!$rows): ?>
      <div class="text-center text-sm text-slate-500 py-4">Belum ada data posts.</div>
    <?php endif; ?>
  </div>

  <div class="hidden md:block overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] tracking-wider">
      <tr>
        <th class="px-4 py-3 text-left">Judul</th>
        <th class="px-4 py-3 text-left">Views</th>
        <th class="px-4 py-3 text-left">Published</th>
        <th class="px-4 py-3 text-left">Status</th>
        <th class="px-4 py-3 text-right">Aksi</th>
      </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
      <?php foreach ($rows as $r): ?>
        <tr>
          <td class="px-4 py-3 font-semibold text-slate-800"><?= admin_e((string) $r['title']) ?></td>
          <td class="px-4 py-3"><?= (int) $r['view_count'] ?></td>
          <td class="px-4 py-3 text-slate-500"><?= admin_e((string) ($r['published_at'] ?? '-')) ?></td>
          <td class="px-4 py-3">
            <span class="px-2 py-1 rounded-full text-xs font-bold <?= $r['status'] === 'published' ? 'bg-emerald-100 text-emerald-700' : ($r['status'] === 'draft' ? 'bg-slate-100 text-slate-700' : 'bg-amber-100 text-amber-700') ?>">
              <?= admin_e((string) $r['status']) ?>
            </span>
          </td>
          <td class="px-4 py-3">
            <form method="post" class="flex flex-col sm:flex-row sm:justify-end gap-2">
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
        <tr><td colspan="5" class="px-4 py-5 text-center text-slate-500">Belum ada data posts.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php admin_render_end(); ?>
