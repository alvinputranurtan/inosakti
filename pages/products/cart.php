<?php
$pageTitle = 'Keranjang Belanja | InoSakti - Engineering & Technology Solutions';
$extraHead = <<<HTML
<style type="text/tailwindcss">
@layer components {
    .cart-card { @apply bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 shadow-sm overflow-hidden; }
    .step-item { @apply flex items-center gap-2 text-sm font-bold transition-colors duration-500; }
    .step-dot { @apply w-8 h-8 rounded-full flex items-center justify-center text-xs border-2 transition-all duration-500; }
    .quantity-btn { @apply w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors text-slate-600 dark:text-slate-300; }
    .animate-fade-in { opacity: 0; transform: translateY(10px); animation: fadeIn 0.5s ease forwards; }
    @keyframes fadeIn { to { opacity: 1; transform: translateY(0); } }
}
</style>
HTML;
include __DIR__ . '/../../inc/header.php';
?>

<main class="container mx-auto px-6 py-8">
<div class="flex items-center justify-center mb-12 max-w-2xl mx-auto">
<div class="step-item text-primary">
<div class="step-dot border-primary bg-primary text-white">1</div>
<span>Keranjang</span>
</div>
<div class="w-16 h-[2px] bg-slate-200 dark:bg-slate-700 mx-4"></div>
<div class="step-item text-slate-400">
<div class="step-dot border-slate-200 dark:border-slate-700">2</div>
<span>Pengiriman</span>
</div>
<div class="w-16 h-[2px] bg-slate-200 dark:bg-slate-700 mx-4"></div>
<div class="step-item text-slate-400">
<div class="step-dot border-slate-200 dark:border-slate-700">3</div>
<span>Pembayaran</span>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
<div class="lg:col-span-8 space-y-4">
<div class="flex items-center justify-between mb-4">
<h2 class="text-xl font-bold flex items-center gap-2">
<span class="material-symbols-outlined text-primary">shopping_cart</span>
                    Daftar Produk
                </h2>
<span class="text-sm font-medium text-slate-500">2 Item dalam keranjang</span>
</div>
<div class="cart-card animate-fade-in" style="animation-delay: 0.1s">
<div class="p-6 flex flex-col md:flex-row items-center gap-6 border-b border-slate-100 dark:border-slate-700">
<div class="flex items-center gap-4 w-full md:w-auto">
<input checked="" class="w-5 h-5 text-primary rounded border-slate-300 focus:ring-primary transition-all cursor-pointer" type="checkbox"/>
<div class="w-24 h-24 bg-slate-100 dark:bg-slate-700 rounded-xl overflow-hidden flex-shrink-0">
<img alt="Modul Early Warning System" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZ69rq5f_67UGOfdC-Ox9nJBtFiF8sLqTb7rjoDrB1d9gSeYPwlyYY0JN1SoQJxIavK6poe-6YoRIeL5bHT9aVLFOQ1ET0c6Jsp30st9MxQSICwQC6Cijv9Ov6jGkPfFmUC605e0KYPkaaAFGqxOAlJ3CdYiiOhGKigs3-hODAAcnAE_ureQA_fMhOxtKGSrQUqt8q0fbSrJeCu2C3Jqinekf5djTCYv-sKaIFfPqV6sKaQvWzRqbl4rFMtD9jiH4YcMqbH8jfQg"/>
</div>
</div>
<div class="flex-grow">
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
<div>
<h3 class="font-bold text-lg text-slate-800 dark:text-slate-100">Modul Early Warning System</h3>
<p class="text-xs text-slate-500 font-medium uppercase tracking-wider mt-1">IoT Core Series</p>
<p class="text-primary font-bold text-lg mt-2">Rp450.000</p>
</div>
<div class="flex items-center gap-6">
<div class="flex items-center gap-3">
<button class="quantity-btn"><span class="material-symbols-outlined text-sm">remove</span></button>
<span class="w-8 text-center font-bold">1</span>
<button class="quantity-btn"><span class="material-symbols-outlined text-sm">add</span></button>
</div>
<button class="p-2 text-slate-400 hover:text-red-500 transition-colors">
<span class="material-symbols-outlined">delete</span>
</button>
</div>
</div>
</div>
</div>
<div class="p-6 flex flex-col md:flex-row items-center gap-6 border-b border-slate-100 dark:border-slate-700 animate-fade-in" style="animation-delay: 0.2s">
<div class="flex items-center gap-4 w-full md:w-auto">
<input checked="" class="w-5 h-5 text-primary rounded border-slate-300 focus:ring-primary transition-all cursor-pointer" type="checkbox"/>
<div class="w-24 h-24 bg-slate-100 dark:bg-slate-700 rounded-xl overflow-hidden flex-shrink-0">
<img alt="Sensors Hub v2" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCklaz7cSSenl8RuyKNIhgzW9M5QKteWPOzm1tfOafDRRe2TckcfvzQVSwnF4b3gZNjK7HpDvYrEHnHWKpn5E-dW6OsC5K4nQuLS2wFgrafWz3uFlDGaTN_XDpROYyekaL7pnT0BbCwJCIHchO6ifyCyPdKnerzKqL91QUrpWJHwoeVYzBx0rhDqzZQToNv6T5c6W3p5Sf5U3QGrJd3wQ3Zr-RoiiwvLR_7ZWnXwyIwsUSnfSf_Gncikckgrg4ecc7Uo_lq3nnRSA"/>
</div>
</div>
<div class="flex-grow">
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
<div>
<h3 class="font-bold text-lg text-slate-800 dark:text-slate-100">Sensors Hub v2 AI Integrated</h3>
<p class="text-xs text-slate-500 font-medium uppercase tracking-wider mt-1">Smart Systems</p>
<p class="text-primary font-bold text-lg mt-2">Rp1.250.000</p>
</div>
<div class="flex items-center gap-6">
<div class="flex items-center gap-3">
<button class="quantity-btn"><span class="material-symbols-outlined text-sm">remove</span></button>
<span class="w-8 text-center font-bold">1</span>
<button class="quantity-btn"><span class="material-symbols-outlined text-sm">add</span></button>
</div>
<button class="p-2 text-slate-400 hover:text-red-500 transition-colors">
<span class="material-symbols-outlined">delete</span>
</button>
</div>
</div>
</div>
</div>
<div class="p-4 bg-slate-50 dark:bg-slate-900/50 flex items-center justify-between">
<label class="flex items-center gap-3 cursor-pointer group">
<input checked="" class="w-5 h-5 text-primary rounded border-slate-300 focus:ring-primary transition-all" type="checkbox"/>
<span class="text-sm font-bold text-slate-600 dark:text-slate-300 group-hover:text-primary transition-colors">Pilih Semua</span>
</label>
<button class="text-xs font-bold text-red-500 hover:underline">Hapus Semua Terpilih</button>
</div>
</div>
</div>
<div class="lg:col-span-4">
<div class="sticky top-24 space-y-4 animate-fade-in" style="animation-delay: 0.3s">
<div class="cart-card p-6">
<h2 class="text-lg font-bold mb-6">Ringkasan Belanja</h2>
<div class="space-y-4 mb-6">
<div class="flex justify-between text-sm text-slate-500 font-medium">
<span>Subtotal (2 Produk)</span>
<span>Rp1.700.000</span>
</div>
<div class="flex justify-between text-sm text-slate-500 font-medium">
<span>Estimasi Pajak (11%)</span>
<span>Rp187.000</span>
</div>
</div>
<hr class="border-slate-100 dark:border-slate-700 mb-6"/>
<div class="flex justify-between items-center mb-8">
<span class="text-base font-bold">Total Harga</span>
<span class="text-2xl font-extrabold text-primary">Rp1.887.000</span>
</div>
<a class="w-full bg-primary hover:bg-blue-800 text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2 group" href="<?php echo $basePath; ?>/pages/products/shipping.php">
                        Lanjut ke Pengiriman
                        <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
</a>
<button onclick="history.back()" class="w-full border-2 border-primary text-primary hover:bg-primary hover:text-white font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2 mt-4">
                        <span class="material-symbols-outlined">arrow_back</span>
                        Kembali
</button>
</div>
<div class="cart-card p-4 border-dashed border-2 flex items-center justify-between bg-slate-50/50">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary">sell</span>
<span class="text-xs font-bold">Gunakan Kode Promo</span>
</div>
<button class="text-primary text-xs font-bold hover:underline">Pilih Promo</button>
</div>
<div class="flex flex-col items-center gap-2 text-[10px] text-slate-400 font-medium px-4">
<div class="flex items-center gap-1">
<span class="material-symbols-outlined text-xs">verified_user</span>
<span>Jaminan Keaslian Produk InoSakti</span>
</div>
</div>
</div>
</div>
</div>
</main>

<?php
include __DIR__ . '/../../inc/footer.php';
?>
