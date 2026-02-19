<?php
$pageTitle = 'Academic Research Publication - InoSakti';
$pageDesc = 'Pendampingan publikasi ilmiah internasional end-to-end: proposal, metodologi, analisis data, penulisan naskah, formatting, submission, hingga HKI.';
include __DIR__.'/../../inc/header.php';
?>

<main class="max-w-7xl mx-auto px-6 py-28"> <!-- add padding top for fixed header -->

    <!-- breadcrumb -->
    <nav class="flex mb-8 text-sm font-medium text-slate-500 dark:text-slate-400 animate-slide-up stagger-1">
        <a class="hover:text-accent transition-colors" href="<?php echo $basePath; ?>/index.php#layanan">Services</a>
        <span class="mx-2">/</span>
        <span class="text-slate-900 dark:text-white">Academic Research Publication</span>
    </nav>

    <!-- hero -->
    <section class="mb-20 animate-slide-up stagger-2">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 md:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none flex flex-col md:flex-row gap-12 items-start">

            <div class="w-full md:w-1/2">
                <div class="inline-flex items-center justify-center p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl mb-8 icon-box transition-transform duration-300">
                    <span class="material-symbols-outlined text-accent text-5xl">history_edu</span>
                </div>

                <h1 class="font-display text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white mb-6 leading-tight">
                    Academic Research Publication
                </h1>

                <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 leading-relaxed mb-8">
                    Layanan komprehensif untuk mendukung keberhasilan publikasi ilmiah internasional.
                    Kami menyediakan pendampingan riset end-to-end, mulai dari pengembangan proposal, analisis data mendalam,
                    hingga finalisasi naskah sesuai standar jurnal bereputasi tinggi. Tingkatkan dampak riset Anda bersama para ahli akademik kami.
                </p>

                <div class="flex flex-wrap gap-4">
                    <span class="px-4 py-2 bg-accent/10 text-accent rounded-full text-sm font-semibold">International Standards</span>
                    <span class="px-4 py-2 bg-emerald-500/10 text-emerald-600 rounded-full text-sm font-semibold">Q1–Q4 Journal Prep</span>
                    <span class="px-4 py-2 bg-amber-500/10 text-amber-600 rounded-full text-sm font-semibold">Expert Mentorship</span>
                </div>
            </div>

            <div class="w-full md:w-1/2 grid grid-cols-2 gap-4">
                <div class="aspect-[4/5] bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center overflow-hidden">
                    <img
                        alt="Scholarly library or study environment"
                        class="w-full h-full object-cover opacity-90 hover:scale-105 transition-transform duration-500"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCmx_9yoAVaoh0ejp3li3JjmawgE21tiFaw6SrDy6hXrOtl-rFWjc3t27wVoVZ4AovX5RIRpks6cbcYCOiPCyqUgXQ8zJMjAqTMSN7o0mPMsdhcI-KUGkC6WuzFnleWhArKQDVqE3K5Icok1fMIYjKQ5MrRGpI0Fvd5sZwkhfxHeLyhE_Zvw2MJFrk7Xeq7OA0p6fnUN_iTiLIcroPOUh72k6QjnM7UjNDkMwBd30L6kk2zR7jmjgA_0tQWNtXAr_BiJL1rPHbUxw"
                    />
                </div>

                <div class="aspect-[4/5] bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center overflow-hidden mt-12">
                    <img
                        alt="Detailed research paper or journal"
                        class="w-full h-full object-cover opacity-90 hover:scale-105 transition-transform duration-500"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDDgPbs-C7tdFYJEQGyajye-KQtw9fyewb6m-ycyH6URmCtVSczU1H0Merefh2HepriwdY-dKaWFRXy9SIi88F1FbvSMIoIgdiLh5hYgFyYDRua-HPoZNj2JZ0pxxfv2N6fRD6kjGaSJYDJ-wWt5-ZVuCiOoi1ucY9H47fFiYvVVwbQs0HnwHsQd50Na9Zen_NMHLOyu1wgTVpLoKT4grDNoCQLF3IXOYuRfn9U8Qd2Hqglu9t4pRZRdifIWLaLJPB-GjnTF53vpw"
                    />
                </div>
            </div>

        </div>
    </section>

    <!-- specialized services (✅ title + line + short desc) -->
    <section class="animate-slide-up stagger-3">

        <div class="text-center mb-12">
            <h2 class="font-display text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">Our Specialized Services</h2>
            <div class="w-20 h-1.5 bg-accent mx-auto rounded-full mb-4"></div>
            <p class="text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
                Pendampingan akademik yang rapi dan terukur — dari perencanaan riset, validasi metodologi, analisis, sampai naskah siap submit dan dokumen HKI.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            <div class="service-card group">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">assignment</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Research Grant Proposal Assistance</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Pendampingan penyusunan proposal pendanaan yang kuat, logis, dan kompetitif untuk hibah nasional maupun internasional.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400">school</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Thesis &amp; Dissertation Guidance</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Pendampingan terstruktur untuk skripsi, tesis, dan disertasi melalui peta jalan, target capaian, serta kendali mutu naskah.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">psychology</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Research Methodology Consultation</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Konsultasi desain riset kualitatif, kuantitatif, maupun campuran yang valid, terukur, dan sesuai ruang lingkup jurnal tujuan.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-sky-50 dark:bg-sky-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-sky-600 dark:text-sky-400">edit_note</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Scientific Paper Writing Assistance</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Pendampingan penyusunan artikel berbasis struktur IMRaD, alur narasi ilmiah yang kuat, dan konsistensi terminologi hingga siap submit.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-cyan-50 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-cyan-600 dark:text-cyan-400">upload_file</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Journal Publication Preparation</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Pendampingan persiapan publikasi mulai dari format template jurnal, manajemen referensi, cover letter, hingga proses pengajuan (submission) dan revisi.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-rose-50 dark:bg-rose-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-rose-600 dark:text-rose-400">gavel</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Patent &amp; Intellectual Property</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Penyusunan dokumen HKI (hak cipta/paten) beserta strategi klaim kebaruan untuk perlindungan inovasi.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">insights</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Research Data Analysis</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Analisis data statistik maupun tematik (SPSS/R/NVivo) disertai interpretasi hasil yang kuat untuk bagian pembahasan.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-amber-600 dark:text-amber-400">science</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Experimental Design Consultation</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Validasi rancangan eksperimen laboratorium maupun lapangan agar data dapat direplikasi, akurat, dan sesuai tujuan penelitian.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-teal-50 dark:bg-teal-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-teal-600 dark:text-teal-400">spellcheck</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Academic Technical Editing</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Layanan proofreading, penyempurnaan bahasa akademik, dan perapian sitasi agar naskah konsisten serta profesional.
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
                <h2 class="font-display text-3xl md:text-4xl font-bold mb-6">Ready to publish your breakthrough?</h2>
                <p class="text-slate-300 max-w-2xl mx-auto mb-10 text-lg">
                    Percepat karier akademik Anda dengan layanan publikasi berstandar internasional. InoSakti siap membantu riset Anda berdampak secara global.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a class="px-8 py-4 bg-accent hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-500/25"
                       href="<?php echo $basePath; ?>/index.php#konsultasi">Get Research Support</a>
                    <a class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl transition-all border border-white/20"
                       href="<?php echo $basePath; ?>/pages/portfolio/publications.php">View Recent Publications</a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include __DIR__.'/../../inc/footer.php'; ?>

