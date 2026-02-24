<?php
declare(strict_types=1);
require_once __DIR__ . '/../lib.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

function learning_auth_user(): ?array
{
    $u = $_SESSION['admin_user'] ?? null;
    return is_array($u) ? $u : null;
}

function learning_find_or_create_enrollment_id(int $courseId): int
{
    if (
        $courseId <= 0 ||
        !learning_table_exists('enrollments')
    ) {
        return 0;
    }
    $user = learning_auth_user();
    if (!$user) {
        return 0;
    }
    $email = strtolower(trim((string) ($user['email'] ?? '')));
    $name = trim((string) ($user['name'] ?? 'Student'));
    if ($email === '') {
        return 0;
    }
    $db = learning_db();
    if (!$db) {
        return 0;
    }
    $find = $db->prepare("SELECT id FROM enrollments WHERE course_id = ? AND learner_email = ? LIMIT 1");
    if ($find) {
        $find->bind_param('is', $courseId, $email);
        $find->execute();
        $row = $find->get_result()->fetch_assoc();
        $find->close();
        if ($row) {
            return (int) ($row['id'] ?? 0);
        }
    }
    $insert = $db->prepare("INSERT INTO enrollments (course_id, learner_name, learner_email, status, enrolled_at, created_at, updated_at)
                            VALUES (?, ?, ?, 'active', NOW(), NOW(), NOW())");
    if (!$insert) {
        return 0;
    }
    $insert->bind_param('iss', $courseId, $name, $email);
    if (!$insert->execute()) {
        $insert->close();
        return 0;
    }
    $id = (int) $db->insert_id;
    $insert->close();
    return $id;
}

function learning_is_lesson_in_course(int $courseId, int $lessonId): bool
{
    if ($courseId <= 0 || $lessonId <= 0) {
        return false;
    }
    $db = learning_db();
    if (!$db) {
        return false;
    }
    $stmt = $db->prepare("SELECT COUNT(*) AS cnt
                          FROM course_lessons l
                          JOIN course_modules m ON m.id = l.module_id
                          WHERE m.course_id = ? AND l.id = ?");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('ii', $courseId, $lessonId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return ((int) ($row['cnt'] ?? 0)) > 0;
}

function learning_fetch_db_progress_map(int $courseId, int $enrollmentId): array
{
    if (
        $courseId <= 0 ||
        $enrollmentId <= 0 ||
        !learning_table_exists('lesson_progress')
    ) {
        return [];
    }
    $db = learning_db();
    if (!$db) {
        return [];
    }
    $stmt = $db->prepare("SELECT lp.lesson_id, lp.is_completed
                          FROM lesson_progress lp
                          JOIN course_lessons l ON l.id = lp.lesson_id
                          JOIN course_modules m ON m.id = l.module_id
                          WHERE lp.enrollment_id = ? AND m.course_id = ?");
    if (!$stmt) {
        return [];
    }
    $stmt->bind_param('ii', $enrollmentId, $courseId);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    $map = [];
    foreach ($rows as $r) {
        $map[(string) ((int) ($r['lesson_id'] ?? 0))] = ((int) ($r['is_completed'] ?? 0)) === 1;
    }
    return $map;
}

$slug = trim((string) ($_GET['slug'] ?? 'basic-iot-esp32'));
$course = learning_fetch_course_by_slug($slug) ?: learning_fetch_course_by_slug('basic-iot-esp32');
$courseId = (int) ($course['id'] ?? 0);
$pageCfg = $courseId > 0 ? learning_fetch_course_page_config($courseId, 'classroom') : null;
$quizAssessment = $courseId > 0 ? learning_fetch_course_assessment($courseId, 'quiz') : null;
$quizQuestions = $quizAssessment ? learning_fetch_assessment_questions_with_options((int) $quizAssessment['id']) : [];
$enrollmentId = learning_find_or_create_enrollment_id($courseId);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    $raw = file_get_contents('php://input');
    $payload = json_decode(is_string($raw) ? $raw : '', true);
    if (!is_array($payload)) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'message' => 'Payload tidak valid.']);
        exit;
    }
    $action = (string) ($payload['action'] ?? '');
    if ($courseId <= 0 || $enrollmentId <= 0) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'message' => 'Login dibutuhkan untuk menyimpan progres.']);
        exit;
    }
    $db = learning_db();
    if (!$db) {
        http_response_code(500);
        echo json_encode(['ok' => false, 'message' => 'Database tidak tersedia.']);
        exit;
    }

    if ($action === 'save_lesson_progress') {
        if (!learning_table_exists('lesson_progress')) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'message' => 'Tabel lesson_progress belum tersedia.']);
            exit;
        }
        $lessonId = (int) ($payload['lesson_id'] ?? 0);
        if (!learning_is_lesson_in_course($courseId, $lessonId)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'Lesson tidak valid.']);
            exit;
        }
        $progressPercent = max(0, min(100, (int) ($payload['progress_percent'] ?? 100)));
        $isCompleted = isset($payload['is_completed']) && (bool) $payload['is_completed'] ? 1 : 0;
        $completedAt = $isCompleted === 1 ? date('Y-m-d H:i:s') : null;
        $stmt = $db->prepare("INSERT INTO lesson_progress (enrollment_id, lesson_id, progress_percent, is_completed, completed_at, created_at, updated_at)
                              VALUES (?, ?, ?, ?, ?, NOW(), NOW())
                              ON DUPLICATE KEY UPDATE
                                progress_percent = VALUES(progress_percent),
                                is_completed = VALUES(is_completed),
                                completed_at = VALUES(completed_at),
                                updated_at = NOW()");
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'message' => 'Gagal menyiapkan simpan progres.']);
            exit;
        }
        $stmt->bind_param('iiiis', $enrollmentId, $lessonId, $progressPercent, $isCompleted, $completedAt);
        $ok = $stmt->execute();
        $stmt->close();
        echo json_encode(['ok' => $ok]);
        exit;
    }

    if ($action === 'save_quiz_attempt') {
        if (!learning_table_exists('quiz_attempts') || !learning_table_exists('quizzes')) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'message' => 'Tabel quiz_attempts/quizzes belum tersedia.']);
            exit;
        }
        $lessonId = (int) ($payload['lesson_id'] ?? 0);
        $score = max(0, min(100, (int) ($payload['score'] ?? 0)));
        if (!learning_is_lesson_in_course($courseId, $lessonId)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'message' => 'Lesson quiz tidak valid.']);
            exit;
        }
        $passScore = max(0, min(100, (int) ($quizAssessment['pass_score'] ?? 70)));
        $findQuiz = $db->prepare("SELECT id FROM quizzes WHERE lesson_id = ? LIMIT 1");
        $quizId = 0;
        if ($findQuiz) {
            $findQuiz->bind_param('i', $lessonId);
            $findQuiz->execute();
            $row = $findQuiz->get_result()->fetch_assoc();
            $findQuiz->close();
            if ($row) {
                $quizId = (int) ($row['id'] ?? 0);
            }
        }
        if ($quizId <= 0) {
            $insertQuiz = $db->prepare("INSERT INTO quizzes (lesson_id, pass_score, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
            if (!$insertQuiz) {
                http_response_code(500);
                echo json_encode(['ok' => false, 'message' => 'Gagal membuat quiz record.']);
                exit;
            }
            $insertQuiz->bind_param('ii', $lessonId, $passScore);
            $okQuiz = $insertQuiz->execute();
            if ($okQuiz) {
                $quizId = (int) $db->insert_id;
            }
            $insertQuiz->close();
            if (!$okQuiz || $quizId <= 0) {
                http_response_code(500);
                echo json_encode(['ok' => false, 'message' => 'Gagal menyimpan metadata quiz.']);
                exit;
            }
        }
        $isPassed = $score >= $passScore ? 1 : 0;
        $insertAttempt = $db->prepare("INSERT INTO quiz_attempts (quiz_id, enrollment_id, score, is_passed, attempted_at, created_at, updated_at)
                                       VALUES (?, ?, ?, ?, NOW(), NOW(), NOW())");
        if (!$insertAttempt) {
            http_response_code(500);
            echo json_encode(['ok' => false, 'message' => 'Gagal menyiapkan simpan nilai quiz.']);
            exit;
        }
        $insertAttempt->bind_param('iiii', $quizId, $enrollmentId, $score, $isPassed);
        $ok = $insertAttempt->execute();
        $insertAttempt->close();

        if ($ok && learning_table_exists('lesson_progress')) {
            $progressPercent = 100;
            $isCompleted = 1;
            $completedAt = date('Y-m-d H:i:s');
            $saveProgress = $db->prepare("INSERT INTO lesson_progress (enrollment_id, lesson_id, progress_percent, is_completed, completed_at, created_at, updated_at)
                                          VALUES (?, ?, ?, ?, ?, NOW(), NOW())
                                          ON DUPLICATE KEY UPDATE
                                            progress_percent = VALUES(progress_percent),
                                            is_completed = VALUES(is_completed),
                                            completed_at = VALUES(completed_at),
                                            updated_at = NOW()");
            if ($saveProgress) {
                $saveProgress->bind_param('iiiis', $enrollmentId, $lessonId, $progressPercent, $isCompleted, $completedAt);
                $saveProgress->execute();
                $saveProgress->close();
            }
        }

        echo json_encode(['ok' => $ok, 'pass_score' => $passScore, 'is_passed' => $isPassed === 1]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Action tidak dikenal.']);
    exit;
}

$initialProgressMap = learning_fetch_db_progress_map($courseId, $enrollmentId);
$modules = $course ? learning_fetch_course_modules_with_lessons((int) $course['id']) : [];
$requestedLessonId = (int) ($_GET['lesson'] ?? 0);
$activeLesson = null;
$activeModuleId = 0;
$lessonList = [];

foreach ($modules as $mIndex => $m) {
    $moduleId = (int) ($m['id'] ?? 0);
    foreach (($m['lessons'] ?? []) as $lIndex => $lesson) {
        $item = [
            'id' => (int) ($lesson['id'] ?? 0),
            'module_id' => $moduleId,
            'module_order' => (int) ($m['module_order'] ?? ($mIndex + 1)),
            'module_title' => (string) ($m['title'] ?? ''),
            'lesson_order' => (int) ($lesson['lesson_order'] ?? ($lIndex + 1)),
            'title' => (string) ($lesson['title'] ?? ''),
            'lesson_type' => (string) ($lesson['lesson_type'] ?? 'video'),
            'content_url' => (string) ($lesson['content_url'] ?? ''),
            'content_body' => (string) ($lesson['content_body'] ?? ''),
            'duration_seconds' => (int) ($lesson['duration_seconds'] ?? 0),
            'duration_label' => learning_format_duration((int) ($lesson['duration_seconds'] ?? 0)),
            'is_preview' => ((int) ($lesson['is_preview'] ?? 0)) === 1,
        ];
        $lessonList[] = $item;
        if ($requestedLessonId > 0 && $requestedLessonId === $item['id']) {
            $activeLesson = $item;
            $activeModuleId = $moduleId;
        }
    }
}

if ($activeLesson === null && !empty($lessonList)) {
    $activeLesson = $lessonList[0];
    $activeModuleId = (int) ($activeLesson['module_id'] ?? 0);
}

$courseSlug = (string) ($course['slug'] ?? 'basic-iot-esp32');
$courseImage = (string) ($course['featured_image'] ?? 'https://images.unsplash.com/photo-1553406830-ef2513450d76?q=80&w=1600&auto=format&fit=crop');
$initialType = (string) ($activeLesson['lesson_type'] ?? 'video');
$initialVideoUrl = (string) ($activeLesson['content_url'] ?? '');
$onlyOfficeServer = rtrim((string) inosakti_env_value('ONLYOFFICE_SERVER_URL', ''), '/');
$lessonJson = json_encode($lessonList, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
if (!is_string($lessonJson)) {
    $lessonJson = '[]';
}
$quizJson = json_encode($quizQuestions, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
if (!is_string($quizJson)) {
    $quizJson = '[]';
}

$pageTitle = ((string) ($pageCfg['title'] ?? 'Kelas Berjalan')) . ' - ' . (string) ($course['title'] ?? 'Basic IoT ESP32');
$pageDesc = (string) ($pageCfg['description'] ?? 'Interface belajar online untuk kelas Basic IoT dengan ESP32.');
$extraHead = <<<'HTML'
<style type="text/tailwindcss">
@layer utilities {
  .lesson-item { @apply flex items-start gap-2 px-3 py-2 rounded-lg text-sm border border-transparent transition; }
  .lesson-item-active { @apply bg-blue-50 border-blue-200 text-blue-900; }
  .module-toggle { @apply w-full text-left flex items-center justify-between px-2 py-1.5 rounded-lg hover:bg-slate-50 transition; }
}
</style>
HTML;
include __DIR__.'/../../../inc/header.php';
?>
<main class="pt-20">
  <div class="max-w-screen-2xl mx-auto px-4 py-6">
    <div class="grid lg:grid-cols-[320px_1fr] gap-6 min-h-[75vh]">
      <aside class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
        <div class="p-5 border-b border-slate-100">
          <h2 class="font-bold"><?php echo htmlspecialchars((string) ($pageCfg['subtitle'] ?? 'Course Content')); ?></h2>
          <p class="text-xs text-slate-500 mt-1"><?php echo htmlspecialchars((string) ($course['title'] ?? '')); ?></p>
          <p class="text-xs text-slate-400 mt-1"><span id="progressLabel">0%</span> selesai</p>
        </div>
        <div class="p-4 space-y-4 max-h-[70vh] overflow-y-auto">
          <?php foreach ($modules as $m): ?>
            <?php $moduleId = (int) ($m['id'] ?? 0); ?>
            <div class="module-wrap" data-module-id="<?php echo $moduleId; ?>">
              <button type="button" class="module-toggle mb-2" data-module-toggle="<?php echo $moduleId; ?>">
                <h3 class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Modul <?php echo (int) $m['module_order']; ?></h3>
                <span class="material-symbols-outlined text-base text-slate-500" data-module-icon="<?php echo $moduleId; ?>">
                  <?php echo $moduleId === $activeModuleId ? 'expand_less' : 'expand_more'; ?>
                </span>
              </button>
              <div class="space-y-1" data-module-lessons="<?php echo $moduleId; ?>" <?php echo $moduleId === $activeModuleId ? '' : 'hidden'; ?>>
                <?php foreach (($m['lessons'] ?? []) as $lesson): ?>
                  <?php
                  $lessonId = (int) ($lesson['id'] ?? 0);
                  $isActive = $activeLesson && $lessonId === (int) ($activeLesson['id'] ?? 0);
                  $type = (string) ($lesson['lesson_type'] ?? 'video');
                  $typeIcon = $type === 'quiz' ? 'quiz' : ($type === 'test' ? 'fact_check' : ($type === 'article' ? 'description' : ($type === 'file' ? 'folder_open' : ($type === 'presentation' ? 'slideshow' : 'play_circle'))));
                  ?>
                  <button
                    type="button"
                    class="lesson-item w-full text-left <?php echo $isActive ? 'lesson-item-active' : 'hover:bg-slate-50'; ?>"
                    data-lesson-id="<?php echo $lessonId; ?>"
                  >
                    <span class="material-symbols-outlined text-base text-slate-400" data-lesson-icon="<?php echo $lessonId; ?>"><?php echo $typeIcon; ?></span>
                    <span class="leading-5 flex-1"><?php echo htmlspecialchars((string) $lesson['title']); ?></span>
                    <span class="material-symbols-outlined text-emerald-500 hidden" data-done-icon="<?php echo $lessonId; ?>">check_circle</span>
                  </button>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
	      </aside>
	      <section class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
	        <div class="p-6">
	          <div class="flex flex-wrap items-center gap-2 mb-3">
	            <span id="badgeType" class="px-2 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold uppercase">
              <?php echo htmlspecialchars((string) ($activeLesson['lesson_type'] ?? 'video')); ?>
            </span>
            <span id="badgeDuration" class="px-2 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
              <?php echo htmlspecialchars((string) ($activeLesson['duration_label'] ?? '-')); ?>
            </span>
            <span id="badgePreview" class="px-2 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold <?php echo (!empty($activeLesson['is_preview'])) ? '' : 'hidden'; ?>">
              Preview
            </span>
          </div>

	          <div class="flex items-start justify-between gap-4">
	            <div class="flex-1">
	              <h1 id="lessonTitle" class="text-2xl font-bold"><?php echo htmlspecialchars((string) ($activeLesson['title'] ?? 'Mulai modul pertama')); ?></h1>
	              <p id="lessonModule" class="text-sm text-slate-500 mt-1">
	                Modul <?php echo (int) ($activeLesson['module_order'] ?? 0); ?> - <?php echo htmlspecialchars((string) ($activeLesson['module_title'] ?? '')); ?>
	              </p>
	              <div id="lessonBody" class="text-slate-600 text-sm mt-3 leading-6 prose prose-sm max-w-none"><?php
                  $initialLessonType = strtolower((string) ($activeLesson['lesson_type'] ?? ''));
                  $initialLessonBody = (string) ($activeLesson['content_body'] ?? 'Pilih lesson dari sidebar untuk mulai belajar.');
                  if ($initialLessonType === 'article') {
                      echo $initialLessonBody;
                  } else {
                      echo htmlspecialchars($initialLessonBody);
                  }
                ?></div>
                <div id="videoPanel" class="mt-4 aspect-video bg-slate-900 rounded-xl overflow-hidden relative <?php echo $initialType === 'video' ? '' : 'hidden'; ?>">
                  <img id="videoPoster" class="absolute inset-0 w-full h-full object-cover opacity-40" src="<?php echo htmlspecialchars($courseImage); ?>" alt="Video"/>
                  <div id="videoNativeWrap" class="hidden w-full">
                    <video id="videoNative" class="block mx-auto w-auto max-w-full h-auto max-h-[70vh] bg-transparent" controls playsinline preload="metadata"></video>
                  </div>
                  <div id="videoEmbedWrap" class="absolute inset-0 hidden">
                    <iframe
                      id="videoFrame"
                      class="w-full h-full"
                      src=""
                      title="Course Video"
                      loading="lazy"
                      allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                      allowfullscreen
                    ></iframe>
                  </div>
                  <div id="videoPlayOverlay" class="absolute inset-0 flex items-center justify-center <?php echo $initialVideoUrl !== '' ? '' : 'hidden'; ?>">
                    <button id="videoPlayBtn" type="button" class="w-20 h-20 rounded-full bg-accent text-white flex items-center justify-center">
                      <span class="material-symbols-outlined text-5xl">play_arrow</span>
                    </button>
                  </div>
                </div>
                <div id="presentationPanel" class="hidden mt-4 rounded-xl border border-slate-200 overflow-hidden">
                  <div id="pptOnlyOfficeHost" class="hidden min-h-[560px] bg-slate-100"></div>
                  <div id="pptEmbedWrap" class="hidden aspect-video bg-slate-100">
                    <iframe
                      id="pptFrame"
                      class="w-full h-full"
                      src=""
                      title="PowerPoint Viewer"
                      loading="lazy"
                    ></iframe>
                  </div>
                  <div id="pptFallback" class="hidden p-4 bg-slate-50 text-sm text-slate-600">
                    <p id="pptFallbackText">Preview PowerPoint belum tersedia untuk URL ini.</p>
                    <a id="pptOpenLink" class="mt-2 inline-flex items-center px-3 py-1.5 rounded-lg bg-blue-800 text-white text-xs font-semibold hover:bg-blue-900" href="#" target="_blank" rel="noopener">Buka File PowerPoint</a>
                  </div>
                </div>
                <div id="quizPanel" class="hidden mt-4 rounded-xl border border-slate-200 p-4">
                  <div class="text-sm font-semibold mb-1"><?php echo htmlspecialchars((string) ($quizAssessment['title'] ?? 'Quiz')); ?></div>
                  <p id="quizInstruction" class="text-xs text-slate-500 mb-3"><?php echo htmlspecialchars((string) ($quizAssessment['instruction_text'] ?? '')); ?></p>
                  <div class="text-xs font-bold uppercase tracking-wider text-accent mb-2">Question <span id="quizIndex">1</span> of <span id="quizTotal"><?php echo count($quizQuestions); ?></span></div>
                  <h2 id="quizQuestionText" class="text-base font-semibold mb-3"></h2>
                  <div id="quizOptions" class="space-y-2"></div>
                  <div id="quizResult" class="hidden mt-3 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-800 px-3 py-3 text-sm">
                    <div class="flex items-center gap-4">
                      <div class="relative w-24 h-24">
                        <svg class="w-24 h-24 -rotate-90" viewBox="0 0 120 120" aria-hidden="true">
                          <circle cx="60" cy="60" r="48" stroke="#bbf7d0" stroke-width="10" fill="none"></circle>
                          <circle id="quizGaugeBar" cx="60" cy="60" r="48" stroke="#16a34a" stroke-width="10" fill="none" stroke-linecap="round" stroke-dasharray="301.59" stroke-dashoffset="301.59"></circle>
                        </svg>
                        <div id="quizGaugeValue" class="absolute inset-0 flex items-center justify-center text-lg font-bold text-emerald-700">0</div>
                      </div>
                      <div class="flex-1">
                        <div class="font-semibold">Hasil Quiz</div>
                        <div id="quizResultText" class="text-emerald-800">Skor: 0 (0/0 benar)</div>
                        <button id="quizRetryBtn" type="button" class="mt-2 px-3 py-1.5 rounded-lg border border-emerald-300 bg-white text-emerald-700 text-xs font-semibold">Ulangi Quiz</button>
                      </div>
                    </div>
                  </div>
                  <div class="mt-4 flex justify-between">
                    <button id="quizPrevBtn" type="button" class="px-3 py-1.5 rounded-lg border border-slate-200 text-sm font-semibold">Previous</button>
                    <button id="quizNextBtn" type="button" class="px-3 py-1.5 rounded-lg bg-blue-800 text-white text-sm font-semibold">Next</button>
                  </div>
                </div>
	            </div>
	          </div>

          <div class="mt-6 flex items-center justify-between gap-3 border-t border-slate-100 pt-4">
            <button id="prevLessonBtn" type="button" class="px-4 py-2 rounded-lg border border-slate-200 font-semibold text-sm inline-flex items-center gap-1">
              <span class="material-symbols-outlined text-base">arrow_back</span>
              Previous
            </button>
            <div class="flex items-center gap-2">
              <button id="markDoneBtn" type="button" class="px-4 py-2 rounded-lg bg-emerald-600 text-white font-semibold text-sm inline-flex items-center gap-1">
                <span class="material-symbols-outlined text-base">task_alt</span>
                Mark Complete
              </button>
              <button id="nextLessonBtn" type="button" class="px-4 py-2 rounded-lg bg-slate-900 text-white font-semibold text-sm inline-flex items-center gap-1">
                Next
                <span class="material-symbols-outlined text-base">arrow_forward</span>
              </button>
            </div>
          </div>
        </div>
      </section>
    </div>
  </div>
</main>
<?php if ($onlyOfficeServer !== ''): ?>
<script src="<?= htmlspecialchars($onlyOfficeServer . '/web-apps/apps/api/documents/api.js', ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endif; ?>
<script>
(() => {
  const lessons = <?php echo $lessonJson; ?>;
  const quizQuestions = <?php echo $quizJson; ?>;
  const initialDbProgress = <?php echo json_encode($initialProgressMap, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?> || {};
  if (!Array.isArray(lessons) || lessons.length === 0) return;

  const slug = <?php echo json_encode($courseSlug, JSON_UNESCAPED_SLASHES); ?>;
  const apiUrl = window.location.pathname + '?slug=' + encodeURIComponent(slug);
  const params = new URLSearchParams(window.location.search);
  const initialLessonId = Number(params.get('lesson') || <?php echo (int) ($activeLesson['id'] ?? 0); ?>);
  let currentLessonId = initialLessonId > 0 ? initialLessonId : Number(lessons[0].id);
  const progressState = {};
  Object.keys(initialDbProgress).forEach((k) => {
    progressState[String(k)] = !!initialDbProgress[k];
  });

  const titleEl = document.getElementById('lessonTitle');
  const moduleEl = document.getElementById('lessonModule');
  const bodyEl = document.getElementById('lessonBody');
  const badgeType = document.getElementById('badgeType');
  const badgeDuration = document.getElementById('badgeDuration');
  const badgePreview = document.getElementById('badgePreview');
  const progressLabel = document.getElementById('progressLabel');
  const prevBtn = document.getElementById('prevLessonBtn');
  const nextBtn = document.getElementById('nextLessonBtn');
  const doneBtn = document.getElementById('markDoneBtn');
  const quizPanel = document.getElementById('quizPanel');
  const quizIndex = document.getElementById('quizIndex');
  const quizTotal = document.getElementById('quizTotal');
  const quizQuestionText = document.getElementById('quizQuestionText');
  const quizOptions = document.getElementById('quizOptions');
  const quizResult = document.getElementById('quizResult');
  const quizResultText = document.getElementById('quizResultText');
  const quizRetryBtn = document.getElementById('quizRetryBtn');
  const quizGaugeBar = document.getElementById('quizGaugeBar');
  const quizGaugeValue = document.getElementById('quizGaugeValue');
  const quizPrevBtn = document.getElementById('quizPrevBtn');
  const quizNextBtn = document.getElementById('quizNextBtn');
  const videoPanel = document.getElementById('videoPanel');
	  const videoPoster = document.getElementById('videoPoster');
	  const videoNativeWrap = document.getElementById('videoNativeWrap');
	  const videoNative = document.getElementById('videoNative');
	  const videoEmbedWrap = document.getElementById('videoEmbedWrap');
	  const videoFrame = document.getElementById('videoFrame');
	  const videoPlayOverlay = document.getElementById('videoPlayOverlay');
	  const videoPlayBtn = document.getElementById('videoPlayBtn');
  const presentationPanel = document.getElementById('presentationPanel');
	  const pptEmbedWrap = document.getElementById('pptEmbedWrap');
  const pptOnlyOfficeHost = document.getElementById('pptOnlyOfficeHost');
  const pptFrame = document.getElementById('pptFrame');
  const pptFallback = document.getElementById('pptFallback');
  const pptFallbackText = document.getElementById('pptFallbackText');
  const pptOpenLink = document.getElementById('pptOpenLink');
  const onlyOfficeServer = <?php echo json_encode($onlyOfficeServer, JSON_UNESCAPED_SLASHES); ?>;
  let onlyOfficeEditor = null;
  let quizCursor = 0;
  const quizAnswers = {};
  const gaugeCirc = 2 * Math.PI * 48;

  function escapeHtml(v) {
    return String(v || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
  }

  function quizQuestionKey(q) {
    return String(q && q.id ? q.id : quizCursor);
  }

  function captureQuizAnswer() {
    if (!Array.isArray(quizQuestions) || quizQuestions.length === 0) return;
    const q = quizQuestions[quizCursor];
    if (!q) return;
    const key = quizQuestionKey(q);
    const inputType = (q.question_type || '').toLowerCase() === 'mcq_multi' ? 'checkbox' : 'radio';
    if (inputType === 'checkbox') {
      const checked = Array.from(quizOptions.querySelectorAll('input[type="checkbox"]:checked')).map((el) => String(el.value));
      quizAnswers[key] = checked;
    } else {
      const checked = quizOptions.querySelector('input[type="radio"]:checked');
      quizAnswers[key] = checked ? [String(checked.value)] : [];
    }
  }

  function applyQuizAnswer() {
    if (!Array.isArray(quizQuestions) || quizQuestions.length === 0) return;
    const q = quizQuestions[quizCursor];
    if (!q) return;
    const key = quizQuestionKey(q);
    const chosen = Array.isArray(quizAnswers[key]) ? quizAnswers[key] : [];
    quizOptions.querySelectorAll('input').forEach((el) => {
      el.checked = chosen.includes(String(el.value));
    });
  }

  function quizScoreSummary() {
    let correctCount = 0;
    const total = Array.isArray(quizQuestions) ? quizQuestions.length : 0;
    quizQuestions.forEach((q) => {
      const key = quizQuestionKey(q);
      const chosen = Array.isArray(quizAnswers[key]) ? quizAnswers[key] : [];
      const options = Array.isArray(q.options) ? q.options : [];
      const correctIds = options.filter((o) => Number(o.is_correct) === 1).map((o) => String(o.id));
      if (correctIds.length === 0) return;
      const chosenSorted = [...chosen].sort();
      const correctSorted = [...correctIds].sort();
      if (chosenSorted.length === correctSorted.length && chosenSorted.every((v, i) => v === correctSorted[i])) {
        correctCount += 1;
      }
    });
    const score = total > 0 ? Math.round((correctCount / total) * 100) : 0;
    return { score, correctCount, total };
  }

  function animateQuizGauge(targetScore) {
    const clamped = Math.max(0, Math.min(100, Number(targetScore) || 0));
    const duration = 800;
    const start = performance.now();
    function frame(now) {
      const p = Math.min(1, (now - start) / duration);
      const eased = 1 - Math.pow(1 - p, 3);
      const value = Math.round(clamped * eased);
      const offset = gaugeCirc * (1 - (value / 100));
      quizGaugeBar.setAttribute('stroke-dashoffset', String(offset));
      quizGaugeValue.textContent = String(value);
      if (p < 1) requestAnimationFrame(frame);
    }
    quizGaugeBar.setAttribute('stroke-dasharray', String(gaugeCirc));
    quizGaugeBar.setAttribute('stroke-dashoffset', String(gaugeCirc));
    quizGaugeValue.textContent = '0';
    requestAnimationFrame(frame);
  }

  function resetQuiz() {
    Object.keys(quizAnswers).forEach((k) => delete quizAnswers[k]);
    quizCursor = 0;
    quizResult.classList.add('hidden');
    quizResultText.textContent = 'Skor: 0 (0/0 benar)';
    quizGaugeBar.setAttribute('stroke-dashoffset', String(gaugeCirc));
    quizGaugeValue.textContent = '0';
    renderQuizQuestion();
  }

  async function postJson(payload) {
    try {
      const res = await fetch(apiUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(payload),
      });
      const data = await res.json();
      return data && typeof data === 'object' ? data : { ok: false };
    } catch (e) {
      return { ok: false };
    }
  }

	  function lessonIndexById(lessonId) {
	    return lessons.findIndex((l) => Number(l.id) === Number(lessonId));
	  }

	  function isDirectVideoUrl(url) {
	    const value = String(url || '').trim().toLowerCase();
	    if (value === '') return false;
	    return /\.(mp4|webm|ogg)(\?.*)?$/.test(value);
	  }

  function isPrivateIpv4(host) {
    return /^10\./.test(host)
      || /^192\.168\./.test(host)
      || /^172\.(1[6-9]|2\d|3[0-1])\./.test(host);
  }

  function isLikelyPublicHttpUrl(rawUrl) {
    try {
      const u = new URL(String(rawUrl || '').trim(), window.location.origin);
      if (!/^https?:$/.test(u.protocol)) return false;
      const host = String(u.hostname || '').toLowerCase();
      if (host === '' || host === 'localhost' || host === '127.0.0.1' || host === '::1') return false;
      if (isPrivateIpv4(host)) return false;
      return true;
    } catch (_) {
      return false;
    }
  }

  function normalizeUrl(rawUrl) {
    try {
      return new URL(String(rawUrl || '').trim(), window.location.origin).toString();
    } catch (_) {
      return String(rawUrl || '').trim();
    }
  }

  function toOfficeViewerUrl(rawUrl) {
    const normalized = normalizeUrl(rawUrl);
    if (!isLikelyPublicHttpUrl(normalized)) return '';
    return 'https://view.officeapps.live.com/op/embed.aspx?src=' + encodeURIComponent(normalized);
  }

  function extractFileType(rawUrl) {
    const normalized = normalizeUrl(rawUrl);
    const clean = normalized.split('#')[0].split('?')[0].toLowerCase();
    const m = clean.match(/\.([a-z0-9]+)$/);
    return m ? m[1] : '';
  }

  function supportsOnlyOfficeSlide(rawUrl) {
    const ext = extractFileType(rawUrl);
    return ext === 'ppt' || ext === 'pptx';
  }

  function destroyOnlyOfficeEditor() {
    if (onlyOfficeEditor && typeof onlyOfficeEditor.destroyEditor === 'function') {
      onlyOfficeEditor.destroyEditor();
    }
    onlyOfficeEditor = null;
    if (pptOnlyOfficeHost) {
      pptOnlyOfficeHost.innerHTML = '';
      pptOnlyOfficeHost.classList.add('hidden');
    }
  }

  function renderOnlyOffice(rawUrl, lessonId) {
    if (!onlyOfficeServer || !window.DocsAPI || !pptOnlyOfficeHost) return false;
    if (!supportsOnlyOfficeSlide(rawUrl)) return false;
    const fileUrl = normalizeUrl(rawUrl);
    const ext = extractFileType(fileUrl) || 'pptx';
    if (fileUrl === '') return false;
    destroyOnlyOfficeEditor();
    pptOnlyOfficeHost.classList.remove('hidden');
    const safeKey = ('lesson_' + String(lessonId || '0') + '_' + btoa(unescape(encodeURIComponent(fileUrl))).replace(/[^a-zA-Z0-9]/g, '')).slice(0, 120);
    onlyOfficeEditor = new window.DocsAPI.DocEditor('pptOnlyOfficeHost', {
      width: '100%',
      height: '560px',
      documentType: 'slide',
      type: 'embedded',
      document: {
        fileType: ext,
        key: safeKey,
        title: 'Presentation.' + ext,
        url: fileUrl,
      },
      editorConfig: {
        mode: 'view',
        lang: 'id',
      },
    });
    return true;
  }

  function setActiveButton(lessonId) {
    document.querySelectorAll('[data-lesson-id]').forEach((btn) => {
      const isActive = Number(btn.dataset.lessonId) === Number(lessonId);
      btn.classList.toggle('lesson-item-active', isActive);
      if (!isActive) btn.classList.add('hover:bg-slate-50');
      if (isActive) btn.scrollIntoView({ block: 'nearest' });
    });
  }

  function openModule(moduleId) {
    document.querySelectorAll('[data-module-lessons]').forEach((box) => {
      const isTarget = Number(box.dataset.moduleLessons) === Number(moduleId);
      box.hidden = !isTarget;
      const icon = document.querySelector('[data-module-icon="' + box.dataset.moduleLessons + '"]');
      if (icon) icon.textContent = isTarget ? 'expand_less' : 'expand_more';
    });
  }

  function updateProgressUI(progress) {
    const completedCount = Object.values(progress).filter(Boolean).length;
    const percent = Math.round((completedCount / lessons.length) * 100);
    progressLabel.textContent = percent + '%';
    lessons.forEach((lesson) => {
      const doneIcon = document.querySelector('[data-done-icon="' + lesson.id + '"]');
      if (doneIcon) {
        doneIcon.classList.toggle('hidden', !progress[String(lesson.id)]);
      }
    });
  }

  function renderQuizQuestion() {
    if (!Array.isArray(quizQuestions) || quizQuestions.length === 0) {
      quizQuestionText.textContent = 'Quiz belum tersedia.';
      quizOptions.innerHTML = '';
      quizPrevBtn.disabled = true;
      quizNextBtn.disabled = true;
      return;
    }
    const q = quizQuestions[quizCursor];
    quizIndex.textContent = String(quizCursor + 1);
    quizTotal.textContent = String(quizQuestions.length);
    quizQuestionText.textContent = q.question_text || 'Question';
    const inputType = (q.question_type || '').toLowerCase() === 'mcq_multi' ? 'checkbox' : 'radio';
    const name = 'quiz_q_' + String(q.id || quizCursor);
    const rows = Array.isArray(q.options) ? q.options : [];
    quizOptions.innerHTML = rows.map((opt) => {
      const id = String(opt.id || Math.random()).replace(/[^a-zA-Z0-9_-]/g, '');
      const text = escapeHtml(opt.option_text || '');
      return '<label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 hover:border-blue-300">'
        + '<input type="' + inputType + '" name="' + name + (inputType === 'checkbox' ? '[]' : '') + '" value="' + id + '" class="text-blue-700">'
        + '<span>' + text + '</span>'
        + '</label>';
    }).join('');
    applyQuizAnswer();
    quizPrevBtn.disabled = quizCursor <= 0;
    quizNextBtn.textContent = quizCursor >= quizQuestions.length - 1 ? 'Finish' : 'Next';
  }

  function setLesson(lessonId, pushState = true) {
    const idx = lessonIndexById(lessonId);
    if (idx < 0) return;
    const lesson = lessons[idx];

    currentLessonId = Number(lesson.id);

    titleEl.textContent = lesson.title || 'Lesson';
    moduleEl.textContent = 'Modul ' + lesson.module_order + ' - ' + lesson.module_title;
    const lessonBody = lesson.content_body || 'Belum ada deskripsi lesson.';
    if ((lesson.lesson_type || '').toLowerCase() === 'article') {
      bodyEl.innerHTML = lessonBody;
    } else {
      bodyEl.textContent = lessonBody;
    }
    badgeType.textContent = (lesson.lesson_type || 'video').toUpperCase();
    badgeDuration.textContent = lesson.duration_label || '-';
    badgePreview.classList.toggle('hidden', !lesson.is_preview);

	    const type = (lesson.lesson_type || '').toLowerCase();
	    const isVideo = type === 'video';
	    const isQuiz = type === 'quiz' || type === 'test';
    const isPresentation = type === 'presentation' || type === 'powerpoint';
	    const videoUrl = (lesson.content_url || '').trim();
    const presentationUrl = (lesson.content_url || '').trim();
	    videoPanel.classList.toggle('hidden', !isVideo);
    presentationPanel.classList.toggle('hidden', !isPresentation);
	    bodyEl.classList.toggle('hidden', isQuiz);
	    quizPanel.classList.toggle('hidden', !isQuiz);
    if (isQuiz) {
      resetQuiz();
    }
	    if (isVideo) {
	      videoPoster.src = <?php echo json_encode($courseImage, JSON_UNESCAPED_SLASHES); ?>;
	      videoPoster.classList.add('hidden');
	      if (videoUrl !== '') {
	        const directVideo = isDirectVideoUrl(videoUrl);
	        if (directVideo) {
	          videoPanel.classList.remove('aspect-video');
	          videoPanel.classList.add('aspect-auto');
	          videoPanel.classList.remove('bg-slate-900');
	          videoPanel.classList.add('bg-transparent');
	          if (videoNative) {
	            videoNative.src = videoUrl;
	            videoNative.load();
	          }
	          videoNativeWrap?.classList.remove('hidden');
	          videoEmbedWrap.classList.add('hidden');
	          videoFrame.src = '';
		    } else {
	          videoPanel.classList.remove('aspect-auto');
	          videoPanel.classList.add('aspect-video');
	          videoPanel.classList.remove('bg-transparent');
	          videoPanel.classList.add('bg-slate-900');
	          videoFrame.src = videoUrl;
	          videoEmbedWrap.classList.remove('hidden');
	          videoNativeWrap?.classList.add('hidden');
	          if (videoNative) videoNative.src = '';
		    }

    if (isPresentation) {
      const normalizedPptUrl = normalizeUrl(presentationUrl);
      const renderedOnlyOffice = renderOnlyOffice(presentationUrl, lesson.id);
      const embedUrl = renderedOnlyOffice ? '' : toOfficeViewerUrl(presentationUrl);
      if (renderedOnlyOffice) {
        pptFrame.src = '';
        pptEmbedWrap.classList.add('hidden');
        pptFallback.classList.add('hidden');
      } else if (embedUrl !== '') {
        destroyOnlyOfficeEditor();
        pptFrame.src = embedUrl;
        pptEmbedWrap.classList.remove('hidden');
        pptFallback.classList.add('hidden');
      } else {
        destroyOnlyOfficeEditor();
        pptFrame.src = '';
        pptEmbedWrap.classList.add('hidden');
        pptFallback.classList.remove('hidden');
        pptOpenLink.href = normalizedPptUrl || '#';
        pptOpenLink.classList.toggle('hidden', normalizedPptUrl === '');
        pptFallbackText.textContent = normalizedPptUrl === ''
          ? 'Belum ada file PowerPoint pada modul ini.'
          : 'Preview interaktif hanya tersedia untuk URL publik (bukan localhost/private).';
      }
    } else {
      destroyOnlyOfficeEditor();
      pptFrame.src = '';
      pptEmbedWrap.classList.add('hidden');
      pptFallback.classList.add('hidden');
      pptOpenLink.href = '#';
    }
	        videoPlayOverlay.classList.add('hidden');
	      } else {
	        videoPanel.classList.remove('aspect-auto');
	        videoPanel.classList.add('aspect-video');
	        videoPanel.classList.remove('bg-transparent');
	        videoPanel.classList.add('bg-slate-900');
	        videoPoster.classList.remove('hidden');
	        videoFrame.src = '';
	        videoEmbedWrap.classList.add('hidden');
	        videoNativeWrap?.classList.add('hidden');
	        if (videoNative) videoNative.src = '';
	        videoPlayOverlay.classList.remove('hidden');
	      }
	    } else {
	      videoPoster.classList.remove('hidden');
	      videoPanel.classList.remove('aspect-auto');
	      videoPanel.classList.add('aspect-video');
	      videoPanel.classList.remove('bg-transparent');
	      videoPanel.classList.add('bg-slate-900');
	      videoFrame.src = '';
	      videoEmbedWrap.classList.add('hidden');
	      videoNativeWrap?.classList.add('hidden');
	      if (videoNative) videoNative.src = '';
	      videoPlayOverlay.classList.add('hidden');
	    }

    setActiveButton(lesson.id);
    openModule(lesson.module_id);

    prevBtn.disabled = idx <= 0;
    nextBtn.disabled = idx >= lessons.length - 1;
    prevBtn.classList.toggle('opacity-40', prevBtn.disabled);
    nextBtn.classList.toggle('opacity-40', nextBtn.disabled);

    if (pushState) {
      const nextParams = new URLSearchParams(window.location.search);
      nextParams.set('slug', slug);
      nextParams.set('lesson', String(lesson.id));
      const nextUrl = window.location.pathname + '?' + nextParams.toString();
      window.history.replaceState({}, '', nextUrl);
    }
  }

  document.querySelectorAll('[data-lesson-id]').forEach((btn) => {
    btn.addEventListener('click', () => setLesson(Number(btn.dataset.lessonId)));
  });

  document.querySelectorAll('[data-module-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const moduleId = Number(btn.dataset.moduleToggle);
      const box = document.querySelector('[data-module-lessons="' + moduleId + '"]');
      const icon = document.querySelector('[data-module-icon="' + moduleId + '"]');
      if (!box) return;
      const willOpen = box.hidden;
      box.hidden = !willOpen;
      if (icon) icon.textContent = willOpen ? 'expand_less' : 'expand_more';
    });
  });

  prevBtn.addEventListener('click', () => {
    const idx = lessonIndexById(currentLessonId);
    if (idx > 0) setLesson(Number(lessons[idx - 1].id));
  });

  nextBtn.addEventListener('click', () => {
    const idx = lessonIndexById(currentLessonId);
    if (idx >= 0 && idx < lessons.length - 1) setLesson(Number(lessons[idx + 1].id));
  });

  doneBtn.addEventListener('click', () => {
    progressState[String(currentLessonId)] = true;
    updateProgressUI(progressState);
    postJson({
      action: 'save_lesson_progress',
      lesson_id: currentLessonId,
      progress_percent: 100,
      is_completed: true,
    });
    doneBtn.textContent = 'Completed';
  });

	  if (videoPlayBtn) {
	    videoPlayBtn.addEventListener('click', () => {
	      const idx = lessonIndexById(currentLessonId);
	      if (idx < 0) return;
	      const lesson = lessons[idx];
	      const videoUrl = (lesson.content_url || '').trim();
	      if (videoUrl === '') return;
	      if (isDirectVideoUrl(videoUrl)) {
	        videoPanel.classList.remove('aspect-video');
	        videoPanel.classList.add('aspect-auto');
	        videoPanel.classList.remove('bg-slate-900');
	        videoPanel.classList.add('bg-transparent');
	        if (videoNative) {
	          videoNative.src = videoUrl;
	          videoNative.load();
	          videoNative.play().catch(() => {});
	        }
	        videoNativeWrap?.classList.remove('hidden');
	        videoFrame.src = '';
	        videoEmbedWrap.classList.add('hidden');
	      } else {
	        videoPanel.classList.remove('aspect-auto');
	        videoPanel.classList.add('aspect-video');
	        videoPanel.classList.remove('bg-transparent');
	        videoPanel.classList.add('bg-slate-900');
	        videoFrame.src = videoUrl;
	        videoEmbedWrap.classList.remove('hidden');
	        videoNativeWrap?.classList.add('hidden');
	        if (videoNative) videoNative.src = '';
	      }
	      videoPlayOverlay.classList.add('hidden');
	    });
	  }

  if (quizPrevBtn) {
    quizPrevBtn.addEventListener('click', () => {
      captureQuizAnswer();
      if (quizCursor > 0) {
        quizCursor -= 1;
        renderQuizQuestion();
      }
    });
  }

  if (quizNextBtn) {
    quizNextBtn.addEventListener('click', () => {
      if (!Array.isArray(quizQuestions) || quizQuestions.length === 0) return;
      captureQuizAnswer();
      if (quizCursor < quizQuestions.length - 1) {
        quizCursor += 1;
        renderQuizQuestion();
      } else {
        const summary = quizScoreSummary();
        quizResultText.textContent = 'Skor: ' + summary.score + ' (' + summary.correctCount + '/' + summary.total + ' benar)';
        quizResult.classList.remove('hidden');
        animateQuizGauge(summary.score);
        postJson({
          action: 'save_quiz_attempt',
          lesson_id: currentLessonId,
          score: summary.score,
          correct_count: summary.correctCount,
          total_questions: summary.total,
        }).then((res) => {
          if (res && res.ok) {
            progressState[String(currentLessonId)] = true;
            updateProgressUI(progressState);
          }
        });
        doneBtn.click();
      }
    });
  }

  if (quizRetryBtn) {
    quizRetryBtn.addEventListener('click', () => {
      resetQuiz();
    });
  }

  updateProgressUI(progressState);
  setLesson(currentLessonId, false);
})();
</script>
<?php include __DIR__.'/../../../inc/footer.php'; ?>
