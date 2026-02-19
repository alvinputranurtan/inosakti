    <?php
    $pageTitle = 'Software Solutions - InoSakti';
$pageDesc = 'Pengembangan sistem perangkat lunak end-to-end, mencakup enterprise apps & web systems berskala industri.';
include __DIR__.'/../../inc/header.php';
?>

    <main class="max-w-7xl mx-auto px-6 py-28"> <!-- add padding top for fixed header -->
        <!-- breadcrumb -->
        <nav class="flex mb-8 text-sm font-medium text-slate-500 dark:text-slate-400 animate-slide-up stagger-1">
            <a class="hover:text-accent" href="<?php echo $basePath; ?>/index.php#layanan">Services</a>
            <span class="mx-2">/</span>
            <span class="text-slate-900 dark:text-white">Software Solutions</span>
        </nav>

        <!-- hero section -->
        <section class="mb-20 animate-slide-up stagger-2">
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 p-8 md:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none flex flex-col md:flex-row gap-12 items-start">
                <div class="w-full md:w-1/2">
                    <div class="inline-flex items-center justify-center p-4 bg-slate-50 dark:bg-slate-800 rounded-2xl mb-8 icon-box transition-transform duration-300">
                        <span class="material-symbols-outlined text-accent text-5xl">developer_mode_tv</span>
                    </div>
                    <h1 class="font-display text-4xl md:text-5xl font-extrabold text-slate-900 dark:text-white mb-6 leading-tight">
                        Software Solutions
                    </h1>
                    <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 leading-relaxed mb-8">
                        Pengembangan sistem perangkat lunak end-to-end, mencakup enterprise apps &amp; web systems berskala industri. Kami mengintegrasikan teknologi terkini untuk memastikan skalabilitas, keamanan, dan performa optimal bagi bisnis Anda di era digital.
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <span class="px-4 py-2 bg-accent/10 text-accent rounded-full text-sm font-semibold">Scalable Architecture</span>
                        <span class="px-4 py-2 bg-emerald-500/10 text-emerald-600 rounded-full text-sm font-semibold">Enterprise Grade</span>
                        <span class="px-4 py-2 bg-amber-500/10 text-amber-600 rounded-full text-sm font-semibold">24/7 Support</span>
                    </div>
                </div>
                <div class="w-full md:w-1/2 grid grid-cols-2 gap-4">
                    <div class="aspect-square bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center overflow-hidden">
                        <img alt="Code on a screen" class="w-full h-full object-cover opacity-80 hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCmx_9yoAVaoh0ejp3li3JjmawgE21tiFaw6SrDy6hXrOtl-rFWjc3t27wVoVZ4AovX5RIRpks6cbcYCOiPCyqUgXQ8zJMjAqTMSN7o0mPMsdhcI-KUGkC6WuzFnleWhArKQDVqE3K5Icok1fMIYjKQ5MrRGpI0Fvd5sZwkhfxHeLyhE_Zvw2MJFrk7Xeq7OA0p6fnUN_iTiLIcroPOUh72k6QjnM7UjNDkMwBd30L6kk2zR7jmjgA_0tQWNtXAr_BiJL1rPHbUxw"/>
                    </div>
                    <div class="aspect-square bg-slate-100 dark:bg-slate-800 rounded-2xl flex items-center justify-center overflow-hidden mt-8">
                        <img alt="Software development setup" class="w-full h-full object-cover opacity-80 hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDDgPbs-C7tdFYJEQGyajye-KQtw9fyewb6m-ycyH6URmCtVSczU1H0Merefh2HepriwdY-dKaWFRXy9SIi88F1FbvSMIoIgdiLh5hYgFyYDRua-HPoZNj2JZ0pxxfv2N6fRD6kjGaSJYDJ-wWt5-ZVuCiOoi1ucY9H47fFiYvVVwbQs0HnwHsQd50Na9Zen_NMHLOyu1wgTVpLoKT4grDNoCQLF3IXOYuRfn9U8Qd2Hqglu9t4pRZRdifIWLaLJPB-GjnTF53vpw"/>
                    </div>
                </div>
            </div>
        </section>

        <!-- specialized services grid -->
        <section class="animate-slide-up stagger-3">
            <div class="text-center mb-12">
                <h2 class="font-display text-3xl font-bold text-slate-900 dark:text-white mb-4">Our Specialized Services</h2>
                <div class="w-20 h-1.5 bg-accent mx-auto rounded-full"></div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="service-card group">
                    <div class="w-12 h-12 bg-blue-50 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                        <span class="material-symbols-outlined text-blue-600 dark:text-blue-400">language</span>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Web Development</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Aplikasi web full-stack berbasis framework modern untuk performa tinggi dan interaktivitas optimal.
                    </p>
                </div>
                <div class="service-card group">
                    <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                        <span class="material-symbols-outlined text-indigo-600 dark:text-indigo-400">business</span>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Enterprise Web Systems</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Sistem enterprise andal untuk menangani proses bisnis kompleks dan volume data yang besar.
                    </p>
                </div>
                <div class="service-card group">
                    <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                        <span class="material-symbols-outlined text-purple-600 dark:text-purple-400">api</span>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">API Development</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Pengembangan API RESTful dan GraphQL yang aman serta skalabel untuk integrasi lintas sistem yang mulus.
                    </p>
                </div>
                <div class="service-card group">
                    <div class="w-12 h-12 bg-sky-50 dark:bg-sky-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                        <span class="material-symbols-outlined text-sky-600 dark:text-sky-400">cloud</span>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Cloud-based App Dev</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Aplikasi cloud-native yang memanfaatkan infrastruktur AWS, Azure, atau Google Cloud secara optimal.
                    </p>
                </div>
                <div class="service-card group">
                    <div class="w-12 h-12 bg-cyan-50 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                        <span class="material-symbols-outlined text-cyan-600 dark:text-cyan-400">layers</span>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">SaaS Platform Dev</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Solusi SaaS multi-tenant dengan manajemen langganan yang terstruktur dan mudah dikembangkan.
                    </p>
                </div>
                <div class="service-card group">
                    <div class="w-12 h-12 bg-rose-50 dark:bg-rose-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                        <span class="material-symbols-outlined text-rose-600 dark:text-rose-400">bar_chart</span>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Data Visualization</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Transformasi data kompleks menjadi insight yang dapat ditindaklanjuti melalui dashboard interaktif.
                    </p>
                </div>
                <div class="service-card group">
                    <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                        <span class="material-symbols-outlined text-emerald-600 dark:text-emerald-400">dns</span>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Server Hosting</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Layanan hosting terkelola dengan ketersediaan tinggi dan performa sistem yang dioptimalkan.
                    </p>
                </div>
                <div class="service-card group">
                    <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                        <span class="material-symbols-outlined text-amber-600 dark:text-amber-400">all_inclusive</span>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">DevOps</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Implementasi pipeline CI/CD dan infrastructure as code untuk mempercepat siklus rilis aplikasi.
                    </p>
                </div>
                <div class="service-card group">
                    <div class="w-12 h-12 bg-teal-50 dark:bg-teal-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                        <span class="material-symbols-outlined text-teal-600 dark:text-teal-400">database</span>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Database Design</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Perancangan basis data relasional dan NoSQL yang efisien untuk menjaga integritas data.
                    </p>
                </div>
                <div class="service-card group">
                    <div class="w-12 h-12 bg-orange-50 dark:bg-orange-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                        <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">settings_applications</span>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">Custom Business Software</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Pengembangan perangkat lunak kustom yang dirancang khusus untuk menyelesaikan tantangan bisnis spesifik.
                    </p>
                </div>
                <div class="service-card group">
                    <div class="w-12 h-12 bg-pink-50 dark:bg-pink-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                        <span class="material-symbols-outlined text-pink-600 dark:text-pink-400">inventory_2</span>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">ERP/CRM/Inventory</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Sistem manajemen terintegrasi untuk meningkatkan kontrol sumber daya dan relasi pelanggan.
                    </p>
                </div>
                <div class="service-card group">
                    <div class="w-12 h-12 bg-violet-50 dark:bg-violet-900/30 rounded-xl flex items-center justify-center mb-5 icon-box transition-transform">
                        <span class="material-symbols-outlined text-violet-600 dark:text-violet-400">palette</span>
                    </div>
                    <h3 class="font-bold text-xl text-slate-900 dark:text-white mb-3">UI/UX System Engineering</h3>
                    <p class="text-slate-600 dark:text-slate-400 leading-relaxed">
                        Perancangan antarmuka yang intuitif dan pengalaman pengguna yang konsisten pada skala besar.
                    </p>
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
                    <h2 class="font-display text-3xl md:text-4xl font-bold mb-6">Ready to build your next big thing?</h2>
                    <p class="text-slate-300 max-w-2xl mx-auto mb-10 text-lg">
                        Consult with our experts today and discover how InoSakti can transform your business processes with high-end software solutions.
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


