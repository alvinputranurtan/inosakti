<?php
// footer.php
// reuse the same basePath logic from config
require_once __DIR__.'/config.php';
$basePath = $basePath ?? '';
?>

<footer class="bg-white dark:bg-slate-900 border-t border-slate-100 dark:border-slate-800 pt-24 pb-12" id="contact">
  <div class="container mx-auto px-6">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-12 mb-20">
      <div>
<div class="flex items-center justify-center lg:justify-start gap-2 mb-8">
  <span class="text-3xl font-black tracking-tighter text-primary dark:text-white flex items-center">
<img src="<?php echo $basePath; ?>/assets/img/logo_inosakti.png"
     alt="InoSakti"
     class="block mx-auto lg:mx-0 max-h-[11rem] h-auto w-auto max-w-full object-contain" />
  </span>
</div>

        <h4 class="font-bold mb-4 uppercase text-sm tracking-widest text-slate-900 dark:text-white">Maps</h4>

        <!-- Open-source Interactive Map (Leaflet + OpenStreetMap) -->
        <div class="mb-8 overflow-hidden rounded-xl h-[15rem] border border-slate-200 dark:border-slate-700 relative z-0 isolate">
          <div id="inosaktiMap" class="h-full w-full"></div>
        </div>
      </div>

      <div>
        <h4 class="font-bold mb-8 uppercase text-sm tracking-widest text-slate-900 dark:text-white">Lokasi</h4>
        <ul class="space-y-6 text-sm text-slate-600 dark:text-slate-400">
          <li>
            <div class="flex gap-3 mb-3 cursor-pointer hover:text-primary transition-colors hover:font-semibold" id="locationOffice">
              <span class="material-symbols-outlined text-primary shrink-0">location_on</span>
              <span>Alamat Office and Workshop : Jalan Dinar Mas Utara IV No.5, Meteseh, Tembalang, Kota Semarang, Jawa Tengah</span>
            </div>
            <button
              type="button"
              id="btnOffice"
              class="ml-8 px-3 py-1.5 rounded text-xs font-bold border border-slate-200 dark:border-slate-700
                     bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200
                     hover:bg-primary hover:text-white hover:border-primary transition"
            >
              Lihat di Peta
            </button>
          </li>
          <li>
            <div class="flex gap-3 mb-3 cursor-pointer hover:text-primary transition-colors hover:font-semibold" id="locationWorkshop">
              <span class="material-symbols-outlined text-primary shrink-0">location_on</span>
              <span>Alamat Workshop : Jalan Bukit Leyangan Damai No.66, Leyangan, Ungaran Timur, Kab. Semarang, Jawa Tengah</span>
            </div>
            <button
              type="button"
              id="btnWorkshop"
              class="ml-8 px-3 py-1.5 rounded text-xs font-bold border border-slate-200 dark:border-slate-700
                     bg-slate-50 dark:bg-slate-800 text-slate-700 dark:text-slate-200
                     hover:bg-primary hover:text-white hover:border-primary transition"
            >
              Lihat di Peta
            </button>
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
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/#layanan">Layanan Kami</a></li>
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/#produk">Produk Kami</a></li>
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/#portofolio">Portofolio</a></li>
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/#ecommerce">Ecommerce</a></li>
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/#social">Sosial Media</a></li>
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/#mitra">Mitra &amp; Pelanggan Kami</a></li>
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/#testimonial">Testimonial</a></li>
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/#special-partners">Special Partners</a></li>
          <li><a class="hover:text-primary transition-colors" href="<?php echo $basePath; ?>/#contact">Kontak</a></li>
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

<!-- Leaflet (Open-source Interactive Map) -->
<link
  rel="stylesheet"
  href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
  integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
  crossorigin=""
/>
<script
  src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
  integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
  crossorigin=""
></script>

<style>
  /* Keep Leaflet layers confined within footer map stack context */
  #inosaktiMap.leaflet-container { z-index: 0; }
  #inosaktiMap .leaflet-pane { z-index: 1; }
  #inosaktiMap .leaflet-top,
  #inosaktiMap .leaflet-bottom { z-index: 2; }
  #inosaktiMap .leaflet-popup-pane { z-index: 3; }
</style>

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

  // Open-source map init (Leaflet + OpenStreetMap)
  (function () {
    const el = document.getElementById("inosaktiMap");
    if (!el || typeof L === "undefined") return;

    const officeWorkshop = [-7.0648307486354325, 110.46269434635737];
    const workshop = [-7.146174401697758, 110.43531104630142];

    // Map init
    const map = L.map("inosaktiMap", {
      zoomControl: true,
      scrollWheelZoom: false, // avoid stealing scroll in footer
    });

    // Light-mode tiles (OSM)
    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
      maxZoom: 19,
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Button-style link used in popups
    const btnStyle = `
      display:inline-flex;align-items:center;gap:8px;
      padding:8px 10px;border-radius:10px;
      background:#1d4ed8;color:#fff;font-weight:800;font-size:12px;
      text-decoration:none;
    `;

    const m1 = L.marker(officeWorkshop).addTo(map).bindPopup(`
      <div style="font-weight:900;margin-bottom:6px;">Office & Workshop</div>
      <div style="font-size:12px;line-height:1.45;opacity:.92;">
        Jalan Dinar Mas Utara IV No.5, Meteseh, Tembalang, Kota Semarang, Jawa Tengah
      </div>
      <div style="margin-top:10px;">
        <a href="https://maps.app.goo.gl/fF8L3uR1WTSrykv97" target="_blank" rel="noopener" style="${btnStyle}">
          <span style="font-size:14px;line-height:0;">📍</span>
          Buka di Google Maps
        </a>
      </div>
    `);

    const m2 = L.marker(workshop).addTo(map).bindPopup(`
      <div style="font-weight:900;margin-bottom:6px;">Workshop</div>
      <div style="font-size:12px;line-height:1.45;opacity:.92;">
        Jalan Bukit Leyangan Damai No.66, Leyangan, Ungaran Timur, Kab. Semarang, Jawa Tengah
      </div>
      <div style="margin-top:10px;">
        <a href="https://maps.app.goo.gl/DbBHCQYmYoY6Ldzv8" target="_blank" rel="noopener" style="${btnStyle}">
          <span style="font-size:14px;line-height:0;">📍</span>
          Buka di Google Maps
        </a>
      </div>
    `);

    // Fit both points
    const bounds = L.latLngBounds([officeWorkshop, workshop]);
    map.fitBounds(bounds, { padding: [18, 18] });

    // Fix tile sizing after layout settles
    setTimeout(() => map.invalidateSize(), 150);

    // Optional: open first popup by default
    setTimeout(() => m1.openPopup(), 350);

    // Quick buttons
    const btnOffice = document.getElementById("btnOffice");
    const btnWorkshop = document.getElementById("btnWorkshop");

    if (btnOffice) {
      btnOffice.addEventListener("click", () => {
        map.flyTo(officeWorkshop, 16, { duration: 0.8 });
        setTimeout(() => m1.openPopup(), 200);
      });
    }
    if (btnWorkshop) {
      btnWorkshop.addEventListener("click", () => {
        map.flyTo(workshop, 16, { duration: 0.8 });
        setTimeout(() => m2.openPopup(), 200);
      });
    }

    // Optional: enable scroll zoom only when user interacts with map
    map.getContainer().addEventListener("mouseenter", () => map.scrollWheelZoom.enable());
    map.getContainer().addEventListener("mouseleave", () => map.scrollWheelZoom.disable());
  })();
</script>

</body>
</html>
