<?php
// header.php
// load configuration (basePath detection etc.)
require_once __DIR__.'/config.php';
// ensure variable is defined for templates
$basePath = $basePath ?? '';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}
$authUser = $_SESSION['admin_user'] ?? null;
$isLoggedIn = is_array($authUser);
$roleCodes = [];
if ($isLoggedIn && isset($authUser['roles']) && is_array($authUser['roles'])) {
    $roleCodes = array_values(array_filter(array_map('strval', $authUser['roles'])));
}

$panelHref = $basePath . '/portal';
$panelText = 'Masuk Panel';
if ($isLoggedIn) {
    if (in_array('super_admin', $roleCodes, true) || in_array('editor', $roleCodes, true) || in_array('hr_admin', $roleCodes, true)) {
        $panelHref = $basePath . '/admin/';
        $panelText = 'Masuk Admin Panel';
    } elseif (in_array('student', $roleCodes, true)) {
        $panelHref = $basePath . '/pages/learning';
        $panelText = 'Lanjutkan Belajar';
    } elseif (in_array('instructor', $roleCodes, true)) {
        $panelHref = $basePath . '/pages/learning';
        $panelText = 'Masuk Instructor Panel';
    } elseif (in_array('employee', $roleCodes, true)) {
        $panelHref = $basePath . '/portal';
        $panelText = 'Masuk Employee Panel';
    } else {
        $panelHref = $basePath . '/portal';
        $panelText = 'Masuk Panel';
    }
}
$loginHref = $basePath . '/login';
$logoutHref = $basePath . '/logout';
$siteNavLinks = [
    ['label' => 'Home', 'href' => $basePath . '/'],
    ['label' => 'Layanan Kami', 'href' => $basePath . '/#layanan'],
    ['label' => 'Produk Kami', 'href' => $basePath . '/#produk'],
    ['label' => 'Portofolio', 'href' => $basePath . '/#portofolio'],
    ['label' => 'Ecommerce', 'href' => $basePath . '/#ecommerce'],
    ['label' => 'Sosial Media', 'href' => $basePath . '/#social'],
    ['label' => 'Mitra & Pelanggan Kami', 'href' => $basePath . '/#mitra'],
    ['label' => 'Testimonial', 'href' => $basePath . '/#testimonial'],
    ['label' => 'Special Partners', 'href' => $basePath . '/#special-partners'],
    ['label' => 'Kontak', 'href' => $basePath . '/#contact'],
];
?>
<!DOCTYPE html>
<html class="scroll-smooth" lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo htmlspecialchars($pageTitle ?? 'InoSakti | Innovate Smartly - Engineering &amp; Technology Solutions'); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($pageDesc ?? 'InoSakti provides Applied Engineering &amp; Technology Solutions, integrated smart systems, AI, IoT, and R&amp;D.'); ?>"/>

  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

  <!-- ========== FAVICON ========== -->
  <link rel="icon" type="image/svg+xml" sizes="any" href="<?php echo $basePath; ?>/assets/img/favicon.svg">
  <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $basePath; ?>/assets/img/favicon-32.png">
  <link rel="icon" href="<?php echo $basePath; ?>/favicon.ico" sizes="any">
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $basePath; ?>/assets/img/apple-touch.png">

  <!-- PWA -->
  <link rel="manifest" href="<?php echo $basePath; ?>/site.webmanifest">
  <meta name="theme-color" content="#1E40AF">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="InoSakti">

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com?plugins=forms,typography,container-queries"></script>

  <script>
    if (window.tailwind) {
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              primary: "rgb(var(--primary) / <alpha-value>)",
              secondary: "rgb(var(--secondary) / <alpha-value>)",
              accent: "rgb(var(--accent) / <alpha-value>)",
              "accent-green": "rgb(var(--accent-green) / <alpha-value>)",
              "accent-red": "rgb(var(--accent-red) / <alpha-value>)",
              "background-light": "#f8fafc",
              "background-dark": "#020617",
            },
            fontFamily: {
              display: ["'Plus Jakarta Sans'", "sans-serif"],
              sans: ["'Plus Jakarta Sans'", "sans-serif"],
            },
            borderRadius: { DEFAULT: "0.75rem" },
          },
        },
      };
    }
  </script>

  <style type="text/tailwindcss">
    @layer base {
      :root {
        --primary: 30 58 138;
        --secondary: 15 23 42;
        --accent: 29 78 216;
        --accent-green: 22 163 74;
        --accent-red: 239 68 68;
      }
    }
    @layer components {
      .service-card { @apply bg-white dark:bg-slate-800 p-8 rounded-2xl border border-slate-100 dark:border-slate-700 transition-all duration-300 hover:shadow-xl hover:-translate-y-1; }
      .glass-effect { backdrop-filter: blur(8px); background-color: rgba(255,255,255,.9); }
      .dark .glass-effect { background-color: rgba(15,23,42,.9); }
      .section-title { @apply text-4xl lg:text-5xl font-extrabold text-center mb-12; }
      .product-thumb { @apply overflow-hidden rounded-2xl aspect-square bg-slate-100 dark:bg-slate-700; }
    }
  </style>

  <style>
    .animate-slide-up { animation: slideUp .6s ease-out forwards; opacity: 0; }
    @keyframes slideUp { from {opacity:0; transform: translateY(20px);} to {opacity:1; transform: translateY(0);} }
    .stagger-1 { animation-delay: .1s; }
    .stagger-2 { animation-delay: .2s; }
    .stagger-3 { animation-delay: .3s; }
    .service-card:hover .icon-box { transform: scale(1.1) rotate(5deg); }
  </style>

  <?php if (!empty($extraHead)) {
      echo $extraHead;
  } ?>
</head>

<body class="bg-[#fdfdfd] dark:bg-background-dark text-slate-900 dark:text-slate-100 font-sans transition-colors duration-300">

<header class="fixed top-0 w-full z-50 glass-effect border-b border-slate-100 dark:border-slate-800">
  <div class="container mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
    <div class="flex items-center gap-2 -ml-2 sm:ml-0">
      <img src="<?php echo $basePath; ?>/assets/img/logo_inosakti.png" alt="InoSakti" class="h-[2rem] w-auto" />
    </div>

    <nav class="hidden lg:flex items-center gap-6 font-bold text-sm">
      <a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/">Home</a>
      <a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/pages/products/shop?category=all">Belanja</a>
      <a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/pages/blog">Blog</a>
      <a class="hover:text-primary transition-colors" href="https://wa.me/+6288207085761">Konsultasi</a>
      <?php if (!$isLoggedIn): ?>
      <a class="hover:text-primary transition-colors" href="<?php echo $loginHref; ?>">Login</a>
      <?php else: ?>
	      <div class="relative">
	        <button id="accountDropdownBtn" type="button" class="inline-flex items-center gap-1 hover:text-primary transition-colors">
	          Akun <span class="material-symbols-outlined text-base">expand_more</span>
	        </button>
	        <div id="accountDropdownMenu" class="absolute right-0 top-full mt-2 w-56 rounded-xl border border-slate-200 bg-white shadow-lg p-2 hidden">
	          <a href="<?php echo $panelHref; ?>" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50"><?php echo $panelText; ?></a>
	          <a href="<?php echo $logoutHref; ?>" class="block px-3 py-2 rounded-lg text-red-600 hover:bg-red-50">Logout</a>
	        </div>
	      </div>
      <?php endif; ?>
    </nav>

	    <div class="flex items-center gap-4">
		      <div class="hidden sm:flex items-center gap-2 text-xs font-bold">
            <div class="relative">
		        <button id="siteNavDropdownBtn" type="button" class="bg-primary text-white px-3 py-1.5 flex items-center gap-1 rounded-lg">
		          Navigasi <span class="material-symbols-outlined text-xs">expand_more</span>
		        </button>
              <div id="siteNavDropdownMenu" class="absolute right-0 top-full mt-2 w-64 max-h-80 overflow-auto rounded-xl border border-slate-200 bg-white shadow-lg p-2 hidden">
                <?php foreach ($siteNavLinks as $link): ?>
                  <a href="<?php echo htmlspecialchars($link['href']); ?>" class="block px-3 py-2 rounded-lg text-slate-700 hover:bg-slate-50">
                    <?php echo htmlspecialchars($link['label']); ?>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
		        <button class="bg-white px-3 py-1.5 flex items-center gap-1 border rounded-lg">
		          <span class="material-symbols-outlined text-xs">language</span>
		          IND <span class="material-symbols-outlined text-xs">expand_more</span>
		        </button>
		      </div>

	      <div class="lg:hidden">
	        <button id="hamburgerBtn" class="p-2 focus:outline-none">
	          <span class="material-symbols-outlined text-3xl">menu</span>
	        </button>
      </div>
    </div>
  </div>
</header>

<div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40"></div>
<div id="mobileMenu" class="fixed inset-y-0 left-0 w-64 z-50 transform -translate-x-full transition-transform duration-300 bg-white dark:bg-background-dark shadow-xl">
  <div class="flex items-center justify-between px-6 h-16 border-b border-slate-200 dark:border-slate-700">
    <span class="font-bold text-lg">Menu</span>
    <button id="closeMenuBtn" class="p-2 focus:outline-none">
      <span class="material-symbols-outlined text-3xl">close</span>
    </button>
  </div>
  <nav class="px-6 py-8 space-y-4">
    <a href="<?php echo $basePath; ?>/" class="block text-lg font-semibold hover:text-primary">Home</a>
    <a href="<?php echo $basePath; ?>/pages/products/shop?category=all" class="block text-lg font-semibold hover:text-primary">Belanja</a>
    <a href="<?php echo $basePath; ?>/pages/blog" class="block text-lg font-semibold hover:text-primary">Blog</a>
    <a href="https://wa.me/+6288207085761" class="block text-lg font-semibold hover:text-primary">Konsultasi</a>
    <?php if (!$isLoggedIn): ?>
    <a href="<?php echo $loginHref; ?>" class="block text-lg font-semibold hover:text-primary">Login</a>
    <?php else: ?>
    <a href="<?php echo $panelHref; ?>" class="block text-lg font-semibold hover:text-primary"><?php echo $panelText; ?></a>
    <a href="<?php echo $logoutHref; ?>" class="block text-lg font-semibold text-red-600 hover:text-red-700">Logout</a>
    <?php endif; ?>
  </nav>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var menu = document.getElementById('mobileMenu');
    var overlay = document.getElementById('mobileOverlay');
	    var openBtn = document.getElementById('hamburgerBtn');
	    var closeBtn = document.getElementById('closeMenuBtn');
      var accountBtn = document.getElementById('accountDropdownBtn');
      var accountMenu = document.getElementById('accountDropdownMenu');
      var siteNavBtn = document.getElementById('siteNavDropdownBtn');
      var siteNavMenu = document.getElementById('siteNavDropdownMenu');

	    function openMenu(){ menu.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); }
	    function closeMenu(){ menu.classList.add('-translate-x-full'); overlay.classList.add('hidden'); }
      function closeAccountDropdown(){ accountMenu?.classList.add('hidden'); }
      function closeSiteNavDropdown(){ siteNavMenu?.classList.add('hidden'); }

	    openBtn?.addEventListener('click', openMenu);
	    closeBtn?.addEventListener('click', closeMenu);
	    overlay?.addEventListener('click', closeMenu);
      accountBtn?.addEventListener('click', function (e) {
        e.stopPropagation();
        if (!accountMenu) return;
        var willOpen = accountMenu.classList.contains('hidden');
        closeSiteNavDropdown();
        accountMenu.classList.toggle('hidden', !willOpen);
      });
      siteNavBtn?.addEventListener('click', function (e) {
        e.stopPropagation();
        if (!siteNavMenu) return;
        var willOpen = siteNavMenu.classList.contains('hidden');
        closeAccountDropdown();
        siteNavMenu.classList.toggle('hidden', !willOpen);
      });
      document.addEventListener('click', function (e) {
        var target = e.target;
        if (accountBtn && accountMenu && !accountBtn.contains(target) && !accountMenu.contains(target)) {
          closeAccountDropdown();
        }
        if (siteNavBtn && siteNavMenu && !siteNavBtn.contains(target) && !siteNavMenu.contains(target)) {
          closeSiteNavDropdown();
        }
      });
      document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
          closeAccountDropdown();
          closeSiteNavDropdown();
        }
      });
	  });
	</script>
