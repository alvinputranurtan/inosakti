<?php
$pageTitle = 'Research, Innovation & Prototyping - InoSakti';
$pageDesc = 'Layanan riset terapan dan pengembangan prototipe fisik maupun digital untuk memvalidasi ide inovatif hingga siap implementasi industri.';
include __DIR__.'/../../inc/header.php';
?>

<main class="max-w-7xl mx-auto px-6 py-28"> <!-- add padding top for fixed header -->

    <!-- breadcrumb -->
    <nav class="flex mb-8 text-sm font-medium text-slate-500 dark:text-slate-400 animate-slide-up stagger-1">
        <a class="hover:text-accent transition-colors" href="<?php echo $basePath; ?>/index.php#layanan">Services</a>
        <span class="mx-2">/</span>
        <span class="text-slate-900 dark:text-white">Research, Innovation &amp; Prototyping</span>
    </nav>

    <!-- hero -->
    <section class="mb-20 animate-slide-up stagger-2">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 md:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none flex flex-col md:flex-row gap-12 items-center">
            <div class="w-full md:w-1/2">
                <div class="inline-flex items-center justify-center p-4 bg-accent/10 rounded-2xl mb-8 icon-box transition-transform duration-300">
                    <span class="material-symbols-outlined text-accent text-5xl">biotech</span>
                </div>

                <h1 class="font-display text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white mb-6 leading-tight">
                    Research, Innovation &amp; Prototyping
                </h1>

                <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 leading-relaxed mb-8">
                    Kami menghadirkan layanan riset terapan dan pengembangan prototipe fisik maupun digital untuk memvalidasi
                    ide-ide inovatif Anda. Dengan pendekatan berbasis data dan rekayasa presisi, kami membantu menjembatani
                    celah antara konsep teoritis dan implementasi industri yang siap pakai.
                </p>

                <div class="flex flex-wrap gap-4">
                    <span class="px-4 py-2 bg-accent/10 text-accent rounded-full text-sm font-semibold">Applied Research</span>
                    <span class="px-4 py-2 bg-emerald-500/10 text-emerald-600 rounded-full text-sm font-semibold">Rapid Prototyping</span>
                    <span class="px-4 py-2 bg-amber-500/10 text-amber-600 rounded-full text-sm font-semibold">IP Development</span>
                </div>
            </div>

            <div class="w-full md:w-1/2 grid grid-cols-2 gap-4">
                <div class="aspect-[4/5] bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center overflow-hidden shadow-lg">
                    <img
                        alt="Team in R&amp;D lab working on technology"
                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-700"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCmx_9yoAVaoh0ejp3li3JjmawgE21tiFaw6SrDy6hXrOtl-rFWjc3t27wVoVZ4AovX5RIRpks6cbcYCOiPCyqUgXQ8zJMjAqTMSN7o0mPMsdhcI-KUGkC6WuzFnleWhArKQDVqE3K5Icok1fMIYjKQ5MrRGpI0Fvd5sZwkhfxHeLyhE_Zvw2MJFrk7Xeq7OA0p6fnUN_iTiLIcroPOUh72k6QjnM7UjNDkMwBd30L6kk2zR7jmjgA_0tQWNtXAr_BiJL1rPHbUxw"
                    />
                </div>

                <div class="aspect-[4/5] bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center overflow-hidden mt-12 shadow-lg">
                    <img
                        alt="Physical prototype being assembled with precision"
                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-700"
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
            <div class="w-20 h-1.5 bg-accent mx-auto rounded-full mb-4"></div>
            <p class="text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
                End-to-end applied R&amp;D services to validate ideas, de-risk engineering decisions, and accelerate prototypes into production-ready innovations.
            </p>
        </div>

        <!-- 5 columns on xl, as original -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
            <div class="service-card group">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">science</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 leading-snug">Research &amp; Development Projects</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    End-to-end systematic investigation and development of novel technical solutions.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400">precision_manufacturing</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 leading-snug">Prototype Development &amp; Validation</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Building functional physical models to test and verify design assumptions.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">query_stats</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 leading-snug">Technology Feasibility Study</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    In-depth technical analysis to determine project viability and potential risks.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-sky-50 dark:bg-sky-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-sky-600 dark:text-sky-400">lightbulb</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 leading-snug">Proof of Concept Development</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Demonstrating practical potential of concepts through MVP functional builds.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-cyan-50 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-cyan-600 dark:text-cyan-400">memory</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 leading-snug">Experimental System Engineering</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Engineering bespoke experimental setups for complex data collection and testing.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-rose-50 dark:bg-rose-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-rose-600 dark:text-rose-400">sensors</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 leading-snug">Signal Processing System Development</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Advanced processing of real-time sensor data for specialized industrial needs.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">functions</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 leading-snug">Advanced Algorithm Engineering</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Custom algorithm design for optimization, prediction, and pattern recognition.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-amber-600 dark:text-amber-400">auto_awesome</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 leading-snug">Product Innovation Engineering</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Infusing creative engineering solutions into product design for competitive edge.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-teal-50 dark:bg-teal-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-teal-600 dark:text-teal-400">factory</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 leading-snug">Industrial Pilot Project</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Small-scale implementation to test new systems in actual industrial environments.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">rocket_launch</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2 leading-snug">Technology Commercialization Preparation</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Optimizing R&amp;D outputs for mass production and market readiness standards.
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
                <h2 class="font-display text-3xl md:text-4xl font-bold mb-6">Have an innovative idea?</h2>
                <p class="text-slate-300 max-w-2xl mx-auto mb-10 text-lg">
                    Consult with our R&amp;D specialists to turn your complex technical concepts into tangible, market-ready innovations.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a class="px-8 py-4 bg-accent hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-500/25"
                       href="https://wa.me/+6288207085761">Start R&amp;D Consultation</a>
                    <a class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl transition-all border border-white/20"
                       href="<?php echo $basePath; ?>/index.php#portfolio">View Innovation Portfolio</a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include __DIR__.'/../../inc/footer.php'; ?>