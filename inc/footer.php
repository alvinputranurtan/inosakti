<?php
// footer.php
$req = $_SERVER['REQUEST_URI'] ?? '/';
$basePath = $basePath ?? (preg_match('#^/inosakti\.com(/|$)#', $req) ? '/inosakti.com' : '');
?>

<footer class="bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 pt-24 pb-12" id="contact">
  <div class="container mx-auto px-6">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-12 mb-20">
      <div>
        <div class="flex items-center gap-2 mb-8">
          <span class="text-3xl font-black tracking-tighter text-primary dark:text-white flex items-center gap-2">
            <img src="<?php echo $basePath; ?>/assets/img/logo_inosakti.png" alt="InoSakti" class="h-21 sm:h-25 w-auto" />
          </span>
        </div>
        <h4 class="font-bold mb-4 uppercase text-sm tracking-widest text-slate-900 dark:text-white">Maps</h4>
        <div class="mb-8 overflow-hidden rounded-xl h-48 border border-slate-200 dark:border-slate-700">
          <iframe allowfullscreen height="100%" loading="lazy"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.954641957262!2d110.4391!3d-7.0142!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zN8KwMDAnNTEuMSJTIDExMMKwMjYnMjAuOCJF!5e0!3m2!1sen!2sid!4v1620000000000!5m2!1sen!2sid"
            style="border:0;" width="100%"></iframe>
        </div>
      </div>

      <div>
        <h4 class="font-bold mb-8 uppercase text-sm tracking-widest text-slate-900 dark:text-white">Lokasi</h4>
        <ul class="space-y-6 text-sm text-slate-600 dark:text-slate-400">
          <li class="flex gap-3">
            <span class="material-symbols-outlined text-primary">location_on</span>
            <span>Alamat Office and Workshop : Jalan Dinar Mas Utara IV No.5, Meteseh, Tembalang, Kota Semarang, Jawa Tengah</span>
          </li>
          <li class="flex gap-3">
            <span class="material-symbols-outlined text-primary">location_on</span>
            <span>Alamat Workshop : Jalan Bukit Leyangan Damai No.66, Leyangan, Ungaran Timur, Kab. Semarang, Jawa Tengah</span>
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
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/">Home</a></li>
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/index.php#layanan">Layanan Kami</a></li>
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/index.php#produk">Produk Kami</a></li>
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/index.php#portofolio">Portofolio</a></li>
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/index.php#ecommerce">Ecommerce</a></li>
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/index.php#social">Sosial Media</a></li>
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/index.php#mitra">Mitra &amp; Pelanggan Kami</a></li>
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/index.php#testimonial">Testimonial</a></li>
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/index.php#special-partners">Special Partners</a></li>
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/index.php#contact">Kontak</a></li>
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
          <button type="button" class="w-full border-2 border-primary text-primary py-3 rounded-lg font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-all mt-4">Konsultasi Sekarang</button>
        </form>
      </div>
    </div>

    <div class="pt-8 border-t border-slate-100 dark:border-slate-800 text-center">
      <p class="text-sm text-slate-500 font-bold">© 2024 InoSakti Group. All Rights Reserved.</p>
    </div>
  </div>
</footer>

<?php if (!empty($extraScripts)) {
    echo $extraScripts;
} ?>

<script>
  // header shadow
  window.addEventListener('scroll', () => {
    const header = document.querySelector('header');
    if (!header) return;
    header.classList.toggle('shadow-sm', window.scrollY > 50);
  });

  // reveal anim
  const observerOptions = { threshold: 0.1 };
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('opacity-100', 'translate-y-0');
        entry.target.classList.remove('opacity-0', 'translate-y-10');
      }
    });
  }, observerOptions);

  document.querySelectorAll('section > div').forEach(el => {
    el.classList.add('transition-all', 'duration-700', 'opacity-0', 'translate-y-10');
    observer.observe(el);
  });

  // Register Service Worker
  (function () {
    if (!("serviceWorker" in navigator)) return;
    window.addEventListener("load", function () {
      navigator.serviceWorker.register("<?php echo $basePath; ?>/sw.js", {
        scope: "<?php echo $basePath; ?>/"
      }).catch(console.error);
    });
  })();
</script>

</body>
</html>