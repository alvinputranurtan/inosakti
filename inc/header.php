<?php
// header.php - reusable header with opening <html>, <head> and navigation
// expects optional variables: $pageTitle, $pageDesc, $basePath
$basePath = $basePath ?? '/inosakti.com';
?>
<!DOCTYPE html>
<html class="scroll-smooth" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?php echo htmlspecialchars($pageTitle ?? 'InoSakti | Innovate Smartly - Engineering &amp; Technology Solutions'); ?></title>
    <meta content="<?php echo htmlspecialchars($pageDesc ?? 'InoSakti provides Applied Engineering &amp; Technology Solutions, integrated smart systems, AI, IoT, and R&amp;D.'); ?>" name="description"/>

    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,container-queries"></script>

    <!-- ✅ Tailwind config: define colors that support /opacity -->
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        // Brand colors from CSS variables (RGB triplets)
                        primary: "rgb(var(--primary) / <alpha-value>)",
                        secondary: "rgb(var(--secondary) / <alpha-value>)",
                        accent: "rgb(var(--accent) / <alpha-value>)",

                        // Optional extra accents if you want them
                        "accent-green": "rgb(var(--accent-green) / <alpha-value>)",
                        "accent-red": "rgb(var(--accent-red) / <alpha-value>)",

                        "background-light": "#f8fafc",
                        "background-dark": "#020617",
                    },
                    fontFamily: {
                        display: ["'Plus Jakarta Sans'", "sans-serif"],
                        sans: ["'Plus Jakarta Sans'", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.75rem",
                    },
                },
            },
        };
    </script>

    <!-- ✅ Tailwind layer: define CSS variables + components -->
    <style type="text/tailwindcss">
        @layer base {
            :root {
                /* IMPORTANT: store as "R G B" (no rgb(), no #hex) */
                --primary: 30 58 138;        /* #1e3a8a */
                --secondary: 15 23 42;       /* #0f172a */
                --accent: 29 78 216;         /* #1d4ed8 */
                --accent-green: 22 163 74;   /* #16a34a */
                --accent-red: 239 68 68;     /* #ef4444 */
            }
        }

        @layer components {
            .service-card {
                @apply bg-white dark:bg-slate-800 p-8 rounded-2xl border border-slate-100 dark:border-slate-700 transition-all duration-300 hover:shadow-xl hover:-translate-y-1;
            }
            .glass-effect {
                backdrop-filter: blur(8px);
                background-color: rgba(255, 255, 255, 0.9);
            }
            .dark .glass-effect {
                background-color: rgba(15, 23, 42, 0.9);
            }
            .section-title {
                @apply text-4xl lg:text-5xl font-extrabold text-center mb-12;
            }
            .product-thumb {
                @apply overflow-hidden rounded-2xl aspect-square bg-slate-100 dark:bg-slate-700;
            }
        }
    </style>

    <!-- Animations -->
    <style>
        .animate-slide-up {
            animation: slideUp 0.6s ease-out forwards;
            opacity: 0;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .stagger-1 { animation-delay: 0.1s; }
        .stagger-2 { animation-delay: 0.2s; }
        .stagger-3 { animation-delay: 0.3s; }
        .service-card:hover .icon-box { transform: scale(1.1) rotate(5deg); }
    </style>
</head>

<body class="bg-[#fdfdfd] dark:bg-background-dark text-slate-900 dark:text-slate-100 font-sans transition-colors duration-300">

<header class="fixed top-0 w-full z-50 glass-effect border-b border-slate-100 dark:border-slate-800">
    <div class="container mx-auto px-6 h-16 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <img src="<?php echo $basePath; ?>/assets/img/logo_inosakti.png" alt="InoSakti" class="h-20 w-auto" />
        </div>

        <nav class="hidden lg:flex items-center gap-6 font-bold text-sm">
            <a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/">Home</a>
            <a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/pages/products/shop.php?category=all">Belanja</a>
            <a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/index.php#blog">Blog</a>
            <a class="hover:text-primary transition-colors" href="https://wa.me/+6288207085761">Konsultasi</a>
            <a class="hover:text-primary transition-colors" href="#">Login</a>
        </nav>

        <div class="flex items-center gap-4">
            <div class="hidden sm:flex border rounded-lg overflow-hidden text-xs font-bold">
                <button class="bg-primary text-white px-3 py-1.5 flex items-center gap-1">
                    Navigasi <span class="material-symbols-outlined text-xs">expand_more</span>
                </button>
                <button class="bg-white px-3 py-1.5 flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">language</span>
                    IND <span class="material-symbols-outlined text-xs">expand_more</span>
                </button>
            </div>
        </div>
    </div>
</header>