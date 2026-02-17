<?php
$pageTitle = 'Tentang InoSakti - Applied Engineering & Technology Solutions';
$pageDesc = 'Profil, visi, misi, dan tim di balik InoSakti — Applied Engineering & Technology Solutions.';
include __DIR__.'/../../inc/header.php';
?>

<main class="max-w-7xl mx-auto px-6 py-28"> <!-- add padding top for fixed header -->

    <!-- breadcrumb -->
    <nav class="flex mb-8 text-sm font-medium text-slate-500 dark:text-slate-400 animate-slide-up stagger-1">
        <a class="hover:text-accent transition-colors" href="<?php echo $basePath; ?>/index.php">Home</a>
        <span class="mx-2">/</span>
        <span class="text-slate-900 dark:text-white">About</span>
    </nav>

    <!-- page title -->
    <header class="text-center mb-16 animate-slide-up stagger-2">
        <h1 class="font-display text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white mb-4">
            Tentang <span class="text-accent">InoSakti</span>
        </h1>
        <div class="h-1.5 w-24 bg-accent mx-auto rounded-full"></div>
        <p class="mt-5 text-slate-600 dark:text-slate-400 max-w-3xl mx-auto">
            Profil singkat perusahaan, arah strategis, serta orang-orang di balik pengembangan InoSakti.
        </p>
    </header>

    <div class="space-y-12">

        <!-- Kenali -->
        <section class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 md:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none animate-slide-up stagger-2">
            <div class="max-w-4xl mx-auto">
                <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-900 dark:text-white text-center mb-6">
                    Kenali InoSakti
                </h2>
                <p class="text-lg text-slate-600 dark:text-slate-400 text-center leading-relaxed">
                    InoSakti adalah Korporasi Applied Engineering &amp; Technology Solutions yang menghadirkan layanan engineering profesional,
                    pengembangan integrated smart systems, serta solusi Agrotechnology berbasis riset dan inovasi. Kami menyediakan R&amp;D,
                    software solutions, dan layanan edukasi di bidang AI, IoT, networking, RF, elektronika, dan energi terbarukan.
                    Berorientasi pada solusi end-to-end, InoSakti mengintegrasikan teknologi dan rekayasa untuk menjawab kebutuhan industri,
                    pendidikan, dan transformasi digital secara berkelanjutan.
                </p>
            </div>
        </section>

        <!-- Visi -->
        <section class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 md:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none animate-slide-up stagger-3">
            <div class="max-w-4xl mx-auto">
                <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-900 dark:text-white text-center mb-6">
                    Visi InoSakti
                </h2>
                <p class="text-xl md:text-2xl italic text-slate-700 dark:text-slate-300 text-center leading-relaxed">
                    “Menjadi korporasi Applied Engineering &amp; Technology Solutions terdepan dalam pengembangan sistem cerdas, rekayasa terapan,
                    dan inovasi berkelanjutan yang memberikan dampak nyata bagi industri, pendidikan, dan transformasi digital.”
                </p>
            </div>
        </section>

        <!-- Misi -->
        <section class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 md:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none animate-slide-up stagger-3">
            <div class="max-w-4xl mx-auto">
                <h2 class="font-display text-2xl md:text-3xl font-bold text-slate-900 dark:text-white text-center mb-10">
                    Misi InoSakti
                </h2>

                <ul class="space-y-6">
                    <?php
                    $misi = [
                        'Mengembangkan solusi engineering dan integrated smart systems berbasis riset dan inovasi yang aplikatif dan scalable.',
                        'Menyediakan layanan R&D dan software solutions yang presisi, efisien, dan berorientasi pada kebutuhan industri.',
                        'Mengintegrasikan teknologi AI, IoT, networking, RF, elektronika, dan energi terbarukan dalam sistem end-to-end yang adaptif dan berkelanjutan.',
                        'Mendorong transformasi digital melalui solusi teknologi yang terukur dan berdampak.',
                        'Menghadirkan layanan edukasi dan transfer teknologi untuk memperkuat kapasitas sumber daya manusia di bidang engineering dan teknologi terapan.',
                    ];
foreach ($misi as $i => $text) { ?>
                        <li class="flex items-start gap-4">
                            <span class="flex-shrink-0 w-9 h-9 rounded-full bg-accent text-white flex items-center justify-center font-bold">
                                <?php echo $i + 1; ?>
                            </span>
                            <p class="text-slate-700 dark:text-slate-300 leading-relaxed">
                                <?php echo $text; ?>
                            </p>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </section>

        <!-- Team -->
        <section class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 md:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none animate-slide-up stagger-3">
            <div class="text-center mb-12">
                <h2 class="font-display text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">
                    Dibalik InoSakti
                </h2>
                <div class="w-20 h-1.5 bg-accent mx-auto rounded-full mb-4"></div>
                <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                    Tim dan advisor yang berperan dalam riset, inovasi, dan pengembangan solusi end-to-end InoSakti.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <!-- Irfan -->
                <article class="group bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-6 rounded-2xl text-center shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="mb-6 relative inline-block">
                        <div class="absolute inset-0 bg-accent/20 rounded-full scale-110 blur group-hover:scale-125 transition-transform"></div>
                        <img
                            alt="Dr. Eng. Ir. Irfan Mujahidin"
                            class="relative w-40 h-40 rounded-full object-cover mx-auto border-4 border-white dark:border-slate-700 shadow-lg"
                            src="<?php echo $basePath; ?>/assets/img/Foto_Irfan.png"
                        />
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2 min-h-[56px] flex items-center justify-center">
                        Dr. Eng. Ir. Irfan Mujahidin, S.T., M.T., M.Sc., IPP
                    </h3>
                    <p class="text-sm text-accent font-semibold mb-6">Expert Advisor</p>
                    <a
                        class="w-full inline-flex items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white py-3 rounded-xl font-medium hover:bg-accent hover:text-white hover:border-accent transition-colors"
                        href="<?php echo $basePath; ?>/pages/team/irfan.php"
                    >
                        Kenali Lebih Lanjut
                    </a>
                </article>

                <!-- Alvin -->
                <article class="group bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-6 rounded-2xl text-center shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="mb-6 relative inline-block">
                        <div class="absolute inset-0 bg-accent/20 rounded-full scale-110 blur group-hover:scale-125 transition-transform"></div>
                        <img
                            alt="Alvin Putra Nurtan"
                            class="relative w-40 h-40 rounded-full object-cover mx-auto border-4 border-white dark:border-slate-700 shadow-lg"
                            src="<?php echo $basePath; ?>/assets/img/Foto_Alvin.png"
                        />
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2 min-h-[56px] flex items-center justify-center">
                        Alvin Putra Nurtan, S.Tr.T
                    </h3>
                    <p class="text-sm text-accent font-semibold mb-6">Lead Developer</p>
                    <a
                        class="w-full inline-flex items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white py-3 rounded-xl font-medium hover:bg-accent hover:text-white hover:border-accent transition-colors"
                        href="<?php echo $basePath; ?>/pages/team/alvin.php"
                    >
                        Kenali Lebih Lanjut
                    </a>
                </article>

                <!-- Sidiq -->
                <article class="group bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 p-6 rounded-2xl text-center shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
                    <div class="mb-6 relative inline-block">
                        <div class="absolute inset-0 bg-accent/20 rounded-full scale-110 blur group-hover:scale-125 transition-transform"></div>
                        <img
                            alt="Sidiq Syamsul Hidayat"
                            class="relative w-40 h-40 rounded-full object-cover mx-auto border-4 border-white dark:border-slate-700 shadow-lg"
                            src="<?php echo $basePath; ?>/assets/img/Foto_Sidiq.png"
                        />
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2 min-h-[56px] flex items-center justify-center">
                        Sidiq Syamsul Hidayat, S.T., M.T., Ph.D., IPU
                    </h3>
                    <p class="text-sm text-accent font-semibold mb-6">Principal Researcher</p>
                    <a
                        class="w-full inline-flex items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white py-3 rounded-xl font-medium hover:bg-accent hover:text-white hover:border-accent transition-colors"
                        href="<?php echo $basePath; ?>/pages/team/sidiq.php"
                    >
                        Kenali Lebih Lanjut
                    </a>
                </article>

            </div>
        </section>

    </div>
</main>

<?php include __DIR__.'/../../inc/footer.php'; ?>