<?php
declare(strict_types=1);
require_once __DIR__ . '/inc/layout.php';
admin_require_login();

if (!admin_table_exists('posts')) {
    admin_set_flash('error', 'Tabel posts belum tersedia.');
    header('Location: ' . admin_url('/admin/'));
    exit;
}

$hasPostCategories = admin_table_exists('post_categories');
$hasPostTranslations = admin_table_exists('post_translations');
$hasLanguages = admin_table_exists('languages');
$canOverrideAuthor = admin_has_any_role(['super_admin']);
$hasAuthorDisplayName = false;
$checkAuthorCol = admin_db()->prepare("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='posts' AND COLUMN_NAME='author_display_name'");
if ($checkAuthorCol) {
    $checkAuthorCol->execute();
    $authorColRow = $checkAuthorCol->get_result()->fetch_assoc();
    $hasAuthorDisplayName = ((int) ($authorColRow['cnt'] ?? 0)) > 0;
    $checkAuthorCol->close();
}

$postImageDirRel = 'assets/content/blog/images';
$postImageDirAbs = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $postImageDirRel);
if (!is_dir($postImageDirAbs)) {
    @mkdir($postImageDirAbs, 0775, true);
}

function normalize_post_image_filename(string $name): string
{
    $name = trim(str_replace(['\\', '/'], '', $name));
    $name = preg_replace('/[^A-Za-z0-9._-]/', '-', $name) ?? '';
    return trim($name, '.- _');
}

function post_image_allowed_ext(string $ext): bool
{
    return in_array(strtolower($ext), ['png', 'jpg', 'jpeg', 'webp'], true);
}

function list_post_images(string $dirAbs): array
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
        if (!post_image_allowed_ext((string) pathinfo($file, PATHINFO_EXTENSION))) {
            continue;
        }
        $images[] = $file;
    }
    natcasesort($images);
    return array_values($images);
}

function admin_blog_language_id(): ?int
{
    if (!admin_table_exists('languages')) {
        return null;
    }
    $res = admin_db()->query("SELECT id FROM languages WHERE code = 'id' LIMIT 1");
    if ($res) {
        $row = $res->fetch_assoc();
        if ($row && (int) ($row['id'] ?? 0) > 0) {
            return (int) $row['id'];
        }
    }
    $resAny = admin_db()->query("SELECT id FROM languages ORDER BY id ASC LIMIT 1");
    if ($resAny) {
        $rowAny = $resAny->fetch_assoc();
        if ($rowAny && (int) ($rowAny['id'] ?? 0) > 0) {
            return (int) $rowAny['id'];
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!admin_verify_csrf(is_string($token) ? $token : null)) {
        admin_set_flash('error', 'Token keamanan tidak valid.');
        header('Location: ' . admin_url('/admin/posts'));
        exit;
    }

    $action = (string) ($_POST['action'] ?? '');
    if ($action === 'toggle_status') {
        $id = (int) ($_POST['id'] ?? 0);
        $status = (string) ($_POST['status'] ?? '');
        $allowed = ['draft', 'published', 'archived'];
        if ($id > 0 && in_array($status, $allowed, true)) {
            $sql = "UPDATE posts SET status = ?, published_at = IF(?='published' AND published_at IS NULL, NOW(), published_at) WHERE id = ?";
            $stmt = admin_db()->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('ssi', $status, $status, $id);
                $stmt->execute();
                $stmt->close();
                admin_set_flash('success', 'Status post berhasil diperbarui.');
            }
        }
        header('Location: ' . admin_url('/admin/posts'));
        exit;
    }

    if ($action === 'save_post') {
        $id = (int) ($_POST['id'] ?? 0);
        $title = trim((string) ($_POST['title'] ?? ''));
        $slug = strtolower(trim((string) ($_POST['slug'] ?? '')));
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug) ?? '';
        $slug = trim($slug, '-');
        $excerpt = trim((string) ($_POST['excerpt'] ?? ''));
        $content = trim((string) ($_POST['content'] ?? ''));
        $seoTitle = trim((string) ($_POST['seo_title'] ?? ''));
        $seoDesc = trim((string) ($_POST['seo_description'] ?? ''));
        $featuredImage = trim((string) ($_POST['featured_image'] ?? ''));
        $selectedFeaturedImage = trim((string) ($_POST['selected_featured_image'] ?? ''));
        $imageFilenameInput = trim((string) ($_POST['image_filename'] ?? ''));
        $authorDisplayName = trim((string) ($_POST['author_display_name'] ?? ''));
        if (!$canOverrideAuthor || !$hasAuthorDisplayName) {
            $authorDisplayName = '';
        }
        $status = (string) ($_POST['status'] ?? 'draft');
        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $categoryId = $categoryId > 0 ? $categoryId : 0;
        $allowed = ['draft', 'published', 'archived'];

        if (!in_array($status, $allowed, true)) {
            $status = 'draft';
        }

        $selectedFeaturedName = normalize_post_image_filename($selectedFeaturedImage);
        if ($selectedFeaturedName !== '') {
            $selectedExt = strtolower((string) pathinfo($selectedFeaturedName, PATHINFO_EXTENSION));
            $selectedAbs = $postImageDirAbs . DIRECTORY_SEPARATOR . $selectedFeaturedName;
            if (post_image_allowed_ext($selectedExt) && is_file($selectedAbs)) {
                $featuredImage = $postImageDirRel . '/' . $selectedFeaturedName;
            }
        }

        $uploadedFeatured = $_FILES['featured_image_file'] ?? null;
        $hasFeaturedUpload = is_array($uploadedFeatured) && isset($uploadedFeatured['error']) && (int) $uploadedFeatured['error'] === UPLOAD_ERR_OK;
        if ($hasFeaturedUpload) {
            $originalName = (string) ($uploadedFeatured['name'] ?? '');
            $ext = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
            if (!post_image_allowed_ext($ext)) {
                admin_set_flash('error', 'Format featured image tidak didukung. Gunakan: png, jpg, jpeg, webp.');
                header('Location: ' . admin_url('/admin/posts'));
                exit;
            }
            $baseName = $imageFilenameInput !== '' ? (string) pathinfo($imageFilenameInput, PATHINFO_FILENAME) : (string) pathinfo($originalName, PATHINFO_FILENAME);
            $baseName = normalize_post_image_filename($baseName);
            if ($baseName === '') {
                $baseName = 'blog-' . time();
            }
            $finalFilename = $baseName . '.' . $ext;
            $targetAbs = $postImageDirAbs . DIRECTORY_SEPARATOR . $finalFilename;
            $suffix = 1;
            while (file_exists($targetAbs)) {
                $finalFilename = $baseName . '-' . $suffix . '.' . $ext;
                $targetAbs = $postImageDirAbs . DIRECTORY_SEPARATOR . $finalFilename;
                $suffix++;
            }
            if (!move_uploaded_file((string) $uploadedFeatured['tmp_name'], $targetAbs)) {
                admin_set_flash('error', 'Gagal upload featured image.');
                header('Location: ' . admin_url('/admin/posts'));
                exit;
            }
            $featuredImage = $postImageDirRel . '/' . $finalFilename;
        }

        $uploadedContentImages = $_FILES['content_image_files'] ?? null;
        if (is_array($uploadedContentImages) && isset($uploadedContentImages['name']) && is_array($uploadedContentImages['name'])) {
            $totalContentImages = count($uploadedContentImages['name']);
            for ($i = 0; $i < $totalContentImages; $i++) {
                $err = (int) ($uploadedContentImages['error'][$i] ?? UPLOAD_ERR_NO_FILE);
                if ($err !== UPLOAD_ERR_OK) {
                    continue;
                }
                $originalName = (string) ($uploadedContentImages['name'][$i] ?? '');
                $tmpName = (string) ($uploadedContentImages['tmp_name'][$i] ?? '');
                $ext = strtolower((string) pathinfo($originalName, PATHINFO_EXTENSION));
                if (!post_image_allowed_ext($ext)) {
                    continue;
                }
                $baseName = normalize_post_image_filename((string) pathinfo($originalName, PATHINFO_FILENAME));
                if ($baseName === '') {
                    $baseName = 'blog-content-' . time() . '-' . $i;
                }
                $finalFilename = $baseName . '.' . $ext;
                $targetAbs = $postImageDirAbs . DIRECTORY_SEPARATOR . $finalFilename;
                $suffix = 1;
                while (file_exists($targetAbs)) {
                    $finalFilename = $baseName . '-' . $suffix . '.' . $ext;
                    $targetAbs = $postImageDirAbs . DIRECTORY_SEPARATOR . $finalFilename;
                    $suffix++;
                }
                @move_uploaded_file($tmpName, $targetAbs);
            }
        }
        if ($hasPostTranslations && $hasLanguages) {
            if ($title === '' || $slug === '') {
                admin_set_flash('error', 'Judul dan slug wajib diisi.');
                header('Location: ' . admin_url('/admin/posts'));
                exit;
            }
        }

        $languageId = ($hasPostTranslations && $hasLanguages) ? admin_blog_language_id() : null;
        if ($hasPostTranslations && $hasLanguages && !$languageId) {
            admin_set_flash('error', 'Bahasa default belum tersedia. Tambahkan data di tabel languages terlebih dahulu.');
            header('Location: ' . admin_url('/admin/posts'));
            exit;
        }

        $db = admin_db();
        $db->begin_transaction();
        try {
            $editor = admin_current_user();
            $editorId = (int) ($editor['id'] ?? 0);
            $editorId = $editorId > 0 ? $editorId : null;
            $categoryValue = ($hasPostCategories && $categoryId > 0) ? $categoryId : null;

            if ($id > 0) {
                if ($hasPostCategories) {
                    if ($hasAuthorDisplayName && $canOverrideAuthor) {
                        $sqlPost = "UPDATE posts
                                    SET category_id = ?, featured_image = ?, author_display_name = ?, status = ?, updated_by = ?,
                                        published_at = IF(?='published' AND published_at IS NULL, NOW(), published_at)
                                    WHERE id = ?";
                    } else {
                        $sqlPost = "UPDATE posts
                                    SET category_id = ?, featured_image = ?, status = ?, updated_by = ?,
                                        published_at = IF(?='published' AND published_at IS NULL, NOW(), published_at)
                                    WHERE id = ?";
                    }
                    $stmtPost = $db->prepare($sqlPost);
                    if ($stmtPost) {
                        if ($hasAuthorDisplayName && $canOverrideAuthor) {
                            $stmtPost->bind_param('isssisi', $categoryValue, $featuredImage, $authorDisplayName, $status, $editorId, $status, $id);
                        } else {
                            $stmtPost->bind_param('issisi', $categoryValue, $featuredImage, $status, $editorId, $status, $id);
                        }
                        $stmtPost->execute();
                        $stmtPost->close();
                    }
                } else {
                    if ($hasAuthorDisplayName && $canOverrideAuthor) {
                        $sqlPost = "UPDATE posts
                                    SET featured_image = ?, author_display_name = ?, status = ?, updated_by = ?,
                                        published_at = IF(?='published' AND published_at IS NULL, NOW(), published_at)
                                    WHERE id = ?";
                    } else {
                        $sqlPost = "UPDATE posts
                                    SET featured_image = ?, status = ?, updated_by = ?,
                                        published_at = IF(?='published' AND published_at IS NULL, NOW(), published_at)
                                    WHERE id = ?";
                    }
                    $stmtPost = $db->prepare($sqlPost);
                    if ($stmtPost) {
                        if ($hasAuthorDisplayName && $canOverrideAuthor) {
                            $stmtPost->bind_param('sssisi', $featuredImage, $authorDisplayName, $status, $editorId, $status, $id);
                        } else {
                            $stmtPost->bind_param('ssisi', $featuredImage, $status, $editorId, $status, $id);
                        }
                        $stmtPost->execute();
                        $stmtPost->close();
                    }
                }
            } else {
                if ($hasPostCategories) {
                    if ($hasAuthorDisplayName && $canOverrideAuthor) {
                        $sqlPost = "INSERT INTO posts (category_id, featured_image, author_display_name, status, published_at, created_by, updated_by)
                                    VALUES (?, ?, ?, ?, IF(?='published', NOW(), NULL), ?, ?)";
                    } else {
                        $sqlPost = "INSERT INTO posts (category_id, featured_image, status, published_at, created_by, updated_by)
                                    VALUES (?, ?, ?, IF(?='published', NOW(), NULL), ?, ?)";
                    }
                    $stmtPost = $db->prepare($sqlPost);
                    if ($stmtPost) {
                        if ($hasAuthorDisplayName && $canOverrideAuthor) {
                            $stmtPost->bind_param('issssii', $categoryValue, $featuredImage, $authorDisplayName, $status, $status, $editorId, $editorId);
                        } else {
                            $stmtPost->bind_param('isssii', $categoryValue, $featuredImage, $status, $status, $editorId, $editorId);
                        }
                        $stmtPost->execute();
                        $id = (int) $stmtPost->insert_id;
                        $stmtPost->close();
                    }
                } else {
                    if ($hasAuthorDisplayName && $canOverrideAuthor) {
                        $sqlPost = "INSERT INTO posts (featured_image, author_display_name, status, published_at, created_by, updated_by)
                                    VALUES (?, ?, ?, IF(?='published', NOW(), NULL), ?, ?)";
                    } else {
                        $sqlPost = "INSERT INTO posts (featured_image, status, published_at, created_by, updated_by)
                                    VALUES (?, ?, IF(?='published', NOW(), NULL), ?, ?)";
                    }
                    $stmtPost = $db->prepare($sqlPost);
                    if ($stmtPost) {
                        if ($hasAuthorDisplayName && $canOverrideAuthor) {
                            $stmtPost->bind_param('ssssii', $featuredImage, $authorDisplayName, $status, $status, $editorId, $editorId);
                        } else {
                            $stmtPost->bind_param('sssii', $featuredImage, $status, $status, $editorId, $editorId);
                        }
                        $stmtPost->execute();
                        $id = (int) $stmtPost->insert_id;
                        $stmtPost->close();
                    }
                }
            }

            if ($id > 0 && $hasPostTranslations && $hasLanguages && $languageId) {
                $check = $db->prepare("SELECT id FROM post_translations WHERE post_id = ? AND language_id = ? LIMIT 1");
                $translationId = 0;
                if ($check) {
                    $check->bind_param('ii', $id, $languageId);
                    $check->execute();
                    $rw = $check->get_result()->fetch_assoc();
                    $translationId = (int) ($rw['id'] ?? 0);
                    $check->close();
                }

                if ($translationId > 0) {
                    $up = $db->prepare("UPDATE post_translations
                                        SET slug = ?, title = ?, excerpt = ?, content = ?, seo_title = ?, seo_description = ?
                                        WHERE id = ?");
                    if ($up) {
                        $up->bind_param('ssssssi', $slug, $title, $excerpt, $content, $seoTitle, $seoDesc, $translationId);
                        $up->execute();
                        $up->close();
                    }
                } else {
                    $ins = $db->prepare("INSERT INTO post_translations (post_id, language_id, slug, title, excerpt, content, seo_title, seo_description)
                                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    if ($ins) {
                        $ins->bind_param('iissssss', $id, $languageId, $slug, $title, $excerpt, $content, $seoTitle, $seoDesc);
                        $ins->execute();
                        $ins->close();
                    }
                }
            }

            $db->commit();
            admin_set_flash('success', $id > 0 ? 'Post berhasil disimpan.' : 'Post baru berhasil ditambahkan.');
        } catch (Throwable $e) {
            $db->rollback();
            admin_set_flash('error', 'Gagal menyimpan post: ' . $e->getMessage());
        }

        header('Location: ' . admin_url('/admin/posts'));
        exit;
    }
}

$categories = [];
if ($hasPostCategories) {
    $catRes = admin_db()->query("SELECT id, name FROM post_categories ORDER BY name ASC");
    if ($catRes) {
        $categories = $catRes->fetch_all(MYSQLI_ASSOC);
    }
}

$repositoryImages = list_post_images($postImageDirAbs);

$rows = [];
if ($hasPostTranslations && $hasLanguages) {
    $languageId = admin_blog_language_id();
    if ($languageId) {
        $sql = "SELECT
                  p.id, p.status, p.published_at, p.view_count, p.created_at, p.featured_image, p.category_id,
                  " . ($hasAuthorDisplayName ? "COALESCE(p.author_display_name, '')" : "''") . " AS author_display_name,
                  COALESCE(pt.title, CONCAT('Post #', p.id)) AS title,
                  COALESCE(pt.slug, '') AS slug,
                  COALESCE(pt.excerpt, '') AS excerpt,
                  COALESCE(pt.content, '') AS content,
                  COALESCE(pt.seo_title, '') AS seo_title,
                  COALESCE(pt.seo_description, '') AS seo_description,
                  COALESCE(pc.name, '-') AS category_name
                FROM posts p
                LEFT JOIN post_translations pt ON pt.post_id = p.id AND pt.language_id = ?
                LEFT JOIN post_categories pc ON pc.id = p.category_id
                WHERE p.deleted_at IS NULL
                ORDER BY p.created_at DESC
                LIMIT 100";
        $stmt = admin_db()->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('i', $languageId);
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
            $stmt->close();
        }
    }
} else {
    $sql = "SELECT p.id, p.status, p.published_at, p.view_count, p.created_at, p.featured_image, p.category_id, " . ($hasAuthorDisplayName ? "COALESCE(p.author_display_name, '')" : "''") . " AS author_display_name, CONCAT('Post #', p.id) AS title,
                   '' AS slug, '' AS excerpt, '' AS content, '' AS seo_title, '' AS seo_description, '-' AS category_name
            FROM posts p
            WHERE p.deleted_at IS NULL
            ORDER BY p.created_at DESC
            LIMIT 100";
    $result = admin_db()->query($sql);
    if ($result) {
        $rows = $result->fetch_all(MYSQLI_ASSOC);
    }
}

admin_render_start('Manajemen Blog Posts', 'posts');
?>

<?php if (!($hasPostTranslations && $hasLanguages)): ?>
  <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 text-amber-700 px-4 py-3 text-sm">
    Tabel <code>post_translations</code> dan/atau <code>languages</code> belum tersedia. Form konten tidak aktif, hanya status post yang bisa diubah.
  </div>
<?php endif; ?>

<?php if ($hasPostTranslations && $hasLanguages): ?>
  <div class="bg-white border border-slate-200 rounded-2xl p-5 mb-6">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between mb-4">
      <h2 class="font-bold text-lg">Form Post</h2>
      <span id="formModeBadge" class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">Mode Tambah</span>
    </div>
    <form id="postForm" method="post" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-3">
      <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
      <input type="hidden" name="action" value="save_post">
      <input type="hidden" id="formPostId" name="id" value="0">

      <div>
        <label for="formTitle" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Judul</label>
        <input id="formTitle" name="title" required class="rounded-lg border-slate-300 w-full">
      </div>
      <div>
        <label for="formSlug" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Slug</label>
        <input id="formSlug" name="slug" required class="rounded-lg border-slate-300 w-full">
      </div>
      <div>
        <label for="formStatus" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Status</label>
        <select id="formStatus" name="status" class="rounded-lg border-slate-300 w-full">
          <option value="draft">draft</option>
          <option value="published">published</option>
          <option value="archived">archived</option>
        </select>
      </div>
      <div>
        <label for="formCategory" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Kategori</label>
        <select id="formCategory" name="category_id" class="rounded-lg border-slate-300 w-full">
          <option value="0">Tanpa kategori</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= (int) $cat['id'] ?>"><?= admin_e((string) $cat['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="md:col-span-2">
        <label for="formFeaturedImage" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Featured Image URL</label>
        <input id="formFeaturedImage" name="featured_image" class="rounded-lg border-slate-300 w-full" placeholder="https://...">
      </div>
      <div>
        <label for="formSelectedFeaturedImage" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Featured Dari Repository</label>
        <select id="formSelectedFeaturedImage" name="selected_featured_image" class="rounded-lg border-slate-300 w-full">
          <option value="">Pilih gambar repository</option>
          <?php foreach ($repositoryImages as $imgName): ?>
            <option value="<?= admin_e($imgName) ?>"><?= admin_e($imgName) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label for="formFeaturedImageFile" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Upload Featured Image</label>
        <input id="formFeaturedImageFile" name="featured_image_file" type="file" accept=".png,.jpg,.jpeg,.webp" class="rounded-lg border-slate-300 w-full">
      </div>
      <div class="md:col-span-2">
        <label for="formImageFilename" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama File Featured (Opsional)</label>
        <input id="formImageFilename" name="image_filename" class="rounded-lg border-slate-300 w-full" placeholder="blog-featured-1.png">
      </div>
      <?php if ($hasAuthorDisplayName && $canOverrideAuthor): ?>
      <div class="md:col-span-2">
        <label for="formAuthorDisplayName" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Nama Author (Opsional Override)</label>
        <input id="formAuthorDisplayName" name="author_display_name" class="rounded-lg border-slate-300 w-full" placeholder="Kosongkan untuk pakai username pembuat post">
      </div>
      <?php endif; ?>
      <div class="md:col-span-2">
        <label for="formExcerpt" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Excerpt</label>
        <textarea id="formExcerpt" name="excerpt" rows="3" class="rounded-lg border-slate-300 w-full"></textarea>
      </div>
      <div class="md:col-span-2">
        <label for="formContent" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Konten (HTML diperbolehkan)</label>
        <textarea id="formContent" name="content" rows="10" class="rounded-lg border-slate-300 w-full font-mono text-sm"></textarea>
      </div>
      <div class="md:col-span-2 grid md:grid-cols-2 gap-3 border border-slate-200 rounded-xl p-3">
        <div>
          <label for="formContentImageRepo" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Sisip Gambar ke Konten (Repository)</label>
          <select id="formContentImageRepo" class="rounded-lg border-slate-300 w-full" size="7">
            <?php foreach ($repositoryImages as $imgName): ?>
              <option value="<?= admin_e($imgName) ?>"><?= admin_e($imgName) ?></option>
            <?php endforeach; ?>
          </select>
          <div class="mt-2">
            <label for="formContentImageSize" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Ukuran Gambar Sisipan</label>
            <select id="formContentImageSize" class="rounded-lg border-slate-300 w-full text-xs">
              <option value="w-full">Full Width</option>
              <option value="w-full md:w-4/5 mx-auto">Large</option>
              <option value="w-full md:w-2/3 mx-auto">Medium</option>
              <option value="w-full md:w-1/2 mx-auto">Small</option>
            </select>
          </div>
          <div class="mt-2 flex flex-wrap gap-2">
            <button type="button" id="btnInsertContentImage" class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 text-xs font-semibold">Insert IMG</button>
            <button type="button" id="btnInsertContentFigure" class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 text-xs font-semibold">Insert FIGURE</button>
          </div>
        </div>
        <div>
          <label for="formContentImageFiles" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Upload Gambar Konten (Multi)</label>
          <input id="formContentImageFiles" name="content_image_files[]" type="file" multiple accept=".png,.jpg,.jpeg,.webp" class="rounded-lg border-slate-300 w-full">
          <div class="mt-2 text-xs text-slate-500">
            Upload akan menambahkan file ke repository blog. Untuk sisip di tengah artikel: upload, simpan, lalu edit lagi dan gunakan tombol insert dari daftar repository.
          </div>
        </div>
      </div>
      <div>
        <label for="formSeoTitle" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">SEO Title</label>
        <input id="formSeoTitle" name="seo_title" class="rounded-lg border-slate-300 w-full">
      </div>
      <div>
        <label for="formSeoDescription" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">SEO Description</label>
        <input id="formSeoDescription" name="seo_description" class="rounded-lg border-slate-300 w-full">
      </div>
      <div class="md:col-span-2 flex flex-wrap items-center gap-2">
        <button id="formSubmitButton" class="px-4 py-2 bg-blue-800 text-white rounded-lg font-semibold">Simpan Post</button>
        <button id="formResetButton" type="button" class="px-4 py-2 border border-slate-300 text-slate-700 rounded-lg font-semibold">Reset</button>
      </div>
    </form>
  </div>
<?php endif; ?>

<div class="bg-white border border-slate-200 rounded-2xl overflow-hidden">
  <div class="p-5 border-b border-slate-200">
    <h2 class="font-bold text-lg">Daftar Post (maks 100 terbaru)</h2>
    <p class="text-sm text-slate-500 mt-1">Aksi cepat: ubah status atau edit isi post.</p>
  </div>
  <div class="hidden md:block overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 text-slate-500 uppercase text-[11px] tracking-wider">
      <tr>
        <th class="px-4 py-3 text-left">Judul</th>
        <th class="px-4 py-3 text-left">Slug</th>
        <th class="px-4 py-3 text-left">Kategori</th>
        <th class="px-4 py-3 text-left">Published</th>
        <th class="px-4 py-3 text-left">Status</th>
        <th class="px-4 py-3 text-right">Aksi</th>
      </tr>
      </thead>
      <tbody class="divide-y divide-slate-100">
      <?php foreach ($rows as $r): ?>
        <tr>
          <td class="px-4 py-3">
            <div class="font-semibold text-slate-800"><?= admin_e((string) $r['title']) ?></div>
            <div class="text-xs text-slate-500 mt-1">Views: <?= (int) $r['view_count'] ?></div>
          </td>
          <td class="px-4 py-3 text-xs text-slate-600"><?= admin_e((string) $r['slug']) ?></td>
          <td class="px-4 py-3"><?= admin_e((string) ($r['category_name'] ?? '-')) ?></td>
          <td class="px-4 py-3 text-slate-500"><?= admin_e((string) ($r['published_at'] ?? '-')) ?></td>
          <td class="px-4 py-3">
            <span class="px-2 py-1 rounded-full text-xs font-bold <?= $r['status'] === 'published' ? 'bg-emerald-100 text-emerald-700' : ($r['status'] === 'draft' ? 'bg-slate-100 text-slate-700' : 'bg-amber-100 text-amber-700') ?>">
              <?= admin_e((string) $r['status']) ?>
            </span>
          </td>
          <td class="px-4 py-3">
            <div class="flex flex-wrap justify-end gap-2">
              <form method="post" class="flex gap-2">
                <input type="hidden" name="csrf_token" value="<?= admin_e(admin_csrf_token()) ?>">
                <input type="hidden" name="action" value="toggle_status">
                <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
                <select name="status" class="rounded-lg border-slate-300 text-sm">
                  <?php foreach (['draft', 'published', 'archived'] as $s): ?>
                    <option value="<?= $s ?>" <?= $s === $r['status'] ? 'selected' : '' ?>><?= $s ?></option>
                  <?php endforeach; ?>
                </select>
                <button class="px-3 py-1.5 bg-blue-800 text-white rounded-lg font-semibold">Simpan</button>
              </form>
              <?php if ($hasPostTranslations && $hasLanguages): ?>
                <button
                  type="button"
                  class="px-3 py-1.5 rounded-lg bg-slate-800 text-white font-semibold btn-edit-post"
                  data-id="<?= (int) $r['id'] ?>"
                  data-title="<?= admin_e((string) $r['title']) ?>"
                  data-slug="<?= admin_e((string) $r['slug']) ?>"
                  data-status="<?= admin_e((string) $r['status']) ?>"
                  data-category-id="<?= (int) ($r['category_id'] ?? 0) ?>"
                  data-featured-image="<?= admin_e((string) ($r['featured_image'] ?? '')) ?>"
                  data-image-filename="<?= admin_e((string) basename((string) ($r['featured_image'] ?? ''))) ?>"
                  data-author-display-name="<?= admin_e((string) ($r['author_display_name'] ?? '')) ?>"
                  data-excerpt="<?= admin_e((string) ($r['excerpt'] ?? '')) ?>"
                  data-content="<?= admin_e((string) ($r['content'] ?? '')) ?>"
                  data-seo-title="<?= admin_e((string) ($r['seo_title'] ?? '')) ?>"
                  data-seo-description="<?= admin_e((string) ($r['seo_description'] ?? '')) ?>"
                >
                  Edit
                </button>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?>
        <tr><td colspan="6" class="px-4 py-5 text-center text-slate-500">Belum ada data posts.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php if ($hasPostTranslations && $hasLanguages): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('postForm');
  const modeBadgeEl = document.getElementById('formModeBadge');
  const submitEl = document.getElementById('formSubmitButton');
  const resetEl = document.getElementById('formResetButton');
  const idEl = document.getElementById('formPostId');
  const titleEl = document.getElementById('formTitle');
  const slugEl = document.getElementById('formSlug');
  const statusEl = document.getElementById('formStatus');
  const categoryEl = document.getElementById('formCategory');
  const featuredEl = document.getElementById('formFeaturedImage');
  const selectedFeaturedEl = document.getElementById('formSelectedFeaturedImage');
  const featuredUploadEl = document.getElementById('formFeaturedImageFile');
  const imageFilenameEl = document.getElementById('formImageFilename');
  const contentImageRepoEl = document.getElementById('formContentImageRepo');
  const contentImageSizeEl = document.getElementById('formContentImageSize');
  const btnInsertContentImageEl = document.getElementById('btnInsertContentImage');
  const btnInsertContentFigureEl = document.getElementById('btnInsertContentFigure');
  const authorDisplayNameEl = document.getElementById('formAuthorDisplayName');
  const excerptEl = document.getElementById('formExcerpt');
  const contentEl = document.getElementById('formContent');
  const seoTitleEl = document.getElementById('formSeoTitle');
  const seoDescEl = document.getElementById('formSeoDescription');

  function setAddMode() {
    modeBadgeEl.textContent = 'Mode Tambah';
    modeBadgeEl.className = 'px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700';
    submitEl.textContent = 'Simpan Post';
  }

  function setEditMode(id) {
    modeBadgeEl.textContent = 'Mode Edit #' + id;
    modeBadgeEl.className = 'px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700';
    submitEl.textContent = 'Update Post';
  }

  function resetForm() {
    form.reset();
    idEl.value = '0';
    if (selectedFeaturedEl) selectedFeaturedEl.value = '';
    if (featuredUploadEl) featuredUploadEl.value = '';
    setAddMode();
  }

  resetEl?.addEventListener('click', resetForm);

  document.querySelectorAll('.btn-edit-post').forEach((btn) => {
    btn.addEventListener('click', function () {
      idEl.value = this.dataset.id || '0';
      titleEl.value = this.dataset.title || '';
      slugEl.value = this.dataset.slug || '';
      statusEl.value = this.dataset.status || 'draft';
      categoryEl.value = this.dataset.categoryId || '0';
      featuredEl.value = this.dataset.featuredImage || '';
      if (imageFilenameEl) imageFilenameEl.value = this.dataset.imageFilename || '';
      if (selectedFeaturedEl) selectedFeaturedEl.value = '';
      if (featuredUploadEl) featuredUploadEl.value = '';
      if (authorDisplayNameEl) authorDisplayNameEl.value = this.dataset.authorDisplayName || '';
      excerptEl.value = this.dataset.excerpt || '';
      contentEl.value = this.dataset.content || '';
      seoTitleEl.value = this.dataset.seoTitle || '';
      seoDescEl.value = this.dataset.seoDescription || '';
      setEditMode(this.dataset.id || '0');
      form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

  selectedFeaturedEl?.addEventListener('change', function () {
    const selected = this.value || '';
    if (!selected) return;
    featuredEl.value = '<?= admin_e($postImageDirRel) ?>/' + selected;
    if (imageFilenameEl) imageFilenameEl.value = selected;
  });

  featuredUploadEl?.addEventListener('change', function () {
    const file = this.files && this.files[0];
    if (file && imageFilenameEl) {
      imageFilenameEl.value = file.name;
    }
  });

  function insertAtCursor(textarea, text) {
    if (!textarea) return;
    const start = textarea.selectionStart || 0;
    const end = textarea.selectionEnd || 0;
    const current = textarea.value || '';
    textarea.value = current.substring(0, start) + text + current.substring(end);
    textarea.focus();
    const pos = start + text.length;
    textarea.setSelectionRange(pos, pos);
  }

  function selectedRepoImagePath() {
    const name = contentImageRepoEl?.value || '';
    if (!name) return '';
    return '<?= admin_e(admin_url('/' . trim($postImageDirRel, '/'))) ?>/' + name;
  }

  function selectedImageSizeClass() {
    return contentImageSizeEl?.value || 'w-full';
  }

  btnInsertContentImageEl?.addEventListener('click', function () {
    const path = selectedRepoImagePath();
    if (!path) return;
    insertAtCursor(contentEl, '\n<img src="' + path + '" alt="" class="' + selectedImageSizeClass() + ' rounded-xl my-6">\n');
  });

  btnInsertContentFigureEl?.addEventListener('click', function () {
    const path = selectedRepoImagePath();
    if (!path) return;
    insertAtCursor(contentEl, '\n<figure class="my-6"><img src="' + path + '" alt="" class="' + selectedImageSizeClass() + ' rounded-xl"><figcaption class="text-sm text-slate-500 mt-2">Caption gambar</figcaption></figure>\n');
  });
});
</script>
<?php endif; ?>

<?php admin_render_end(); ?>
