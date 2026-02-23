<?php
declare(strict_types=1);
require_once __DIR__ . '/../lib.php';

$slug = trim((string) ($_GET['slug'] ?? 'basic-iot-esp32'));
$course = learning_fetch_course_by_slug($slug) ?: learning_fetch_course_by_slug('basic-iot-esp32');
$courseId = (int) ($course['id'] ?? 0);
$pageCfg = $courseId > 0 ? learning_fetch_course_page_config($courseId, 'slides') : null;
$media = $courseId > 0 ? learning_fetch_course_media($courseId, 'presentation') : [];
$fallbackDocs = $courseId > 0 ? learning_fetch_course_media($courseId, 'document') : [];
$primary = $media[0] ?? ($fallbackDocs[0] ?? null);

$pageTitle = ((string) ($pageCfg['title'] ?? 'Slides')) . ' - ' . (string) ($course['title'] ?? 'Basic IoT ESP32');
$pageDesc = (string) ($pageCfg['description'] ?? 'Slide viewer materi kelas Basic IoT dengan ESP32.');
include __DIR__.'/../../../inc/header.php';
?>
<main class="max-w-7xl mx-auto px-6 py-28">
  <div class="flex items-center justify-between mb-6">
    <div>
      <h1 class="text-2xl font-bold"><?php echo htmlspecialchars((string) ($pageCfg['title'] ?? 'Slide Materi')); ?></h1>
      <p class="text-slate-500 text-sm"><?php echo htmlspecialchars((string) ($course['title'] ?? '')); ?></p>
    </div>
    <?php if (!empty($primary['file_url'])): ?>
      <a href="<?php echo htmlspecialchars((string) $primary['file_url']); ?>" class="px-4 py-2 rounded-lg border border-slate-200 text-sm font-semibold" download>Download</a>
    <?php endif; ?>
  </div>
  <div class="bg-white border border-slate-200 rounded-2xl p-6">
    <div class="aspect-video rounded-xl overflow-hidden border border-slate-200">
      <img class="w-full h-full object-cover" src="<?php echo htmlspecialchars((string) ($course['featured_image'] ?? 'https://images.unsplash.com/photo-1553406830-ef2513450d76?q=80&w=1600&auto=format&fit=crop')); ?>" alt="Slide"/>
    </div>
    <div class="mt-4 text-sm text-slate-600">
      <?php echo htmlspecialchars((string) ($pageCfg['description'] ?? 'Gunakan slide ini untuk memahami topic design, QoS, reconnect strategy, dan validasi payload telemetry.')); ?>
    </div>
  </div>
</main>
<?php include __DIR__.'/../../../inc/footer.php'; ?>
