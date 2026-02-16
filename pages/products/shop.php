<!DOCTYPE html>
<html class="scroll-smooth" lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Belanja | InoSakti - Engineering &amp; Technology Solutions</title>
<meta content="InoSakti provides Applied Engineering &amp; Technology Solutions, integrated smart systems, AI, IoT, and R&amp;D." name="description"/>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<style type="text/tailwindcss">
        @layer base {
            :root {
                --primary: #1e3a8a;
                --secondary: #0f172a;
                --accent-blue: #1d4ed8;
                --accent-green: #16a34a;
                --accent-red: #ef4444;
            }
        }
        @layer components {
            .glass-effect {
                backdrop-filter: blur(8px);
                background-color: rgba(255, 255, 255, 0.9);
            }
            .dark .glass-effect {
                background-color: rgba(15, 23, 42, 0.9);
            }
            .product-card {
                @apply bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 opacity-0 translate-y-10;
            }
            .filter-dropdown {
                @apply bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-1.5 text-xs font-semibold flex items-center gap-2;
            }
            .category-badge {
                @apply absolute bottom-3 left-3 z-10 bg-slate-100/95 dark:bg-slate-800/95 text-slate-800 dark:text-slate-200 text-[9px] font-bold px-2.5 py-1 rounded-full shadow-sm;
            }
        }
    </style>
<script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#1e3a8a",
                        secondary: "#0f172a",
                        "background-light": "#f8fafc",
                        "background-dark": "#020617",
                    },
                    fontFamily: {
                        display: ["'Plus Jakarta+Sans'", "sans-serif"],
                        sans: ["'Plus Jakarta Sans'", "sans-serif"],
                    },
                    borderRadius: {
                        DEFAULT: "0.75rem",
                    },
                },
            },
        };
    </script>
</head>
<body class="bg-[#fdfdfd] dark:bg-background-dark text-slate-900 dark:text-slate-100 font-sans transition-colors duration-300 pt-16">
<header class="fixed top-0 w-full z-50 glass-effect border-b border-slate-100 dark:border-slate-800">
<div class="container mx-auto px-6 h-16 flex items-center justify-between">
<div class="flex items-center gap-2">
<div class="flex items-center gap-2">
                    <img src="../../assets/img/logo_inosakti.png" alt="InoSakti" class="h-20 w-auto" />
                </div>
</div>
<nav class="hidden lg:flex items-center gap-6 font-bold text-sm">
<a class="hover:text-primary transition-colors" href="../../index.php">Home</a>
<a class="hover:text-primary transition-colors" href="../../index.php#produk">Belanja</a>
<a class="hover:text-primary transition-colors" href="#">Blog</a>
<a class="hover:text-primary transition-colors" href="#">Konsultasi</a>
<a class="hover:text-primary transition-colors" href="#">Login</a>
</nav>
<div class="flex items-center gap-4">
<div class="hidden sm:flex border rounded-lg overflow-hidden text-xs font-bold">
<button class="bg-primary text-white px-3 py-1.5 flex items-center gap-1">Navigasi <span class="material-symbols-outlined text-xs">expand_more</span></button>
<button class="bg-white px-3 py-1.5 flex items-center gap-1"><span class="material-symbols-outlined text-xs">language</span> IND <span class="material-symbols-outlined text-xs">expand_more</span></button>
</div>
</div>
</div>
</header>

<div class="bg-slate-100 dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 sticky top-16 z-40">
    <div class="container mx-auto px-6 py-4">
        <div class="flex flex-wrap items-center gap-4 text-xs font-bold text-slate-600 dark:text-slate-400">
            <div class="flex flex-col gap-1">
                <label>Kategori</label>
                <select class="filter-dropdown min-w-[150px] appearance-none focus:ring-1 focus:ring-primary outline-none">
                    <option>Semua Produk</option>
                    <option>Sistem Pintar</option>
                    <option>Komponen</option>
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label>Urutkan Dari</label>
                <select class="filter-dropdown min-w-[150px] appearance-none focus:ring-1 focus:ring-primary outline-none">
                    <option>Termurah</option>
                    <option>Terbaru</option>
                    <option>Populer</option>
                </select>
            </div>
            <div class="flex flex-col gap-1">
                <label>Urutan</label>
                <div class="flex items-center gap-2">
                    <span class="filter-dropdown px-4">1/14</span>
                    <button class="filter-dropdown px-2"><span class="material-symbols-outlined text-sm">chevron_left</span></button>
                    <button class="filter-dropdown px-2"><span class="material-symbols-outlined text-sm">chevron_right</span></button>
                </div>
            </div>
            <div class="flex flex-col gap-1 flex-grow lg:max-w-xs ml-auto">
                <label>Pencarian</label>
                <div class="relative">
                    <input class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-1.5 pl-9 text-xs focus:ring-1 focus:ring-primary outline-none" placeholder="Cari Item..." type="text"/>
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container mx-auto px-6 py-2 text-center text-sm text-red-500">
    Pembelian item melalui website sementara belum bisa dilakukan
</div>
<main class="container mx-auto px-6 py-12">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8" id="product-grid">
        <div class="product-card group">
            <div class="relative aspect-square bg-slate-100 dark:bg-slate-700 overflow-hidden">
                <span class="category-badge">Sistem Pintar</span>
                <img alt="Modul Early Warning System" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="../../assets/img/produk_1.png"/>
            </div>
            <div class="p-5">
                <h3 class="font-bold text-sm mb-2">Modul Early Warning System</h3>
                <p class="text-slate-500 dark:text-slate-400 text-[10px] leading-relaxed mb-3">IoT menggunakan ESP32 untuk deteksi dini kebencanaan dan sistem peringatan real-time.</p>
                <div class="inline-block bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded-full text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-4">
                    Rp.450.000
                </div>
                <div class="flex gap-2">
                    <button class="flex-grow bg-primary hover:bg-blue-800 text-white text-[11px] font-bold py-2 rounded-lg transition-colors">Lihat deskripsi</button>
                    <button onclick="window.location.href='cart.php'" class="w-10 bg-primary hover:bg-blue-800 text-white rounded-lg flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-lg">shopping_cart</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="product-card group">
            <div class="relative aspect-square bg-slate-100 dark:bg-slate-700 overflow-hidden">
                <span class="category-badge">Sistem Pintar</span>
                <img alt="Modul Monitoring Greenhouse" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="../../assets/img/produk_2.png"/>
            </div>
            <div class="p-5">
                <h3 class="font-bold text-sm mb-2">Modul Monitoring Greenhouse</h3>
                <p class="text-slate-500 dark:text-slate-400 text-[10px] leading-relaxed mb-3">Monitoring Greenhouse menggunakan ESP32 dengan sensor suhu, kelembaban, dan intensitas cahaya.</p>
                <div class="inline-block bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded-full text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-4">
                    Rp.450.000
                </div>
                <div class="flex gap-2">
                    <button class="flex-grow bg-primary hover:bg-blue-800 text-white text-[11px] font-bold py-2 rounded-lg transition-colors">Lihat deskripsi</button>
                    <button class="w-10 bg-primary hover:bg-blue-800 text-white rounded-lg flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-lg">shopping_cart</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="product-card group">
            <div class="relative aspect-square bg-slate-100 dark:bg-slate-700 overflow-hidden">
                <span class="category-badge">Komponen</span>
                <img alt="Raspberry Pi 4" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="../../assets/img/produk_3.png"/>
            </div>
            <div class="p-5">
                <h3 class="font-bold text-sm mb-2">Raspberry Pi 4</h3>
                <p class="text-slate-500 dark:text-slate-400 text-[10px] leading-relaxed mb-3">Raspberry pi 4 kondisi baru dengan RAM varian tinggi untuk kebutuhan komputasi edge.</p>
                <div class="inline-block bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded-full text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-4">
                    Rp.450.000
                </div>
                <div class="flex gap-2">
                    <button class="flex-grow bg-primary hover:bg-blue-800 text-white text-[11px] font-bold py-2 rounded-lg transition-colors">Lihat deskripsi</button>
                    <button onclick="window.location.href='cart.php'" class="w-10 bg-primary hover:bg-blue-800 text-white rounded-lg flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-lg">shopping_cart</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="product-card group">
            <div class="relative aspect-square bg-slate-100 dark:bg-slate-700 overflow-hidden">
                <span class="category-badge">Komponen</span>
                <img alt="Adaptor 12v 3a" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="../../assets/img/produk_4.png"/>
            </div>
            <div class="p-5">
                <h3 class="font-bold text-sm mb-2">Adaptor 12v 3a</h3>
                <p class="text-slate-500 dark:text-slate-400 text-[10px] leading-relaxed mb-3">Merk Taffware, output stabil 12v 3a untuk power supply proyek elektronik dan router.</p>
                <div class="inline-block bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded-full text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-4">
                    Rp.450.000
                </div>
                <div class="flex gap-2">
                    <button onclick="window.location.href='cart.php'" class="flex-grow bg-primary hover:bg-blue-800 text-white text-[11px] font-bold py-2 rounded-lg transition-colors">Lihat deskripsi</button>
                    <button onclick="window.location.href='cart.php'" class="w-10 bg-primary hover:bg-blue-800 text-white rounded-lg flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-lg">shopping_cart</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    // Reveal animation
    document.addEventListener('DOMContentLoaded', () => {
        const cards = document.querySelectorAll('.product-card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.classList.remove('opacity-0', 'translate-y-10');
                card.classList.add('opacity-100', 'translate-y-0');
            }, index * 100);
        });
    });
    window.addEventListener('scroll', () => {
        const header = document.querySelector('header');
        if (window.scrollY > 50) {
            header.classList.add('shadow-sm');
        } else {
            header.classList.remove('shadow-sm');
        }
    });
</script>

<footer class="bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 pt-24 pb-12">
<div class="container mx-auto px-6">
<div class="grid grid-cols-1 lg:grid-cols-4 gap-12 mb-20">
<div>
<div class="flex items-center gap-2 mb-8">
<span class="text-3xl font-black tracking-tighter text-primary dark:text-white flex items-center gap-2">
<img src="../../assets/img/logo_inosakti.png" alt="InoSakti" class="h-25 w-auto" />
                        </span>
</div>
<h4 class="font-bold mb-4 uppercase text-sm tracking-widest text-slate-900 dark:text-white">Maps</h4>
<div class="mb-8 overflow-hidden rounded-xl h-48 border border-slate-200 dark:border-slate-700">
<iframe allowfullscreen="" height="100%" loading="lazy" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.954641957262!2d110.4391!3d-7.0142!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zN8KwMDAnNTEuMSJTIDExMMKwMjYnMjAuOCJF!5e0!3m2!1sen!2sid!4v1620000000000!5m2!1sen!2sid" style="border:0;" width="100%"></iframe>
</div>
</div>
<div>
<h4 class="font-bold mb-8 uppercase text-sm tracking-widest text-slate-900 dark:text-white">Lokasi</h4>
<ul class="space-y-6 text-sm text-slate-600 dark:text-slate-400">
<li class="flex gap-3">
<span class="material-symbols-outlined text-primary">location_on</span>
<span>Alamat Office and Workshop : Dinar Blok B No. 1, Semarang, Jawa Tengah</span>
</li>
<li class="flex gap-3">
<span class="material-symbols-outlined text-primary">location_on</span>
<span>Alamat Workshop : Leyangan Baru Blok A No. 12, Ungaran, Kab. Semarang</span>
</li>
<li class="flex gap-3">
<span class="material-symbols-outlined text-primary">location_on</span>
<span>Alamat Kursus dan Pelatihan : Lamper Tengah Gg. IX No. 11, Semarang</span>
</li>
</ul>
<h4 class="font-bold mt-8 mb-4 uppercase text-sm tracking-widest text-slate-900 dark:text-white">Kontak</h4>
<ul class="space-y-3 text-sm text-slate-600 dark:text-slate-400">
<li class="flex gap-3">
<span class="material-symbols-outlined text-primary">call</span>
<span>0882007085761</span>
</li>
<li class="flex gap-3">
<span class="material-symbols-outlined text-primary">mail</span>
<span>inosakti25@gmail.com</span>
</li>
</ul>
</div>
<div>
<h4 class="font-bold mb-8 uppercase text-sm tracking-widest text-slate-900 dark:text-white">Navigasi</h4>
<ul class="space-y-3 text-sm font-semibold text-slate-600 dark:text-slate-400">
<li><a class="hover:text-primary transition-colors" href="../../index.php">Home</a></li>
<li><a class="hover:text-primary transition-colors" href="../../index.php#layanan">Layanan Kami</a></li>
<li><a class="hover:text-primary transition-colors" href="../../index.php#produk">Produk Kami</a></li>
<li><a class="hover:text-primary transition-colors" href="../../index.php#portofolio">Portofolio</a></li>
<li><a class="hover:text-primary transition-colors" href="../../index.php#ecommerce">Ecommerce</a></li>
<li><a class="hover:text-primary transition-colors" href="../../index.php#social">Sosial Media</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Mitra &amp; Pelanggan Kami</a></li>
<li><a class="hover:text-primary transition-colors" href="../../index.php#testimonial">Testimonial</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Special Partners</a></li>
<li><a class="hover:text-primary transition-colors" href="#">Kontak</a></li>
</ul>
</div>
<div>
<h4 class="font-bold mb-8 uppercase text-sm tracking-widest text-slate-900 dark:text-white">Tinggalkan Pesan</h4>
<form class="space-y-4">
<div class="grid grid-cols-1 gap-4">
<input class="bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary w-full" placeholder="Nama Lengkap" type="text"/>
<input class="bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary w-full" placeholder="Email" type="email"/>
</div>
<textarea class="bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg text-sm px-4 py-3 focus:ring-2 focus:ring-primary w-full" placeholder="Pesan Anda" rows="4"></textarea>
<button class="w-full bg-primary py-3 rounded-lg font-bold text-white hover:bg-blue-800 transition-all">Kirim Pesan</button>
<button class="w-full border-2 border-primary text-primary py-3 rounded-lg font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-all mt-4">Konsultasi Sekarang</button>
</form>
</div>
</div>
<div class="pt-8 border-t border-slate-100 dark:border-slate-800 text-center">
<p class="text-sm text-slate-500 font-bold">© 2024 InoSakti Group. All Rights Reserved.</p>
</div>
</div>
</footer>


</body></html>
