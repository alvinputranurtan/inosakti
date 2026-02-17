<?php
$pageTitle = 'Checkout | InoSakti - Engineering & Technology Solutions';
$extraHead = <<<HTML
<style type="text/tailwindcss">
@layer components {
    .form-input { @apply w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all duration-200; }
    .checkout-card { @apply bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 shadow-sm; }
    .step-item { @apply flex items-center gap-2 text-sm font-bold transition-colors duration-500; }
    .step-dot { @apply w-8 h-8 rounded-full flex items-center justify-center text-xs border-2 transition-all duration-500; }
    .animate-fade-in { opacity: 0; transform: translateY(10px); animation: fadeIn 0.5s ease forwards; }
    @keyframes fadeIn { to { opacity: 1; transform: translateY(0); } }
}
</style>
HTML;
include __DIR__ . '/../../inc/header.php';
?>

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
<button onclick="window.location.href='<?php echo $basePath; ?>/pages/products/payment.php'" class="w-full bg-primary hover:bg-blue-800 text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2 group">
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

<?php
include __DIR__ . '/../../inc/footer.php';
?>
