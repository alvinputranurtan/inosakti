<?php
declare(strict_types=1);
require_once __DIR__ . '/../lib.php';

$slug = trim((string) ($_GET['slug'] ?? 'basic-iot-esp32'));
$course = learning_fetch_course_by_slug($slug);
if (!$course && $slug !== 'basic-iot-esp32') {
    $course = learning_fetch_course_by_slug('basic-iot-esp32');
}
$courseId = (int) ($course['id'] ?? 0);
$modules = $courseId > 0 ? learning_fetch_course_modules_with_lessons($courseId) : [];
$resources = $courseId > 0 ? learning_fetch_course_resources($courseId) : [];
$pageCfg = $courseId > 0 ? learning_fetch_course_page_config($courseId, 'landing') : null;
$pageLayout = is_array($pageCfg['layout'] ?? null) ? $pageCfg['layout'] : [];

$title = (string) ($pageCfg['title'] ?? ($course['title'] ?? 'Basic IoT dengan ESP32 dari Nol sampai Siap Deploy'));
$shortDescription = (string) ($pageCfg['description'] ?? ($course['short_description'] ?? 'Belajar IoT end-to-end: sensor, konektivitas WiFi, MQTT, dashboard, dan deployment mini project dengan ESP32.'));
$image = (string) ($course['featured_image'] ?? 'https://images.unsplash.com/photo-1553406830-ef2513450d76?q=80&w=1600&auto=format&fit=crop');
$price = (float) ($course['price'] ?? 450000);
$heroBorderPreset = (string) ($pageLayout['hero_border_preset'] ?? 'border-white');
$heroBgPreset = (string) ($pageLayout['hero_bg_preset'] ?? 'slate-cyan');

$heroSectionClass = 'bg-slate-950';
$heroBlobTopClass = 'bg-accent';
$heroBlobBottomClass = 'bg-cyan-400';
$heroCtaBgClass = 'bg-accent';
$heroCtaHoverClass = 'hover:bg-blue-700';
$heroCtaShadowClass = 'shadow-accent/20';
if ($heroBgPreset === 'indigo-blue') {
    $heroSectionClass = 'bg-indigo-950';
    $heroBlobTopClass = 'bg-blue-500';
    $heroBlobBottomClass = 'bg-indigo-400';
    $heroCtaBgClass = 'bg-blue-600';
    $heroCtaHoverClass = 'hover:bg-blue-700';
    $heroCtaShadowClass = 'shadow-blue-600/30';
} elseif ($heroBgPreset === 'emerald-teal') {
    $heroSectionClass = 'bg-emerald-950';
    $heroBlobTopClass = 'bg-teal-400';
    $heroBlobBottomClass = 'bg-emerald-400';
    $heroCtaBgClass = 'bg-teal-500';
    $heroCtaHoverClass = 'hover:bg-teal-600';
    $heroCtaShadowClass = 'shadow-teal-500/30';
} elseif ($heroBgPreset === 'amber-rose') {
    $heroSectionClass = 'bg-rose-950';
    $heroBlobTopClass = 'bg-amber-400';
    $heroBlobBottomClass = 'bg-rose-400';
    $heroCtaBgClass = 'bg-amber-500';
    $heroCtaHoverClass = 'hover:bg-amber-600';
    $heroCtaShadowClass = 'shadow-amber-500/30';
}

$heroBorderClass = 'border-white/10';
if ($heroBorderPreset === 'border-cyan') {
    $heroBorderClass = 'border-cyan-300/50';
} elseif ($heroBorderPreset === 'border-amber') {
    $heroBorderClass = 'border-amber-300/50';
} elseif ($heroBorderPreset === 'border-emerald') {
    $heroBorderClass = 'border-emerald-300/50';
}

$pageTitle = $title . ' - InoSakti Learning';
$pageDesc = $shortDescription;
include __DIR__.'/../../../inc/header.php';
?>

<main class="pt-16 md:pt-0">
  <section class="<?php echo htmlspecialchars($heroSectionClass); ?> text-white py-16 md:py-24 overflow-hidden relative">
    <div class="absolute inset-0 opacity-20 pointer-events-none">
      <div class="absolute top-0 right-0 w-[520px] h-[520px] <?php echo htmlspecialchars($heroBlobTopClass); ?> rounded-full blur-[120px]"></div>
      <div class="absolute bottom-0 left-0 w-[320px] h-[320px] <?php echo htmlspecialchars($heroBlobBottomClass); ?> rounded-full blur-[110px]"></div>
    </div>
    <div class="max-w-7xl mx-auto px-6 relative z-10">
      <div class="grid lg:grid-cols-2 gap-12 items-center">
        <div>
          <a href="<?php echo $basePath; ?>/pages/learning" class="mb-6 px-4 py-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white font-bold rounded-xl transition-all inline-flex items-center justify-center gap-2">
            <span class="material-symbols-outlined">arrow_back</span>
            Kembali
          </a>
          <h1 class="font-display text-4xl md:text-5xl font-extrabold leading-tight mb-6">
            <?php echo htmlspecialchars($title); ?>
          </h1>
          <p class="text-slate-300 text-lg mb-8 max-w-xl"><?php echo htmlspecialchars($shortDescription); ?></p>
          <div class="flex flex-col sm:flex-row gap-4">
            <a href="<?php echo $basePath; ?>/pages/learning/course/classroom?slug=<?php echo urlencode((string) ($course['slug'] ?? 'basic-iot-esp32')); ?>" class="px-8 py-4 <?php echo htmlspecialchars($heroCtaBgClass); ?> <?php echo htmlspecialchars($heroCtaHoverClass); ?> text-white font-bold rounded-xl transition-all shadow-lg <?php echo htmlspecialchars($heroCtaShadowClass); ?> inline-flex items-center justify-center gap-2">
              Mulai Kelas
              <span class="material-symbols-outlined">arrow_forward</span>
            </a>
          </div>
        </div>
        <div class="aspect-video bg-slate-800 rounded-2xl overflow-hidden border-4 <?php echo htmlspecialchars($heroBorderClass); ?> shadow-2xl relative">
          <img alt="<?php echo htmlspecialchars($title); ?>" class="w-full h-full object-cover opacity-70" src="<?php echo htmlspecialchars($image); ?>"/>
        </div>
      </div>
    </div>
  </section>

  <div class="max-w-7xl mx-auto px-6 py-12">
    <div class="grid lg:grid-cols-3 gap-10">
      <div class="lg:col-span-2 space-y-10">
        <section>
          <h2 class="font-display text-2xl font-bold mb-6"><?php echo htmlspecialchars((string) ($pageLayout['syllabus_title'] ?? 'Syllabus')); ?></h2>
          <?php if ($modules): ?>
            <div class="space-y-4">
              <?php foreach ($modules as $module): ?>
                <div class="bg-white border border-slate-200 rounded-2xl p-5">
                  <h3 class="font-bold">Modul <?php echo (int) $module['module_order']; ?>: <?php echo htmlspecialchars((string) $module['title']); ?></h3>
                  <div class="mt-3 space-y-2 text-sm">
                    <?php foreach (($module['lessons'] ?? []) as $lesson): ?>
                      <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                        <div>
                          <span class="font-semibold"><?php echo (int) $lesson['lesson_order']; ?>.</span>
                          <?php echo htmlspecialchars((string) $lesson['title']); ?>
                          <span class="text-xs text-slate-500">(<?php echo htmlspecialchars((string) $lesson['lesson_type']); ?>)</span>
                        </div>
                        <span class="text-xs text-slate-500"><?php echo htmlspecialchars(learning_format_duration((int) ($lesson['duration_seconds'] ?? 0))); ?></span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <div class="rounded-xl border border-dashed border-slate-300 p-5 text-sm text-slate-500">
              Modul belum tersedia. Tambahkan dari Admin Panel > Kursus.
            </div>
          <?php endif; ?>
        </section>
      </div>
      <aside class="space-y-6">
        <div class="bg-white border border-slate-200 rounded-2xl p-6 sticky top-28 lg:mt-14">
          <div class="text-3xl font-bold mb-6">Rp <?php echo number_format($price, 0, ',', '.'); ?></div>
          <a href="<?php echo $basePath; ?>/pages/learning/course/classroom?slug=<?php echo urlencode((string) ($course['slug'] ?? 'basic-iot-esp32')); ?>" class="w-full py-4 <?php echo htmlspecialchars($heroCtaBgClass); ?> <?php echo htmlspecialchars($heroCtaHoverClass); ?> text-white font-bold rounded-xl mb-4 inline-flex justify-center transition-colors">Enroll & Mulai</a>
          <div class="text-sm text-slate-600 mt-2">
            <div>Total resource: <?php echo count($resources); ?></div>
            <div>Total modul: <?php echo count($modules); ?></div>
          </div>
        </div>
      </aside>
    </div>
  </div>
</main>

<?php include __DIR__.'/../../../inc/footer.php'; ?>
