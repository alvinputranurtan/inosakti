<?php
declare(strict_types=1);

$pageTitle = 'InoSakti | Blog & Articles - Engineering & Technology Insights';
$pageDesc = 'InoSakti Blog features the latest insights on AI, IoT, Software, and Hardware Engineering solutions.';

$extraHead = <<<'HTML'
<style type="text/tailwindcss">
@layer components {
  .blog-card {
    @apply bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1;
  }
  .category-tag {
    @apply px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-full text-xs font-bold uppercase tracking-wider;
  }
}
</style>
HTML;

include __DIR__ . '/../../inc/header.php';

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

$db = blog_db_connect();
$posts = [];
$categories = [];
$errorMessage = null;
$filters = [
    'q' => trim((string) ($_GET['q'] ?? '')),
    'category' => trim((string) ($_GET['category'] ?? 'all')),
];

if (!$db) {
    $errorMessage = 'Database tidak bisa diakses saat ini.';
} elseif (!blog_table_exists($db, 'posts') || !blog_table_exists($db, 'post_translations')) {
    $errorMessage = 'Tabel blog belum tersedia.';
} else {
    $where = [
        "p.deleted_at IS NULL",
        "p.status = 'published'",
    ];
    $types = '';
    $params = [];

    if ($filters['q'] !== '') {
        $where[] = "(pt.title LIKE ? OR pt.excerpt LIKE ? OR pt.content LIKE ?)";
        $kw = '%' . $filters['q'] . '%';
        $params[] = $kw;
        $params[] = $kw;
        $params[] = $kw;
        $types .= 'sss';
    }
    if ($filters['category'] !== '' && $filters['category'] !== 'all') {
        $where[] = "pc.slug = ?";
        $params[] = $filters['category'];
        $types .= 's';
    }
    $whereSql = implode(' AND ', $where);

    $catSql = "SELECT pc.slug, pc.name, COUNT(*) AS total
               FROM posts p
               JOIN post_translations pt ON pt.post_id = p.id
               LEFT JOIN post_categories pc ON pc.id = p.category_id
               WHERE p.deleted_at IS NULL AND p.status = 'published'
               GROUP BY pc.slug, pc.name
               ORDER BY pc.name ASC";
    $catRes = $db->query($catSql);
    if ($catRes) {
        while ($cr = $catRes->fetch_assoc()) {
            $categories[] = [
                'slug' => (string) ($cr['slug'] ?? 'uncategorized'),
                'name' => (string) ($cr['name'] ?? 'Uncategorized'),
                'total' => (int) ($cr['total'] ?? 0),
            ];
        }
    }

    $sql = "SELECT
              p.id,
              pt.slug,
              pt.title,
              pt.excerpt,
              p.featured_image,
              p.published_at,
              COALESCE(pc.slug, 'uncategorized') AS category_slug,
              COALESCE(pc.name, 'Uncategorized') AS category_name
            FROM posts p
            JOIN post_translations pt ON pt.post_id = p.id
            LEFT JOIN post_categories pc ON pc.id = p.category_id
            WHERE {$whereSql}
            ORDER BY COALESCE(p.published_at, p.created_at) DESC
            LIMIT 60";
    $stmt = $db->prepare($sql);
    if ($stmt) {
        if ($types !== '') {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        $posts = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
    } else {
        $errorMessage = 'Gagal mengambil data blog.';
    }
}

$featured = $posts[0] ?? null;
$listPosts = $featured ? array_slice($posts, 1) : [];
$blogBase = $basePath . '/pages/blog/detail';
?>

<main class="pt-16">
  <?php if ($featured): ?>
    <section class="py-12 md:py-20 bg-white dark:bg-slate-900 overflow-hidden">
      <div class="container mx-auto px-6">
        <div class="grid lg:grid-cols-2 gap-12 items-center bg-slate-50 dark:bg-slate-800/50 rounded-3xl overflow-hidden border border-slate-100 dark:border-slate-700">
          <div class="relative h-[300px] lg:h-full overflow-hidden group">
            <a href="<?= htmlspecialchars($blogBase . '?slug=' . urlencode((string) $featured['slug'])) ?>" class="block w-full h-full">
              <img alt="<?= htmlspecialchars((string) $featured['title']) ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" src="<?= htmlspecialchars((string) (blog_resolve_image_url((string) ($featured['featured_image'] ?? ''), (string) $basePath) ?: 'https://placehold.co/1200x800?text=InoSakti+Blog')) ?>">
            </a>
            <div class="absolute top-6 left-6">
              <span class="bg-primary text-white px-4 py-2 rounded-lg font-bold text-xs uppercase tracking-widest shadow-lg">Featured Article</span>
            </div>
          </div>
          <div class="p-8 md:p-12">
            <div class="flex items-center gap-2 mb-6 text-sm font-bold text-slate-500 dark:text-slate-400">
              <span class="material-symbols-outlined text-primary text-lg">calendar_today</span>
              <?= htmlspecialchars(blog_format_date((string) ($featured['published_at'] ?? ''))) ?> |
              <span class="text-primary uppercase tracking-tighter"><?= htmlspecialchars((string) $featured['category_name']) ?></span>
            </div>
            <h1 class="text-3xl md:text-5xl font-black mb-6 leading-tight tracking-tight"><?= htmlspecialchars((string) $featured['title']) ?></h1>
            <p class="text-lg text-slate-600 dark:text-slate-400 mb-10 leading-relaxed"><?= htmlspecialchars((string) ($featured['excerpt'] ?? 'Artikel terbaru dari InoSakti Blog.')) ?></p>
            <a href="<?= htmlspecialchars($blogBase . '?slug=' . urlencode((string) $featured['slug'])) ?>" class="px-8 py-4 bg-primary text-white rounded-xl font-bold hover:bg-blue-800 transition-all shadow-lg shadow-blue-200 dark:shadow-none inline-flex items-center gap-2">
              Baca Selengkapnya <span class="material-symbols-outlined">arrow_forward</span>
            </a>
          </div>
        </div>
      </div>
    </section>
  <?php endif; ?>

  <section class="py-6 border-y border-slate-100 dark:border-slate-800 sticky top-16 z-40 glass-effect">
    <div class="container mx-auto px-6">
      <form method="get" class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-3 overflow-x-auto pb-2 md:pb-0">
          <span class="text-sm font-bold text-slate-400 uppercase tracking-widest mr-2">Kategori:</span>
          <a href="?category=all&q=<?= urlencode($filters['q']) ?>" class="whitespace-nowrap px-5 py-2 rounded-full font-bold text-sm <?= $filters['category'] === 'all' ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' ?>">Semua</a>
          <?php foreach ($categories as $cat): ?>
            <a href="?category=<?= urlencode((string) $cat['slug']) ?>&q=<?= urlencode($filters['q']) ?>" class="whitespace-nowrap px-5 py-2 rounded-full font-bold text-sm <?= $filters['category'] === (string) $cat['slug'] ? 'bg-primary text-white' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400' ?>">
              <?= htmlspecialchars((string) $cat['name']) ?>
            </a>
          <?php endforeach; ?>
        </div>
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
          <input name="q" value="<?= htmlspecialchars($filters['q']) ?>" class="bg-slate-100 dark:bg-slate-800 border-none rounded-full pl-10 pr-6 py-2 text-sm w-full md:w-64 focus:ring-2 focus:ring-primary" placeholder="Cari artikel..." type="text">
          <input type="hidden" name="category" value="<?= htmlspecialchars($filters['category']) ?>">
        </div>
      </form>
    </div>
  </section>

  <section class="py-20 bg-[#f8fafc] dark:bg-slate-900/50">
    <div class="container mx-auto px-6">
      <?php if ($errorMessage !== null): ?>
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm"><?= htmlspecialchars($errorMessage) ?></div>
      <?php elseif (!$posts): ?>
        <div class="rounded-xl border border-slate-200 bg-white text-slate-600 px-5 py-10 text-center">Belum ada artikel published. Kelola dari Admin Console.</div>
      <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <?php foreach ($listPosts as $post): ?>
            <article class="blog-card group">
              <div class="aspect-video overflow-hidden relative">
                <a href="<?= htmlspecialchars($blogBase . '?slug=' . urlencode((string) $post['slug'])) ?>" class="block">
                  <img alt="<?= htmlspecialchars((string) $post['title']) ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="<?= htmlspecialchars((string) (blog_resolve_image_url((string) ($post['featured_image'] ?? ''), (string) $basePath) ?: 'https://placehold.co/800x450?text=InoSakti+Blog')) ?>">
                  <div class="absolute bottom-4 left-4">
                    <span class="category-tag bg-white/90 dark:bg-slate-900/90 backdrop-blur"><?= htmlspecialchars((string) $post['category_name']) ?></span>
                  </div>
                </a>
              </div>
              <div class="p-6">
                <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 mb-4">
                  <span class="material-symbols-outlined text-sm">event</span> <?= htmlspecialchars(blog_format_date((string) ($post['published_at'] ?? ''))) ?>
                </div>
                <h3 class="text-xl font-bold mb-4 group-hover:text-primary transition-colors">
                  <a href="<?= htmlspecialchars($blogBase . '?slug=' . urlencode((string) $post['slug'])) ?>" class="hover:text-primary transition-colors">
                    <?= htmlspecialchars((string) $post['title']) ?>
                  </a>
                </h3>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 line-clamp-3"><?= htmlspecialchars((string) ($post['excerpt'] ?: '')) ?></p>
                <a class="text-primary font-bold text-sm flex items-center gap-1 hover:gap-3 transition-all" href="<?= htmlspecialchars($blogBase . '?slug=' . urlencode((string) $post['slug'])) ?>">
                  Baca Selengkapnya <span class="material-symbols-outlined text-lg">arrow_right_alt</span>
                </a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>
</main>

<?php include __DIR__ . '/../../inc/footer.php'; ?>
