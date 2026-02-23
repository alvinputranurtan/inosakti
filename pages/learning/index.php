<?php
declare(strict_types=1);
require_once __DIR__ . '/lib.php';

$pageTitle = 'InoSakti Online Education Platform';
$pageDesc = 'Platform pembelajaran online InoSakti untuk kursus engineering, AI, IoT, web development, networking, dan energi terbarukan.';
$courses = learning_fetch_published_courses(24);
$resumeCourse = $courses[0] ?? null;

$extraHead = <<<'HTML'
<style type="text/tailwindcss">
@layer utilities {
  .course-card { @apply transition-all duration-300 hover:-translate-y-1 hover:shadow-xl; }
}
</style>
HTML;

include __DIR__.'/../../inc/header.php';
?>

<main class="max-w-7xl mx-auto px-6 py-28">
  <section class="mb-10">
    <h2 class="font-display text-xl font-bold text-slate-900 dark:text-white mb-4">Resume Learning</h2>
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
      <div>
        <h3 class="font-bold text-slate-900 dark:text-white">
          <?php echo htmlspecialchars((string) ($resumeCourse['title'] ?? 'Lanjutkan Pembelajaran')); ?>
        </h3>
        <p class="text-sm text-slate-500">
          <?php echo htmlspecialchars((string) ($resumeCourse['short_description'] ?? 'Pilih kursus dan lanjutkan progress belajar Anda.')); ?>
        </p>
      </div>
      <a href="<?php echo $basePath; ?>/pages/learning/course?slug=<?php echo urlencode((string) ($resumeCourse['slug'] ?? 'basic-iot-esp32')); ?>" class="w-full md:w-auto px-6 py-3 bg-accent text-white font-bold rounded-xl hover:bg-blue-700 transition-colors inline-flex items-center justify-center gap-2">
        <span class="material-symbols-outlined">play_circle</span>
        Continue
      </a>
    </div>
  </section>

  <section class="mb-8 flex items-center justify-between gap-4">
    <h2 class="font-display text-2xl font-bold text-slate-900 dark:text-white">Online Courses</h2>
    <span class="text-sm font-medium text-slate-500">Published: <?php echo count($courses); ?> kursus</span>
  </section>

  <section class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
    <?php if ($courses): ?>
      <?php foreach ($courses as $course): ?>
        <article class="course-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
          <div class="relative aspect-video overflow-hidden">
            <img
              alt="<?php echo htmlspecialchars((string) $course['title']); ?>"
              class="w-full h-full object-cover"
              src="<?php echo htmlspecialchars((string) ($course['featured_image'] ?: 'https://images.unsplash.com/photo-1553406830-ef2513450d76?q=80&w=1600&auto=format&fit=crop')); ?>"
            />
            <div class="absolute top-3 right-3 px-2 py-1 bg-white/90 dark:bg-slate-900/90 rounded-lg text-xs font-bold text-accent">
              <?php echo htmlspecialchars(ucfirst((string) $course['level'])); ?>
            </div>
          </div>
          <div class="p-5 flex-1 flex flex-col">
            <h3 class="font-bold text-slate-900 dark:text-white mb-2"><?php echo htmlspecialchars((string) $course['title']); ?></h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4"><?php echo htmlspecialchars((string) $course['short_description']); ?></p>
            <div class="mt-auto flex items-center justify-between gap-3">
              <div class="text-lg font-bold text-slate-900 dark:text-white">
                Rp <?php echo number_format((float) $course['price'], 0, ',', '.'); ?>
              </div>
              <a href="<?php echo $basePath; ?>/pages/learning/course?slug=<?php echo urlencode((string) $course['slug']); ?>" class="px-3 py-2 bg-blue-800 text-white rounded-lg text-sm font-semibold">
                Detail
              </a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="col-span-full rounded-2xl border border-dashed border-slate-300 p-8 text-center text-slate-500">
        Belum ada kursus published di database. Silakan buat di Admin Panel > Kursus.
      </div>
    <?php endif; ?>
  </section>
</main>

<?php include __DIR__.'/../../inc/footer.php'; ?>
