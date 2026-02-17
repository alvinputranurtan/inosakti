<?php
$pageTitle = 'Embedded & IoT Engineering - InoSakti';
$pageDesc = 'Solusi rekayasa perangkat keras, sistem tertanam, dan AIoT: desain hardware, integrasi sensor, konektivitas, dan edge intelligence.';
include __DIR__.'/../../inc/header.php';
?>

<main class="max-w-7xl mx-auto px-6 py-28"> <!-- padding top for fixed header -->

    <!-- breadcrumb -->
    <nav class="flex mb-8 text-sm font-medium text-slate-500 dark:text-slate-400 animate-slide-up stagger-1">
        <a class="hover:text-accent transition-colors" href="<?php echo $basePath; ?>/index.php#layanan">Services</a>
        <span class="mx-2">/</span>
        <span class="text-slate-900 dark:text-white">Embedded, IoT &amp; AIoT Engineering</span>
    </nav>

 
    <!-- hero -->
    <section class="mb-24 animate-slide-up stagger-2">
        <div class="bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-200 dark:border-slate-800 p-8 md:p-14 shadow-2xl shadow-slate-200/50 dark:shadow-none flex flex-col lg:flex-row gap-12 items-center">
            <div class="w-full lg:w-3/5">
                <div class="inline-flex items-center justify-center p-4 bg-accent/10 rounded-2xl mb-8 icon-box transition-transform duration-300">
                    <span class="material-symbols-outlined text-accent text-5xl">memory</span>
                </div>

                <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-extrabold text-slate-900 dark:text-white mb-6 leading-[1.1]">
                    Embedded, IoT &amp; AIoT Engineering
                </h1>

                <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 leading-relaxed mb-10 max-w-2xl">
                    Solusi rekayasa perangkat keras dan sistem tertanam yang cerdas. Kami merancang dari level sirkuit, integrasi sensor hingga implementasi kecerdasan buatan pada perangkat (Edge AI) untuk menciptakan ekosistem IoT yang handal dan mandiri.
                </p>

                <div class="flex flex-wrap gap-4">
                    <span class="px-5 py-2.5 bg-accent/10 text-accent rounded-full text-sm font-bold flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">developer_board</span>
                        Hardware Design
                    </span>
                    <span class="px-5 py-2.5 bg-emerald-500/10 text-emerald-600 rounded-full text-sm font-bold flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">sensors</span>
                        Real-time Data
                    </span>
                    <span class="px-5 py-2.5 bg-amber-500/10 text-amber-600 rounded-full text-sm font-bold flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">psychology</span>
                        Edge Intelligence
                    </span>
                </div>
            </div>

            <div class="w-full lg:w-2/5 grid grid-cols-2 gap-4">
                <div class="aspect-[4/5] bg-slate-100 dark:bg-slate-800 rounded-2xl overflow-hidden shadow-lg">
                    <img
                        alt="PCBA Circuit Board"
                        class="w-full h-full object-cover opacity-90 hover:opacity-100 hover:scale-110 transition-all duration-700"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCmx_9yoAVaoh0ejp3li3JjmawgE21tiFaw6SrDy6hXrOtl-rFWjc3t27wVoVZ4AovX5RIRpks6cbcYCOiPCyqUgXQ8zJMjAqTMSN7o0mPMsdhcI-KUGkC6WuzFnleWhArKQDVqE3K5Icok1fMIYjKQ5MrRGpI0Fvd5sZwkhfxHeLyhE_Zvw2MJFrk7Xeq7OA0p6fnUN_iTiLIcroPOUh72k6QjnM7UjNDkMwBd30L6kk2zR7jmjgA_0tQWNtXAr_BiJL1rPHbUxw"
                    />
                </div>
                <div class="aspect-[4/5] bg-slate-100 dark:bg-slate-800 rounded-2xl overflow-hidden shadow-lg mt-12">
                    <img
                        alt="Industrial IoT Sensor Deployment"
                        class="w-full h-full object-cover opacity-90 hover:opacity-100 hover:scale-110 transition-all duration-700"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDDgPbs-C7tdFYJEQGyajye-KQtw9fyewb6m-ycyH6URmCtVSczU1H0Merefh2HepriwdY-dKaWFRXy9SIi88F1FbvSMIoIgdiLh5hYgFyYDRua-HPoZNj2JZ0pxxfv2N6fRD6kjGaSJYDJ-wWt5-ZVuCiOoi1ucY9H47fFiYvVVwbQs0HnwHsQd50Na9Zen_NMHLOyu1wgTVpLoKT4grDNoCQLF3IXOYuRfn9U8Qd2Hqglu9t4pRZRdifIWLaLJPB-GjnTF53vpw"
                    />
                </div>
            </div>
        </div>
    </section>

    <!-- services grid title -->
    <section class="animate-slide-up stagger-3">
      <div class="text-center mb-12">
    <h2 class="font-display text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">
        Our Specialized Services
    </h2>

    <div class="w-20 h-1.5 bg-accent mx-auto rounded-full mb-4"></div>

    <p class="text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
        Comprehensive embedded systems, IoT devices, and edge intelligence engineering for modern connected industries.
    </p>
</div>

        <!-- cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- 1 -->
            <div class="service-card group">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">developer_board</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-3 leading-tight">Embedded System Development</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Perancangan sistem tertanam kustom dari konsep hingga produksi massal.
                </p>
            </div>

            <!-- 2 -->
            <div class="service-card group">
                <div class="w-12 h-12 bg-teal-50 dark:bg-teal-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-teal-600 dark:text-teal-400">router</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-3 leading-tight">IoT Device Development</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Pengembangan perangkat IoT yang hemat energi dengan konektivitas luas.
                </p>
            </div>

            <!-- 3 -->
            <div class="service-card group">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400">smart_toy</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-3 leading-tight">AIoT Smart Control System</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Integrasi AI pada sistem kontrol untuk otomasi yang lebih cerdas dan adaptif.
                </p>
            </div>

            <!-- 4 -->
            <div class="service-card group">
                <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-amber-600 dark:text-amber-400">terminal</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-3 leading-tight">Microcontroller Programming</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Pemrograman low-level untuk berbagai arsitektur MCU (ARM, ESP, AVR, dll).
                </p>
            </div>

            <!-- 5 -->
            <div class="service-card group">
                <div class="w-12 h-12 bg-rose-50 dark:bg-rose-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-rose-600 dark:text-rose-400">computer</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-3 leading-tight">Single Board Computer System</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Implementasi sistem berbasis SBC untuk aplikasi industri yang haus performa.
                </p>
            </div>

            <!-- 6 -->
            <div class="service-card group">
                <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">settings_input_component</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-3 leading-tight">Industrial Automation Controller</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Sistem kontrol otomatisasi industri dengan standar keandalan tinggi.
                </p>
            </div>

            <!-- 7 -->
            <div class="service-card group">
                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">sensors</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-3 leading-tight">Sensor Integration System</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Integrasi berbagai sensor analog dan digital untuk akuisisi data presisi.
                </p>
            </div>

            <!-- 8 -->
            <div class="service-card group">
                <div class="w-12 h-12 bg-sky-50 dark:bg-sky-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-sky-600 dark:text-sky-400">settings_remote</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-3 leading-tight">Remote Monitoring System</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Pemantauan aset jarak jauh secara real-time melalui dashboard terpusat.
                </p>
            </div>

            <!-- 9 -->
            <div class="service-card group">
                <div class="w-12 h-12 bg-violet-50 dark:bg-violet-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-violet-600 dark:text-violet-400">lan</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-3 leading-tight">Telemetry &amp; Data Logging</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Transmisi data nirkabel dan pencatatan histori data yang aman.
                </p>
            </div>

            <!-- 10 -->
            <div class="service-card group">
                <div class="w-12 h-12 bg-cyan-50 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-cyan-600 dark:text-cyan-400">dynamic_form</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-3 leading-tight">Edge Computing Implementation</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Pemrosesan data di level perangkat untuk mengurangi latensi dan beban cloud.
                </p>
            </div>

            <!-- 11 -->
            <div class="service-card group">
                <div class="w-12 h-12 bg-green-50 dark:bg-green-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-green-600 dark:text-green-400">agriculture</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-3 leading-tight">Smart Agriculture &amp; Industry</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Solusi khusus untuk optimalisasi hasil tani dan efisiensi pabrik.
                </p>
            </div>

            <!-- 12 -->
            <div class="service-card group">
                <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">code_blocks</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-3 leading-tight">Firmware Dev &amp; Optimization</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Optimasi kode firmware untuk performa maksimal dan konsumsi daya rendah.
                </p>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="mt-28 mb-12 animate-slide-up stagger-3">
        <div class="bg-primary rounded-[3rem] p-12 md:p-20 text-center text-white relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                <div class="absolute -top-24 -left-24 w-96 h-96 bg-accent rounded-full blur-[100px]"></div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-400 rounded-full blur-[100px]"></div>
            </div>

            <div class="relative z-10">
                <h2 class="font-display text-4xl md:text-5xl font-bold mb-8">Ready to Connect Your Industry?</h2>
                <p class="text-slate-300 max-w-2xl mx-auto mb-12 text-lg md:text-xl">
                    Konsultasikan kebutuhan hardware dan IoT Anda dengan tim engineering kami. Mari kita bangun solusi yang cerdas dan efisien bersama.
                </p>
                <div class="flex flex-col sm:flex-row gap-5 justify-center">
                    <a class="px-10 py-5 bg-accent hover:bg-blue-700 text-white font-bold rounded-2xl transition-all shadow-xl shadow-blue-500/25 flex items-center justify-center gap-2"
                       href="https://wa.me/+6288207085761">
                        <span class="material-symbols-outlined">chat</span>
                        Konsultasi Sekarang
                    </a>
                    <a class="px-10 py-5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-2xl transition-all border border-white/20 backdrop-blur-sm flex items-center justify-center gap-2"
                       href="<?php echo $basePath; ?>/index.php#portfolio">
                        <span class="material-symbols-outlined">visibility</span>
                        Lihat Portofolio
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include __DIR__.'/../../inc/footer.php'; ?>