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
    return trim($name, '.- _');
}

function product_image_allowed_ext(string $ext): bool
{
    return in_array(strtolower($ext), ['png', 'jpg', 'jpeg', 'webp'], true);
}

function list_product_images(string $dirAbs): array
{
    if (!is_dir($dirAbs)) {
        return [];
    }
    $files = scandir($dirAbs) ?: [];
    $images = [];
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $full = $dirAbs . DIRECTORY_SEPARATOR . $file;
        if (!is_file($full)) {
            continue;
        }
        if (!product_image_allowed_ext((string) pathinfo($file, PATHINFO_EXTENSION))) {
            continue;
        }
        $images[] = $file;
    }
    natcasesort($images);
    return array_values($images);
}

function parse_product_image_paths(string $imagePath): array
{
    $raw = preg_split('/[|,]/', $imagePath) ?: [];
    $paths = [];
    foreach ($raw as $p) {
        $p = trim((string) $p);
        if ($p === '') {
            continue;
        }
        $paths[] = $p;
    }
    return array_values(array_unique($paths));
}

function build_product_image_paths(array $paths): string
{
    $clean = [];
    foreach ($paths as $p) {
        $p = trim((string) $p);
        if ($p === '') {
            continue;
        }
        $clean[] = $p;
    }
    return implode('|', array_values(array_unique($clean)));
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
$hasFullDescription = false;
$checkFullDescCol = admin_db()->prepare("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='products' AND COLUMN_NAME='full_description'");
if ($checkFullDescCol) {
    $checkFullDescCol->execute();
    $rowFullDescCol = $checkFullDescCol->get_result()->fetch_assoc();
    $hasFullDescription = ((int) ($rowFullDescCol['cnt'] ?? 0)) > 0;
    $checkFullDescCol->close();
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
        $fullDescription = trim((string) ($_POST['full_description'] ?? ''));
        $price = (float) ($_POST['price'] ?? 0);
        $stock = (int) ($_POST['stock'] ?? 0);
        $isActive = isset($_POST['is_active']) ? 1 : 0;
        $imageFilenameInput = trim((string) ($_POST['image_filename'] ?? ''));
        $selectedImage = trim((string) ($_POST['selected_image'] ?? ''));
        $selectedImagesInput = $_POST['selected_images'] ?? [];
        $orderedImagesInput = (string) ($_POST['ordered_images'] ?? '');
        $primaryImageInput = trim((string) ($_POST['primary_image'] ?? ''));
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $categoryIdValue = $categoryId > 0 ? $categoryId : null;
        $imagePath = '';
        $currentImagePath = '';
        $currentImagePaths = [];

        if ($id > 0 && $hasImagePath) {
            $getCurrent = admin_db()->prepare("SELECT image_path FROM products WHERE id = ? LIMIT 1");
            if ($getCurrent) {
                $getCurrent->bind_param('i', $id);
                $getCurrent->execute();
                $cur = $getCurrent->get_result()->fetch_assoc();
                $currentImagePath = (string) ($cur['image_path'] ?? '');
                $currentImagePaths = parse_product_image_paths($currentImagePath);
                $getCurrent->close();
            }
        }

        if ($sku === '' || $slug === '' || $name === '') {
            admin_set_flash('error', 'SKU, slug, dan nama wajib diisi.');
            header('Location: ' . admin_url('/admin/products'));
            exit;
        }

        if ($hasImagePath) {
            $imagePaths = $currentImagePaths;
            $repoNames = [];
            $orderedRepoNames = [];
            if ($orderedImagesInput !== '') {
                $decoded = json_decode($orderedImagesInput, true);
                if (is_array($decoded)) {
                    foreach ($decoded as $n) {
                        $n = normalize_image_filename((string) $n);
                        if ($n === '') {
                            continue;
                        }
                        $ext = strtolower((string) pathinfo($n, PATHINFO_EXTENSION));
                        $abs = $productImageDirAbs . DIRECTORY_SEPARATOR . $n;
                        if (product_image_allowed_ext($ext) && is_file($abs)) {
                            $orderedRepoNames[] = $productImageDirRel . '/' . $n;
                        }
                    }
                }
            }
            if (!$orderedRepoNames) {
                if (is_array($selectedImagesInput)) {
                    foreach ($selectedImagesInput as $n) {
                        $n = normalize_image_filename((string) $n);
                        if ($n === '') {
                            continue;
                        }
                        $ext = strtolower((string) pathinfo($n, PATHINFO_EXTENSION));
                        $abs = $productImageDirAbs . DIRECTORY_SEPARATOR . $n;
                        if (product_image_allowed_ext($ext) && is_file($abs)) {
                            $repoNames[] = $productImageDirRel . '/' . $n;
                        }
                    }
                }
                if (!$repoNames && $selectedImage !== '') {
                    $n = normalize_image_filename($selectedImage);
                    $ext = strtolower((string) pathinfo($n, PATHINFO_EXTENSION));
                    $abs = $productImageDirAbs . DIRECTORY_SEPARATOR . $n;
                    if ($n !== '' && product_image_allowed_ext($ext) && is_file($abs)) {
                        $repoNames[] = $productImageDirRel . '/' . $n;
                    }
                }
            }
            if ($orderedRepoNames) {
                $imagePaths = $orderedRepoNames;
            } elseif ($repoNames) {
                $imagePaths = $repoNames;
            }

            $uploadedMulti = $_FILES['image_files'] ?? null;
            if (is_array($uploadedMulti) && isset($uploadedMulti['name']) && is_array($uploadedMulti['name'])) {
                $total = count($uploadedMulti['name']);
                for ($i = 0; $i < $total; $i++) {
                    $err = (int) ($uploadedMulti['error'][$i] ?? UPLOAD_ERR_NO_FILE);
                    if ($err !== UPLOAD_ERR_OK) {
                        continue;
                    }
                    $originalName = (string) ($uploadedMulti['name'][$i] ?? '');
                    $tmpName = (string) ($uploadedMulti['tmp_name'][$i] ?? '');
                    $ext = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
                    if (!product_image_allowed_ext($ext)) {
                        continue;
                    }
                    $baseName = (string) pathinfo($originalName, PATHINFO_FILENAME);
                    $baseName = normalize_image_filename($baseName);
                    if ($baseName === '') {
                        $baseName = 'product-' . time() . '-' . $i;
                    }
                    $finalFilename = $baseName . '.' . $ext;
                    $targetAbs = $productImageDirAbs . DIRECTORY_SEPARATOR . $finalFilename;
                    $suffix = 1;
                    while (file_exists($targetAbs)) {
                        $finalFilename = $baseName . '-' . $suffix . '.' . $ext;
                        $targetAbs = $productImageDirAbs . DIRECTORY_SEPARATOR . $finalFilename;
                        $suffix++;
                    }
                    if (@move_uploaded_file($tmpName, $targetAbs)) {
                        $imagePaths[] = $productImageDirRel . '/' . $finalFilename;
                    }
                }
            }

            $uploaded = $_FILES['image_file'] ?? null;
            $hasUpload = is_array($uploaded) && isset($uploaded['error']) && (int) $uploaded['error'] === UPLOAD_ERR_OK;
            if ($hasUpload) {
                $originalName = (string) ($uploaded['name'] ?? '');
                $ext = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
                if (!product_image_allowed_ext($ext)) {
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
                $imagePaths[] = $productImageDirRel . '/' . $finalFilename;
            } elseif ($imageFilenameInput !== '' && !empty($imagePaths)) {
                $typedName = normalize_image_filename($imageFilenameInput);
                if ($typedName === '') {
                    admin_set_flash('error', 'Nama file gambar tidak valid.');
                    header('Location: ' . admin_url('/admin/products'));
                    exit;
                }
                $firstPath = (string) $imagePaths[0];
                $sourceFilename = basename($firstPath);
                $sourceExt = strtolower((string) pathinfo($sourceFilename, PATHINFO_EXTENSION));
                $typedExt = strtolower((string) pathinfo($typedName, PATHINFO_EXTENSION));
                if ($typedExt === '' && $sourceExt !== '') {
                    $typedName .= '.' . $sourceExt;
                    $typedExt = $sourceExt;
                }
                if (!product_image_allowed_ext($typedExt)) {
                    admin_set_flash('error', 'Ekstensi file gambar tidak didukung.');
                    header('Location: ' . admin_url('/admin/products'));
                    exit;
                }
                $sourceAbs = $productImageDirAbs . DIRECTORY_SEPARATOR . $sourceFilename;
                $targetAbs = $productImageDirAbs . DIRECTORY_SEPARATOR . $typedName;
                if (is_file($sourceAbs) && $sourceFilename !== $typedName) {
                    if (is_file($targetAbs)) {
                        admin_set_flash('error', 'Nama file tujuan sudah ada di repository.');
                        header('Location: ' . admin_url('/admin/products'));
                        exit;
                    }
                    if (!@rename($sourceAbs, $targetAbs)) {
                        admin_set_flash('error', 'Gagal rename file gambar di repository.');
                        header('Location: ' . admin_url('/admin/products'));
                        exit;
                    }
                    $oldPath = $productImageDirRel . '/' . $sourceFilename;
                    $newPath = $productImageDirRel . '/' . $typedName;
                    $resUpdateRefs = admin_db()->query("SELECT id, image_path FROM products WHERE image_path LIKE '%" . admin_db()->real_escape_string($sourceFilename) . "%'");
                    if ($resUpdateRefs) {
                        while ($rw = $resUpdateRefs->fetch_assoc()) {
                            $rowId = (int) ($rw['id'] ?? 0);
                            $rowPath = (string) ($rw['image_path'] ?? '');
                            $parts = parse_product_image_paths($rowPath);
                            $changed = false;
                            foreach ($parts as $idxPart => $part) {
                                if ($part === $oldPath) {
                                    $parts[$idxPart] = $newPath;
                                    $changed = true;
                                }
                            }
                            if ($changed && $rowId > 0) {
                                $merged = build_product_image_paths($parts);
                                $st = admin_db()->prepare("UPDATE products SET image_path = ? WHERE id = ?");
                                if ($st) {
                                    $st->bind_param('si', $merged, $rowId);
                                    $st->execute();
                                    $st->close();
                                }
                            }
                        }
                    }
                    $imagePaths[0] = $newPath;
                }
            }

            if (!empty($imagePaths) && $primaryImageInput !== '') {
                $primaryName = normalize_image_filename($primaryImageInput);
                if ($primaryName !== '') {
                    $primaryPath = $productImageDirRel . '/' . $primaryName;
                    $idxPrimary = array_search($primaryPath, $imagePaths, true);
                    if ($idxPrimary !== false) {
                        unset($imagePaths[$idxPrimary]);
                        array_unshift($imagePaths, $primaryPath);
                        $imagePaths = array_values($imagePaths);
                    }
                }
            }

            $imagePath = build_product_image_paths($imagePaths);
        }

        if ($id > 0) {
            if ($hasCategoryId && $hasImagePath) {
                if ($hasFullDescription) {
                    $sql = "UPDATE products SET sku=?, slug=?, name=?, description=?, full_description=?, price=?, stock=?, is_active=?, category_id=?, image_path=? WHERE id=?";
                } else {
                    $sql = "UPDATE products SET sku=?, slug=?, name=?, description=?, price=?, stock=?, is_active=?, category_id=?, image_path=? WHERE id=?";
                }
                $stmt = admin_db()->prepare($sql);
                if ($stmt) {
                    if ($hasFullDescription) {
                        $stmt->bind_param('sssssdiiisi', $sku, $slug, $name, $description, $fullDescription, $price, $stock, $isActive, $categoryIdValue, $imagePath, $id);
                    } else {
                        $stmt->bind_param('ssssdiiisi', $sku, $slug, $name, $description, $price, $stock, $isActive, $categoryIdValue, $imagePath, $id);
                    }
                    $stmt->execute();
                    $stmt->close();
                }
            } elseif ($hasCategoryId) {
                if ($hasFullDescription) {
                    $sql = "UPDATE products SET sku=?, slug=?, name=?, description=?, full_description=?, price=?, stock=?, is_active=?, category_id=? WHERE id=?";
                } else {
                    $sql = "UPDATE products SET sku=?, slug=?, name=?, description=?, price=?, stock=?, is_active=?, category_id=? WHERE id=?";
                }
                $stmt = admin_db()->prepare($sql);
                if ($stmt) {
                    if ($hasFullDescription) {
                        $stmt->bind_param('sssssdiiii', $sku, $slug, $name, $description, $fullDescription, $price, $stock, $isActive, $categoryIdValue, $id);
                    } else {
                        $stmt->bind_param('ssssdiiii', $sku, $slug, $name, $description, $price, $stock, $isActive, $categoryIdValue, $id);
                    }
                    $stmt->execute();
                    $stmt->close();
                }
            } elseif ($hasImagePath) {
                if ($hasFullDescription) {
                    $sql = "UPDATE products SET sku=?, slug=?, name=?, description=?, full_description=?, price=?, stock=?, is_active=?, image_path=? WHERE id=?";
                } else {
                    $sql = "UPDATE products SET sku=?, slug=?, name=?, description=?, price=?, stock=?, is_active=?, image_path=? WHERE id=?";
                }
                $stmt = admin_db()->prepare($sql);
                if ($stmt) {
                    if ($hasFullDescription) {
                        $stmt->bind_param('sssssdiisi', $sku, $slug, $name, $description, $fullDescription, $price, $stock, $isActive, $imagePath, $id);
                    } else {
                        $stmt->bind_param('ssssdiisi', $sku, $slug, $name, $description, $price, $stock, $isActive, $imagePath, $id);
                    }
                    $stmt->execute();
                    $stmt->close();
                }
            } else {
                if ($hasFullDescription) {
                    $sql = "UPDATE products SET sku=?, slug=?, name=?, description=?, full_description=?, price=?, stock=?, is_active=? WHERE id=?";
                } else {
                    $sql = "UPDATE products SET sku=?, slug=?, name=?, description=?, price=?, stock=?, is_active=? WHERE id=?";
                }
                $stmt = admin_db()->prepare($sql);
                if ($stmt) {
                    if ($hasFullDescription) {
                        $stmt->bind_param('sssssdiii', $sku, $slug, $name, $description, $fullDescription, $price, $stock, $isActive, $id);
                    } else {
                        $stmt->bind_param('ssssdiii', $sku, $slug, $name, $description, $price, $stock, $isActive, $id);
                    }
                    $stmt->execute();
                    $stmt->close();
                }
            }
            admin_set_flash('success', 'Produk berhasil diperbarui.');
        } else {
            if ($hasCategoryId && $hasImagePath) {
                if ($hasFullDescription) {
                    $sql = "INSERT INTO products (sku, slug, name, description, full_description, price, stock, is_active, category_id, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                } else {
                    $sql = "INSERT INTO products (sku, slug, name, description, price, stock, is_active, category_id, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                }
                $stmt = admin_db()->prepare($sql);
                if ($stmt) {
                    if ($hasFullDescription) {
                        $stmt->bind_param('sssssdiiis', $sku, $slug, $name, $description, $fullDescription, $price, $stock, $isActive, $categoryIdValue, $imagePath);
                    } else {
                        $stmt->bind_param('ssssdiiis', $sku, $slug, $name, $description, $price, $stock, $isActive, $categoryIdValue, $imagePath);
                    }
                    $stmt->execute();
                    $stmt->close();
                }
            } elseif ($hasCategoryId) {
                if ($hasFullDescription) {
                    $sql = "INSERT INTO products (sku, slug, name, description, full_description, price, stock, is_active, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                } else {
                    $sql = "INSERT INTO products (sku, slug, name, description, price, stock, is_active, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                }
                $stmt = admin_db()->prepare($sql);
                if ($stmt) {
                    if ($hasFullDescription) {
                        $stmt->bind_param('sssssdiii', $sku, $slug, $name, $description, $fullDescription, $price, $stock, $isActive, $categoryIdValue);
                    } else {
                        $stmt->bind_param('ssssdiii', $sku, $slug, $name, $description, $price, $stock, $isActive, $categoryIdValue);
                    }
                    $stmt->execute();
                    $stmt->close();
                }
            } elseif ($hasImagePath) {
                if ($hasFullDescription) {
                    $sql = "INSERT INTO products (sku, slug, name, description, full_description, price, stock, is_active, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                } else {
                    $sql = "INSERT INTO products (sku, slug, name, description, price, stock, is_active, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                }
                $stmt = admin_db()->prepare($sql);
                if ($stmt) {
                    if ($hasFullDescription) {
                        $stmt->bind_param('sssssdiis', $sku, $slug, $name, $description, $fullDescription, $price, $stock, $isActive, $imagePath);
                    } else {
                        $stmt->bind_param('ssssdiis', $sku, $slug, $name, $description, $price, $stock, $isActive, $imagePath);
                    }
                    $stmt->execute();
                    $stmt->close();
                }
            } else {
                if ($hasFullDescription) {
                    $sql = "INSERT INTO products (sku, slug, name, description, full_description, price, stock, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                } else {
                    $sql = "INSERT INTO products (sku, slug, name, description, price, stock, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)";
                }
                $stmt = admin_db()->prepare($sql);
                if ($stmt) {
                    if ($hasFullDescription) {
                        $stmt->bind_param('sssssdii', $sku, $slug, $name, $description, $fullDescription, $price, $stock, $isActive);
                    } else {
                        $stmt->bind_param('ssssdii', $sku, $slug, $name, $description, $price, $stock, $isActive);
                    }
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

$repositoryImages = $hasImagePath ? list_product_images($productImageDirAbs) : [];

$rows = [];
$fullDescriptionSelect = $hasFullDescription
    ? "p.full_description"
    : "'' AS full_description";
if ($hasCategories && $hasCategoryId && $hasImagePath) {
    $sql = "SELECT p.id, p.sku, p.slug, p.name, p.description, {$fullDescriptionSelect}, p.price, p.stock, p.is_active, p.created_at, p.image_path, p.category_id, pc.name AS category_name
            FROM products p
            LEFT JOIN product_categories pc ON pc.id = p.category_id
            ORDER BY p.created_at DESC
            LIMIT 150";
} elseif ($hasCategories && $hasCategoryId) {
    $sql = "SELECT p.id, p.sku, p.slug, p.name, p.description, {$fullDescriptionSelect}, p.price, p.stock, p.is_active, p.created_at, NULL AS image_path, p.category_id, pc.name AS category_name
            FROM products p
            LEFT JOIN product_categories pc ON pc.id = p.category_id
            ORDER BY p.created_at DESC
            LIMIT 150";
} elseif ($hasImagePath) {
    $sql = "SELECT p.id, p.sku, p.slug, p.name, p.description, {$fullDescriptionSelect}, p.price, p.stock, p.is_active, p.created_at, p.image_path, NULL AS category_id, NULL AS category_name
            FROM products p
            ORDER BY p.created_at DESC
            LIMIT 150";
} else {
    $sql = "SELECT p.id, p.sku, p.slug, p.name, p.description, {$fullDescriptionSelect}, p.price, p.stock, p.is_active, p.created_at, NULL AS image_path, NULL AS category_id, NULL AS category_name
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
<div class="mb-4 flex justify-end">
  <button id="productFormToggleButton" type="button" class="px-4 py-2 bg-blue-800 text-white rounded-lg font-semibold">Buka Form Produk</button>
</div>
<div class="bg-white border border-slate-200 rounded-2xl p-5 mb-6">
  <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-3">
    <h2 class="font-bold text-lg">Form Produk</h2>
    <span id="formModeBadge" class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Mode Tambah Produk Baru</span>
  </div>
  <div id="productFormWrapper" class="hidden">
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
      <div class="md:col-span-2 xl:col-span-3 grid md:grid-cols-3 gap-3 border border-slate-200 rounded-xl p-3">
        <div class="md:col-span-2">
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pilih Multi Gambar Dari Repository</label>
          <div id="formSelectedImageList" class="rounded-lg border border-slate-300 bg-white max-h-56 overflow-auto p-2 space-y-1">
            <?php foreach ($repositoryImages as $imgName): ?>
              <label class="flex items-center gap-2 rounded px-2 py-1.5 hover:bg-slate-50 cursor-pointer">
                <input type="checkbox" class="repo-image-checkbox rounded border-slate-300" name="selected_images[]" value="<?= admin_e($imgName) ?>">
                <span class="text-sm text-slate-700"><?= admin_e($imgName) ?></span>
              </label>
            <?php endforeach; ?>
            <?php if (!$repositoryImages): ?>
              <div class="px-2 py-1 text-xs text-slate-500">Belum ada gambar di repository.</div>
            <?php endif; ?>
          </div>
          <input type="hidden" id="formSelectedImageSingle" name="selected_image" value="">
          <input type="hidden" id="formOrderedImages" name="ordered_images" value="">
          <input type="hidden" id="formPrimaryImage" name="primary_image" value="">
          <div class="mt-2 text-xs text-slate-500">Centang gambar yang akan dipakai, lalu atur urutan/primary di panel preview.</div>
        </div>
        <div class="flex items-start">
          <div class="w-full border border-slate-200 rounded-lg p-2 bg-slate-50">
            <div class="text-[11px] uppercase font-bold text-slate-500 mb-2">Preview Gallery</div>
            <div id="formImagePreviewList" class="grid grid-cols-3 gap-2"></div>
            <div id="formImagePreviewEmpty" class="w-full max-w-[220px] aspect-square flex items-center justify-center rounded-md border border-dashed border-slate-300 text-xs text-slate-500 bg-white">Belum ada gambar</div>
          </div>
        </div>
        <div>
          <label for="formImageFilename" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama File Gambar (Opsional Rename)</label>
          <input id="formImageFilename" name="image_filename" placeholder="produk_1.png" class="rounded-lg border-slate-300 w-full">
          <div class="mt-2 text-xs text-slate-500">Jika diisi saat edit, file repository akan di-rename dan path produk ikut diperbarui.</div>
        </div>
        <div>
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Upload Gambar Baru (Multi)</label>
          <label class="inline-flex w-full justify-center px-3 py-2 rounded-lg border border-slate-300 bg-slate-50 text-slate-700 font-semibold cursor-pointer">
            Pilih File
            <input id="formImageFile" name="image_files[]" type="file" accept=".png,.jpg,.jpeg,.webp" multiple class="hidden">
          </label>
          <div class="mt-2 text-xs text-slate-500">Upload akan membuat file baru di repository.</div>
        </div>
      </div>
    <?php endif; ?>
    <div class="md:col-span-2 xl:col-span-2">
      <label for="formDescription" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Deskripsi Singkat</label>
      <input id="formDescription" name="description" placeholder="Deskripsi singkat produk" class="rounded-lg border-slate-300 w-full">
    </div>
    <div class="md:col-span-2 xl:col-span-3">
      <label for="formFullDescription" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Deskripsi Lengkap</label>
      <textarea id="formFullDescription" name="full_description" rows="6" placeholder="Deskripsi lengkap produk untuk halaman detail, spesifikasi, penggunaan, dll." class="rounded-lg border-slate-300 w-full"></textarea>
    </div>
    <label class="inline-flex items-center gap-2 text-sm font-semibold">
      <input id="formIsActive" type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300"> Active
    </label>
    <div class="md:col-span-2 xl:col-span-3 flex flex-wrap items-center gap-2">
      <button id="formSubmitButton" class="px-4 py-2 bg-blue-800 text-white rounded-lg font-semibold w-full sm:w-auto">Simpan Produk</button>
      <button id="formResetButton" type="button" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg font-semibold w-full sm:w-auto">Reset</button>
      <button id="formCancelEditButton" type="button" class="px-4 py-2 border border-red-300 text-red-700 rounded-lg font-semibold hidden w-full sm:w-auto">Batal Edit</button>
    </div>
  </form>
  </div>
</div>

<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
  <div class="p-5 border-b border-slate-200">
    <h2 class="font-bold text-lg">Daftar Produk</h2>
    <p class="text-sm text-slate-500 mt-1">Data ini langsung dipakai oleh halaman Shop.</p>
  </div>
  <div class="md:hidden p-4 space-y-3">
    <?php foreach ($rows as $r): ?>
      <?php
        $imagePath = (string) ($r['image_path'] ?? '');
        $imagePaths = parse_product_image_paths($imagePath);
        $firstImagePath = (string) ($imagePaths[0] ?? '');
        $imageFilename = $firstImagePath !== '' ? basename($firstImagePath) : '';
        $imageCount = count($imagePaths);
      ?>
      <div class="rounded-xl border border-slate-200 p-4">
        <div class="flex items-start gap-3">
          <?php if ($firstImagePath !== ''): ?>
            <img src="<?= admin_e(admin_url('/' . ltrim($firstImagePath, '/'))) ?>" alt="<?= admin_e((string) $r['name']) ?>" class="w-16 h-16 min-w-16 object-contain rounded-lg border border-slate-200 bg-white p-0.5">
          <?php else: ?>
            <div class="w-16 h-16 min-w-16 rounded-lg border border-dashed border-slate-300 text-[10px] text-slate-500 flex items-center justify-center">No Image</div>
          <?php endif; ?>
          <div class="min-w-0">
            <div class="font-semibold text-slate-800"><?= admin_e((string) $r['name']) ?></div>
            <div class="text-xs text-slate-500 line-clamp-2"><?= admin_e((string) ($r['description'] ?? '')) ?></div>
            <?php if ($imageFilename !== ''): ?>
              <div class="text-[11px] text-slate-400 mt-1 truncate"><?= admin_e($imageFilename) ?><?= $imageCount > 1 ? ' (+' . (int) ($imageCount - 1) . ')' : '' ?></div>
            <?php endif; ?>
          </div>
        </div>
        <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
          <div><span class="text-slate-500">SKU:</span> <span class="font-semibold"><?= admin_e((string) $r['sku']) ?></span></div>
          <div><span class="text-slate-500">Slug:</span> <span class="font-semibold"><?= admin_e((string) $r['slug']) ?></span></div>
          <div><span class="text-slate-500">Kategori:</span> <span class="font-semibold"><?= admin_e((string) ($r['category_name'] ?? '-')) ?></span></div>
          <div><span class="text-slate-500">Stok:</span> <span class="font-semibold"><?= (int) $r['stock'] ?></span></div>
          <div class="col-span-2"><span class="text-slate-500">Harga:</span> <span class="font-semibold">Rp <?= number_format((float) $r['price'], 0, ',', '.') ?></span></div>
        </div>
        <div class="mt-2">
          <span class="px-2 py-1 rounded-full text-xs font-bold <?= (int) $r['is_active'] === 1 ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' ?>">
            <?= (int) $r['is_active'] === 1 ? 'active' : 'inactive' ?>
          </span>
        </div>
        <div class="mt-3 flex flex-col gap-2">
          <form method="post">
            <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
            <input type="hidden" name="action" value="toggle">
            <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
            <input type="hidden" name="is_active" value="<?= (int) $r['is_active'] === 1 ? 0 : 1 ?>">
            <button class="px-3 py-2 rounded-lg text-white font-semibold w-full <?= (int) $r['is_active'] === 1 ? 'bg-amber-500' : 'bg-emerald-600' ?>">
              <?= (int) $r['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?>
            </button>
          </form>
          <button
            type="button"
            class="px-3 py-2 rounded-lg bg-slate-800 text-white font-semibold btn-edit-product w-full"
            data-id="<?= (int) $r['id'] ?>"
            data-sku="<?= admin_e((string) $r['sku']) ?>"
            data-slug="<?= admin_e((string) $r['slug']) ?>"
            data-name="<?= admin_e((string) $r['name']) ?>"
            data-description="<?= admin_e((string) ($r['description'] ?? '')) ?>"
            data-full-description="<?= admin_e((string) ($r['full_description'] ?? '')) ?>"
            data-price="<?= (float) $r['price'] ?>"
            data-stock="<?= (int) $r['stock'] ?>"
            data-is-active="<?= (int) $r['is_active'] ?>"
            data-category-id="<?= (int) ($r['category_id'] ?? 0) ?>"
            data-image-filename="<?= admin_e($imageFilename) ?>"
            data-image-path="<?= admin_e($imagePath) ?>"
          >
            Edit
          </button>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (!$rows): ?>
      <div class="text-center text-sm text-slate-500 py-4">Belum ada data produk.</div>
    <?php endif; ?>
  </div>

  <div class="hidden md:block overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] tracking-wider">
      <tr>
        <th class="px-4 py-3 text-left">Gambar</th>
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
        <?php
          $imagePath = (string) ($r['image_path'] ?? '');
          $imagePaths = parse_product_image_paths($imagePath);
          $firstImagePath = (string) ($imagePaths[0] ?? '');
          $imageFilename = $firstImagePath !== '' ? basename($firstImagePath) : '';
          $imageCount = count($imagePaths);
        ?>
        <tr>
          <td class="px-4 py-3">
            <?php if ($firstImagePath !== ''): ?>
              <img src="<?= admin_e(admin_url('/' . ltrim($firstImagePath, '/'))) ?>" alt="<?= admin_e((string) $r['name']) ?>" class="w-14 h-14 min-w-14 object-contain rounded-lg border border-slate-200 bg-white p-0.5">
            <?php else: ?>
              <div class="w-14 h-14 min-w-14 rounded-lg border border-dashed border-slate-300 text-[10px] text-slate-500 flex items-center justify-center">No Image</div>
            <?php endif; ?>
          </td>
          <td class="px-4 py-3">
            <div class="font-semibold text-slate-800"><?= admin_e((string) $r['name']) ?></div>
            <div class="text-xs text-slate-500 line-clamp-1"><?= admin_e((string) ($r['description'] ?? '')) ?></div>
            <?php if ($imageFilename !== ''): ?>
              <div class="text-[11px] text-slate-400 mt-1"><?= admin_e($imageFilename) ?><?= $imageCount > 1 ? ' (+' . (int) ($imageCount - 1) . ')' : '' ?></div>
            <?php endif; ?>
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
            <div class="flex flex-wrap justify-end gap-2">
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
                data-full-description="<?= admin_e((string) ($r['full_description'] ?? '')) ?>"
                data-price="<?= (float) $r['price'] ?>"
                data-stock="<?= (int) $r['stock'] ?>"
                data-is-active="<?= (int) $r['is_active'] ?>"
                data-category-id="<?= (int) ($r['category_id'] ?? 0) ?>"
                data-image-filename="<?= admin_e($imageFilename) ?>"
                data-image-path="<?= admin_e($imagePath) ?>"
              >
                Edit
              </button>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?>
        <tr><td colspan="8" class="px-4 py-5 text-center text-slate-500">Belum ada data produk.</td></tr>
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
  const fullDescEl = document.getElementById('formFullDescription');
  const priceEl = document.getElementById('formPrice');
  const stockEl = document.getElementById('formStock');
  const activeEl = document.getElementById('formIsActive');
  const categoryEl = document.getElementById('formCategoryId');
  const selectedImageListEl = document.getElementById('formSelectedImageList');
	 const selectedImageSingleEl = document.getElementById('formSelectedImageSingle');
	  const orderedImagesEl = document.getElementById('formOrderedImages');
	  const primaryImageEl = document.getElementById('formPrimaryImage');
	  const imageFilenameEl = document.getElementById('formImageFilename');
	  const imageFileEl = document.getElementById('formImageFile');
	  const imagePreviewListEl = document.getElementById('formImagePreviewList');
  const imagePreviewEmptyEl = document.getElementById('formImagePreviewEmpty');
  const submitEl = document.getElementById('formSubmitButton');
  const resetEl = document.getElementById('formResetButton');
	  const cancelEditEl = document.getElementById('formCancelEditButton');
  const modeBadgeEl = document.getElementById('formModeBadge');
  const modeHintEl = document.getElementById('formModeHint');
  const formWrapperEl = document.getElementById('productFormWrapper');
  const formToggleButtonEl = document.getElementById('productFormToggleButton');
  let isFormOpen = true;
		  let uploadedPreviewUrls = [];
		  let currentImagePaths = [];
		  let selectedOrder = [];
		  let primaryImageName = '';

  function setFormVisibility(open) {
    isFormOpen = !!open;
    if (formWrapperEl) {
      formWrapperEl.classList.toggle('hidden', !isFormOpen);
    }
    if (formToggleButtonEl) {
      formToggleButtonEl.textContent = isFormOpen ? 'Tutup Form Produk' : 'Buka Form Produk';
    }
  }

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

  function clearUploadedPreview() {
    uploadedPreviewUrls.forEach((url) => URL.revokeObjectURL(url));
    uploadedPreviewUrls = [];
  }

	  function clearPreviewList() {
	    if (!imagePreviewListEl) return;
	    imagePreviewListEl.innerHTML = '';
	  }

  function selectedImageValues() {
    return Array.from(document.querySelectorAll('.repo-image-checkbox:checked')).map((el) => el.value).filter(Boolean);
  }

	  function syncSelectedOrderFromSelect() {
	    const selected = new Set(selectedImageValues());
	    selectedOrder = selectedOrder.filter((name) => selected.has(name));
	    selected.forEach((name) => {
	      if (!selectedOrder.includes(name)) {
	        selectedOrder.push(name);
	      }
	    });
	  }

  function syncSelectedOrderToSelect() {
    if (!selectedImageListEl) return;
    const selected = new Set(selectedOrder);
    document.querySelectorAll('.repo-image-checkbox').forEach((el) => {
      el.checked = selected.has(el.value);
    });
  }

	  function ensurePrimaryInSelection() {
	    if (!selectedOrder.length) {
	      primaryImageName = '';
	      return;
	    }
	    if (!primaryImageName || !selectedOrder.includes(primaryImageName)) {
	      primaryImageName = selectedOrder[0];
	    }
	  }

	  function moveSelectedOrder(name, dir) {
	    const idx = selectedOrder.indexOf(name);
	    if (idx < 0) return;
	    const target = idx + dir;
	    if (target < 0 || target >= selectedOrder.length) return;
	    const tmp = selectedOrder[target];
	    selectedOrder[target] = selectedOrder[idx];
	    selectedOrder[idx] = tmp;
	  }

	  function renderOrderedPreview() {
	    if (!imagePreviewListEl) return;
	    clearPreviewList();
	    let count = 0;
	    selectedOrder.forEach((name, idx) => {
	      const wrap = document.createElement('div');
	      wrap.className = 'rounded-md border border-slate-200 bg-white p-1';

	      const img = document.createElement('img');
	      img.src = '<?= admin_e(admin_url('/' . trim($productImageDirRel, '/'))) ?>/' + encodeURIComponent(name);
	      img.alt = name;
	      img.className = 'w-full aspect-square object-contain rounded-sm bg-white';
	      wrap.appendChild(img);

	      const info = document.createElement('div');
	      info.className = 'mt-1 flex items-center justify-between gap-1';

	      const order = document.createElement('span');
	      order.className = 'text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-700';
	      order.textContent = '#' + (idx + 1);
	      info.appendChild(order);

	      const actions = document.createElement('div');
	      actions.className = 'flex items-center gap-1';

	      const primaryBtn = document.createElement('button');
	      primaryBtn.type = 'button';
	      primaryBtn.className = primaryImageName === name ? 'text-[10px] px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-700 font-bold' : 'text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-600';
	      primaryBtn.textContent = primaryImageName === name ? 'Primary' : 'Set';
	      primaryBtn.addEventListener('click', function () {
	        primaryImageName = name;
	        renderOrderedPreview();
	        persistImageMeta();
	      });
	      actions.appendChild(primaryBtn);

	      const upBtn = document.createElement('button');
	      upBtn.type = 'button';
	      upBtn.className = 'text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-700';
	      upBtn.textContent = '↑';
	      upBtn.disabled = idx === 0;
	      upBtn.addEventListener('click', function () {
	        moveSelectedOrder(name, -1);
	        syncSelectedOrderToSelect();
	        ensurePrimaryInSelection();
	        renderOrderedPreview();
	        persistImageMeta();
	      });
	      actions.appendChild(upBtn);

	      const downBtn = document.createElement('button');
	      downBtn.type = 'button';
	      downBtn.className = 'text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-700';
	      downBtn.textContent = '↓';
	      downBtn.disabled = idx === selectedOrder.length - 1;
	      downBtn.addEventListener('click', function () {
	        moveSelectedOrder(name, 1);
	        syncSelectedOrderToSelect();
	        ensurePrimaryInSelection();
	        renderOrderedPreview();
	        persistImageMeta();
	      });
	      actions.appendChild(downBtn);

	      info.appendChild(actions);
	      wrap.appendChild(info);
	      imagePreviewListEl.appendChild(wrap);
	      count++;
	    });

	    const uploadedFiles = Array.from(imageFileEl?.files || []);
	    uploadedFiles.forEach((file) => {
	      const wrap = document.createElement('div');
	      wrap.className = 'rounded-md border border-blue-200 bg-blue-50 p-1';
	      const url = URL.createObjectURL(file);
	      uploadedPreviewUrls.push(url);
	      const img = document.createElement('img');
	      img.src = url;
	      img.alt = file.name;
	      img.className = 'w-full aspect-square object-contain rounded-sm bg-white';
	      wrap.appendChild(img);
	      const note = document.createElement('div');
	      note.className = 'mt-1 text-[10px] text-blue-700 font-semibold truncate';
	      note.textContent = 'Upload baru';
	      wrap.appendChild(note);
	      imagePreviewListEl.appendChild(wrap);
	      count++;
	    });

	    if (imagePreviewEmptyEl) {
	      imagePreviewEmptyEl.classList.toggle('hidden', count > 0);
	    }
	  }

	  function parseImagePathList(raw) {
	    if (!raw) return [];
	    return String(raw).split(/[|,]/).map((x) => x.trim()).filter(Boolean);
	  }

	  function persistImageMeta() {
	    if (orderedImagesEl) {
	      orderedImagesEl.value = JSON.stringify(selectedOrder);
	    }
	    if (primaryImageEl) {
	      primaryImageEl.value = primaryImageName || '';
	    }
	    if (selectedImageSingleEl) {
	      selectedImageSingleEl.value = selectedOrder[0] || '';
	    }
	  }

	  function updatePreview() {
	    clearUploadedPreview();
	    syncSelectedOrderFromSelect();
	    ensurePrimaryInSelection();
	    renderOrderedPreview();
	    persistImageMeta();
	  }

		  function resetFormState() {
		    idEl.value = '0';
		    currentImagePaths = [];
		    selectedOrder = [];
		    primaryImageName = '';
	    form.reset();
	    if (activeEl) activeEl.checked = true;
    if (imageFileEl) imageFileEl.value = '';
    document.querySelectorAll('.repo-image-checkbox').forEach((el) => {
      el.checked = false;
    });
	    if (selectedImageSingleEl) selectedImageSingleEl.value = '';
	    if (orderedImagesEl) orderedImagesEl.value = '';
	    if (primaryImageEl) primaryImageEl.value = '';
		    clearUploadedPreview();
		    updatePreview();
		    setAddMode();
    setFormVisibility(true);
	  }

  document.querySelectorAll('.btn-edit-product').forEach((btn) => {
    btn.addEventListener('click', function () {
      idEl.value = this.dataset.id || '0';
      skuEl.value = this.dataset.sku || '';
      slugEl.value = this.dataset.slug || '';
	      nameEl.value = this.dataset.name || '';
	      descEl.value = this.dataset.description || '';
      if (fullDescEl) fullDescEl.value = this.dataset.fullDescription || '';
	      priceEl.value = this.dataset.price || '';
	      stockEl.value = this.dataset.stock || '';
	      currentImagePaths = parseImagePathList(this.dataset.imagePath || '');
	      selectedOrder = currentImagePaths.map((p) => p.split('/').pop()).filter(Boolean);
	      primaryImageName = selectedOrder[0] || '';
      if (activeEl) activeEl.checked = (this.dataset.isActive || '0') === '1';
      if (categoryEl) categoryEl.value = this.dataset.categoryId || '0';
      syncSelectedOrderToSelect();
      if (imageFilenameEl) imageFilenameEl.value = this.dataset.imageFilename || '';
      if (imageFileEl) imageFileEl.value = '';
      clearUploadedPreview();
      updatePreview();
	      setEditMode(this.dataset.id || '0');
      setFormVisibility(true);
	      form.scrollIntoView({ behavior: 'smooth', block: 'start' });
	    });
	  });

  selectedImageListEl?.addEventListener('change', function (e) {
    const target = e.target;
    if (!(target instanceof HTMLInputElement) || !target.classList.contains('repo-image-checkbox')) return;
    syncSelectedOrderFromSelect();
    ensurePrimaryInSelection();
    const first = selectedOrder[0] || '';
	    if (imageFilenameEl && first) {
	      imageFilenameEl.value = first;
	    }
	    updatePreview();
	  });

  imageFileEl?.addEventListener('change', function () {
    const file = this.files && this.files[0];
    if (file && imageFilenameEl) {
      imageFilenameEl.value = file.name;
    }
    updatePreview();
  });

	  resetEl?.addEventListener('click', resetFormState);
	  cancelEditEl?.addEventListener('click', resetFormState);
  formToggleButtonEl?.addEventListener('click', function () {
    setFormVisibility(!isFormOpen);
  });
  setAddMode();
  setFormVisibility(false);
  updatePreview();
});
</script>

<?php admin_render_end(); ?>
