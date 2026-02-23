<?php
declare(strict_types=1);
require_once __DIR__ . '/../lib.php';

$slug = trim((string) ($_GET['slug'] ?? 'basic-iot-esp32'));
$course = learning_fetch_course_by_slug($slug) ?: learning_fetch_course_by_slug('basic-iot-esp32');
$courseId = (int) ($course['id'] ?? 0);
$resources = $courseId > 0 ? learning_fetch_course_resources($courseId) : [];
$media = $courseId > 0 ? learning_fetch_course_media($courseId) : [];
$pageCfg = $courseId > 0 ? learning_fetch_course_page_config($courseId, 'resources') : null;
$merged = [];
foreach ($resources as $r) {
    $merged[] = [
        'title' => (string) ($r['title'] ?? ''),
        'resource_type' => (string) ($r['resource_type'] ?? 'file'),
        'resource_url' => (string) ($r['resource_url'] ?? ''),
        'description' => (string) ($r['description'] ?? ''),
    ];
}
foreach ($media as $m) {
    $merged[] = [
        'title' => (string) ($m['title'] ?? ''),
        'resource_type' => (string) ($m['media_kind'] ?? 'document'),
        'resource_url' => (string) ($m['file_url'] ?? ''),
        'description' => (string) ($m['description'] ?? ''),
    ];
}

$pageTitle = ((string) ($pageCfg['title'] ?? 'Resources')) . ' - ' . (string) ($course['title'] ?? 'Basic IoT ESP32');
$pageDesc = (string) ($pageCfg['description'] ?? 'Downloadable resources untuk kelas Basic IoT dengan ESP32.');
include __DIR__.'/../../../inc/header.php';
?>
<main class="max-w-6xl mx-auto px-6 py-28">
  <div class="flex items-center justify-between mb-8">
    <div>
      <h1 class="text-3xl font-display font-bold"><?php echo htmlspecialchars((string) ($course['title'] ?? 'Course')); ?></h1>
      <p class="text-slate-500 mt-1"><?php echo htmlspecialchars((string) ($pageCfg['subtitle'] ?? 'Downloadable Resources')); ?></p>
    </div>
    <a href="<?php echo $basePath; ?>/pages/learning/course/classroom?slug=<?php echo urlencode((string) ($course['slug'] ?? 'basic-iot-esp32')); ?>" class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-semibold">Kembali ke Kelas</a>
  </div>

  <div class="space-y-4">
    <?php foreach ($merged as $r): ?>
      <div class="bg-white border border-slate-200 rounded-2xl p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h3 class="font-bold"><?php echo htmlspecialchars((string) $r['title']); ?></h3>
          <p class="text-xs text-slate-500 mt-1"><?php echo htmlspecialchars((string) $r['resource_type']); ?> | <?php echo htmlspecialchars((string) ($r['description'] ?? '')); ?></p>
        </div>
        <?php if (!empty($r['resource_url'])): ?>
          <a href="<?php echo htmlspecialchars((string) $r['resource_url']); ?>" class="px-4 py-2 rounded-lg bg-blue-800 text-white text-sm font-semibold inline-flex items-center gap-2" download>
            <span class="material-symbols-outlined text-base">download</span>
            Download
          </a>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
    <?php if (!$merged): ?>
      <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500">
        Resource belum tersedia. Tambahkan dari Admin Panel > Kursus.
      </div>
    <?php endif; ?>
  </div>
</main>
<?php include __DIR__.'/../../../inc/footer.php'; ?>
