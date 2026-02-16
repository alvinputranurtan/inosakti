<!DOCTYPE html>
<html class="scroll-smooth" lang="id"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Pembayaran QRIS | InoSakti - Engineering &amp; Technology Solutions</title>
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
            .checkout-card {
                @apply bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 shadow-sm;
            }
            @keyframes scan {
                0% { top: 0%; }
                100% { top: 100%; }
            }
            .scan-line {
                height: 2px;
                background: linear-gradient(to right, transparent, #1d4ed8, transparent);
                position: absolute;
                width: 100%;
                z-index: 10;
                animation: scan 3s linear infinite;
                box-shadow: 0 0 8px #1d4ed8;
            }
            @keyframes pulse-red {
                0%, 100% { color: #dc2626; opacity: 1; }
                50% { color: #dc2626; opacity: 0.5; }
            }
            .timer-pulse {
                animation: pulse-red 1s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }
            @keyframes slide-up {
                from { transform: translateY(20px); opacity: 0; }
                to { transform: translateY(0); opacity: 1; }
            }
            .modal-entrance {
                animation: slide-up 0.5s ease-out forwards;
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
<body class="bg-[#f8fafc] dark:bg-background-dark text-slate-900 dark:text-slate-100 font-sans transition-colors duration-300 pt-16 min-h-screen">
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
<main class="container mx-auto px-6 py-12 flex items-center justify-center">
<div class="max-w-5xl w-full grid grid-cols-1 lg:grid-cols-12 gap-8 items-start modal-entrance">
<div class="lg:col-span-7">
<div class="checkout-card overflow-hidden">
<div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-50 dark:border-slate-700/50">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-primary">qr_code_2</span>
<h2 class="text-xl font-bold">Pembayaran QRIS</h2>
</div>
<div class="h-8 w-auto">
<img alt="QRIS Logo" class="h-full object-contain grayscale brightness-0 dark:brightness-200" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDGnMGKMurYYfAinzqS0tZp6qa2H3rluWfzXKH6FfMXrB5uXY_1ChIVl_xWShQPhYA885jSj8sjuHCEokOtUVRrHXzAr0bKK0nqsoqEpK7v3gKh2UfxoiVMJDQmdcc8r2bbjMqiwCkKPk_cJT-NxH8Cxuqg_3ov2QXDS5cgKtv6Oow1HWvbIqv3_IVQcq81oUvfC4rCzaX2J1blhCPmwAwKTJgT3Cpx9FdW6EtcRpuerUABEgE4-v8kgt_fEkC0M_tScDinfPbVQg"/>
</div>
</div>
<div class="flex flex-col items-center py-6">
<div class="relative p-6 bg-white rounded-2xl shadow-[0_0_40px_rgba(30,58,138,0.15)] dark:shadow-[0_0_40px_rgba(30,58,138,0.3)] border border-slate-100 group">
<div class="scan-line top-0 left-0"></div>
<div class="relative w-64 h-64 bg-white">
<img alt="Payment QR Code" class="w-full h-full object-contain" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBL1m1xGH527cx6DS8JCTHf19shKs0gRNoxBXIApzjiWAH7w48svXK8FROVgaI_7HKYO4apnzmsNDPUyPXWQCDei6ap727IVD2ltsSNKgjuEvppqYN8hS_kaClQIE37xYBxiBgrJThk0u1R-k8LMH9gMb1HQ7Gsxfnj0zSMVl6aNmTjg30di_6wdb7f-qefn7ChYWp-MjWp40RLRlJg2PkJ6hQu8bL18auL3XamYDiwCS4mo8sG01Ecx1Q-mG1I1T_pD_N2G9toSg"/>
</div>
</div>
<div class="mt-10 w-full max-w-sm">
<h3 class="font-bold text-sm mb-4 text-center text-slate-500 uppercase tracking-wider">Petunjuk Pembayaran</h3>
<div class="space-y-4">
<div class="flex gap-4 items-start">
<div class="w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-bold shrink-0">1</div>
<p class="text-sm font-medium">Buka aplikasi e-wallet (GoPay, OVO, Dana, LinkAja) atau Mobile Banking Anda.</p>
</div>
<div class="flex gap-4 items-start">
<div class="w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-bold shrink-0">2</div>
<p class="text-sm font-medium">Pilih menu <span class="font-bold">Scan QR</span> dan arahkan kamera ke kode QR di atas.</p>
</div>
<div class="flex gap-4 items-start">
<div class="w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-bold shrink-0">3</div>
<p class="text-sm font-medium">Periksa detail tagihan lalu klik <span class="font-bold">Bayar</span> untuk menyelesaikan transaksi.</p>
</div>
</div>
</div>
</div>
</div>
</div>
<div class="lg:col-span-5 space-y-4">
<div class="bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-2xl p-4 flex items-center justify-between">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-red-600 timer-pulse">timer</span>
<span class="text-sm font-bold text-red-800 dark:text-red-400">Selesaikan dalam</span>
</div>
<span class="text-lg font-black text-red-600 timer-pulse">14:59</span>
</div>
<div class="checkout-card">
<h2 class="text-lg font-bold mb-6">Ringkasan Pembayaran</h2>
<div class="space-y-4 mb-6">
<div class="flex items-center justify-between text-sm">
<span class="text-slate-500">ID Pesanan</span>
<span class="font-bold">#INO-8842109</span>
</div>
<div class="flex items-center justify-between text-sm">
<span class="text-slate-500">Metode</span>
<span class="font-bold">QRIS Dynamic</span>
</div>
</div>
<div class="space-y-3 mb-8 pt-6 border-t border-slate-100 dark:border-slate-700">
<div class="flex justify-between items-center">
<span class="text-base font-bold text-slate-600 dark:text-slate-400">Total Bayar</span>
<span class="text-2xl font-extrabold text-primary dark:text-white">Rp466.000</span>
</div>
</div>
<div class="space-y-4">
<button class="w-full bg-primary hover:bg-blue-800 text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2 group">
                        Cek Status Pembayaran
                        <span class="material-symbols-outlined text-xl group-hover:scale-110 transition-transform">sync</span>
</button>
<button onclick="history.back()" class="w-full text-sm font-bold text-slate-500 hover:text-primary transition-colors text-center block py-2">
                        Ganti Metode Pembayaran
                    </button>
</div>
<div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-700">
<div class="flex items-center gap-3 justify-center">
<div class="p-2 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
<span class="material-symbols-outlined text-slate-400 text-xl">verified_user</span>
</div>
<p class="text-[10px] text-slate-400 font-medium leading-tight">
                            Pembayaran Aman &amp; Terenkripsi<br/>
                            Diawasi oleh Bank Indonesia
                        </p>
</div>
</div>
</div>
<div class="checkout-card p-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
<span class="material-symbols-outlined text-primary text-xl">help</span>
</div>
<div>
<p class="text-xs font-bold">Butuh Bantuan?</p>
<p class="text-[10px] text-slate-500 font-medium mt-0.5">Hubungi dukungan teknis InoSakti</p>
</div>
<button class="ml-auto material-symbols-outlined text-slate-300 hover:text-primary transition-colors">arrow_forward_ios</button>
</div>
</div>
</div>
</div>
</main>
<footer class="bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 pt-24 pb-12 mt-auto">
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