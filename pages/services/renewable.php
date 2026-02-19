<?php
$pageTitle = 'Renewable & Sustainable Energy Engineering - InoSakti';
$pageDesc = 'Solusi rekayasa energi terbarukan: PLTS, micro-hydro, monitoring IoT, manajemen energi cerdas, integrasi baterai, dan studi kelayakan.';
include __DIR__.'/../../inc/header.php';
?>

<main class="max-w-7xl mx-auto px-6 py-28"> <!-- add padding top for fixed header -->

    <!-- breadcrumb -->
    <nav class="flex mb-8 text-sm font-medium text-slate-500 dark:text-slate-400 animate-slide-up stagger-1">
        <a class="hover:text-accent transition-colors" href="<?php echo $basePath; ?>/index.php#layanan">Services</a>
        <span class="mx-2">/</span>
        <span class="text-slate-900 dark:text-white">Renewable &amp; Sustainable Energy</span>
    </nav>

    <!-- hero -->
    <section class="mb-20 animate-slide-up stagger-2">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 md:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none flex flex-col md:flex-row gap-12 items-center">
            <div class="w-full md:w-1/2">
                <!-- ✅ changed: green -> accent blue -->
                <div class="inline-flex items-center justify-center p-4 bg-accent/10 rounded-2xl mb-8 icon-box transition-transform duration-300">
                    <span class="material-symbols-outlined text-accent text-5xl">potted_plant</span>
                </div>

                <h1 class="font-display text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white mb-6 leading-tight">
                    Renewable &amp; Sustainable Energy Engineering
                </h1>

                <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 leading-relaxed mb-8">
                    Kami menghadirkan solusi rekayasa energi terbarukan yang efisien dan berkelanjutan. Dari instalasi panel surya
                    hingga sistem manajemen energi cerdas berbasis IoT, kami membantu transisi energi Anda menjadi lebih bersih,
                    andal, dan hemat biaya untuk masa depan yang lebih hijau.
                </p>

                <div class="flex flex-wrap gap-4">
                    <!-- ✅ changed: green tag -> accent blue -->
                    <span class="px-4 py-2 bg-accent/10 text-accent rounded-full text-sm font-semibold">Green Energy</span>
                    <span class="px-4 py-2 bg-blue-500/10 text-blue-600 rounded-full text-sm font-semibold">Smart Monitoring</span>
                    <span class="px-4 py-2 bg-amber-500/10 text-amber-600 rounded-full text-sm font-semibold">Sustainability</span>
                </div>
            </div>

            <div class="w-full md:w-1/2 grid grid-cols-2 gap-4">
                <div class="aspect-[4/5] bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center overflow-hidden">
                    <img
                        alt="Solar panel array installation"
                        class="w-full h-full object-cover opacity-90 hover:scale-105 transition-transform duration-500"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCmx_9yoAVaoh0ejp3li3JjmawgE21tiFaw6SrDy6hXrOtl-rFWjc3t27wVoVZ4AovX5RIRpks6cbcYCOiPCyqUgXQ8zJMjAqTMSN7o0mPMsdhcI-KUGkC6WuzFnleWhArKQDVqE3K5Icok1fMIYjKQ5MrRGpI0Fvd5sZwkhfxHeLyhE_Zvw2MJFrk7Xeq7OA0p6fnUN_iTiLIcroPOUh72k6QjnM7UjNDkMwBd30L6kk2zR7jmjgA_0tQWNtXAr_BiJL1rPHbUxw"
                    />
                </div>

                <div class="aspect-[4/5] bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center overflow-hidden mt-12">
                    <img
                        alt="Energy monitoring dashboard"
                        class="w-full h-full object-cover opacity-90 hover:scale-105 transition-transform duration-500"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDDgPbs-C7tdFYJEQGyajye-KQtw9fyewb6m-ycyH6URmCtVSczU1H0Merefh2HepriwdY-dKaWFRXy9SIi88F1FbvSMIoIgdiLh5hYgFyYDRua-HPoZNj2JZ0pxxfv2N6fRD6kjGaSJYDJ-wWt5-ZVuCiOoi1ucY9H47fFiYvVVwbQs0HnwHsQd50Na9Zen_NMHLOyu1wgTVpLoKT4grDNoCQLF3IXOYuRfn9U8Qd2Hqglu9t4pRZRdifIWLaLJPB-GjnTF53vpw"
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
            <!-- ✅ changed: bg-energy -> bg-accent -->
            <div class="w-20 h-1.5 bg-accent mx-auto rounded-full mb-4"></div>
            <p class="text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
                Practical renewable energy engineering: planning, installation, monitoring, optimization, and scalable hybrid systems for real-world deployment.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            <div class="service-card group">
                <div class="w-14 h-14 bg-amber-50 dark:bg-amber-900/20 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-amber-600 text-3xl">light_mode</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Solar Panel Planning &amp; Installation</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Solusi panel surya atap dan ground-mounted yang dioptimalkan untuk menghasilkan energi maksimal.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-14 h-14 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-blue-600 text-3xl">water_drop</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Micro-Hydropower System</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Sistem mikrohidro berkelanjutan untuk wilayah terpencil maupun aliran air industri.
                </p>
            </div>

            <div class="service-card group">
                <!-- ✅ changed: emerald -> accent -->
                <div class="w-14 h-14 bg-accent/10 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-accent text-3xl">monitoring</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Energy Monitoring IoT System</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Pemantauan pola konsumsi dan produksi energi secara real-time melalui sensor cerdas.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-indigo-600 text-3xl">settings_remote</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Smart Energy Management</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Platform berbasis data untuk mengoptimalkan distribusi daya dan menekan pemborosan operasional.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-14 h-14 bg-rose-50 dark:bg-rose-900/20 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-rose-600 text-3xl">battery_charging_full</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Battery &amp; Storage Integration</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Integrasi sistem penyimpanan energi berkapasitas tinggi (BESS) untuk peak shaving dan keandalan daya cadangan.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-14 h-14 bg-cyan-50 dark:bg-cyan-900/20 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-cyan-600 text-3xl">hub</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Hybrid Power System Design</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Integrasi tenaga surya, angin, dan sumber konvensional secara mulus untuk stabilitas energi 24/7.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-14 h-14 bg-orange-50 dark:bg-orange-900/20 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-orange-600 text-3xl">electric_bolt</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Power Electronics Integration</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Integrasi sistem inverter dan konverter tingkat lanjut untuk menjaga kualitas daya yang bersih dan stabil.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-14 h-14 bg-teal-50 dark:bg-teal-900/20 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-teal-600 text-3xl">analytics</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Performance Optimization</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Analisis data mendalam dan pemeliharaan berkala untuk menjaga sistem energi tetap pada efisiensi puncak.
                </p>
            </div>

            <div class="service-card group">
                <!-- ✅ changed: lime -> accent -->
                <div class="w-14 h-14 bg-accent/10 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-accent text-3xl">assignment</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Green Energy Feasibility Study</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Studi kelayakan teknis dan ekonomi untuk menentukan strategi energi terbarukan paling tepat bagi lokasi Anda.
                </p>
            </div>

        </div>
    </section>

    <!-- CTA -->
    <section class="mt-24 mb-12 animate-slide-up stagger-3">
        <div class="bg-primary rounded-[2.5rem] p-12 text-center text-white relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                <!-- ✅ changed: bg-energy -> bg-accent -->
                <div class="absolute -top-24 -left-24 w-96 h-96 bg-accent rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-400 rounded-full blur-3xl"></div>
            </div>

            <div class="relative z-10">
                <h2 class="font-display text-3xl md:text-4xl font-bold mb-6">Empower Your Business with Sustainable Energy</h2>
                <p class="text-slate-300 max-w-2xl mx-auto mb-10 text-lg">
                    Consult with our energy experts to design a system that meets your sustainability goals while reducing operational costs.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <!-- ✅ changed: bg-energy -> bg-accent -->
                    <a class="px-8 py-4 bg-accent hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-500/25 flex items-center justify-center"
                       href="https://wa.me/+6288207085761">
                        <span class="material-symbols-outlined mr-2">calendar_today</span> Schedule a Consultation
                    </a>
                    <a class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl transition-all border border-white/20"
                       href="<?php echo $basePath; ?>/index.php#portfolio">Download Energy Catalog</a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include __DIR__.'/../../inc/footer.php'; ?>

