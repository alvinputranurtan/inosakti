<?php
$pageTitle = 'Technology Advisory & Lifecycle Services - InoSakti';
$pageDesc = 'Panduan teknis strategis end-to-end: konsultasi, review arsitektur, audit, optimasi performa, dokumentasi, SOP, hingga dukungan teknis jangka panjang.';
include __DIR__.'/../../inc/header.php';
?>

<main class="max-w-7xl mx-auto px-6 py-28"> <!-- add padding top for fixed header -->

    <!-- breadcrumb -->
    <nav class="flex mb-8 text-sm font-medium text-slate-500 dark:text-slate-400 animate-slide-up stagger-1">
        <a class="hover:text-accent transition-colors" href="<?php echo $basePath; ?>/index.php#layanan">Services</a>
        <span class="mx-2">/</span>
        <span class="text-slate-900 dark:text-white">Technology Advisory &amp; Lifecycle</span>
    </nav>

    <!-- hero -->
    <section class="mb-20 animate-slide-up stagger-2">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 md:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none flex flex-col lg:flex-row gap-12 items-center">

            <div class="w-full lg:w-1/2">
                <div class="inline-flex items-center justify-center p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl mb-8 icon-box transition-transform duration-300">
                    <span class="material-symbols-outlined text-accent text-5xl">partner_reports</span>
                </div>

                <h1 class="font-display text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white mb-6 leading-tight">
                    Technology Advisory &amp; Lifecycle Services
                </h1>

                <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 leading-relaxed mb-8">
                    Kami memberikan panduan teknis strategis di setiap tahapan siklus hidup teknologi Anda. Mulai dari perencanaan arsitektur,
                    audit infrastruktur, hingga optimalisasi performa dan dukungan jangka panjang untuk memastikan investasi teknologi Anda
                    memberikan nilai bisnis maksimal.
                </p>

                <div class="flex flex-wrap gap-4">
                    <span class="px-4 py-2 bg-accent/10 text-accent rounded-full text-sm font-semibold">Strategic Consulting</span>
                    <span class="px-4 py-2 bg-emerald-500/10 text-emerald-600 rounded-full text-sm font-semibold">Lifecycle Support</span>
                    <span class="px-4 py-2 bg-amber-500/10 text-amber-600 rounded-full text-sm font-semibold">Audit &amp; Compliance</span>
                </div>
            </div>

            <div class="w-full lg:w-1/2 grid grid-cols-2 gap-4">
                <div class="aspect-[4/5] bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center overflow-hidden">
                    <img
                        alt="Technical consultation meeting"
                        class="w-full h-full object-cover opacity-90 hover:scale-105 transition-transform duration-500"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCmx_9yoAVaoh0ejp3li3JjmawgE21tiFaw6SrDy6hXrOtl-rFWjc3t27wVoVZ4AovX5RIRpks6cbcYCOiPCyqUgXQ8zJMjAqTMSN7o0mPMsdhcI-KUGkC6WuzFnleWhArKQDVqE3K5Icok1fMIYjKQ5MrRGpI0Fvd5sZwkhfxHeLyhE_Zvw2MJFrk7Xeq7OA0p6fnUN_iTiLIcroPOUh72k6QjnM7UjNDkMwBd30L6kk2zR7jmjgA_0tQWNtXAr_BiJL1rPHbUxw"
                    />
                </div>

                <div class="aspect-[4/5] bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center overflow-hidden mt-12">
                    <img
                        alt="Server room maintenance"
                        class="w-full h-full object-cover opacity-90 hover:scale-105 transition-transform duration-500"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDDgPbs-C7tdFYJEQGyajye-KQtw9fyewb6m-ycyH6URmCtVSczU1H0Merefh2HepriwdY-dKaWFRXy9SIi88F1FbvSMIoIgdiLh5hYgFyYDRua-HPoZNj2JZ0pxxfv2N6fRD6kjGaSJYDJ-wWt5-ZVuCiOoi1ucY9H47fFiYvVVwbQs0HnwHsQd50Na9Zen_NMHLOyu1wgTVpLoKT4grDNoCQLF3IXOYuRfn9U8Qd2Hqglu9t4pRZRdifIWLaLJPB-GjnTF53vpw"
                    />
                </div>
            </div>

        </div>
    </section>

    <!-- specialized services (✅ title + line + short desc) -->
    <section class="animate-slide-up stagger-3 mb-24">
        <div class="text-center mb-12">
            <h2 class="font-display text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">Our Specialized Services</h2>
            <div class="w-20 h-1.5 bg-accent mx-auto rounded-full mb-4"></div>
            <p class="text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
                Layanan advisory yang praktis dan bisa dieksekusi: audit, improvement, dokumentasi, SOP, hingga dukungan operasional jangka panjang.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <div class="service-card group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-accent">help_clinic</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Technology Consulting</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Konsultasi strategi teknologi dan roadmap implementasi agar selaras dengan target bisnis dan growth.
                </p>
            </div>

            <div class="service-card group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400">account_tree</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Architecture Design Review</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Review desain sistem untuk memastikan skalabilitas, keamanan, reliability, dan maintainability.
                </p>
            </div>

            <div class="service-card group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300">
                <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">build_circle</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Maintenance &amp; Repair System</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Stabilitas operasional lewat preventive maintenance, patching, dan respons insiden yang cepat.
                </p>
            </div>

            <div class="service-card group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300">
                <div class="w-12 h-12 bg-sky-50 dark:bg-sky-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-sky-600 dark:text-sky-400">upgrade</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Modification &amp; Upgrade</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Modernisasi sistem (feature/stack/infra) agar tetap kompetitif dan sesuai kebutuhan enterprise.
                </p>
            </div>

            <div class="service-card group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300">
                <div class="w-12 h-12 bg-cyan-50 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-cyan-600 dark:text-cyan-400">speed</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Performance Optimization</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Tuning performa aplikasi, database, dan infrastruktur untuk latency rendah dan efisiensi resource.
                </p>
            </div>

            <div class="service-card group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300">
                <div class="w-12 h-12 bg-rose-50 dark:bg-rose-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-rose-600 dark:text-rose-400">fact_check</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Infrastructure Audit</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Audit keamanan & kinerja untuk mengidentifikasi risiko, bottleneck, serta rekomendasi perbaikan.
                </p>
            </div>

            <div class="service-card group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300">
                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">rocket_launch</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Deployment Assistance</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Bantuan implementasi, migrasi, dan rollout dengan downtime minimal untuk sistem kritikal.
                </p>
            </div>

            <div class="service-card group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300">
                <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-amber-600 dark:text-amber-400">description</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Technical Documentation</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Dokumentasi sistem, runbook, dan manual teknis untuk knowledge management dan continuity.
                </p>
            </div>

            <div class="service-card group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300">
                <div class="w-12 h-12 bg-teal-50 dark:bg-teal-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-teal-600 dark:text-teal-400">assignment_turned_in</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Operational SOP Development</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Penyusunan SOP operasional agar workflow konsisten, aman, dan mudah diaudit.
                </p>
            </div>

            <div class="service-card group bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300">
                <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">support_agent</span>
                </div>
                <h3 class="font-bold text-lg text-slate-900 dark:text-white mb-2">Long-Term Technical Support</h3>
                <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                    Dukungan berkelanjutan melalui kanal support, SLA, serta monitoring & improvement berkala.
                </p>
            </div>

        </div>
    </section>

    <!-- CTA -->
    <section class="mt-24 mb-12 animate-slide-up stagger-4">
        <div class="bg-primary rounded-[2.5rem] p-12 text-center text-white relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                <div class="absolute -top-24 -left-24 w-96 h-96 bg-accent rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-400 rounded-full blur-3xl"></div>
            </div>

            <div class="relative z-10">
                <h2 class="font-display text-3xl md:text-4xl font-bold mb-6">Need expert guidance for your technology lifecycle?</h2>
                <p class="text-slate-300 max-w-2xl mx-auto mb-10 text-lg">
                    Konsultasikan kebutuhan teknis perusahaan Anda bersama tim ahli InoSakti untuk solusi yang berkelanjutan dan efisien.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a class="px-8 py-4 bg-accent hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-500/25"
                       href="<?php echo $basePath; ?>/index.php#konsultasi">
                        Consult Now
                    </a>

                    <a class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl transition-all border border-white/20"
                       href="<?php echo $basePath; ?>/pages/services/overview.php">
                        Download Service Overview
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include __DIR__.'/../../inc/footer.php'; ?>