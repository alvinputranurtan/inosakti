<?php
$pageTitle = 'Advanced Network & RF Engineering - InoSakti';
$pageDesc = 'Solusi rekayasa jaringan dan RF untuk konektivitas tanpa batas: desain wireless, optimasi spektrum, simulasi RF, antena, dan pengujian.';
include __DIR__.'/../../inc/header.php';
?>

<main class="max-w-7xl mx-auto px-6 py-28"> <!-- add padding top for fixed header -->

    <!-- breadcrumb -->
    <nav class="flex mb-8 text-sm font-medium text-slate-500 dark:text-slate-400 animate-slide-up stagger-1">
        <a class="hover:text-accent transition-colors" href="<?php echo $basePath; ?>/index.php#layanan">Services</a>
        <span class="mx-2">/</span>
        <span class="text-slate-900 dark:text-white">Advanced Network &amp; RF Engineering</span>
    </nav>

    <!-- hero section -->
    <section class="mb-20 animate-slide-up stagger-2">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 md:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none flex flex-col md:flex-row gap-12 items-center">
            <div class="w-full md:w-1/2">
                <div class="inline-flex items-center justify-center p-4 bg-accent/10 rounded-2xl mb-8 icon-box transition-transform duration-300">
                    <span class="material-symbols-outlined text-accent text-5xl">settings_input_antenna</span>
                </div>

                <h1 class="font-display text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white mb-6 leading-tight">
                    Advanced Network &amp; RF Engineering
                </h1>

                <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 leading-relaxed mb-8">
                    Solusi rekayasa jaringan dan frekuensi radio (RF) yang komprehensif untuk mendukung konektivitas tanpa batas.
                    Kami mengkhususkan diri dalam desain sistem komunikasi nirkabel, optimasi spektrum, serta implementasi
                    infrastruktur jaringan modern yang tangguh untuk kebutuhan industri dan enterprise.
                </p>

                <div class="flex flex-wrap gap-4">
                    <span class="px-4 py-2 bg-accent/10 text-accent rounded-full text-sm font-semibold">RF Simulation</span>
                    <span class="px-4 py-2 bg-emerald-500/10 text-emerald-600 rounded-full text-sm font-semibold">Spectrum Optimization</span>
                    <span class="px-4 py-2 bg-amber-500/10 text-amber-600 rounded-full text-sm font-semibold">Next-Gen Connectivity</span>
                </div>
            </div>

            <div class="w-full md:w-1/2 grid grid-cols-2 gap-4">
                <div class="aspect-[4/5] bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center overflow-hidden">
                    <img
                        alt="Network server racks"
                        class="w-full h-full object-cover opacity-90 hover:scale-105 transition-transform duration-500"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCmx_9yoAVaoh0ejp3li3JjmawgE21tiFaw6SrDy6hXrOtl-rFWjc3t27wVoVZ4AovX5RIRpks6cbcYCOiPCyqUgXQ8zJMjAqTMSN7o0mPMsdhcI-KUGkC6WuzFnleWhArKQDVqE3K5Icok1fMIYjKQ5MrRGpI0Fvd5sZwkhfxHeLyhE_Zvw2MJFrk7Xeq7OA0p6fnUN_iTiLIcroPOUh72k6QjnM7UjNDkMwBd30L6kk2zR7jmjgA_0tQWNtXAr_BiJL1rPHbUxw"
                    />
                </div>

                <div class="aspect-[4/5] bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center overflow-hidden mt-12">
                    <img
                        alt="RF testing equipment"
                        class="w-full h-full object-cover opacity-90 hover:scale-105 transition-transform duration-500"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDDgPbs-C7tdFYJEQGyajye-KQtw9fyewb6m-ycyH6URmCtVSczU1H0Merefh2HepriwdY-dKaWFRXy9SIi88F1FbvSMIoIgdiLh5hYgFyYDRua-HPoZNj2JZ0pxxfv2N6fRD6kjGaSJYDJ-wWt5-ZVuCiOoi1ucY9H47fFiYvVVwbQs0HnwHsQd50Na9Zen_NMHLOyu1wgTVpLoKT4grDNoCQLF3IXOYuRfn9U8Qd2Hqglu9t4pRZRdifIWLaLJPB-GjnTF53vpw"
                    />
                </div>
            </div>
        </div>
    </section>

    <!-- specialized services grid -->
    <section class="animate-slide-up stagger-3">

        <!-- ✅ header section same as other pages -->
        <div class="text-center mb-12">
            <h2 class="font-display text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">
                Our Specialized Services
            </h2>
            <div class="w-20 h-1.5 bg-accent mx-auto rounded-full mb-4"></div>
            <p class="text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
                Comprehensive network engineering and RF solutions to strengthen modern connectivity, coverage, and spectrum efficiency.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="service-card group">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">lan</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Network Planning &amp; Installation</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Strategi dan implementasi infrastruktur jaringan berperforma tinggi secara end-to-end.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400">hub</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Infrastructure Network Engineering</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Perancangan sistem inti yang tangguh untuk pengelolaan data kelas enterprise.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">settings_input_antenna</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Wireless Communication System Design</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Perancangan arsitektur jaringan nirkabel yang dioptimalkan untuk cakupan dan kapasitas.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-sky-50 dark:bg-sky-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-sky-600 dark:text-sky-400">waves</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">RF Design &amp; Simulation</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Pemodelan dan simulasi lingkungan frekuensi radio secara tingkat lanjut.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-cyan-50 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-cyan-600 dark:text-cyan-400">sensors</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Antenna Design &amp; Fabrication</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Solusi antena kustom yang disesuaikan dengan kebutuhan frekuensi dan gain spesifik.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-rose-50 dark:bg-rose-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-rose-600 dark:text-rose-400">analytics</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">RF Measurement &amp; Characterization</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Pengujian serta validasi presisi terhadap komponen RF dan performa sistem.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">verified_user</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">RF Pre-Compliant Testing</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Evaluasi perangkat terhadap standar regulasi sebelum proses sertifikasi akhir.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-amber-600 dark:text-amber-400">equalizer</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Spectrum Analysis &amp; Optimization</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Optimasi efisiensi spektrum frekuensi sekaligus meminimalkan interferensi.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-teal-50 dark:bg-teal-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-teal-600 dark:text-teal-400">shield</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">EMI/EMC Evaluation Support</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Memastikan kompatibilitas elektromagnetik dan efektivitas perlindungan terhadap gangguan.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">speed</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">High Throughput Data Communication</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Optimalisasi bandwidth untuk transmisi data besar dengan latensi rendah.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-pink-50 dark:bg-pink-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-pink-600 dark:text-pink-400">sync_alt</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">IoT Communication Protocol Engineering</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Pengembangan protokol komunikasi berdaya rendah dan cakupan luas untuk perangkat terhubung.
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
                <h2 class="font-display text-3xl md:text-4xl font-bold mb-6">Ready to build your next big thing?</h2>
                <p class="text-slate-300 max-w-2xl mx-auto mb-10 text-lg">
                    Consult with our experts today and discover how InoSakti can transform your business processes with world-class Network and RF Engineering solutions.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a class="px-8 py-4 bg-accent hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-500/25"
                       href="https://wa.me/+6288207085761">Get Started Now</a>
                    <a class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl transition-all border border-white/20"
                       href="<?php echo $basePath; ?>/index.php#portfolio">View Case Studies</a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include __DIR__.'/../../inc/footer.php'; ?>

