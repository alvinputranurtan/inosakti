<?php
declare(strict_types=1);

$pageTitle = 'Belanja | InoSakti - Engineering & Technology Solutions';
$extraHead = <<<HTML
<style type="text/tailwindcss">
@layer components {
  .product-card { @apply bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1; }
  .filter-dropdown { @apply bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2 text-xs font-semibold; }
  .category-badge { @apply absolute bottom-3 left-3 z-10 bg-slate-100/95 dark:bg-slate-800/95 text-slate-800 dark:text-slate-200 text-[9px] font-bold px-2.5 py-1 rounded-full shadow-sm; }
}
</style>
HTML;
include __DIR__ . '/../../inc/header.php';

$db = @new mysqli(
    (string) ($dbConfig['host'] ?? 'localhost'),
    (string) ($dbConfig['user'] ?? ''),
    (string) ($dbConfig['pass'] ?? ''),
    (string) ($dbConfig['name'] ?? ''),
    (int) ($dbConfig['port'] ?? 3306)
);

$dbError = null;
if ($db->connect_errno) {
    $dbError = 'Database tidak bisa diakses saat ini.';
} else {
    $db->set_charset('utf8mb4');
}

function shop_table_exists(mysqli $db, string $name): bool
{
    $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?");
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return ((int) ($row['cnt'] ?? 0)) > 0;
}

function shop_column_exists(mysqli $db, string $table, string $column): bool
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

$products = [];
$categories = [];
$pagination = ['page' => 1, 'total_pages' => 1, 'total_items' => 0, 'per_page' => 12];
$filters = [
    'category' => (string) ($_GET['category'] ?? 'all'),
    'sort' => (string) ($_GET['sort'] ?? 'newest'),
    'order' => (string) ($_GET['order'] ?? 'desc'),
    'q' => trim((string) ($_GET['q'] ?? '')),
];

if ($dbError === null) {
    $hasProducts = shop_table_exists($db, 'products');
    $hasProductCategories = shop_table_exists($db, 'product_categories');
    $hasCategoryId = shop_column_exists($db, 'products', 'category_id');
    $hasImagePath = shop_column_exists($db, 'products', 'image_path');

    if (!$hasProducts) {
        $dbError = 'Tabel products belum tersedia.';
    } else {
        if ($hasProductCategories) {
            $catSql = "SELECT slug, name FROM product_categories WHERE is_active = 1 ORDER BY name ASC";
            $catRes = $db->query($catSql);
            if ($catRes) {
                $categories = $catRes->fetch_all(MYSQLI_ASSOC);
            }
        }

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $allowedSort = [
            'newest' => 'p.created_at',
            'price_low' => 'p.price',
            'price_high' => 'p.price',
            'name' => 'p.name',
            'stock' => 'p.stock',
        ];
        $sortKey = array_key_exists($filters['sort'], $allowedSort) ? $filters['sort'] : 'newest';
        $orderBy = $allowedSort[$sortKey];
        $direction = strtolower($filters['order']) === 'asc' ? 'ASC' : 'DESC';
        if ($sortKey === 'price_low') {
            $direction = 'ASC';
        } elseif ($sortKey === 'price_high') {
            $direction = 'DESC';
        }

        $where = ["p.is_active = 1"];
        $params = [];
        $types = '';

        if ($filters['q'] !== '') {
            $where[] = "(p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)";
            $kw = '%' . $filters['q'] . '%';
            $params[] = $kw;
            $params[] = $kw;
            $params[] = $kw;
            $types .= 'sss';
        }

        if ($filters['category'] !== 'all' && $hasProductCategories && $hasCategoryId) {
            $where[] = "pc.slug = ?";
            $params[] = $filters['category'];
            $types .= 's';
        }

        $whereSql = implode(' AND ', $where);

        $countSql = "SELECT COUNT(*) AS total
                     FROM products p" .
                    ($hasProductCategories && $hasCategoryId ? " LEFT JOIN product_categories pc ON pc.id = p.category_id" : "") .
                    " WHERE {$whereSql}";
        $stmtCount = $db->prepare($countSql);
        if ($stmtCount) {
            if ($types !== '') {
                $stmtCount->bind_param($types, ...$params);
            }
            $stmtCount->execute();
            $countRow = $stmtCount->get_result()->fetch_assoc();
            $totalItems = (int) ($countRow['total'] ?? 0);
            $stmtCount->close();
        } else {
            $totalItems = 0;
        }

        $totalPages = max(1, (int) ceil($totalItems / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
            $offset = ($page - 1) * $perPage;
        }
        $pagination = ['page' => $page, 'total_pages' => $totalPages, 'total_items' => $totalItems, 'per_page' => $perPage];

        $selectCategory = $hasProductCategories && $hasCategoryId ? "COALESCE(pc.name, 'Uncategorized') AS category_name, COALESCE(pc.slug, 'uncategorized') AS category_slug," : "'Uncategorized' AS category_name, 'uncategorized' AS category_slug,";
        $selectImage = $hasImagePath ? "p.image_path," : "NULL AS image_path,";
        $dataSql = "SELECT p.id, p.sku, p.slug, p.name, p.description, p.price, p.stock, {$selectCategory} {$selectImage} p.created_at
                    FROM products p" .
                   ($hasProductCategories && $hasCategoryId ? " LEFT JOIN product_categories pc ON pc.id = p.category_id" : "") .
                   " WHERE {$whereSql}
                    ORDER BY {$orderBy} {$direction}
                    LIMIT ? OFFSET ?";
        $stmtData = $db->prepare($dataSql);
        if ($stmtData) {
            $paramsData = $params;
            $typesData = $types . 'ii';
            $paramsData[] = $perPage;
            $paramsData[] = $offset;
            $stmtData->bind_param($typesData, ...$paramsData);
            $stmtData->execute();
            $result = $stmtData->get_result();
            $products = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            $stmtData->close();
        } else {
            $dbError = 'Gagal mengambil data produk.';
        }
    }
}
?>

<div class="bg-slate-100 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 sticky top-16 z-40">
  <div class="container mx-auto px-6 py-4">
    <form method="get" class="flex flex-wrap items-end gap-4 text-xs font-bold text-slate-600 dark:text-slate-400">
      <div class="flex flex-col gap-1">
        <label>Kategori</label>
        <select name="category" onchange="this.form.submit()" class="filter-dropdown min-w-[170px] appearance-none focus:ring-1 focus:ring-primary outline-none">
          <option value="all" <?= $filters['category'] === 'all' ? 'selected' : '' ?>>Semua Produk</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= htmlspecialchars((string) $cat['slug']) ?>" <?= $filters['category'] === (string) $cat['slug'] ? 'selected' : '' ?>>
              <?= htmlspecialchars((string) $cat['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="flex flex-col gap-1">
        <label>Urutkan</label>
        <select name="sort" onchange="this.form.submit()" class="filter-dropdown min-w-[150px] appearance-none focus:ring-1 focus:ring-primary outline-none">
          <option value="newest" <?= $filters['sort'] === 'newest' ? 'selected' : '' ?>>Terbaru</option>
          <option value="price_low" <?= $filters['sort'] === 'price_low' ? 'selected' : '' ?>>Termurah</option>
          <option value="price_high" <?= $filters['sort'] === 'price_high' ? 'selected' : '' ?>>Termahal</option>
          <option value="name" <?= $filters['sort'] === 'name' ? 'selected' : '' ?>>Nama</option>
          <option value="stock" <?= $filters['sort'] === 'stock' ? 'selected' : '' ?>>Stok</option>
        </select>
      </div>

      <div class="flex flex-col gap-1">
        <label>Urutan</label>
        <select name="order" onchange="this.form.submit()" class="filter-dropdown min-w-[120px] appearance-none focus:ring-1 focus:ring-primary outline-none">
          <option value="desc" <?= $filters['order'] === 'desc' ? 'selected' : '' ?>>Desc</option>
          <option value="asc" <?= $filters['order'] === 'asc' ? 'selected' : '' ?>>Asc</option>
        </select>
      </div>

      <div class="flex flex-col gap-1 flex-grow lg:max-w-xs ml-auto">
        <label>Pencarian</label>
        <div class="relative">
          <input name="q" value="<?= htmlspecialchars($filters['q']) ?>" class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2 pl-9 text-xs focus:ring-1 focus:ring-primary outline-none" placeholder="Cari item...">
          <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
        </div>
      </div>
      <input type="hidden" name="page" value="1">
    </form>
  </div>
</div>

<div class="container mx-auto px-6 py-3 text-sm text-slate-500">
  Menampilkan <?= (int) count($products) ?> dari <?= (int) $pagination['total_items'] ?> produk.
</div>

<main class="container mx-auto px-6 pb-12">
  <?php if ($dbError !== null): ?>
    <div class="rounded-xl border border-red-200 bg-red-50 text-red-700 px-4 py-3 text-sm">
      <?= htmlspecialchars($dbError) ?>
    </div>
  <?php elseif (!$products): ?>
    <div class="rounded-xl border border-slate-200 bg-white text-slate-600 px-5 py-10 text-center">
      Produk tidak ditemukan. Ubah filter atau tambahkan produk dari CMS.
    </div>
  <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8" id="product-grid">
      <?php
      $fallbackImages = [
          $basePath . '/assets/content/shop/images/produk_1.png',
          $basePath . '/assets/content/shop/images/produk_2.png',
          $basePath . '/assets/content/shop/images/produk_3.png',
          $basePath . '/assets/content/shop/images/produk_4.png',
          $basePath . '/assets/content/shop/images/produk_5.png',
          $basePath . '/assets/content/shop/images/produk_6.png',
      ];
      foreach ($products as $idx => $p):
          $img = trim((string) ($p['image_path'] ?? ''));
          $imageUrl = $img !== '' ? (str_starts_with($img, 'http') ? $img : $basePath . '/' . ltrim($img, '/')) : $fallbackImages[$idx % count($fallbackImages)];
          $shortDesc = trim((string) ($p['description'] ?? '')) !== '' ? (string) $p['description'] : 'Produk teknologi InoSakti.';
      ?>
      <article class="product-card group">
        <div class="relative aspect-square bg-slate-100 dark:bg-slate-700 overflow-hidden">
          <span class="category-badge"><?= htmlspecialchars((string) $p['category_name']) ?></span>
          <img alt="<?= htmlspecialchars((string) $p['name']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="<?= htmlspecialchars($imageUrl) ?>">
        </div>
        <div class="p-5">
          <h3 class="font-bold text-sm mb-2 line-clamp-1"><?= htmlspecialchars((string) $p['name']) ?></h3>
          <p class="text-slate-500 dark:text-slate-400 text-[10px] leading-relaxed mb-3 line-clamp-2"><?= htmlspecialchars($shortDesc) ?></p>
          <div class="inline-block bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded-full text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-4">
            Rp<?= number_format((float) $p['price'], 0, ',', '.') ?>
          </div>
          <div class="flex gap-2">
            <button
              type="button"
              class="flex-grow bg-primary hover:bg-blue-800 text-white text-[11px] font-bold py-2 rounded-lg transition-colors btn-product-detail"
              data-name="<?= htmlspecialchars((string) $p['name']) ?>"
              data-price="Rp<?= number_format((float) $p['price'], 0, ',', '.') ?>"
              data-stock="<?= (int) $p['stock'] ?>"
              data-category="<?= htmlspecialchars((string) $p['category_name']) ?>"
              data-description="<?= htmlspecialchars($shortDesc) ?>"
              data-image="<?= htmlspecialchars($imageUrl) ?>"
            >
              Lihat deskripsi
            </button>
            <a href="<?= htmlspecialchars($basePath . '/pages/products/cart?product=' . urlencode((string) $p['slug'])) ?>" class="w-10 bg-primary hover:bg-blue-800 text-white rounded-lg flex items-center justify-center transition-colors">
              <span class="material-symbols-outlined text-lg">shopping_cart</span>
            </a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <?php if ((int) $pagination['total_pages'] > 1): ?>
      <div class="mt-8 flex items-center justify-center gap-2 text-sm">
        <?php
        $baseQuery = $_GET;
        $current = (int) $pagination['page'];
        $totalPages = (int) $pagination['total_pages'];
        $prevPage = max(1, $current - 1);
        $nextPage = min($totalPages, $current + 1);
        $baseQuery['page'] = $prevPage;
        ?>
        <a class="px-3 py-2 border rounded-lg <?= $current <= 1 ? 'pointer-events-none opacity-40' : 'hover:bg-slate-50' ?>" href="?<?= http_build_query($baseQuery) ?>">Prev</a>
        <span class="px-4 py-2 bg-primary text-white rounded-lg font-bold"><?= $current ?> / <?= $totalPages ?></span>
        <?php $baseQuery['page'] = $nextPage; ?>
        <a class="px-3 py-2 border rounded-lg <?= $current >= $totalPages ? 'pointer-events-none opacity-40' : 'hover:bg-slate-50' ?>" href="?<?= http_build_query($baseQuery) ?>">Next</a>
      </div>
    <?php endif; ?>
  <?php endif; ?>
</main>

<div id="productDetailModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
  <div class="bg-white dark:bg-slate-900 rounded-2xl max-w-xl w-full overflow-hidden border border-slate-200 dark:border-slate-700">
    <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
      <h3 class="font-bold text-lg">Detail Produk</h3>
      <button id="btnCloseProductModal" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800">
        <span class="material-symbols-outlined">close</span>
      </button>
    </div>
    <div class="p-5 space-y-3">
      <img id="modalProductImage" src="" alt="Product" class="w-full aspect-video object-cover rounded-xl bg-slate-100">
      <div class="text-xs font-bold uppercase tracking-wider text-slate-500" id="modalProductCategory"></div>
      <h4 class="font-extrabold text-xl" id="modalProductName"></h4>
      <div class="text-primary font-bold" id="modalProductPrice"></div>
      <div class="text-sm text-slate-500">Stok: <span id="modalProductStock"></span></div>
      <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed" id="modalProductDesc"></p>
    </div>
  </div>
</div>

<?php
$extraScripts = <<<HTML
<script>
document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('productDetailModal');
  const closeBtn = document.getElementById('btnCloseProductModal');
  const elName = document.getElementById('modalProductName');
  const elPrice = document.getElementById('modalProductPrice');
  const elStock = document.getElementById('modalProductStock');
  const elCategory = document.getElementById('modalProductCategory');
  const elDesc = document.getElementById('modalProductDesc');
  const elImage = document.getElementById('modalProductImage');

  document.querySelectorAll('.btn-product-detail').forEach((btn) => {
    btn.addEventListener('click', function () {
      elName.textContent = this.dataset.name || '';
      elPrice.textContent = this.dataset.price || '';
      elStock.textContent = this.dataset.stock || '0';
      elCategory.textContent = this.dataset.category || '';
      elDesc.textContent = this.dataset.description || '';
      elImage.src = this.dataset.image || '';
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    });
  });

  function closeModal() {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
  }

  closeBtn?.addEventListener('click', closeModal);
  modal?.addEventListener('click', function (e) {
    if (e.target === modal) closeModal();
  });
});
</script>
HTML;
include __DIR__ . '/../../inc/footer.php';
exit;
