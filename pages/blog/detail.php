<?php
declare(strict_types=1);
require_once __DIR__ . '/../../inc/config.php';

function blog_db_connect(): ?mysqli
{
    global $dbConfig;
    try {
        $db = @new mysqli(
            (string) ($dbConfig['host'] ?? 'localhost'),
            (string) ($dbConfig['user'] ?? ''),
            (string) ($dbConfig['pass'] ?? ''),
            (string) ($dbConfig['name'] ?? ''),
            (int) ($dbConfig['port'] ?? 3306)
        );
    } catch (Throwable $e) {
        return null;
    }
    if ($db->connect_errno) {
        return null;
    }
    $db->set_charset('utf8mb4');
    return $db;
}

function blog_table_exists(mysqli $db, string $table): bool
{
    $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('s', $table);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return ((int) ($row['cnt'] ?? 0)) > 0;
}

function blog_column_exists(mysqli $db, string $table, string $column): bool
{
    $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('ss', $table, $column);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return ((int) ($row['cnt'] ?? 0)) > 0;
}

function blog_format_date(?string $date): string
{
    if (!$date) {
        return '-';
    }
    $ts = strtotime($date);
    if (!$ts) {
        return $date;
    }
    $months = [
        1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
        'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des',
    ];
    return date('d', $ts) . ' ' . ($months[(int) date('n', $ts)] ?? date('M', $ts)) . ' ' . date('Y', $ts);
}

function blog_resolve_image_url(string $path, string $basePath): string
{
    $path = trim($path);
    if ($path === '') {
        return '';
    }
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
        return $path;
    }
    return rtrim($basePath, '/') . '/' . ltrim($path, '/');
}

function blog_prepare_content_html(string $html, string $basePath): string
{
    if ($html === '') {
        return '';
    }
    return preg_replace_callback('/(<img[^>]*\ssrc\s*=\s*["\'])([^"\']+)(["\'])/i', static function (array $m) use ($basePath) {
        $src = (string) ($m[2] ?? '');
        if ($src === '' || str_starts_with($src, 'http://') || str_starts_with($src, 'https://') || str_starts_with($src, 'data:') || str_starts_with($src, '/')) {
            return $m[0];
        }
        return $m[1] . rtrim($basePath, '/') . '/' . ltrim($src, '/') . $m[3];
    }, $html) ?? $html;
}

$slugInput = strtolower((string) ($_GET['slug'] ?? ''));
$slug = preg_replace('/[^a-z0-9\-]/', '', $slugInput);

$db = blog_db_connect();
$article = null;
$related = [];
$errorMessage = null;

if (!$db) {
    $errorMessage = 'Database tidak bisa diakses saat ini.';
} elseif (!$slug) {
    $errorMessage = 'Artikel tidak ditemukan.';
} elseif (!blog_table_exists($db, 'posts') || !blog_table_exists($db, 'post_translations')) {
    $errorMessage = 'Tabel blog belum tersedia.';
} else {
    $hasUsersTable = blog_table_exists($db, 'users');
    $hasAuthorDisplayName = blog_column_exists($db, 'posts', 'author_display_name');
    $authorSelect = $hasAuthorDisplayName
        ? "COALESCE(NULLIF(p.author_display_name, ''), u.name, 'InoSakti Team') AS author_name,"
        : "COALESCE(u.name, 'InoSakti Team') AS author_name,";
    $authorJoin = $hasUsersTable ? "LEFT JOIN users u ON u.id = p.created_by" : "LEFT JOIN (SELECT NULL AS id, NULL AS name) u ON 1=0";
    $sql = "SELECT
              p.id,
              pt.slug,
              pt.title,
              pt.excerpt,
              pt.content,
              p.featured_image,
              p.published_at,
              {$authorSelect}
              COALESCE(pc.slug, 'uncategorized') AS category_slug,
              COALESCE(pc.name, 'Uncategorized') AS category_name
            FROM posts p
            JOIN post_translations pt ON pt.post_id = p.id
            {$authorJoin}
            LEFT JOIN post_categories pc ON pc.id = p.category_id
            WHERE p.deleted_at IS NULL
              AND p.status = 'published'
              AND pt.slug = ?
            LIMIT 1";
    $stmt = $db->prepare($sql);
    if ($stmt) {
        $stmt->bind_param('s', $slug);
        $stmt->execute();
        $article = $stmt->get_result()->fetch_assoc() ?: null;
        $stmt->close();
    }

    if (!$article) {
        $errorMessage = 'Artikel tidak ditemukan.';
    } else {
        $relatedSql = "SELECT
                         pt.slug,
                         pt.title,
                         p.featured_image,
                         COALESCE(pc.name, 'Uncategorized') AS category_name
                       FROM posts p
                       JOIN post_translations pt ON pt.post_id = p.id
                       LEFT JOIN post_categories pc ON pc.id = p.category_id
                       WHERE p.deleted_at IS NULL
                         AND p.status = 'published'
                         AND p.id <> ?
                       ORDER BY COALESCE(p.published_at, p.created_at) DESC
                       LIMIT 3";
        $relStmt = $db->prepare($relatedSql);
        if ($relStmt) {
            $articleId = (int) ($article['id'] ?? 0);
            $relStmt->bind_param('i', $articleId);
            $relStmt->execute();
            $res = $relStmt->get_result();
            $related = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
            $relStmt->close();
        }
    }
}

$pageTitle = $article ? ((string) $article['title'] . ' | InoSakti Blog') : 'Artikel Tidak Ditemukan | InoSakti Blog';
$pageDesc = $article ? (string) ($article['excerpt'] ?? 'Artikel InoSakti') : 'Artikel blog tidak ditemukan.';

$extraHead = <<<'HTML'
<style type="text/tailwindcss">
@layer components {
  .category-tag {
    @apply px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-full text-xs font-bold uppercase tracking-wider;
  }
  #progress-bar {
    @apply fixed top-0 left-0 h-1 bg-primary z-[60] transition-all duration-150;
    width: 0%;
  }
}
</style>
HTML;

$extraScripts = <<<'HTML'
<script>
  window.addEventListener('scroll', () => {
    const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    const scrolled = height > 0 ? (winScroll / height) * 100 : 0;
    const progressBar = document.getElementById('progress-bar');
    if (progressBar) {
      progressBar.style.width = scrolled + '%';
    }
  });
</script>
HTML;

include __DIR__ . '/../../inc/header.php';

$blogBase = $basePath . '/pages/blog/detail';
$blogList = $basePath . '/pages/blog';
?>

<div id="progress-bar"></div>

<main class="pt-16">
  <article class="max-w-screen-xl mx-auto px-6 py-12">
    <?php if ($errorMessage !== null): ?>
      <div class="rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-4 text-sm">
        <?= htmlspecialchars($errorMessage) ?>
      </div>
      <div class="mt-6">
        <a href="<?= htmlspecialchars($blogList) ?>" class="px-6 py-2 border border-slate-200 dark:border-slate-700 rounded-full text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
          Kembali ke Blog
        </a>
      </div>
    <?php else: ?>
      <nav class="flex items-center gap-2 text-sm font-semibold text-slate-500 mb-8 overflow-x-auto whitespace-nowrap">
        <a class="hover:text-primary" href="<?= htmlspecialchars($basePath . '/') ?>">Home</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <a class="hover:text-primary" href="<?= htmlspecialchars($blogList) ?>">Blog</a>
        <span class="material-symbols-outlined text-sm">chevron_right</span>
        <span class="text-primary truncate"><?= htmlspecialchars((string) $article['title']) ?></span>
      </nav>

      <header class="mb-12">
        <h1 class="text-4xl md:text-6xl font-black text-slate-900 dark:text-white leading-tight mb-8">
          <?= htmlspecialchars((string) $article['title']) ?>
        </h1>
        <div class="flex flex-wrap items-center gap-6 py-6 border-y border-slate-100 dark:border-slate-800">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center">
              <span class="material-symbols-outlined text-slate-500">person</span>
            </div>
            <div>
              <p class="text-sm font-bold"><?= htmlspecialchars((string) ($article['author_name'] ?? 'InoSakti Team')) ?></p>
              <p class="text-xs text-slate-500">Author</p>
            </div>
          </div>
          <div class="flex items-center gap-2 text-slate-500 text-sm font-medium">
            <span class="material-symbols-outlined text-lg">calendar_today</span>
            <?= htmlspecialchars(blog_format_date((string) ($article['published_at'] ?? ''))) ?>
          </div>
          <span class="category-tag"><?= htmlspecialchars((string) $article['category_name']) ?></span>
        </div>
      </header>

      <div class="w-full aspect-[21/9] rounded-3xl overflow-hidden mb-16 shadow-2xl">
        <img alt="<?= htmlspecialchars((string) $article['title']) ?>" class="w-full h-full object-cover" src="<?= htmlspecialchars((string) (blog_resolve_image_url((string) ($article['featured_image'] ?? ''), (string) $basePath) ?: 'https://placehold.co/1200x600?text=InoSakti+Blog')) ?>">
      </div>

      <div class="grid lg:grid-cols-12 gap-16">
        <div class="lg:col-span-8">
          <div class="prose prose-lg prose-slate dark:prose-invert max-w-none">
            <?php if (trim((string) ($article['content'] ?? '')) !== ''): ?>
              <?= blog_prepare_content_html((string) $article['content'], (string) $basePath) ?>
            <?php else: ?>
              <p class="lead text-xl text-slate-600 dark:text-slate-400 font-medium">
                <?= htmlspecialchars((string) ($article['excerpt'] ?? '')) ?>
              </p>
            <?php endif; ?>
          </div>

          <div class="mt-16 pt-8 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-6">
            <a href="<?= htmlspecialchars($blogList) ?>" class="px-6 py-2 border border-slate-200 dark:border-slate-700 rounded-full text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
              Kembali ke Blog
            </a>
            <a href="https://wa.me/+6288207085761" class="px-6 py-2 bg-slate-100 dark:bg-slate-800 rounded-full text-sm font-bold flex items-center gap-2">
              <span class="material-symbols-outlined text-lg">chat</span> Konsultasi
            </a>
          </div>
        </div>

        <aside class="lg:col-span-4 space-y-12">
          <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 border border-slate-100 dark:border-slate-700 shadow-sm">
            <h4 class="text-xl font-bold mb-6 flex items-center gap-2">
              <span class="material-symbols-outlined text-primary">psychology</span>
              Butuh Konsultasi?
            </h4>
            <p class="text-slate-600 dark:text-slate-400 text-sm mb-8 leading-relaxed">
              Konsultasikan kebutuhan implementasi AI, IoT, software, dan hardware bersama tim engineer InoSakti.
            </p>
            <a href="https://wa.me/+6288207085761" class="w-full bg-primary text-white py-4 rounded-2xl font-bold inline-flex items-center justify-center gap-2 hover:bg-blue-800 transition-all">
              <span class="material-symbols-outlined">chat</span> Hubungi Kami Sekarang
            </a>
          </div>

          <div>
            <h4 class="text-xl font-bold mb-6">Artikel Terkait</h4>
            <div class="space-y-6">
              <?php foreach ($related as $item): ?>
                <a class="group flex gap-4" href="<?= htmlspecialchars($blogBase . '?slug=' . urlencode((string) $item['slug'])) ?>">
                  <div class="w-24 h-24 rounded-2xl overflow-hidden shrink-0">
                    <img alt="<?= htmlspecialchars((string) $item['title']) ?>" class="w-full h-full object-cover transition-transform group-hover:scale-110" src="<?= htmlspecialchars((string) (blog_resolve_image_url((string) ($item['featured_image'] ?? ''), (string) $basePath) ?: 'https://placehold.co/300x300?text=Blog')) ?>">
                  </div>
                  <div class="flex flex-col justify-center">
                    <span class="text-[10px] font-bold text-primary uppercase mb-1"><?= htmlspecialchars((string) $item['category_name']) ?></span>
                    <h5 class="text-sm font-bold group-hover:text-primary transition-colors line-clamp-2"><?= htmlspecialchars((string) $item['title']) ?></h5>
                  </div>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        </aside>
      </div>
    <?php endif; ?>
  </article>
</main>

<?php include __DIR__ . '/../../inc/footer.php'; ?>
