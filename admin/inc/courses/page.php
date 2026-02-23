<?php
declare(strict_types=1);
require_once __DIR__ . '/../layout.php';
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
$hasTranslations = admin_table_exists('course_translations') && admin_table_exists('languages');
$hasCourseCategories = admin_table_exists('course_categories');
$hasInstructors = admin_table_exists('instructors');
$hasEnrollments = admin_table_exists('enrollments');

if ($hasTranslations) {
    $sql = "SELECT c.id, c.status, c.price, c.published_at, c.created_at,
                   COALESCE(ct.title, CONCAT('Course #', c.id)) AS title,
                   COALESCE(ct.slug, '') AS slug,
                   " . ($hasCourseCategories ? "COALESCE(cc.name, '-')" : "'-'") . " AS category_name,
                   " . ($hasInstructors ? "COALESCE(i.display_name, '-')" : "'-'") . " AS author_name,
                   " . ($hasEnrollments ? "COALESCE(ec.student_count, 0)" : "0") . " AS student_count
            FROM courses c
            LEFT JOIN course_translations ct
              ON ct.course_id = c.id
             AND ct.language_id = (SELECT id FROM languages WHERE code='id' ORDER BY id ASC LIMIT 1)
            " . ($hasCourseCategories ? "LEFT JOIN course_categories cc ON cc.id = c.category_id" : "") . "
            " . ($hasInstructors ? "LEFT JOIN instructors i ON i.id = c.instructor_id" : "") . "
            " . ($hasEnrollments ? "LEFT JOIN (
                SELECT course_id, COUNT(*) AS student_count
                FROM enrollments
                WHERE status = 'active'
                GROUP BY course_id
            ) ec ON ec.course_id = c.id" : "") . "
            WHERE c.deleted_at IS NULL
            ORDER BY c.created_at DESC
            LIMIT 100";
} else {
    $sql = "SELECT c.id, c.status, c.price, c.published_at, c.created_at,
                   CONCAT('Course #', c.id) AS title,
                   '' AS slug,
                   " . ($hasCourseCategories ? "COALESCE(cc.name, '-')" : "'-'") . " AS category_name,
                   " . ($hasInstructors ? "COALESCE(i.display_name, '-')" : "'-'") . " AS author_name,
                   " . ($hasEnrollments ? "COALESCE(ec.student_count, 0)" : "0") . " AS student_count
            FROM courses c
            " . ($hasCourseCategories ? "LEFT JOIN course_categories cc ON cc.id = c.category_id" : "") . "
            " . ($hasInstructors ? "LEFT JOIN instructors i ON i.id = c.instructor_id" : "") . "
            " . ($hasEnrollments ? "LEFT JOIN (
                SELECT course_id, COUNT(*) AS student_count
                FROM enrollments
                WHERE status = 'active'
                GROUP BY course_id
            ) ec ON ec.course_id = c.id" : "") . "
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
  <div class="p-5 border-b border-slate-200 flex items-start justify-between gap-3">
    <div>
      <h2 class="font-bold text-lg">Daftar Kursus (maks 100 terbaru)</h2>
      <p class="text-sm text-slate-500 mt-1">Update status publikasi kursus secara langsung.</p>
    </div>
    <a href="<?= admin_e(admin_url('/admin/courses?mode=create')) ?>" class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-800 text-white text-sm font-semibold hover:bg-blue-900 whitespace-nowrap">
      Tambah Kursus
    </a>
  </div>
  <div class="md:hidden p-4 space-y-3">
    <?php foreach ($rows as $idx => $r): ?>
      <div class="rounded-xl border border-slate-200 p-4">
        <div class="font-semibold text-slate-800"><?= ($idx + 1) ?>. <?= admin_e((string) $r['title']) ?></div>
        <div class="mt-1 text-xs text-slate-500">URL: <?= admin_e((string) (($r['slug'] ?? '') !== '' ? '/learning/' . (string) $r['slug'] : '-')) ?></div>
        <div class="text-xs text-slate-500">Kategori: <?= admin_e((string) ($r['category_name'] ?? '-')) ?></div>
        <div class="text-xs text-slate-500">Author: <?= admin_e((string) ($r['author_name'] ?? '-')) ?></div>
        <div class="text-xs text-slate-500">Student: <?= (int) ($r['student_count'] ?? 0) ?></div>
        <div class="mt-2 text-xs text-slate-500">Harga: Rp <?= number_format((float) $r['price'], 0, ',', '.') ?></div>
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
        <a href="<?= admin_e(admin_url('/admin/courses?course_id=' . (int) $r['id'])) ?>" class="mt-2 block text-center px-3 py-2 border border-slate-300 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50">Edit</a>
      </div>
    <?php endforeach; ?>
    <?php if (!$rows): ?>
      <div class="text-center text-sm text-slate-500 py-4">Belum ada data courses.</div>
    <?php endif; ?>
  </div>

  <div class="hidden md:block overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] tracking-wider">
      <tr>
        <th class="px-4 py-3 text-left">Urutan</th>
        <th class="px-4 py-3 text-left">Judul</th>
        <th class="px-4 py-3 text-left">URL</th>
        <th class="px-4 py-3 text-left">Kategori</th>
        <th class="px-4 py-3 text-left">Author</th>
        <th class="px-4 py-3 text-left">Student</th>
        <th class="px-4 py-3 text-left">Harga</th>
        <th class="px-4 py-3 text-left">Published</th>
        <th class="px-4 py-3 text-left">Status</th>
        <th class="px-4 py-3 text-right">Aksi</th>
      </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
      <?php foreach ($rows as $idx => $r): ?>
        <tr>
          <td class="px-4 py-3"><?= ($idx + 1) ?></td>
          <td class="px-4 py-3 font-semibold text-slate-800"><?= admin_e((string) $r['title']) ?></td>
          <td class="px-4 py-3 text-slate-500"><?= admin_e((string) (($r['slug'] ?? '') !== '' ? '/learning/' . (string) $r['slug'] : '-')) ?></td>
          <td class="px-4 py-3"><?= admin_e((string) ($r['category_name'] ?? '-')) ?></td>
          <td class="px-4 py-3"><?= admin_e((string) ($r['author_name'] ?? '-')) ?></td>
          <td class="px-4 py-3"><?= (int) ($r['student_count'] ?? 0) ?></td>
          <td class="px-4 py-3">Rp <?= number_format((float) $r['price'], 0, ',', '.') ?></td>
          <td class="px-4 py-3 text-slate-500"><?= admin_e((string) ($r['published_at'] ?? '-')) ?></td>
          <td class="px-4 py-3">
            <span class="px-2 py-1 rounded-full text-xs font-bold <?= $r['status'] === 'published' ? 'bg-emerald-100 text-emerald-700' : ($r['status'] === 'draft' ? 'bg-slate-100 text-slate-700' : 'bg-amber-100 text-amber-700') ?>">
              <?= admin_e((string) $r['status']) ?>
            </span>
          </td>
          <td class="px-4 py-3">
            <form method="post" class="flex flex-col sm:flex-row sm:justify-end gap-2">
              <a href="<?= admin_e(admin_url('/admin/courses?course_id=' . (int) $r['id'])) ?>" class="px-3 py-1.5 border border-slate-300 rounded-lg text-sm font-semibold text-slate-700 hover:bg-slate-50 text-center">Edit</a>
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
        <tr><td colspan="10" class="px-4 py-5 text-center text-slate-500">Belum ada data courses.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php admin_render_end(); ?>
