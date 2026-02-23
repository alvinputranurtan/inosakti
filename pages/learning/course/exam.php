<?php
declare(strict_types=1);
require_once __DIR__ . '/../lib.php';

$slug = trim((string) ($_GET['slug'] ?? 'basic-iot-esp32'));
$course = learning_fetch_course_by_slug($slug) ?: learning_fetch_course_by_slug('basic-iot-esp32');
$courseId = (int) ($course['id'] ?? 0);
$pageCfg = $courseId > 0 ? learning_fetch_course_page_config($courseId, 'exam') : null;
$assessment = $courseId > 0 ? learning_fetch_course_assessment($courseId, 'exam') : null;
$questions = $assessment ? learning_fetch_assessment_questions_with_options((int) $assessment['id']) : [];
$activeIndex = max(1, (int) ($_GET['q'] ?? 1));
$activeIndex = min($activeIndex, max(1, count($questions)));
$activeQuestion = $questions[$activeIndex - 1] ?? null;

$pageTitle = ((string) ($pageCfg['title'] ?? 'Final Test')) . ' - ' . (string) ($course['title'] ?? 'Basic IoT ESP32');
$pageDesc = (string) ($pageCfg['description'] ?? 'Final test pilihan ganda multi answer kelas ESP32.');
include __DIR__.'/../../../inc/header.php';
?>
<main class="max-w-7xl mx-auto px-6 py-28">
  <div class="grid lg:grid-cols-[1fr_320px] gap-6">
    <section class="bg-white border border-slate-200 rounded-2xl p-8">
      <?php if ($activeQuestion): ?>
        <div class="text-xs font-bold uppercase tracking-widest text-accent mb-2">Question <?php echo $activeIndex; ?> of <?php echo count($questions); ?></div>
        <h1 class="text-xl font-bold mb-2"><?php echo htmlspecialchars((string) ($assessment['title'] ?? 'Final Test')); ?></h1>
        <p class="text-sm text-slate-500 mb-6"><?php echo htmlspecialchars((string) ($assessment['instruction_text'] ?? '')); ?></p>
        <h2 class="text-lg font-semibold mb-4"><?php echo htmlspecialchars((string) ($activeQuestion['question_text'] ?? '')); ?></h2>
        <div class="space-y-3">
          <?php $inputType = ((string) ($activeQuestion['question_type'] ?? 'mcq_single')) === 'mcq_multi' ? 'checkbox' : 'radio'; ?>
          <?php foreach (($activeQuestion['options'] ?? []) as $option): ?>
            <label class="flex items-center gap-3 p-4 rounded-xl border-2 border-slate-100 hover:border-blue-300">
              <input type="<?php echo $inputType; ?>" name="exam<?php echo (int) $activeQuestion['id']; ?><?php echo $inputType === 'checkbox' ? '[]' : ''; ?>" value="<?php echo (int) $option['id']; ?>" class="text-blue-700">
              <span><?php echo htmlspecialchars((string) ($option['option_text'] ?? '')); ?></span>
            </label>
          <?php endforeach; ?>
        </div>
        <div class="mt-8 flex justify-between">
          <?php if ($activeIndex > 1): ?>
            <a href="<?php echo $basePath; ?>/pages/learning/course/exam?slug=<?php echo urlencode((string) ($course['slug'] ?? 'basic-iot-esp32')); ?>&q=<?php echo $activeIndex - 1; ?>" class="px-4 py-2 rounded-lg border border-slate-200 font-semibold text-sm">Previous</a>
          <?php else: ?>
            <span></span>
          <?php endif; ?>
          <?php if ($activeIndex < count($questions)): ?>
            <a href="<?php echo $basePath; ?>/pages/learning/course/exam?slug=<?php echo urlencode((string) ($course['slug'] ?? 'basic-iot-esp32')); ?>&q=<?php echo $activeIndex + 1; ?>" class="px-5 py-2 rounded-lg bg-blue-800 text-white font-semibold text-sm">Save & Next</a>
          <?php else: ?>
            <a href="<?php echo $basePath; ?>/pages/learning/course/essay?slug=<?php echo urlencode((string) ($course['slug'] ?? 'basic-iot-esp32')); ?>" class="px-5 py-2 rounded-lg bg-blue-800 text-white font-semibold text-sm">Lanjut Essay</a>
          <?php endif; ?>
        </div>
      <?php else: ?>
        <div class="rounded-xl border border-dashed border-slate-300 p-6 text-slate-500 text-sm">
          Final test belum tersedia. Tambahkan dari Admin Panel > Kursus.
        </div>
      <?php endif; ?>
    </section>
    <aside class="bg-white border border-slate-200 rounded-2xl p-5">
      <h2 class="font-bold mb-4">Exam Navigation</h2>
      <div class="grid grid-cols-5 gap-2">
        <?php for ($i = 1; $i <= max(1, count($questions)); $i++): ?>
          <a href="<?php echo $basePath; ?>/pages/learning/course/exam?slug=<?php echo urlencode((string) ($course['slug'] ?? 'basic-iot-esp32')); ?>&q=<?php echo $i; ?>" class="h-9 rounded-lg border <?php echo $i === $activeIndex ? 'border-blue-700 text-blue-700' : 'border-slate-200 text-slate-500'; ?> flex items-center justify-center text-xs font-bold">
            <?php echo str_pad((string) $i, 2, '0', STR_PAD_LEFT); ?>
          </a>
        <?php endfor; ?>
      </div>
    </aside>
  </div>
</main>
<?php include __DIR__.'/../../../inc/footer.php'; ?>
