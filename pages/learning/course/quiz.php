<?php
declare(strict_types=1);
require_once __DIR__.'/../lib.php';

$slug = trim((string) ($_GET['slug'] ?? 'basic-iot-esp32'));
$course = learning_fetch_course_by_slug($slug) ?: learning_fetch_course_by_slug('basic-iot-esp32');
$courseId = (int) ($course['id'] ?? 0);
$pageCfg = $courseId > 0 ? learning_fetch_course_page_config($courseId, 'quiz') : null;
$assessment = $courseId > 0 ? learning_fetch_course_assessment($courseId, 'quiz') : null;
$questions = $assessment ? learning_fetch_assessment_questions_with_options((int) $assessment['id']) : [];
$activeIndex = max(1, (int) ($_GET['q'] ?? 1));
$activeIndex = min($activeIndex, max(1, count($questions)));
$activeQuestion = $questions[$activeIndex - 1] ?? null;

$pageTitle = ((string) ($pageCfg['title'] ?? 'Quiz')) . ' - ' . (string) ($course['title'] ?? 'Basic IoT ESP32');
$pageDesc = (string) ($pageCfg['description'] ?? 'Quiz pilihan ganda untuk evaluasi lesson ESP32.');
include __DIR__.'/../../../inc/header.php';
?>
<main class="max-w-4xl mx-auto px-6 py-28">
  <div class="bg-white border border-slate-200 rounded-2xl p-8">
    <?php if ($activeQuestion): ?>
      <div class="text-sm font-bold text-accent uppercase tracking-widest mb-2">Question <?php echo $activeIndex; ?> of <?php echo count($questions); ?></div>
      <h1 class="text-xl font-bold mb-2"><?php echo htmlspecialchars((string) ($assessment['title'] ?? 'Quiz')); ?></h1>
      <p class="text-sm text-slate-500 mb-6"><?php echo htmlspecialchars((string) ($assessment['instruction_text'] ?? '')); ?></p>
      <h2 class="text-lg font-semibold mb-4"><?php echo htmlspecialchars((string) ($activeQuestion['question_text'] ?? '')); ?></h2>
      <form class="space-y-3">
        <?php foreach (($activeQuestion['options'] ?? []) as $option): ?>
          <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 hover:border-blue-300">
            <input type="radio" name="q<?php echo (int) $activeQuestion['id']; ?>" value="<?php echo (int) $option['id']; ?>" class="text-blue-700">
            <span><?php echo htmlspecialchars((string) ($option['option_text'] ?? '')); ?></span>
          </label>
        <?php endforeach; ?>
        <div class="pt-4 flex justify-between">
          <a href="<?php echo $basePath; ?>/pages/learning/course/classroom?slug=<?php echo urlencode((string) ($course['slug'] ?? 'basic-iot-esp32')); ?>" class="px-4 py-2 text-sm font-semibold text-slate-600">Kembali</a>
          <?php if ($activeIndex < count($questions)): ?>
            <a href="<?php echo $basePath; ?>/pages/learning/course/quiz?slug=<?php echo urlencode((string) ($course['slug'] ?? 'basic-iot-esp32')); ?>&q=<?php echo $activeIndex + 1; ?>" class="px-5 py-2 bg-blue-800 text-white rounded-lg text-sm font-semibold">Submit & Next</a>
          <?php else: ?>
            <a href="<?php echo $basePath; ?>/pages/learning/course/exam?slug=<?php echo urlencode((string) ($course['slug'] ?? 'basic-iot-esp32')); ?>" class="px-5 py-2 bg-blue-800 text-white rounded-lg text-sm font-semibold">Lanjut Final Test</a>
          <?php endif; ?>
        </div>
      </form>
    <?php else: ?>
      <div class="rounded-xl border border-dashed border-slate-300 p-6 text-slate-500 text-sm">
        Quiz belum tersedia. Tambahkan dari Admin Panel > Kursus.
      </div>
    <?php endif; ?>
  </div>
</main>
<?php include __DIR__.'/../../../inc/footer.php'; ?>
