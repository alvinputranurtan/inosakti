<?php
$pageTitle = 'Education & Training - InoSakti';
$pageDesc = 'Program pelatihan praktis berbasis proyek untuk menjembatani teori akademis dan kebutuhan industri: software, AI, IoT, networking, RF, electronics, hingga energi terbarukan.';
include __DIR__.'/../../inc/header.php';
?>

<main class="max-w-7xl mx-auto px-6 py-28"> <!-- add padding top for fixed header -->

    <!-- breadcrumb -->
    <nav class="flex mb-8 text-sm font-medium text-slate-500 dark:text-slate-400 animate-slide-up stagger-1">
        <a class="hover:text-accent transition-colors" href="<?php echo $basePath; ?>/index.php#layanan">Services</a>
        <span class="mx-2">/</span>
        <span class="text-slate-900 dark:text-white">Education &amp; Training</span>
    </nav>

    <!-- hero -->
    <section class="mb-20 animate-slide-up stagger-2">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 md:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none flex flex-col md:flex-row gap-12 items-start">
            <div class="w-full md:w-1/2">
                <div class="inline-flex items-center justify-center p-4 bg-accent/10 rounded-2xl mb-8 icon-box transition-transform duration-300">
                    <span class="material-symbols-outlined text-accent text-5xl">school</span>
                </div>

                <h1 class="font-display text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white mb-6 leading-tight">
                    Education &amp; Training
                </h1>

                <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 leading-relaxed mb-8">
                    Kami menyediakan program pelatihan praktis berbasis proyek yang dirancang untuk menjembatani kesenjangan
                    antara teori akademis dan kebutuhan industri. Dari pengembangan perangkat lunak hingga teknik energi
                    terbarukan, kurikulum kami fokus pada penguasaan keterampilan teknis yang siap pakai.
                </p>

                <div class="flex flex-wrap gap-4">
                    <span class="px-4 py-2 bg-accent/10 text-accent rounded-full text-sm font-semibold">Project-Based Learning</span>
                    <span class="px-4 py-2 bg-emerald-500/10 text-emerald-600 rounded-full text-sm font-semibold">Industry Ready</span>
                    <span class="px-4 py-2 bg-amber-500/10 text-amber-600 rounded-full text-sm font-semibold">Expert Mentors</span>
                </div>
            </div>

            <div class="w-full md:w-1/2 grid grid-cols-2 gap-4">
                <div class="aspect-[4/5] bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center overflow-hidden">
                    <img
                        alt="Hands-on technical workshop"
                        class="w-full h-full object-cover opacity-90 hover:scale-105 transition-transform duration-500"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCmx_9yoAVaoh0ejp3li3JjmawgE21tiFaw6SrDy6hXrOtl-rFWjc3t27wVoVZ4AovX5RIRpks6cbcYCOiPCyqUgXQ8zJMjAqTMSN7o0mPMsdhcI-KUGkC6WuzFnleWhArKQDVqE3K5Icok1fMIYjKQ5MrRGpI0Fvd5sZwkhfxHeLyhE_Zvw2MJFrk7Xeq7OA0p6fnUN_iTiLIcroPOUh72k6QjnM7UjNDkMwBd30L6kk2zR7jmjgA_0tQWNtXAr_BiJL1rPHbUxw"
                    />
                </div>

                <div class="aspect-[4/5] bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center overflow-hidden mt-8">
                    <img
                        alt="Student working on engineering project"
                        class="w-full h-full object-cover opacity-90 hover:scale-105 transition-transform duration-500"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDDgPbs-C7tdFYJEQGyajye-KQtw9fyewb6m-ycyH6URmCtVSczU1H0Merefh2HepriwdY-dKaWFRXy9SIi88F1FbvSMIoIgdiLh5hYgFyYDRua-HPoZNj2JZ0pxxfv2N6fRD6kjGaSJYDJ-wWt5-ZVuCiOoi1ucY9H47fFiYvVVwbQs0HnwHsQd50Na9Zen_NMHLOyu1wgTVpLoKT4grDNoCQLF3IXOYuRfn9U8Qd2Hqglu9t4pRZRdifIWLaLJPB-GjnTF53vpw"
                    />
                </div>
            </div>
        </div>
    </section>

    <!-- programs -->
    <section class="animate-slide-up stagger-3">

        <!-- ✅ same layout style: title → line → short desc -->
        <div class="text-center mb-12">
            <h2 class="font-display text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">Our Programs</h2>
            <div class="w-20 h-1.5 bg-accent mx-auto rounded-full mb-4"></div>
            <p class="text-slate-500 dark:text-slate-400 max-w-2xl mx-auto">
                Program pembelajaran berbasis proyek untuk meningkatkan skill teknis dari level fundamental hingga implementasi industri.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

            <div class="service-card group">
                <div class="w-14 h-14 bg-accent/10 dark:bg-accent/20 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-accent text-3xl">laptop_mac</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Edukasi Online</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-6">
                    Akses materi pembelajaran fleksibel kapan saja dan di mana saja melalui platform digital interaktif kami.
                </p>
                <a class="inline-flex items-center text-accent font-bold group-hover:underline" href="<?php echo $basePath; ?>/pages/learning">
                    Masuk ke Platform
                    <span class="material-symbols-outlined ml-2 text-sm">open_in_new</span>
                </a>
            </div>

            <div class="service-card group">
                <div class="w-14 h-14 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400 text-3xl">code</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Web Development Training</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Master modern full-stack development with hands-on practice in frontend, backend, API integration, and database design.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-14 h-14 bg-purple-50 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-purple-600 dark:text-purple-400 text-3xl">psychology</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">AI Development Training</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Learn to build and deploy AI solutions: image processing, object detection, deep learning workflows, and model deployment.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400 text-3xl">memory</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Embedded &amp; IoT Programming</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Hardware-software integration focusing on microcontrollers, sensors, communication (WiFi/BLE/LoRa), and real-time data pipelines.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-14 h-14 bg-sky-50 dark:bg-sky-900/30 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-sky-600 dark:text-sky-400 text-3xl">hub</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Network Engineering Training</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Comprehensive training in network infrastructure: routing, switching, planning, installation, and secure deployment practices.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400 text-3xl">settings_input_antenna</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">RF Engineering Training</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Specialized training in RF fundamentals, antenna basics, measurement, and wireless communication system design workflows.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-14 h-14 bg-amber-50 dark:bg-amber-900/30 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-amber-600 dark:text-amber-400 text-3xl">electrical_services</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Electronics Engineering</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Deep dive into circuit design, PCB layout, component selection, prototyping, and testing/validation for real projects.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-14 h-14 bg-cyan-50 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-cyan-600 dark:text-cyan-400 text-3xl">solar_power</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Renewable Energy Training</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Practical workshops on solar and hybrid systems: sizing, installation, monitoring, and performance optimization.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-14 h-14 bg-rose-50 dark:bg-rose-900/30 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-rose-600 dark:text-rose-400 text-3xl">rocket_launch</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Project-Based Bootcamp</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Intensive program focused on delivering end-to-end solutions for real-world engineering challenges (portfolio-ready).
                </p>
            </div>

            <div class="service-card group">
                <div class="w-14 h-14 bg-teal-50 dark:bg-teal-900/30 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-teal-600 dark:text-teal-400 text-3xl">groups</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Corporate Training</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Customized upskilling programs to enhance technical competencies of your workforce, aligned with your tools and SOP.
                </p>
            </div>

            <div class="service-card group">
                <div class="w-14 h-14 bg-slate-50 dark:bg-slate-800 rounded-xl flex items-center justify-center mb-6 icon-box transition-transform">
                    <span class="material-symbols-outlined text-slate-600 dark:text-slate-300 text-3xl">precision_manufacturing</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Custom Industrial Workshop</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                    Modul pelatihan teknis yang dirancang khusus untuk menjawab kebutuhan industri yang spesifik dan teknologi khusus.
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
                <h2 class="font-display text-3xl md:text-4xl font-bold mb-6">Elevate your technical expertise today</h2>
                <p class="text-slate-300 max-w-2xl mx-auto mb-10 text-lg">
                    Ready to start your learning journey? Join our upcoming sessions or contact us for a customized corporate program.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a class="px-8 py-4 bg-accent hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-500/25"
                       href="<?php echo $basePath; ?>/index.php#edukasi">Check Course Schedule</a>
                    <a class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl transition-all border border-white/20"
                       href="<?php echo $basePath; ?>/pages/downloads/curriculum.php">Download Curriculum</a>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include __DIR__.'/../../inc/footer.php'; ?>

