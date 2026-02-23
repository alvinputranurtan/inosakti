<?php
declare(strict_types=1);
require_once __DIR__ . '/../lib.php';

$slug = trim((string) ($_GET['slug'] ?? 'basic-iot-esp32'));
$course = learning_fetch_course_by_slug($slug) ?: learning_fetch_course_by_slug('basic-iot-esp32');
$courseId = (int) ($course['id'] ?? 0);
$pageCfg = $courseId > 0 ? learning_fetch_course_page_config($courseId, 'essay') : null;
$assessment = $courseId > 0 ? learning_fetch_course_assessment($courseId, 'essay') : null;
$questions = $assessment ? learning_fetch_assessment_questions_with_options((int) $assessment['id']) : [];
$activeIndex = max(1, (int) ($_GET['q'] ?? 1));
$activeIndex = min($activeIndex, max(1, count($questions)));
$activeQuestion = $questions[$activeIndex - 1] ?? null;

$pageTitle = ((string) ($pageCfg['title'] ?? 'Essay Assessment')) . ' - ' . (string) ($course['title'] ?? 'Basic IoT ESP32');
$pageDesc = (string) ($pageCfg['description'] ?? 'Essay assessment untuk validasi pemahaman konsep IoT.');
include __DIR__.'/../../../inc/header.php';
?>
<main class="max-w-6xl mx-auto px-6 py-28">
  <div class="grid lg:grid-cols-[1fr_280px] gap-6">
    <section class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
      <div class="p-6 border-b border-slate-100">
        <div class="text-xs uppercase tracking-widest text-accent font-bold mb-2">Essay <?php echo $activeIndex; ?> of <?php echo max(1, count($questions)); ?></div>
        <h1 class="text-xl font-bold"><?php echo htmlspecialchars((string) ($activeQuestion['question_text'] ?? 'Essay belum tersedia.')); ?></h1>
      </div>
      <div class="p-6">
        <textarea class="w-full min-h-[320px] rounded-xl border-slate-300" placeholder="Tulis jawaban teknis kamu di sini..."></textarea>
      </div>
      <div class="px-6 pb-6 flex justify-between">
        <?php if ($activeIndex > 1): ?>
          <a href="<?php echo $basePath; ?>/pages/learning/course/essay?slug=<?php echo urlencode((string) ($course['slug'] ?? 'basic-iot-esp32')); ?>&q=<?php echo $activeIndex - 1; ?>" class="px-6 py-3 border border-slate-200 rounded-xl font-semibold">Previous</a>
        <?php else: ?>
          <span></span>
        <?php endif; ?>
        <?php if ($activeIndex < count($questions)): ?>
          <a href="<?php echo $basePath; ?>/pages/learning/course/essay?slug=<?php echo urlencode((string) ($course['slug'] ?? 'basic-iot-esp32')); ?>&q=<?php echo $activeIndex + 1; ?>" class="px-6 py-3 bg-blue-800 text-white rounded-xl font-semibold">Save & Next</a>
        <?php else: ?>
          <a href="<?php echo $basePath; ?>/pages/learning/course/certificate?slug=<?php echo urlencode((string) ($course['slug'] ?? 'basic-iot-esp32')); ?>" class="px-6 py-3 bg-blue-800 text-white rounded-xl font-semibold">Submit Final Answer</a>
        <?php endif; ?>
      </div>
    </section>
    <aside class="bg-white border border-slate-200 rounded-2xl p-5">
      <h2 class="font-bold mb-3">Tips Jawaban</h2>
      <ul class="text-sm text-slate-600 space-y-2 list-disc pl-5">
        <?php if (!empty($activeQuestion['hint_text'])): ?>
          <li><?php echo htmlspecialchars((string) $activeQuestion['hint_text']); ?></li>
        <?php endif; ?>
        <li>Gunakan struktur: masalah, desain, implementasi, evaluasi.</li>
        <li>Sertakan angka/parameter teknis bila memungkinkan.</li>
      </ul>
    </aside>
  </div>
</main>
<?php include __DIR__.'/../../../inc/footer.php'; ?>
