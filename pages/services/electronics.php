<?php
$pageTitle = 'Electronics & Hardware Engineering - InoSakti';
$pageDesc = 'Perancangan dan pengembangan perangkat keras elektronik: skematik, PCB, prototyping, testing, hingga persiapan manufaktur massal.';
include __DIR__.'/../../inc/header.php';
?>

<main class="max-w-7xl mx-auto px-6 py-28"> <!-- add padding top for fixed header -->

    <!-- breadcrumb -->
    <nav class="flex mb-8 text-sm font-medium text-slate-500 dark:text-slate-400 animate-slide-up stagger-1">
        <a class="hover:text-accent transition-colors" href="<?php echo $basePath; ?>/index.php#layanan">Layanan</a>
        <span class="mx-2">/</span>
        <span class="text-slate-900 dark:text-white">Electronics &amp; Hardware Engineering</span>
    </nav>

    <!-- hero -->
    <section class="mb-20 animate-slide-up stagger-2">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 md:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none flex flex-col lg:flex-row gap-12 items-center">
            <div class="w-full lg:w-3/5">
                <div class="inline-flex items-center justify-center p-4 bg-accent/10 rounded-2xl mb-8 icon-box transition-transform duration-300">
                    <span class="material-symbols-outlined text-accent text-5xl">memory</span>
                </div>

                <h1 class="font-display text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white mb-6 leading-tight">
                    Electronics &amp; Hardware Engineering
                </h1>

                <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 leading-relaxed mb-8">
                    Kami menyediakan layanan perancangan dan pengembangan perangkat keras elektronik secara komprehensif,
                    mulai dari konsep ide, skematik, desain PCB, hingga produksi massal. Solusi kami dirancang untuk
                    keandalan tinggi dan efisiensi performa di berbagai sektor industri.
                </p>

                <div class="flex flex-wrap gap-4">
                    <span class="px-4 py-2 bg-accent/10 text-accent rounded-full text-sm font-semibold">Custom PCB Design</span>
                    <span class="px-4 py-2 bg-emerald-500/10 text-emerald-600 rounded-full text-sm font-semibold">Industrial Grade</span>
                    <span class="px-4 py-2 bg-amber-500/10 text-amber-600 rounded-full text-sm font-semibold">Rapid Prototyping</span>
                </div>
            </div>

            <div class="w-full lg:w-2/5 grid grid-cols-2 gap-4">
                <div class="aspect-[4/5] bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center overflow-hidden">
                    <img
                        alt="Professional PCB design workstation"
                        class="w-full h-full object-cover opacity-90 hover:scale-105 transition-transform duration-500"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDDgPbs-C7tdFYJEQGyajye-KQtw9fyewb6m-ycyH6URmCtVSczU1H0Merefh2HepriwdY-dKaWFRXy9SIi88F1FbvSMIoIgdiLh5hYgFyYDRua-HPoZNj2JZ0pxxfv2N6fRD6kjGaSJYDJ-wWt5-ZVuCiOoi1ucY9H47fFiYvVVwbQs0HnwHsQd50Na9Zen_NMHLOyu1wgTVpLoKT4grDNoCQLF3IXOYuRfn9U8Qd2Hqglu9t4pRZRdifIWLaLJPB-GjnTF53vpw"
                    />
                </div>

                <div class="aspect-[4/5] bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center overflow-hidden mt-8">
                    <img
                        alt="High-precision electronic testing lab"
                        class="w-full h-full object-cover opacity-90 hover:scale-105 transition-transform duration-500"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCmx_9yoAVaoh0ejp3li3JjmawgE21tiFaw6SrDy6hXrOtl-rFWjc3t27wVoVZ4AovX5RIRpks6cbcYCOiPCyqUgXQ8zJMjAqTMSN7o0mPMsdhcI-KUGkC6WuzFnleWhArKQDVqE3K5Icok1fMIYjKQ5MrRGpI0Fvd5sZwkhfxHeLyhE_Zvw2MJFrk7Xeq7OA0p6fnUN_iTiLIcroPOUh72k6QjnM7UjNDkMwBd30L6kk2zR7jmjgA_0tQWNtXAr_BiJL1rPHbUxw"
                    />
                </div>
            </div>
        </div>
    </section>

    <!-- specialized services -->
    <section class="animate-slide-up stagger-3">
        <!-- ✅ same layout as other pages -->
        <div class="text-center mb-12">
            <h2 class="font-display text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">
                Our Specialized Services
            </h2>
            <div class="w-20 h-1.5 bg-accent mx-auto rounded-full mb-4"></div>
            <p class="text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
                End-to-end electronics engineering services: from concept, schematic, PCB layout, prototyping, validation, to production-ready documentation.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div class="service-card group">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">schema</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Electronic System Design</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Arsitektur sistem elektronik yang efisien untuk berbagai aplikasi industri dan konsumen.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400">grid_view</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">PCB Design &amp; Layout</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Desain layout PCB multi-layer dengan standar integritas sinyal dan manajemen panas yang baik.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">precision_manufacturing</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Prototyping &amp; Bring-Up</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Pembuatan prototipe fungsional dan pengujian awal untuk memastikan sistem berjalan sesuai spesifikasi.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-sky-50 dark:bg-sky-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-sky-600 dark:text-sky-400">rule</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Testing &amp; Validation</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Pengujian ketat hardware untuk validasi performa, keamanan, dan standar kepatuhan regulasi.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-cyan-50 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-cyan-600 dark:text-cyan-400">conveyor_belt</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Manufacturing Prep</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Persiapan file produksi (Gerber, BOM, Assembly) untuk transisi mulus ke manufaktur massal.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-rose-50 dark:bg-rose-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-rose-600 dark:text-rose-400">history</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Reverse Engineering</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Analisis mendalam perangkat keras yang ada untuk pembaruan teknologi atau dokumentasi ulang.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">settings_input_component</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Instrumentation System</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Pengembangan alat ukur dan sistem akuisisi data presisi tinggi untuk kebutuhan laboratorium atau industri.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-amber-600 dark:text-amber-400">factory</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Industrial Equipment</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Perancangan perangkat elektronik khusus yang tahan lama untuk lingkungan operasional industri berat.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-teal-50 dark:bg-teal-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-teal-600 dark:text-teal-400">hub</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Embedded Hardware</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Integrasi mikrokontroler dan prosesor dengan sensor/aktuator untuk sistem cerdas tertanam.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">build</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Troubleshooting &amp; Repair</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Layanan diagnosa kerusakan dan perbaikan perangkat elektronik tingkat lanjut (component-level).
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-pink-50 dark:bg-pink-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-pink-600 dark:text-pink-400">view_in_ar</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">3D Mechanical Design</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Desain enclosure dan casing mekanik yang presisi untuk melindungi modul elektronik Anda.
                </p>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="mt-24 mb-12 animate-slide-up stagger-3">
        <div class="bg-primary rounded-[2.5rem] p-12 text-center text-white relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                <div class="absolute -top-24 -left-24 w-96 h-96 bg-accent rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-400 rounded-full blur-3xl"></div>
            </div>

            <div class="relative z-10">
                <h2 class="font-display text-3xl md:text-4xl font-bold mb-6">Wujudkan Ide Perangkat Keras Anda</h2>
                <p class="text-slate-300 max-w-2xl mx-auto mb-10 text-lg">
                    Konsultasikan kebutuhan engineering elektronik Anda dengan tim ahli kami. Dari konsep hingga produksi massal, kami siap membantu.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a class="px-8 py-4 bg-accent hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-500/25"
                       href="https://wa.me/+6288207085761">Mulai Konsultasi</a>
                    <a class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl transition-all border border-white/20"
                       href="<?php echo $basePath; ?>/index.php#portfolio">Lihat Portfolio Hardware</a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include __DIR__.'/../../inc/footer.php'; ?>