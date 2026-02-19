<?php
$pageTitle = 'InoSakti | Blog & Articles - Engineering & Technology Insights';
$pageDesc = 'InoSakti Blog features the latest insights on AI, IoT, Software, and Hardware Engineering solutions.';

$extraHead = <<<'HTML'
<style type="text/tailwindcss">
@layer components {
  .blog-card {
    @apply bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden transition-all duration-300 hover:shadow-xl hover:-translate-y-1;
  }
  .category-tag {
    @apply px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-full text-xs font-bold uppercase tracking-wider;
  }
}
</style>
HTML;

$extraScripts = <<<'HTML'
<script>
  document.querySelectorAll('.blog-card').forEach((el, i) => {
    el.classList.add('transition-all', 'duration-700', 'opacity-0', 'translate-y-10');
    el.style.transitionDelay = `${(i % 3) * 150}ms`;
  });
</script>
HTML;

include __DIR__.'/../../inc/header.php';

$blogBase = $basePath . '/pages/blog/detail';
?>

<main class="pt-16">
  <section class="py-12 md:py-20 bg-white dark:bg-slate-900 overflow-hidden">
    <div class="container mx-auto px-6">
      <div class="grid lg:grid-cols-2 gap-12 items-center bg-slate-50 dark:bg-slate-800/50 rounded-3xl overflow-hidden border border-slate-100 dark:border-slate-700">
        <div class="relative h-[300px] lg:h-full overflow-hidden group">
          <img alt="High-tech greenhouse monitoring" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCrXK6VDZg63pKO5HFT56bF0UpXddOze-Ef_cUXF2nBQ_BuIm39PtiLREa8Wj_QoyNE-mYOzIMl8Uwhyu1SuoApuMf28gvNg-_VieKV9GVJE2AJo029XKhJRbEtTlNjNsOtZ7KWOwHWYFyPoUCkfIPP6c3WqEWyjq0E2xgDAbUsBGxKoEy3KGldE_IGeOQq_M8oLN52p2Lgkx11_CUGRadZWf5FRJmPGCpB5xWEObBLGLYrI81UgJngvDjaeF_N0mqd3mgF2fTsfQ"/>
          <div class="absolute top-6 left-6">
            <span class="bg-primary text-white px-4 py-2 rounded-lg font-bold text-xs uppercase tracking-widest shadow-lg">Featured Article</span>
          </div>
        </div>
        <div class="p-8 md:p-12">
          <div class="flex items-center gap-2 mb-6 text-sm font-bold text-slate-500 dark:text-slate-400">
            <span class="material-symbols-outlined text-primary text-lg">calendar_today</span>
            24 Okt 2023 |
            <span class="text-primary uppercase tracking-tighter">AI Technology</span>
          </div>
          <h1 class="text-3xl md:text-5xl font-black mb-6 leading-tight tracking-tight">Implementasi AI dalam Sistem Monitoring Greenhouse</h1>
          <p class="text-lg text-slate-600 dark:text-slate-400 mb-10 leading-relaxed">
            Bagaimana teknologi kecerdasan buatan merevolusi cara petani memantau dan mengoptimalkan lingkungan tumbuh secara real-time untuk hasil panen maksimal.
          </p>
          <a href="<?php echo $blogBase; ?>?slug=implementasi-ai-monitoring-greenhouse" class="px-8 py-4 bg-primary text-white rounded-xl font-bold hover:bg-blue-800 transition-all shadow-lg shadow-blue-200 dark:shadow-none inline-flex items-center gap-2">
            Baca Selengkapnya <span class="material-symbols-outlined">arrow_forward</span>
          </a>
        </div>
      </div>
    </div>
  </section>

  <section class="py-6 border-y border-slate-100 dark:border-slate-800 sticky top-16 z-40 glass-effect">
    <div class="container mx-auto px-6">
      <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4 overflow-x-auto pb-2 md:pb-0">
          <span class="text-sm font-bold text-slate-400 uppercase tracking-widest mr-2">Kategori:</span>
          <button class="whitespace-nowrap px-6 py-2 rounded-full font-bold text-sm bg-primary text-white transition-all shadow-md">Semua</button>
          <button class="whitespace-nowrap px-6 py-2 rounded-full font-bold text-sm bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">AI</button>
          <button class="whitespace-nowrap px-6 py-2 rounded-full font-bold text-sm bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">IoT</button>
          <button class="whitespace-nowrap px-6 py-2 rounded-full font-bold text-sm bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">Software</button>
          <button class="whitespace-nowrap px-6 py-2 rounded-full font-bold text-sm bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">Hardware</button>
        </div>
        <div class="relative">
          <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">search</span>
          <input class="bg-slate-100 dark:bg-slate-800 border-none rounded-full pl-10 pr-6 py-2 text-sm w-full md:w-64 focus:ring-2 focus:ring-primary" placeholder="Cari artikel..." type="text"/>
        </div>
      </div>
    </div>
  </section>

  <section class="py-20 bg-[#f8fafc] dark:bg-slate-900/50">
    <div class="container mx-auto px-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <article class="blog-card group">
          <div class="aspect-video overflow-hidden relative">
            <img alt="IoT Greenhouse" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDZ69rq5f_67UGOfdC-Ox9nJBtFiF8sLqTb7rjoDrB1d9gSeYPwlyYY0JN1SoQJxIavK6poe-6YoRIeL5bHT9aVLFOQ1ET0c6Jsp30st9MxQSICwQC6Cijv9Ov6jGkPfFmUC605e0KYPkaaAFGqxOAlJ3CdYiiOhGKigs3-hODAAcnAE_ureQA_fMhOxtKGSrQUqt8q0fbSrJeCu2C3Jqinekf5djTCYv-sKaIFfPqV6sKaQvWzRqbl4rFMtD9jiH4YcMqbH8jfQg"/>
            <div class="absolute bottom-4 left-4">
              <span class="category-tag bg-white/90 dark:bg-slate-900/90 backdrop-blur">IoT</span>
            </div>
          </div>
          <div class="p-6">
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 mb-4">
              <span class="material-symbols-outlined text-sm">event</span> 12 Okt 2023
            </div>
            <h3 class="text-xl font-bold mb-4 group-hover:text-primary transition-colors">Integrasi Sensor IoT untuk Efisiensi Irigasi Otomatis</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 line-clamp-3">
              Pemanfaatan sensor kelembaban tanah dan otomasi katup untuk menghemat penggunaan air hingga 40% pada lahan terbuka.
            </p>
            <a class="text-primary font-bold text-sm flex items-center gap-1 hover:gap-3 transition-all" href="<?php echo $blogBase; ?>?slug=integrasi-sensor-iot-irigasi">
              Baca Selengkapnya <span class="material-symbols-outlined text-lg">arrow_right_alt</span>
            </a>
          </div>
        </article>

        <article class="blog-card group">
          <div class="aspect-video overflow-hidden relative">
            <img alt="Software Engineering" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBo8jnP5zm9f_2h6dQIcCDQ0RVLZG7mw2xcqdkQCUuU7io9tuu0vGXwsMB-X9nt_FqNYULP5C_wj9maVDd2p8s9B8xcc63FqjG6syw3BtddhJCEfXWLgWLyJXf6LHf9iRqyPVd03uEuRkWZ--5z6rPnnY9E8cU63_wJ7UVslydEBR2pty5AqRLIczIVBy8e8uPTBjGlkJbKg231Bbdw4FjFLJ3p-8RnVu9rO8cn4ZGUgdJkl_BnHudigT9ex9a7N7klJ1z4njKN_g"/>
            <div class="absolute bottom-4 left-4">
              <span class="category-tag bg-white/90 dark:bg-slate-900/90 backdrop-blur">Software</span>
            </div>
          </div>
          <div class="p-6">
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 mb-4">
              <span class="material-symbols-outlined text-sm">event</span> 08 Okt 2023
            </div>
            <h3 class="text-xl font-bold mb-4 group-hover:text-primary transition-colors">Optimasi Dashboard Monitoring Real-time Berbasis Web</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 line-clamp-3">
              Membangun sistem visualisasi data yang ringan dan responsif untuk monitoring ribuan node sensor secara simultan.
            </p>
            <a class="text-primary font-bold text-sm flex items-center gap-1 hover:gap-3 transition-all" href="<?php echo $blogBase; ?>?slug=optimasi-dashboard-monitoring-realtime">
              Baca Selengkapnya <span class="material-symbols-outlined text-lg">arrow_right_alt</span>
            </a>
          </div>
        </article>

        <article class="blog-card group">
          <div class="aspect-video overflow-hidden relative">
            <img alt="Hardware Development" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDOtUoDVd-Jie3O1R6M2xRYu7ZL7mB5fK5tzjn-RamiAIWY8Ye58a3ZC0QY1AtK8YvWd3FThVovfk6a0T9AnT4_MTvtFA3IXgOKjPCDpx_lEeGETcJ1Y1mu0AP_C8ebc0nytT8w5xOnZeZZ2weoNGqrU_8o4lBMY3oW0UT8ffC5y-cqrOFb8KYGhdTq3z8w6LvvSp50g6YtFhl7c1Udo7gzSI4KJ2rbNd7n65JAW1RnbCPEaQy7DQ5o334clxR-1IXNBRBUaKB7Gg"/>
            <div class="absolute bottom-4 left-4">
              <span class="category-tag bg-white/90 dark:bg-slate-900/90 backdrop-blur">Hardware</span>
            </div>
          </div>
          <div class="p-6">
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 mb-4">
              <span class="material-symbols-outlined text-sm">event</span> 05 Okt 2023
            </div>
            <h3 class="text-xl font-bold mb-4 group-hover:text-primary transition-colors">Tantangan Desain PCB untuk Perangkat Edge Computing</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 line-clamp-3">
              Tips dan trik dalam merancang board elektronik yang tahan banting di lingkungan industri yang ekstrem.
            </p>
            <a class="text-primary font-bold text-sm flex items-center gap-1 hover:gap-3 transition-all" href="<?php echo $blogBase; ?>?slug=tantangan-desain-pcb-edge-computing">
              Baca Selengkapnya <span class="material-symbols-outlined text-lg">arrow_right_alt</span>
            </a>
          </div>
        </article>

        <article class="blog-card group">
          <div class="aspect-video overflow-hidden relative">
            <img alt="Smart Farming" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCrX1v7atYH-vMGAY4Zp7qMI7PAURWPFyefyL5CZip9MpprRbGUdoXme18W2sQ16mq09-26C2339-7zr5V4-kxAoDJSikFVlWy2kOz8psuXX-OpiBsVsGiQInQT-T8wUlouMdantiU78zEcJKuR-5cCKsW0Omp7hPkBYUpG4z_atfD_ET03Tb0ApF8bwQVCvNTNv4g71-0NJHk1jK0SfQTXVySJvRo0SDpM7Wwdyyq0ijouJIEsjAnfMhSDeN55XtyukINU8Aosvw"/>
            <div class="absolute bottom-4 left-4">
              <span class="category-tag bg-white/90 dark:bg-slate-900/90 backdrop-blur">AI</span>
            </div>
          </div>
          <div class="p-6">
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 mb-4">
              <span class="material-symbols-outlined text-sm">event</span> 28 Sep 2023
            </div>
            <h3 class="text-xl font-bold mb-4 group-hover:text-primary transition-colors">Prediksi Hama Menggunakan Machine Learning</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 line-clamp-3">
              Menganalisis pola cuaca dan data historis untuk memprediksi serangan hama seminggu sebelum kejadian terjadi.
            </p>
            <a class="text-primary font-bold text-sm flex items-center gap-1 hover:gap-3 transition-all" href="<?php echo $blogBase; ?>?slug=prediksi-hama-machine-learning">
              Baca Selengkapnya <span class="material-symbols-outlined text-lg">arrow_right_alt</span>
            </a>
          </div>
        </article>

        <article class="blog-card group">
          <div class="aspect-video overflow-hidden relative">
            <img alt="Electronics" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBcfdGs3SdvUKdCNl9XHWNP99_w05fuWh7rUK_KvqgTtj16nL5XJWw_Jg3lJWLOlBumw12ky5qrraB9uVc0vkCjFhIrPSRZhoC7PZ0KK709w733A6iBtPErcJKE2ViIiGkf3ecSnIT4HrKTl6uiAxwP0MTeLjodDelLU3dE6iO9hmAP9DFiXShOa1H8tFYTfBOHv53w79xoHp28FanVl-5x7JCj1bS_dOYejTOS8HLfzMFDhHl7xgz2iR5koeduPyJ5lQUYPLJEvA"/>
            <div class="absolute bottom-4 left-4">
              <span class="category-tag bg-white/90 dark:bg-slate-900/90 backdrop-blur">Hardware</span>
            </div>
          </div>
          <div class="p-6">
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 mb-4">
              <span class="material-symbols-outlined text-sm">event</span> 20 Sep 2023
            </div>
            <h3 class="text-xl font-bold mb-4 group-hover:text-primary transition-colors">Peran Mikrokontroler Generasi Terbaru dalam AIoT</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 line-clamp-3">
              Eksplorasi chipset terbaru yang mendukung akselerasi neural network secara native di tingkat perangkat keras.
            </p>
            <a class="text-primary font-bold text-sm flex items-center gap-1 hover:gap-3 transition-all" href="<?php echo $blogBase; ?>?slug=peran-mikrokontroler-aiot">
              Baca Selengkapnya <span class="material-symbols-outlined text-lg">arrow_right_alt</span>
            </a>
          </div>
        </article>

        <article class="blog-card group">
          <div class="aspect-video overflow-hidden relative">
            <img alt="Renewable Energy" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB1g0WA2Ol4T8ApAjxXSfakCJ8FcMKma0vPHaywBRjtb4ZBIjc62GtbeOg8FxjjvFeOAsrqWEiWX4ICvbhs67rNpr0l3imx9JgAsW1gppFiA4sL6_1_7CpbSX3g_vWJP59XaDCiEBniy0m8GFp7U6y9Qv3KyCBNJ_FcBaA9JyPRcSqOcTUVCCZFatwSfGjpfaAIxTX0uudokCDFVHsl3wdUoBctG5JwSi9Qy1tAtSKzQ-KIpvp4mqqy2Q0OrdvHlr_VinNZR6GM1A"/>
            <div class="absolute bottom-4 left-4">
              <span class="category-tag bg-white/90 dark:bg-slate-900/90 backdrop-blur">Software</span>
            </div>
          </div>
          <div class="p-6">
            <div class="flex items-center gap-2 text-xs font-bold text-slate-500 dark:text-slate-400 mb-4">
              <span class="material-symbols-outlined text-sm">event</span> 15 Sep 2023
            </div>
            <h3 class="text-xl font-bold mb-4 group-hover:text-primary transition-colors">Sistem Manajemen Energi untuk Smart Factory</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 mb-6 line-clamp-3">
              Implementasi algoritma cerdas untuk menyeimbangkan beban listrik dan penggunaan energi terbarukan di pabrik.
            </p>
            <a class="text-primary font-bold text-sm flex items-center gap-1 hover:gap-3 transition-all" href="<?php echo $blogBase; ?>?slug=sistem-manajemen-energi-smart-factory">
              Baca Selengkapnya <span class="material-symbols-outlined text-lg">arrow_right_alt</span>
            </a>
          </div>
        </article>
      </div>

      <div class="mt-16 flex justify-center items-center gap-2">
        <button class="w-10 h-10 rounded-lg border border-slate-200 dark:border-slate-700 flex items-center justify-center hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
          <span class="material-symbols-outlined">chevron_left</span>
        </button>
        <button class="w-10 h-10 rounded-lg bg-primary text-white font-bold text-sm shadow-md">1</button>
        <button class="w-10 h-10 rounded-lg border border-slate-200 dark:border-slate-700 flex items-center justify-center hover:bg-slate-50 dark:hover:bg-slate-800 transition-all font-bold text-sm">2</button>
        <button class="w-10 h-10 rounded-lg border border-slate-200 dark:border-slate-700 flex items-center justify-center hover:bg-slate-50 dark:hover:bg-slate-800 transition-all font-bold text-sm">3</button>
        <span class="px-2 text-slate-400 font-bold">...</span>
        <button class="w-10 h-10 rounded-lg border border-slate-200 dark:border-slate-700 flex items-center justify-center hover:bg-slate-50 dark:hover:bg-slate-800 transition-all font-bold text-sm">12</button>
        <button class="w-10 h-10 rounded-lg border border-slate-200 dark:border-slate-700 flex items-center justify-center hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
          <span class="material-symbols-outlined">chevron_right</span>
        </button>
      </div>
    </div>
  </section>

  <section class="py-20 bg-primary text-white">
    <div class="container mx-auto px-6 text-center">
      <h2 class="text-3xl md:text-4xl font-black mb-6">Dapatkan Insight Teknologi Terbaru</h2>
      <p class="text-blue-100 mb-10 max-w-2xl mx-auto">Berlangganan newsletter kami untuk mendapatkan artikel eksklusif seputar AI, IoT, dan solusi rekayasa langsung ke inbox Anda.</p>
      <form class="max-w-md mx-auto flex flex-col sm:flex-row gap-4">
        <input class="flex-1 bg-white/10 border-white/20 rounded-xl px-6 py-4 placeholder:text-blue-200 text-white focus:ring-2 focus:ring-white outline-none" placeholder="Alamat Email Anda" type="email"/>
        <button class="bg-white text-primary px-8 py-4 rounded-xl font-bold hover:bg-blue-50 transition-all shadow-xl">Langganan</button>
      </form>
    </div>
  </section>
</main>

<?php include __DIR__.'/../../inc/footer.php'; ?>
