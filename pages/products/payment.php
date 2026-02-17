<?php
$pageTitle = 'Pembayaran | InoSakti - Engineering & Technology Solutions';
$extraHead = <<<HTML
<style type="text/tailwindcss">
@layer components {
    .checkout-card { @apply bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-6 shadow-sm; }
    .step-item { @apply flex items-center gap-2 text-sm font-bold transition-colors duration-500; }
    .step-dot { @apply w-8 h-8 rounded-full flex items-center justify-center text-xs border-2 transition-all duration-500; }
    .accordion-content { display: none; }
    input:checked ~ .accordion-content { display: block; }
    .payment-method-label { @apply flex items-center justify-between p-4 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors rounded-xl border border-transparent; }
    input:checked + .payment-method-label { @apply border-primary bg-primary/5; }
}
</style>
HTML;
include __DIR__ . '/../../inc/header.php';
?>

<main class="container mx-auto px-6 py-8">
<div class="flex items-center justify-center mb-12 max-w-2xl mx-auto">
<div class="step-item text-primary">
<div class="step-dot border-primary bg-primary/10 text-primary">
<span class="material-symbols-outlined text-sm">check</span>
</div>
<span>Keranjang</span>
</div>
<div class="w-16 h-[2px] bg-primary mx-4"></div>
<div class="step-item text-primary">
<div class="step-dot border-primary bg-primary/10 text-primary">
<span class="material-symbols-outlined text-sm">check</span>
</div>
<span>Pengiriman</span>
</div>
<div class="w-16 h-[2px] bg-primary mx-4"></div>
<div class="step-item text-primary">
<div class="step-dot border-primary bg-primary text-white">3</div>
<span>Pembayaran</span>
</div>
</div>
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
<div class="lg:col-span-8 space-y-6">
<section class="checkout-card">
<div class="flex items-center gap-3 mb-8">
<span class="material-symbols-outlined text-primary">account_balance_wallet</span>
<h2 class="text-lg font-bold">Pilih Metode Pembayaran</h2>
</div>
<div class="space-y-4">
<div class="border border-slate-100 dark:border-slate-700 rounded-2xl overflow-hidden">
<input checked="" class="hidden peer" id="bank-transfer" name="payment-group" type="radio"/>
<label class="payment-method-label peer-checked:bg-slate-50 dark:peer-checked:bg-slate-700/30" for="bank-transfer">
<div class="flex items-center gap-4">
<span class="material-symbols-outlined text-slate-400">account_balance</span>
<span class="font-bold">Transfer Bank (Virtual Account)</span>
</div>
<span class="material-symbols-outlined transition-transform duration-300">expand_more</span>
</label>
<div class="accordion-content p-6 pt-0 border-t border-slate-50 dark:border-slate-700/50">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
<label class="flex items-center justify-between p-4 border rounded-xl cursor-pointer hover:border-primary transition-all">
<div class="flex items-center gap-3">
<div class="w-10 h-6 bg-slate-100 rounded flex items-center justify-center font-black text-[10px] text-blue-800">BCA</div>
<span class="text-sm font-bold">BCA Virtual Account</span>
</div>
<input class="text-primary focus:ring-primary" name="bank-option" type="radio"/>
</label>
<label class="flex items-center justify-between p-4 border rounded-xl cursor-pointer hover:border-primary transition-all">
<div class="flex items-center gap-3">
<div class="w-10 h-6 bg-slate-100 rounded flex items-center justify-center font-black text-[10px] text-orange-600">MANDIRI</div>
<span class="text-sm font-bold">Mandiri VA</span>
</div>
<input class="text-primary focus:ring-primary" name="bank-option" type="radio"/>
</label>
<label class="flex items-center justify-between p-4 border rounded-xl cursor-pointer hover:border-primary transition-all">
<div class="flex items-center gap-3">
<div class="w-10 h-6 bg-slate-100 rounded flex items-center justify-center font-black text-[10px] text-orange-700">BNI</div>
<span class="text-sm font-bold">BNI Virtual Account</span>
</div>
<input class="text-primary focus:ring-primary" name="bank-option" type="radio"/>
</label>
</div>
</div>
</div>
<div class="border border-slate-100 dark:border-slate-700 rounded-2xl overflow-hidden">
<input class="hidden peer" id="e-wallet" name="payment-group" type="radio"/>
<label class="payment-method-label peer-checked:bg-slate-50 dark:peer-checked:bg-slate-700/30" for="e-wallet">
<div class="flex items-center gap-4">
<span class="material-symbols-outlined text-slate-400">payments</span>
<span class="font-bold">E-Wallet &amp; QRIS</span>
</div>
<span class="material-symbols-outlined transition-transform duration-300">expand_more</span>
</label>
<div class="accordion-content p-6 pt-0 border-t border-slate-50 dark:border-slate-700/50">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
<label class="flex items-center justify-between p-4 border rounded-xl cursor-pointer hover:border-primary transition-all">
<div class="flex items-center gap-3">
<div class="w-10 h-6 bg-slate-100 rounded flex items-center justify-center font-black text-[10px] text-purple-700">OVO</div>
<span class="text-sm font-bold">OVO</span>
</div>
<input class="text-primary focus:ring-primary" name="wallet-option" type="radio"/>
</label>
<label class="flex items-center justify-between p-4 border rounded-xl cursor-pointer hover:border-primary transition-all">
<div class="flex items-center gap-3">
<div class="w-10 h-6 bg-slate-100 rounded flex items-center justify-center font-black text-[10px] text-blue-500">DANA</div>
<span class="text-sm font-bold">DANA</span>
</div>
<input class="text-primary focus:ring-primary" name="wallet-option" type="radio"/>
</label>
<label class="flex items-center justify-between p-4 border rounded-xl cursor-pointer hover:border-primary transition-all sm:col-span-2">
<div class="flex items-center gap-3">
<div class="w-10 h-6 bg-slate-100 rounded flex items-center justify-center font-black text-[10px] text-red-600">QRIS</div>
<span class="text-sm font-bold">QRIS (All E-Wallet)</span>
</div>
<input class="text-primary focus:ring-primary" name="wallet-option" type="radio"/>
</label>
</div>
</div>
</div>
<div class="border border-slate-100 dark:border-slate-700 rounded-2xl overflow-hidden">
<input class="hidden peer" id="credit-card" name="payment-group" type="radio"/>
<label class="payment-method-label peer-checked:bg-slate-50 dark:peer-checked:bg-slate-700/30" for="credit-card">
<div class="flex items-center gap-4">
<span class="material-symbols-outlined text-slate-400">credit_card</span>
<span class="font-bold">Kartu Kredit / Debit</span>
</div>
<span class="material-symbols-outlined transition-transform duration-300">expand_more</span>
</label>
<div class="accordion-content p-6 pt-0 border-t border-slate-50 dark:border-slate-700/50">
<div class="mt-4 space-y-4">
<div class="space-y-1.5">
<label class="text-[10px] font-bold text-slate-500 uppercase">Nomor Kartu</label>
<input class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 outline-none" placeholder="0000 0000 0000 0000" type="text"/>
</div>
<div class="grid grid-cols-2 gap-4">
<div class="space-y-1.5">
<label class="text-[10px] font-bold text-slate-500 uppercase">Masa Berlaku</label>
<input class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 outline-none" placeholder="MM / YY" type="text"/>
</div>
<div class="space-y-1.5">
<label class="text-[10px] font-bold text-slate-500 uppercase">CVV</label>
<input class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/20 outline-none" placeholder="123" type="password"/>
</div>
</div>
</div>
</div>
</div>
</div>
</section>
</div>
<div class="lg:col-span-4">
<div class="sticky top-24 space-y-4">
<div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-100 dark:border-orange-800 rounded-2xl p-4 flex items-center justify-between">
<div class="flex items-center gap-3">
<span class="material-symbols-outlined text-orange-600 animate-pulse">timer</span>
<span class="text-sm font-bold text-orange-800 dark:text-orange-400">Selesaikan dalam</span>
</div>
<span class="text-sm font-black text-orange-600">24:00:00</span>
</div>
<div class="checkout-card">
<h2 class="text-lg font-bold mb-6">Ringkasan Tagihan</h2>
<div class="space-y-4 mb-6">
<div class="flex gap-4">
<div class="w-14 h-14 bg-slate-100 dark:bg-slate-700 rounded-lg overflow-hidden flex-shrink-0">
<img alt="Product" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZ69rq5f_67UGOfdC-Ox9nJBtFiF8sLqTb7rjoDrB1d9gSeYPwlyYY0JN1SoQJxIavK6poe-6YoRIeL5bHT9aVLFOQ1ET0c6Jsp30st9MxQSICwQC6Cijv9Ov6jGkPfFmUC605e0KYPkaaAFGqxOAlJ3CdYiiOhGKigs3-hODAAcnAE_ureQA_fMhOxtKGSrQUqt8q0fbSrJeCu2C3Jqinekf5djTCYv-sKaIFfPqV6sKaQvWzRqbl4rFMtD9jiH4YcMqbH8jfQg"/>
</div>
<div class="flex-grow">
<h4 class="text-xs font-bold leading-tight line-clamp-1">Modul Early Warning System</h4>
<div class="flex justify-between items-center mt-1">
<span class="text-[10px] text-slate-500 font-bold uppercase">Applied Tech</span>
<span class="text-xs font-bold text-slate-700 dark:text-slate-300">1x</span>
</div>
</div>
</div>
</div>
<div class="space-y-3 mb-6 border-t border-slate-100 dark:border-slate-700 pt-6">
<div class="flex justify-between text-sm text-slate-500 font-medium">
<span>Subtotal</span>
<span>Rp450.000</span>
</div>
<div class="flex justify-between text-sm text-slate-500 font-medium">
<span>Biaya Pengiriman</span>
<span>Rp15.000</span>
</div>
<div class="flex justify-between text-sm text-slate-500 font-medium">
<span>Biaya Layanan</span>
<span>Rp1.000</span>
</div>
<div class="flex justify-between items-center pt-2 border-t border-slate-100 dark:border-slate-700">
<span class="text-base font-bold">Total Tagihan</span>
<span class="text-xl font-extrabold text-primary">Rp466.000</span>
</div>
</div>
<button class="w-full bg-primary hover:bg-blue-800 text-white font-bold py-4 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2 group" onclick="window.location.href='<?php echo $basePath; ?>/pages/products/qris-payment.php'">
                        Bayar Sekarang
                        <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">verified_user</span>
</button>
<button onclick="history.back()" class="w-full border-2 border-primary text-primary hover:bg-primary hover:text-white font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2 mt-4">
                        <span class="material-symbols-outlined">arrow_back</span>
                        Kembali
</button>
</div>
<div class="checkout-card p-4">
<div class="flex items-start gap-3">
<span class="material-symbols-outlined text-slate-400 text-lg">location_on</span>
<div>
<p class="text-[10px] font-bold text-slate-500 uppercase">Dikirim ke:</p>
<p class="text-xs font-bold mt-0.5 line-clamp-1">John Doe • Jl. Teknologi No. 42...</p>
</div>
</div>
</div>
</div>
</div>
</div>
</main>
<?php
include __DIR__ . '/../../inc/footer.php';
exit;
?>
<div class="flex items-center gap-2 mb-8">
<span class="text-3xl font-black tracking-tighter text-primary dark:text-white flex items-center gap-2">
<img src="<?php echo $basePath; ?>/assets/img/logo_inosakti.png" alt="InoSakti" class="h-25 w-auto" />
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
