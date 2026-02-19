<?php
$pageTitle = 'AI-Driven Systems - InoSakti';
$pageDesc = 'Implementasi kecerdasan buatan (AI) untuk analisis data mendalam dan prediksi yang akurat.';
include __DIR__.'/../../inc/header.php';
?>

<main class="max-w-7xl mx-auto px-6 py-28"> <!-- add padding top for fixed/sticky header -->
    <!-- breadcrumb -->
    <nav class="flex mb-8 text-sm font-medium text-slate-500 dark:text-slate-400 animate-slide-up stagger-1">
        <a class="hover:text-accent transition-colors" href="<?php echo $basePath; ?>/#layanan">Services</a>
        <span class="mx-2">/</span>
        <span class="text-slate-900 dark:text-white">AI-Driven Systems</span>
    </nav>

    <!-- hero section -->
    <section class="mb-20 animate-slide-up stagger-2">
        <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 md:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none flex flex-col md:flex-row gap-12 items-start">
            <!-- left -->
            <div class="w-full md:w-1/2">
                <div class="inline-flex items-center justify-center p-4 bg-blue-50 dark:bg-blue-900/30 rounded-2xl mb-8 icon-box transition-transform duration-300">
                    <span class="material-symbols-outlined text-accent text-5xl">neurology</span>
                </div>

                <h1 class="font-display text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white mb-6 leading-tight">
                    AI-Driven Systems
                </h1>

                <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 leading-relaxed mb-8">
                    Implementasi kecerdasan buatan (AI) untuk analisis data mendalam dan prediksi yang akurat. Kami menghadirkan solusi cerdas mulai dari computer vision hingga predictive analytics untuk mengotomatisasi keputusan bisnis dan meningkatkan efisiensi operasional secara signifikan.
                </p>

                <div class="flex flex-wrap gap-4">
                    <span class="px-4 py-2 bg-accent/10 text-accent rounded-full text-sm font-semibold flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">hub</span> Neural Networks
                    </span>
                    <span class="px-4 py-2 bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-sm font-semibold flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">visibility</span> Computer Vision
                    </span>
                    <span class="px-4 py-2 bg-amber-500/10 text-amber-700 dark:text-amber-400 rounded-full text-sm font-semibold flex items-center gap-2">
                        <span class="material-symbols-outlined text-sm">insights</span> Predictive Modeling
                    </span>
                </div>
            </div>

            <!-- right -->
            <div class="w-full md:w-1/2 relative">
                <div class="grid grid-cols-2 gap-4 relative z-10">
                    <div class="aspect-square bg-slate-100 dark:bg-slate-800 rounded-2xl overflow-hidden shadow-2xl">
                        <img
                            alt="Neural network visualization"
                            class="w-full h-full object-cover opacity-90 hover:scale-105 transition-transform duration-500"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCmx_9yoAVaoh0ejp3li3JjmawgE21tiFaw6SrDy6hXrOtl-rFWjc3t27wVoVZ4AovX5RIRpks6cbcYCOiPCyqUgXQ8zJMjAqTMSN7o0mPMsdhcI-KUGkC6WuzFnleWhArKQDVqE3K5Icok1fMIYjKQ5MrRGpI0Fvd5sZwkhfxHeLyhE_Zvw2MJFrk7Xeq7OA0p6fnUN_iTiLIcroPOUh72k6QjnM7UjNDkMwBd30L6kk2zR7jmjgA_0tQWNtXAr_BiJL1rPHbUxw"
                        />
                    </div>

                    <div class="aspect-square bg-slate-100 dark:bg-slate-800 rounded-2xl overflow-hidden shadow-2xl mt-8">
                        <img
                            alt="AI analysis dashboard"
                            class="w-full h-full object-cover opacity-90 hover:scale-105 transition-transform duration-500"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDDgPbs-C7tdFYJEQGyajye-KQtw9fyewb6m-ycyH6URmCtVSczU1H0Merefh2HepriwdY-dKaWFRXy9SIi88F1FbvSMIoIgdiLh5hYgFyYDRua-HPoZNj2JZ0pxxfv2N6fRD6kjGaSJYDJ-wWt5-ZVuCiOoi1ucY9H47fFiYvVVwbQs0HnwHsQd50Na9Zen_NMHLOyu1wgTVpLoKT4grDNoCQLF3IXOYuRfn9U8Qd2Hqglu9t4pRZRdifIWLaLJPB-GjnTF53vpw"
                        />
                    </div>
                </div>

                <!-- glow -->
                <div class="absolute -top-10 -right-10 w-64 h-64 bg-accent/10 rounded-full blur-3xl -z-0"></div>
            </div>
        </div>
    </section>

    <!-- specialized services grid -->
    <section class="animate-slide-up stagger-3">
        <div class="text-center mb-12">
            <h2 class="font-display text-3xl font-bold text-slate-900 dark:text-white mb-4">Our Specialized Services</h2>
            <div class="w-20 h-1.5 bg-accent mx-auto rounded-full"></div>
            <p class="mt-4 text-slate-500 dark:text-slate-400">Comprehensive AI &amp; Machine Learning solutions tailored for modern industry.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="service-card group">
                <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">filter_center_focus</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3 leading-tight">Image Processing</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Algoritma tingkat lanjut untuk peningkatan kualitas, analisis, dan ekstraksi informasi dari citra digital.</p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400">frame_inspect</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3 leading-tight">Object Detection</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Identifikasi dan pelokalan multiobjek secara real-time pada aliran video.</p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">model_training</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3 leading-tight">Deep Learning Model Development</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Perancangan arsitektur jaringan saraf kustom untuk menyelesaikan permasalahan bisnis yang kompleks.</p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-sky-50 dark:bg-sky-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-sky-600 dark:text-sky-400">query_stats</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3 leading-tight">Predictive Analytics</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Prediksi tren serta perilaku masa depan berbasis data historis dan pemodelan statistik.</p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">factory</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3 leading-tight">Computer Vision Industrial System</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Sistem inspeksi kualitas dan panduan proses otomatis untuk lingkungan manufaktur.</p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-rose-50 dark:bg-rose-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-rose-600 dark:text-rose-400">memory</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3 leading-tight">AI Edge Inference System</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Penerapan model AI langsung pada perangkat keras untuk pemrosesan berlatensi rendah.</p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-cyan-50 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-cyan-600 dark:text-cyan-400">smart_toy</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3 leading-tight">Intelligent Automation System</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Integrasi AI dan RPA untuk mengotomatisasi alur kerja end-to-end yang kompleks.</p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-amber-600 dark:text-amber-400">videocam</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3 leading-tight">AI-based Monitoring &amp; Surveillance</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Solusi keamanan cerdas dengan analisis perilaku dan deteksi ancaman otomatis.</p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-teal-50 dark:bg-teal-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-teal-600 dark:text-teal-400">account_tree</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3 leading-tight">Machine Learning Pipeline Development</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Pembangunan pipeline MLOps end-to-end mulai dari akuisisi data hingga deployment model.</p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">label_important</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3 leading-tight">Dataset Preparation &amp; Labeling</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Layanan anotasi data berkualitas tinggi untuk melatih model AI yang akurat dan andal.</p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-pink-50 dark:bg-pink-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-pink-600 dark:text-pink-400">rocket_launch</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3 leading-tight">AI Model Optimization &amp; Deployment</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Teknik kuantisasi dan pruning untuk meningkatkan kecepatan inferensi pada server produksi.</p>
            </div>

            <div class="service-card group">
                <div class="w-12 h-12 bg-violet-50 dark:bg-violet-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                    <span class="material-symbols-outlined text-violet-600 dark:text-violet-400">settings_suggest</span>
                </div>
                <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3 leading-tight">Decision Support System</h3>
                <p class="text-slate-600 dark:text-slate-400 leading-relaxed">Platform berbasis AI yang memberikan rekomendasi strategis dengan dukungan data yang kuat.</p>
            </div>
        </div>
    </section>

    <!-- call to action -->
    <section class="mt-24 mb-12 animate-slide-up stagger-3">
        <div class="bg-primary rounded-[2.5rem] p-12 text-center text-white relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full opacity-10">
                <div class="absolute -top-24 -left-24 w-96 h-96 bg-accent rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-blue-400 rounded-full blur-3xl"></div>
            </div>
            <div class="relative z-10">
                <h2 class="font-display text-3xl md:text-4xl font-bold mb-6">Empower Your Business with AI Intelligence</h2>
                <p class="text-slate-300 max-w-2xl mx-auto mb-10 text-lg">
                    Our experts are ready to help you implement state-of-the-art AI solutions. Start your digital transformation with InoSakti today.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a class="px-8 py-4 bg-accent hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-500/25" href="#">Get Started Now</a>
                    <a class="px-8 py-4 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl transition-all border border-white/20" href="#">View Case Studies</a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include __DIR__.'/../../inc/footer.php'; ?>

