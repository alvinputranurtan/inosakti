<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/layout.php';
admin_require_login();

$productImageDirRel = 'assets/content/shop/images';
$productImageDirAbs = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $productImageDirRel);
if (!is_dir($productImageDirAbs)) {
    @mkdir($productImageDirAbs, 0775, true);
}

function normalize_image_filename(string $name): string
{
    $name = trim(str_replace(['\\', '/'], '', $name));
    $name = preg_replace('/[^A-Za-z0-9._-]/', '-', $name) ?? '';
    $name = trim($name, '.- _');
    return $name;
}

if (!admin_table_exists('products')) {
    admin_set_flash('error', 'Tabel products belum tersedia.');
    header('Location: ' . admin_url('/admin/'));
    exit;
}

$hasCategories = admin_table_exists('product_categories');
$hasImagePath = false;
$checkCol = admin_db()->prepare("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='products' AND COLUMN_NAME='image_path'");
if ($checkCol) {
    $checkCol->execute();
    $rowCol = $checkCol->get_result()->fetch_assoc();
    $hasImagePath = ((int) ($rowCol['cnt'] ?? 0)) > 0;
    $checkCol->close();
}
$hasCategoryId = false;
$checkCatCol = admin_db()->prepare("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='products' AND COLUMN_NAME='category_id'");
if ($checkCatCol) {
    $checkCatCol->execute();
    $rowCatCol = $checkCatCol->get_result()->fetch_assoc();
    $hasCategoryId = ((int) ($rowCatCol['cnt'] ?? 0)) > 0;
    $checkCatCol->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!admin_verify_csrf(is_string($token) ? $token : null)) {
        admin_set_flash('error', 'Token keamanan tidak valid.');
        header('Location: ' . admin_url('/admin/products'));
        exit;
    }

    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'save') {
        $id = (int) ($_POST['id'] ?? 0);
        $sku = trim((string) ($_POST['sku'] ?? ''));
        $slug = trim((string) ($_POST['slug'] ?? ''));
        $name = trim((string) ($_POST['name'] ?? ''));
        $description = trim((string) ($_POST['description'] ?? ''));
        $price = (float) ($_POST['price'] ?? 0);
        $stock = (int) ($_POST['stock'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $imageFilenameInput = trim((string) ($_POST['image_filename'] ?? ''));
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $categoryIdValue = $categoryId > 0 ? $categoryId : null;
        $imagePath = '';
        $currentImagePath = '';

        if ($id > 0 && $hasImagePath) {
            $getCurrent = admin_db()->prepare("SELECT image_path FROM products WHERE id = ? LIMIT 1");
            if ($getCurrent) {
                $getCurrent->bind_param('i', $id);
                $getCurrent->execute();
                $cur = $getCurrent->get_result()->fetch_assoc();
                $currentImagePath = (string) ($cur['image_path'] ?? '');
                $getCurrent->close();
            }
        }

        if ($sku === '' || $slug === '' || $name === '') {
            admin_set_flash('error', 'SKU, slug, dan nama wajib diisi.');
            header('Location: ' . admin_url('/admin/products'));
            exit;
        }

        if ($hasImagePath) {
            $imagePath = $currentImagePath;
            $uploaded = $_FILES['image_file'] ?? null;
            $hasUpload = is_array($uploaded) && isset($uploaded['error']) && (int) $uploaded['error'] === UPLOAD_ERR_OK;

            if ($hasUpload) {
                $originalName = (string) ($uploaded['name'] ?? '');
                $ext = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
                $allowedExt = ['png', 'jpg', 'jpeg', 'webp'];
                if (!in_array($ext, $allowedExt, true)) {
                    admin_set_flash('error', 'Format file tidak didukung. Gunakan: png, jpg, jpeg, webp.');
                    header('Location: ' . admin_url('/admin/products'));
                    exit;
                }

                $baseName = $imageFilenameInput !== '' ? (string) pathinfo($imageFilenameInput, PATHINFO_FILENAME) : (string) pathinfo($originalName, PATHINFO_FILENAME);
                $baseName = normalize_image_filename($baseName);
                if ($baseName === '') {
                    $baseName = 'product-' . time();
                }

                $finalFilename = $baseName . '.' . $ext;
                $targetAbs = $productImageDirAbs . DIRECTORY_SEPARATOR . $finalFilename;
                $suffix = 1;
                while (file_exists($targetAbs)) {
                    $finalFilename = $baseName . '-' . $suffix . '.' . $ext;
                    $targetAbs = $productImageDirAbs . DIRECTORY_SEPARATOR . $finalFilename;
                    $suffix++;
                }

                if (!move_uploaded_file((string) $uploaded['tmp_name'], $targetAbs)) {
                    admin_set_flash('error', 'Gagal upload file gambar.');
                    header('Location: ' . admin_url('/admin/products'));
                    exit;
                }

                $imagePath = $productImageDirRel . '/' . $finalFilename;
            } elseif ($imageFilenameInput !== '') {
                $typedName = normalize_image_filename($imageFilenameInput);
                if ($typedName === '') {
                    admin_set_flash('error', 'Nama file gambar tidak valid.');
                    header('Location: ' . admin_url('/admin/products'));
                    exit;
                }

                $oldFilename = basename($currentImagePath);
                $oldExt = strtolower((string) pathinfo($oldFilename, PATHINFO_EXTENSION));
                $typedExt = strtolower((string) pathinfo($typedName, PATHINFO_EXTENSION));
                if ($typedExt === '' && $oldExt !== '') {
                    $typedName .= '.' . $oldExt;
                    $typedExt = $oldExt;
                }

                $allowedExt = ['png', 'jpg', 'jpeg', 'webp'];
                if ($typedExt !== '' && !in_array($typedExt, $allowedExt, true)) {
                    admin_set_flash('error', 'Ekstensi file gambar tidak didukung.');
                    header('Location: ' . admin_url('/admin/products'));
                    exit;
                }

                if ($currentImagePath !== '') {
                    $sourceAbs = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $currentImagePath);
                    $targetAbs = $productImageDirAbs . DIRECTORY_SEPARATOR . $typedName;
                    if (is_file($sourceAbs) && $oldFilename !== $typedName && !file_exists($targetAbs)) {
                        @rename($sourceAbs, $targetAbs);
                    }
                }
                $imagePath = $productImageDirRel . '/' . $typedName;
            }
        }

        if ($id > 0) {
            if ($hasCategoryId && $hasImagePath) {
                $sql = "UPDATE products SET sku=?, slug=?, name=?, description=?, price=?, stock=?, is_active=?, category_id=?, image_path=? WHERE id=?";
                $stmt = admin_db()->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param('ssssdiiisi', $sku, $slug, $name, $description, $price, $stock, $isActive, $categoryIdValue, $imagePath, $id);
                    $stmt->execute();
                    $stmt->close();
                }
            } elseif ($hasCategoryId) {
                $sql = "UPDATE products SET sku=?, slug=?, name=?, description=?, price=?, stock=?, is_active=?, category_id=? WHERE id=?";
                $stmt = admin_db()->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param('ssssdiiii', $sku, $slug, $name, $description, $price, $stock, $isActive, $categoryIdValue, $id);
                    $stmt->execute();
                    $stmt->close();
                }
            } elseif ($hasImagePath) {
                $sql = "UPDATE products SET sku=?, slug=?, name=?, description=?, price=?, stock=?, is_active=?, image_path=? WHERE id=?";
                $stmt = admin_db()->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param('ssssdiisi', $sku, $slug, $name, $description, $price, $stock, $isActive, $imagePath, $id);
                    $stmt->execute();
                    $stmt->close();
                }
            } else {
                $sql = "UPDATE products SET sku=?, slug=?, name=?, description=?, price=?, stock=?, is_active=? WHERE id=?";
                $stmt = admin_db()->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param('ssssdiii', $sku, $slug, $name, $description, $price, $stock, $isActive, $id);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            admin_set_flash('success', 'Produk berhasil diperbarui.');
        } else {
            if ($hasCategoryId && $hasImagePath) {
                $sql = "INSERT INTO products (sku, slug, name, description, price, stock, is_active, category_id, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = admin_db()->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param('ssssdiiis', $sku, $slug, $name, $description, $price, $stock, $isActive, $categoryIdValue, $imagePath);
                    $stmt->execute();
                    $stmt->close();
                }
            } elseif ($hasCategoryId) {
                $sql = "INSERT INTO products (sku, slug, name, description, price, stock, is_active, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = admin_db()->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param('ssssdiii', $sku, $slug, $name, $description, $price, $stock, $isActive, $categoryIdValue);
                    $stmt->execute();
                    $stmt->close();
                }
            } elseif ($hasImagePath) {
                $sql = "INSERT INTO products (sku, slug, name, description, price, stock, is_active, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = admin_db()->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param('ssssdiis', $sku, $slug, $name, $description, $price, $stock, $isActive, $imagePath);
                    $stmt->execute();
                    $stmt->close();
                }
            } else {
                $sql = "INSERT INTO products (sku, slug, name, description, price, stock, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = admin_db()->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param('ssssdii', $sku, $slug, $name, $description, $price, $stock, $isActive);
                    $stmt->execute();
                    $stmt->close();
                }
            }
            admin_set_flash('success', 'Produk baru berhasil ditambahkan.');
        }

        header('Location: ' . admin_url('/admin/products'));
        exit;
    }

    if ($action === 'toggle') {
        $id = (int) ($_POST['id'] ?? 0);
        $isActive = (int) ($_POST['is_active'] ?? 0);
        if ($id > 0) {
            $stmt = admin_db()->prepare("UPDATE products SET is_active = ? WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param('ii', $isActive, $id);
                $stmt->execute();
                $stmt->close();
                admin_set_flash('success', 'Status produk berhasil diperbarui.');
            }
        }
        header('Location: ' . admin_url('/admin/products'));
        exit;
    }
}

$categories = [];
if ($hasCategories) {
    $catRes = admin_db()->query("SELECT id, name FROM product_categories WHERE is_active=1 ORDER BY name");
    if ($catRes) {
        $categories = $catRes->fetch_all(MYSQLI_ASSOC);
    }
}

$rows = [];
if ($hasCategories && $hasCategoryId && $hasImagePath) {
    $sql = "SELECT p.id, p.sku, p.slug, p.name, p.description, p.price, p.stock, p.is_active, p.created_at, p.image_path, p.category_id, pc.name AS category_name
            FROM products p
            LEFT JOIN product_categories pc ON pc.id = p.category_id
            ORDER BY p.created_at DESC
            LIMIT 150";
} elseif ($hasCategories && $hasCategoryId) {
    $sql = "SELECT p.id, p.sku, p.slug, p.name, p.description, p.price, p.stock, p.is_active, p.created_at, NULL AS image_path, p.category_id, pc.name AS category_name
            FROM products p
            LEFT JOIN product_categories pc ON pc.id = p.category_id
            ORDER BY p.created_at DESC
            LIMIT 150";
} elseif ($hasImagePath) {
    $sql = "SELECT p.id, p.sku, p.slug, p.name, p.description, p.price, p.stock, p.is_active, p.created_at, p.image_path, NULL AS category_id, NULL AS category_name
            FROM products p
            ORDER BY p.created_at DESC
            LIMIT 150";
} else {
    $sql = "SELECT p.id, p.sku, p.slug, p.name, p.description, p.price, p.stock, p.is_active, p.created_at, NULL AS image_path, NULL AS category_id, NULL AS category_name
            FROM products p
            ORDER BY p.created_at DESC
            LIMIT 150";
}
$result = admin_db()->query($sql);
if ($result) {
    $rows = $result->fetch_all(MYSQLI_ASSOC);
}

admin_render_start('Manajemen Produk', 'products');
?>

<div class="bg-white border border-slate-200 rounded-2xl p-5 mb-6">
  <div class="flex items-center justify-between mb-3">
    <h2 class="font-bold text-lg">Form Produk</h2>
    <span id="formModeBadge" class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Mode Tambah Produk Baru</span>
  </div>
  <div id="formModeHint" class="mb-4 text-xs text-slate-500">Isi form ini untuk menambahkan produk baru.</div>
  <form id="productForm" method="post" enctype="multipart/form-data" class="grid md:grid-cols-2 xl:grid-cols-3 gap-3">
    <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
    <input type="hidden" name="action" value="save">
    <input id="formProductId" type="hidden" name="id" value="0">
    <div>
      <label for="formSku" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kode Produk</label>
      <input id="formSku" name="sku" required placeholder="IS-COMP-001" class="rounded-lg border-slate-300 w-full">
    </div>
    <div>
      <label for="formSlug" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">URL Produk</label>
      <input id="formSlug" name="slug" required placeholder="raspberry-pi-4" class="rounded-lg border-slate-300 w-full">
    </div>
    <div>
      <label for="formName" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Produk</label>
      <input id="formName" name="name" required placeholder="Raspberry Pi 4" class="rounded-lg border-slate-300 w-full">
    </div>
    <div>
      <label for="formPrice" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Harga (Rp)</label>
      <input id="formPrice" name="price" type="number" step="0.01" min="0" required placeholder="450000" class="rounded-lg border-slate-300 w-full">
    </div>
    <div>
      <label for="formStock" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Stok</label>
      <input id="formStock" name="stock" type="number" min="0" required placeholder="10" class="rounded-lg border-slate-300 w-full">
    </div>
    <?php if ($hasCategoryId): ?>
      <div>
        <label for="formCategoryId" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kategori</label>
        <select id="formCategoryId" name="category_id" class="rounded-lg border-slate-300 w-full">
          <option value="0">Tanpa kategori</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= (int) $cat['id'] ?>"><?= admin_e((string) $cat['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    <?php endif; ?>
    <?php if ($hasImagePath): ?>
      <div>
        <label for="formImageFilename" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama File Gambar</label>
        <div class="flex gap-2">
          <input id="formImageFilename" name="image_filename" placeholder="produk_1.png" class="rounded-lg border-slate-300 w-full">
          <label class="px-3 py-2 rounded-lg border border-slate-300 bg-slate-50 text-slate-700 font-semibold cursor-pointer">
            Upload
            <input id="formImageFile" name="image_file" type="file" accept=".png,.jpg,.jpeg,.webp" class="hidden">
          </label>
        </div>
      </div>
    <?php endif; ?>
    <div class="md:col-span-2 xl:col-span-2">
      <label for="formDescription" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Deskripsi Singkat</label>
      <input id="formDescription" name="description" placeholder="Deskripsi singkat produk" class="rounded-lg border-slate-300 w-full">
    </div>
    <label class="inline-flex items-center gap-2 text-sm font-semibold">
      <input id="formIsActive" type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300"> Active
    </label>
    <div class="flex items-center gap-2">
      <button id="formSubmitButton" class="px-4 py-2 bg-blue-800 text-white rounded-lg font-semibold">Simpan Produk</button>
      <button id="formResetButton" type="button" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg font-semibold">Reset</button>
      <button id="formCancelEditButton" type="button" class="px-4 py-2 border border-red-300 text-red-700 rounded-lg font-semibold hidden">Batal Edit</button>
    </div>
  </form>
</div>

<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
  <div class="p-5 border-b border-slate-200">
    <h2 class="font-bold text-lg">Daftar Produk</h2>
    <p class="text-sm text-slate-500 mt-1">Data ini langsung dipakai oleh halaman Shop.</p>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] tracking-wider">
      <tr>
        <th class="px-4 py-3 text-left">Produk</th>
        <th class="px-4 py-3 text-left">SKU/Slug</th>
        <th class="px-4 py-3 text-left">Kategori</th>
        <th class="px-4 py-3 text-left">Harga</th>
        <th class="px-4 py-3 text-left">Stok</th>
        <th class="px-4 py-3 text-left">Status</th>
        <th class="px-4 py-3 text-right">Aksi</th>
      </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
      <?php foreach ($rows as $r): ?>
        <tr>
          <td class="px-4 py-3">
            <div class="font-semibold text-slate-800"><?= admin_e((string) $r['name']) ?></div>
            <div class="text-xs text-slate-500 line-clamp-1"><?= admin_e((string) ($r['description'] ?? '')) ?></div>
          </td>
          <td class="px-4 py-3 text-xs">
            <div class="font-semibold"><?= admin_e((string) $r['sku']) ?></div>
            <div class="text-slate-500"><?= admin_e((string) $r['slug']) ?></div>
          </td>
          <td class="px-4 py-3"><?= admin_e((string) ($r['category_name'] ?? '-')) ?></td>
          <td class="px-4 py-3">Rp <?= number_format((float) $r['price'], 0, ',', '.') ?></td>
          <td class="px-4 py-3"><?= (int) $r['stock'] ?></td>
          <td class="px-4 py-3">
            <span class="px-2 py-1 rounded-full text-xs font-bold <?= (int) $r['is_active'] === 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' ?>">
              <?= (int) $r['is_active'] === 1 ? 'active' : 'inactive' ?>
            </span>
          </td>
          <td class="px-4 py-3">
            <div class="flex justify-end gap-2">
              <form method="post">
                <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                <input type="hidden" name="is_active" value="<?= (int) $r['is_active'] === 1 ? 0 : 1 ?>">
                <button class="px-3 py-1.5 rounded-lg text-white font-semibold <?= (int) $r['is_active'] === 1 ? 'bg-amber-500' : 'bg-emerald-600' ?>">
                  <?= (int) $r['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?>
                </button>
              </form>
              <button
                type="button"
                class="px-3 py-1.5 rounded-lg bg-slate-800 text-white font-semibold btn-edit-product"
                data-id="<?= (int) $r['id'] ?>"
                data-sku="<?= admin_e((string) $r['sku']) ?>"
                data-slug="<?= admin_e((string) $r['slug']) ?>"
                data-name="<?= admin_e((string) $r['name']) ?>"
                data-description="<?= admin_e((string) ($r['description'] ?? '')) ?>"
                data-price="<?= (float) $r['price'] ?>"
                data-stock="<?= (int) $r['stock'] ?>"
                data-is-active="<?= (int) $r['is_active'] ?>"
                data-category-id="<?= (int) ($r['category_id'] ?? 0) ?>"
                data-image-filename="<?= admin_e((string) basename((string) ($r['image_path'] ?? ''))) ?>"
              >
                Edit
              </button>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?>
        <tr><td colspan="7" class="px-4 py-5 text-center text-slate-500">Belum ada data produk.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('productForm');
  const idEl = document.getElementById('formProductId');
  const skuEl = document.getElementById('formSku');
  const slugEl = document.getElementById('formSlug');
  const nameEl = document.getElementById('formName');
  const descEl = document.getElementById('formDescription');
  const priceEl = document.getElementById('formPrice');
  const stockEl = document.getElementById('formStock');
  const activeEl = document.getElementById('formIsActive');
  const categoryEl = document.getElementById('formCategoryId');
  const imageFilenameEl = document.getElementById('formImageFilename');
  const imageFileEl = document.getElementById('formImageFile');
  const submitEl = document.getElementById('formSubmitButton');
  const resetEl = document.getElementById('formResetButton');
  const cancelEditEl = document.getElementById('formCancelEditButton');
  const modeBadgeEl = document.getElementById('formModeBadge');
  const modeHintEl = document.getElementById('formModeHint');

  function setAddMode() {
    if (modeBadgeEl) {
      modeBadgeEl.textContent = 'Mode Tambah Produk Baru';
      modeBadgeEl.className = 'px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700';
    }
    if (modeHintEl) {
      modeHintEl.textContent = 'Isi form ini untuk menambahkan produk baru.';
    }
    if (submitEl) submitEl.textContent = 'Simpan Produk Baru';
    cancelEditEl?.classList.add('hidden');
  }

  function setEditMode(id) {
    if (modeBadgeEl) {
      modeBadgeEl.textContent = 'Mode Edit Produk #' + id;
      modeBadgeEl.className = 'px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700';
    }
    if (modeHintEl) {
      modeHintEl.textContent = 'Anda sedang mengedit produk yang sudah ada. Klik "Batal Edit" untuk kembali ke mode tambah.';
    }
    if (submitEl) submitEl.textContent = 'Update Produk';
    cancelEditEl?.classList.remove('hidden');
  }

  function resetFormState() {
    idEl.value = '0';
    form.reset();
    if (activeEl) activeEl.checked = true;
    if (imageFileEl) imageFileEl.value = '';
    setAddMode();
  }

  document.querySelectorAll('.btn-edit-product').forEach((btn) => {
    btn.addEventListener('click', function () {
      idEl.value = this.dataset.id || '0';
      skuEl.value = this.dataset.sku || '';
      slugEl.value = this.dataset.slug || '';
      nameEl.value = this.dataset.name || '';
      descEl.value = this.dataset.description || '';
      priceEl.value = this.dataset.price || '';
      stockEl.value = this.dataset.stock || '';
      if (activeEl) activeEl.checked = (this.dataset.isActive || '0') === '1';
      if (categoryEl) categoryEl.value = this.dataset.categoryId || '0';
      if (imageFilenameEl) imageFilenameEl.value = this.dataset.imageFilename || '';
      if (imageFileEl) imageFileEl.value = '';
      setEditMode(this.dataset.id || '0');
      form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

  imageFileEl?.addEventListener('change', function () {
    const file = this.files && this.files[0];
    if (!file || !imageFilenameEl) return;
    imageFilenameEl.value = file.name;
  });

  resetEl?.addEventListener('click', resetFormState);
  cancelEditEl?.addEventListener('click', resetFormState);
  setAddMode();
});
</script>

<?php admin_render_end(); ?>
