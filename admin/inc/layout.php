<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function admin_nav_items(): array
{
    return [
        'dashboard' => ['label' => 'Dashboard', 'icon' => 'dashboard', 'href' => admin_url('/admin/')],
        'posts' => ['label' => 'Blog Posts', 'icon' => 'article', 'href' => admin_url('/admin/posts')],
        'courses' => ['label' => 'Kursus', 'icon' => 'menu_book', 'href' => admin_url('/admin/courses')],
        'products' => ['label' => 'Produk', 'icon' => 'inventory_2', 'href' => admin_url('/admin/products')],
        'orders' => ['label' => 'Order', 'icon' => 'storefront', 'href' => admin_url('/admin/orders')],
        'users' => ['label' => 'Pengguna', 'icon' => 'group', 'href' => admin_url('/admin/users')],
    ];
}

function admin_render_start(string $title, string $active): void
{
    $user = admin_current_user();
    $navItems = admin_nav_items();
    ?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= admin_e($title) ?> | InoSakti Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@200..700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ["'Plus Jakarta Sans'", 'sans-serif'] },
          colors: {
            ink: '#0f172a',
            panel: '#102248',
            brand: '#1e40af',
            skyline: '#e2ebff'
          }
        }
      }
    };
  </script>
  <style>
    .material-symbols-outlined { font-variation-settings: "FILL" 0, "wght" 300, "GRAD" 0, "opsz" 24; }
  </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased">
<div class="min-h-screen flex">
  <aside class="hidden lg:flex w-72 bg-panel text-white flex-col fixed inset-y-0 z-30">
    <div class="px-7 py-6 border-b border-white/10">
      <div class="flex items-center gap-2">
        <span class="material-symbols-outlined text-2xl">settings_input_component</span>
        <div class="text-xl font-extrabold tracking-tight">INOSAKTI ADMIN</div>
      </div>
      <div class="text-[11px] text-blue-100/80 mt-1">Operational Control Center</div>
    </div>
    <nav class="flex-1 px-4 py-4 space-y-1">
      <?php foreach ($navItems as $key => $item): ?>
        <?php $isActive = ($key === $active); ?>
        <a href="<?= admin_e($item['href']) ?>"
           class="flex items-center gap-3 px-4 py-3 rounded-xl transition <?= $isActive ? 'bg-white/15 font-bold border border-white/20' : 'text-blue-100 hover:bg-white/10' ?>">
          <span class="material-symbols-outlined"><?= admin_e($item['icon']) ?></span>
          <span><?= admin_e($item['label']) ?></span>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="p-5 border-t border-white/10">
      <div class="text-sm font-semibold"><?= admin_e((string) ($user['name'] ?? 'Admin')) ?></div>
      <div class="text-xs text-blue-100/80 truncate"><?= admin_e((string) ($user['email'] ?? '')) ?></div>
      <a href="<?= admin_e(admin_url('/admin/logout')) ?>" class="mt-3 inline-flex items-center gap-2 text-sm text-blue-100 hover:text-white">
        <span class="material-symbols-outlined text-base">logout</span> Logout
      </a>
    </div>
  </aside>

  <main class="flex-1 lg:ml-72">
    <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-5 lg:px-8 sticky top-0 z-20">
      <h1 class="font-bold text-slate-800"><?= admin_e($title) ?></h1>
      <a href="<?= admin_e(admin_url('/')) ?>" class="text-sm text-brand font-semibold hover:underline">Lihat Website</a>
    </header>
    <div class="p-5 lg:p-8">
      <?php $flash = admin_get_flash(); ?>
      <?php if ($flash): ?>
        <div class="mb-6 rounded-xl border px-4 py-3 text-sm <?= $flash['type'] === 'error' ? 'border-red-200 bg-red-50 text-red-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700' ?>">
          <?= admin_e((string) $flash['message']) ?>
        </div>
      <?php endif; ?>
<?php
}

function admin_render_end(): void
{
    ?>
    </div>
  </main>
</div>
</body>
</html>
<?php
}
