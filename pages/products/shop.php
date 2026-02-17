<?php
$pageTitle = 'Belanja | InoSakti - Engineering & Technology Solutions';
$extraHead = <<<HTML
<style type="text/tailwindcss">
@layer components {
    .product-card { @apply bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1 opacity-0 translate-y-10; }
    .filter-dropdown { @apply bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-1.5 text-xs font-semibold flex items-center gap-2; }
    .category-badge { @apply absolute bottom-3 left-3 z-10 bg-slate-100/95 dark:bg-slate-800/95 text-slate-800 dark:text-slate-200 text-[9px] font-bold px-2.5 py-1 rounded-full shadow-sm; }
}
</style>
HTML;
include __DIR__.'/../../inc/header.php';
?>

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
                <img alt="Modul Early Warning System" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="<?php echo $basePath; ?>/assets/img/produk_1.png"/>
            </div>
            <div class="p-5">
                <h3 class="font-bold text-sm mb-2">Modul Early Warning System</h3>
                <p class="text-slate-500 dark:text-slate-400 text-[10px] leading-relaxed mb-3">IoT menggunakan ESP32 untuk deteksi dini kebencanaan dan sistem peringatan real-time.</p>
                <div class="inline-block bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded-full text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-4">
                    Rp.450.000
                </div>
                <div class="flex gap-2">
                    <button class="flex-grow bg-primary hover:bg-blue-800 text-white text-[11px] font-bold py-2 rounded-lg transition-colors">Lihat deskripsi</button>
                    <button onclick="window.location.href='<?php echo $basePath; ?>/pages/products/cart.php'" class="w-10 bg-primary hover:bg-blue-800 text-white rounded-lg flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-lg">shopping_cart</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="product-card group">
            <div class="relative aspect-square bg-slate-100 dark:bg-slate-700 overflow-hidden">
                <span class="category-badge">Sistem Pintar</span>
                <img alt="Modul Monitoring Greenhouse" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="<?php echo $basePath; ?>/assets/img/produk_2.png"/>
            </div>
            <div class="p-5">
                <h3 class="font-bold text-sm mb-2">Modul Monitoring Greenhouse</h3>
                <p class="text-slate-500 dark:text-slate-400 text-[10px] leading-relaxed mb-3">Monitoring Greenhouse menggunakan ESP32 dengan sensor suhu, kelembaban, dan intensitas cahaya.</p>
                <div class="inline-block bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded-full text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-4">
                    Rp.450.000
                </div>
                <div class="flex gap-2">
                    <button class="flex-grow bg-primary hover:bg-blue-800 text-white text-[11px] font-bold py-2 rounded-lg transition-colors">Lihat deskripsi</button>
                    <button onclick="window.location.href='<?php echo $basePath; ?>/pages/products/cart.php'" class="w-10 bg-primary hover:bg-blue-800 text-white rounded-lg flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-lg">shopping_cart</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="product-card group">
            <div class="relative aspect-square bg-slate-100 dark:bg-slate-700 overflow-hidden">
                <span class="category-badge">Komponen</span>
                <img alt="Raspberry Pi 4" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="<?php echo $basePath; ?>/assets/img/produk_3.png"/>
            </div>
            <div class="p-5">
                <h3 class="font-bold text-sm mb-2">Raspberry Pi 4</h3>
                <p class="text-slate-500 dark:text-slate-400 text-[10px] leading-relaxed mb-3">Raspberry pi 4 kondisi baru dengan RAM varian tinggi untuk kebutuhan komputasi edge.</p>
                <div class="inline-block bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded-full text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-4">
                    Rp.450.000
                </div>
                <div class="flex gap-2">
                    <button class="flex-grow bg-primary hover:bg-blue-800 text-white text-[11px] font-bold py-2 rounded-lg transition-colors">Lihat deskripsi</button>
                    <button onclick="window.location.href='<?php echo $basePath; ?>/pages/products/cart.php'" class="w-10 bg-primary hover:bg-blue-800 text-white rounded-lg flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-lg">shopping_cart</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="product-card group">
            <div class="relative aspect-square bg-slate-100 dark:bg-slate-700 overflow-hidden">
                <span class="category-badge">Komponen</span>
                <img alt="Adaptor 12v 3a" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" src="<?php echo $basePath; ?>/assets/img/produk_4.png"/>
            </div>
            <div class="p-5">
                <h3 class="font-bold text-sm mb-2">Adaptor 12v 3a</h3>
                <p class="text-slate-500 dark:text-slate-400 text-[10px] leading-relaxed mb-3">Merk Taffware, output stabil 12v 3a untuk power supply proyek elektronik dan router.</p>
                <div class="inline-block bg-slate-100 dark:bg-slate-700 px-3 py-1 rounded-full text-[11px] font-bold text-slate-700 dark:text-slate-300 mb-4">
                    Rp.450.000
                </div>
                <div class="flex gap-2">
                    <button onclick="window.location.href='<?php echo $basePath; ?>/pages/products/cart.php'" class="flex-grow bg-primary hover:bg-blue-800 text-white text-[11px] font-bold py-2 rounded-lg transition-colors">Lihat deskripsi</button>
                    <button onclick="window.location.href='<?php echo $basePath; ?>/pages/products/cart.php'" class="w-10 bg-primary hover:bg-blue-800 text-white rounded-lg flex items-center justify-center transition-colors">
                        <span class="material-symbols-outlined text-lg">shopping_cart</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$extraScripts = <<<HTML
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
HTML;
include __DIR__.'/../../inc/footer.php';
exit;
