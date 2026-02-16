<!DOCTYPE html>
<html class="scroll-smooth" lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Checkout | InoSakti - Engineering &amp; Technology Solutions</title>
<meta content="InoSakti provides Applied Engineering &amp; Technology Solutions, integrated smart systems, AI, IoT, and R&amp;D." name="description"/>
<link href="https://fonts.googleapis.com" rel="preconnect"/>
<link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<style type="text/tailwindcss">
        @layer base {
            :root {
                --primary: #1e3a8a;
                --secondary: #0f172a;
                --accent-blue: #1d4ed8;
                --accent-green: #16a34a;
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
            .form-input {
                @apply w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all duration-200;
            }
            .checkout-card {
                @apply bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 shadow-sm;
            }
            .step-item {
                @apply flex items-center gap-2 text-sm font-bold transition-colors duration-500;
            }
            .step-dot {
                @apply w-8 h-8 rounded-full flex items-center justify-center text-xs border-2 transition-all duration-500;
            }
            .animate-fade-in {
                opacity: 0;
                transform: translateY(10px);
                animation: fadeIn 0.5s ease forwards;
            }
            @keyframes fadeIn {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
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
</head>
<body class="bg-[#f8fafc] dark:bg-background-dark text-slate-900 dark:text-slate-100 font-sans transition-colors duration-300 pt-16">
<header class="fixed top-0 w-full z-50 glass-effect border-b border-slate-100 dark:border-slate-800">
<div class="container mx-auto px-6 h-16 flex items-center justify-between">
<div class="flex items-center gap-2">
<div class="flex items-center gap-2">
                    <img src="../../assets/img/logo_inosakti.png" alt="InoSakti" class="h-20 w-auto" />
                </div>
</div>
<nav class="hidden lg:flex items-center gap-6 font-bold text-sm">
<a class="hover:text-primary transition-colors" href="../../index.php">Home</a>
<a class="hover:text-primary transition-colors" href="shop.php">Belanja</a>
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
<main class="container mx-auto px-6 py-8">
<div class="flex items-center justify-center mb-12 max-w-2xl mx-auto">
<div class="step-item text-slate-400">
<div class="step-dot border-slate-200 dark:border-slate-700">1</div>
<span>Keranjang</span>
</div>
<div class="w-16 h-[2px] bg-slate-200 dark:bg-slate-700 mx-4"></div>
<div class="step-item text-primary">
<div class="step-dot border-primary bg-primary text-white">2</div>
<span>Pengiriman</span>
</div>
<div class="w-16 h-[2px] bg-slate-200 dark:bg-slate-700 mx-4"></div>
<div class="step-item text-slate-400">
<div class="step-dot border-slate-200 dark:border-slate-700">3</div>
<span>Pembayaran</span>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
<div class="lg:col-span-8 space-y-6">
<section class="checkout-card animate-fade-in" style="animation-delay: 0.1s">
<div class="flex items-center gap-3 mb-6">
<span class="material-symbols-outlined text-primary">local_shipping</span>
<h2 class="text-lg font-bold">Data Pengiriman</h2>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
<div class="space-y-1.5">
<label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Lengkap</label>
<input class="form-input" placeholder="Contoh: John Doe" type="text"/>
</div>
<div class="space-y-1.5">
<label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Nomor Telepon</label>
<input class="form-input" placeholder="0812xxxx" type="tel"/>
</div>
<div class="md:col-span-2 space-y-1.5">
<label class="text-xs font-bold text-slate-500 uppercase tracking-wider">Alamat Lengkap</label>
<textarea class="form-input resize-none" placeholder="Jl. Teknologi No. 42, Kota Digital..." rows="3"></textarea>
</div>
</div>
</section>
<section class="checkout-card animate-fade-in" style="animation-delay: 0.2s">
<div class="flex items-center gap-3 mb-6">
<span class="material-symbols-outlined text-primary">package_2</span>
<h2 class="text-lg font-bold">Pilih Kurir</h2>
</div>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
<label class="relative flex flex-col p-4 border-2 border-primary bg-primary/5 rounded-xl cursor-pointer">
<input checked="" class="hidden" name="courier" type="radio"/>
<span class="font-bold text-sm">JNE Express</span>
<span class="text-xs text-slate-500 mt-1">Regular (2-3 Hari)</span>
<span class="text-sm font-bold text-primary mt-2">Rp15.000</span>
<div class="absolute top-3 right-3">
<span class="material-symbols-outlined text-primary text-lg">check_circle</span>
</div>
</label>
<label class="relative flex flex-col p-4 border-2 border-slate-100 dark:border-slate-700 rounded-xl cursor-pointer hover:border-primary/50 transition-colors">
<input class="hidden" name="courier" type="radio"/>
<span class="font-bold text-sm">Sicepat</span>
<span class="text-xs text-slate-500 mt-1">Halu (3-4 Hari)</span>
<span class="text-sm font-bold text-slate-700 dark:text-slate-300 mt-2">Rp12.000</span>
</label>
<label class="relative flex flex-col p-4 border-2 border-slate-100 dark:border-slate-700 rounded-xl cursor-pointer hover:border-primary/50 transition-colors">
<input class="hidden" name="courier" type="radio"/>
<span class="font-bold text-sm">J&amp;T Express</span>
<span class="text-xs text-slate-500 mt-1">EZ (2-3 Hari)</span>
<span class="text-sm font-bold text-slate-700 dark:text-slate-300 mt-2">Rp14.000</span>
</label>
</div>
</section>
</div>
<div class="lg:col-span-4">
<div class="sticky top-24 space-y-4 animate-fade-in" style="animation-delay: 0.3s">
<div class="checkout-card">
<h2 class="text-lg font-bold mb-6">Ringkasan Belanja</h2>
<div class="space-y-4 mb-6">
<div class="flex gap-4">
<div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-lg overflow-hidden flex-shrink-0">
<img alt="Product" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZ69rq5f_67UGOfdC-Ox9nJBtFiF8sLqTb7rjoDrB1d9gSeYPwlyYY0JN1SoQJxIavK6poe-6YoRIeL5bHT9aVLFOQ1ET0c6Jsp30st9MxQSICwQC6Cijv9Ov6jGkPfFmUC605e0KYPkaaAFGqxOAlJ3CdYiiOhGKigs3-hODAAcnAE_ureQA_fMhOxtKGSrQUqt8q0fbSrJeCu2C3Jqinekf5djTCYv-sKaIFfPqV6sKaQvWzRqbl4rFMtD9jiH4YcMqbH8jfQg"/>
</div>
<div class="flex-grow">
<h4 class="text-sm font-bold leading-tight line-clamp-2">Modul Early Warning System</h4>
<div class="flex justify-between items-center mt-1">
<span class="text-xs text-slate-500">1x</span>
<span class="text-sm font-bold">Rp450.000</span>
</div>
</div>
</div>
</div>
<hr class="border-slate-100 dark:border-slate-700 mb-6"/>
<div class="space-y-3 mb-8">
<div class="flex justify-between text-sm text-slate-500 font-medium">
<span>Subtotal</span>
<span>Rp450.000</span>
</div>
<div class="flex justify-between text-sm text-slate-500 font-medium">
<span>Biaya Pengiriman</span>
<span>Rp15.000</span>
</div>
<div class="flex justify-between items-center pt-2">
<span class="text-base font-bold">Total Tagihan</span>
<span class="text-xl font-extrabold text-primary">Rp465.000</span>
</div>
</div>
<button onclick="window.location.href='payment.php'" class="w-full bg-primary hover:bg-blue-800 text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2 group">
                            Proses Pembayaran
                            <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
</button>
<button onclick="history.back()" class="w-full border-2 border-primary text-primary hover:bg-primary hover:text-white font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2 mt-4">
                        <span class="material-symbols-outlined">arrow_back</span>
                        Kembali
</button>
</div>
<div class="checkout-card p-4 border-dashed border-2 flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary">sell</span>
<span class="text-xs font-bold">Ada Kode Promo?</span>
</div>
<button class="text-primary text-xs font-bold hover:underline">Masukkan</button>
</div>
</div>
</div>
</div>
</main>
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
