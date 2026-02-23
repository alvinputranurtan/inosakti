<?php
declare(strict_types=1);
require_once __DIR__ . '/../lib.php';

$slug = trim((string) ($_GET['slug'] ?? 'basic-iot-esp32'));
$course = learning_fetch_course_by_slug($slug) ?: learning_fetch_course_by_slug('basic-iot-esp32');
$courseId = (int) ($course['id'] ?? 0);
$pageCfg = $courseId > 0 ? learning_fetch_course_page_config($courseId, 'certificate') : null;
$layout = is_array($pageCfg['layout'] ?? null) ? $pageCfg['layout'] : [];
$issuer = (string) ($layout['issuer_name'] ?? 'InoSakti Learning');
$learnerName = trim((string) ($_GET['name'] ?? 'Alvin Putra Nurtan'));

$pageTitle = ((string) ($pageCfg['title'] ?? 'Certificate')) . ' - ' . (string) ($course['title'] ?? 'Basic IoT ESP32');
$pageDesc = (string) ($pageCfg['description'] ?? 'Sertifikat kelulusan kelas online Basic IoT dengan ESP32.');
include __DIR__.'/../../../inc/header.php';
?>
<main class="max-w-6xl mx-auto px-6 py-28">
  <section class="text-center mb-8">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-bold uppercase tracking-wider mb-4">
      <span class="material-symbols-outlined text-sm">military_tech</span>
      Course Completed
    </div>
    <h1 class="text-4xl font-display font-extrabold"><?php echo htmlspecialchars((string) ($pageCfg['subtitle'] ?? 'Selamat! Kamu Lulus')); ?></h1>
    <p class="text-slate-500 mt-2"><?php echo htmlspecialchars((string) ($course['title'] ?? 'Basic IoT dengan ESP32')); ?></p>
  </section>

  <div class="bg-white border-[10px] border-slate-100 rounded-2xl shadow-xl p-8 md:p-12">
    <div class="text-center">
      <p class="uppercase tracking-[0.25em] text-xs text-slate-400 font-semibold"><?php echo htmlspecialchars((string) ($pageCfg['title'] ?? 'Certificate of Completion')); ?></p>
      <h2 class="text-3xl font-display font-bold mt-5 mb-2"><?php echo htmlspecialchars($learnerName); ?></h2>
      <p class="text-slate-600">telah menyelesaikan kelas</p>
      <p class="text-xl font-bold mt-2"><?php echo htmlspecialchars((string) ($course['title'] ?? '')); ?></p>
      <div class="mt-8 grid md:grid-cols-3 gap-4 text-sm text-slate-600">
        <div>
          <div class="text-xs uppercase text-slate-400">Tanggal</div>
          <div><?php echo date('d M Y'); ?></div>
        </div>
        <div>
          <div class="text-xs uppercase text-slate-400">Certificate ID</div>
          <div>IS-ESP32-<?php echo date('ymd'); ?></div>
        </div>
        <div>
          <div class="text-xs uppercase text-slate-400">Issued By</div>
          <div><?php echo htmlspecialchars($issuer); ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
    <button class="px-6 py-3 bg-blue-800 text-white rounded-xl font-semibold inline-flex items-center justify-center gap-2">
      <span class="material-symbols-outlined text-base">download</span>
      Download Certificate
    </button>
    <a href="<?php echo $basePath; ?>/pages/learning" class="px-6 py-3 border border-slate-200 rounded-xl font-semibold inline-flex items-center justify-center gap-2">
      <span class="material-symbols-outlined text-base">school</span>
      Kembali ke Learning
    </a>
  </div>
</main>
<?php include __DIR__.'/../../../inc/footer.php'; ?>
