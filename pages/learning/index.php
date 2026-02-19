<?php
$pageTitle = 'InoSakti Online Education Platform';
$pageDesc = 'Platform pembelajaran online InoSakti untuk kursus engineering, AI, IoT, web development, networking, dan energi terbarukan.';

$extraHead = <<<'HTML'
<style type="text/tailwindcss">
@layer utilities {
  .course-card {
    @apply transition-all duration-300 hover:-translate-y-1 hover:shadow-xl;
  }
  .sidebar-filter-item {
    @apply flex items-center space-x-3 py-2 cursor-pointer text-slate-600 dark:text-slate-400 hover:text-accent transition-colors;
  }
}
</style>
<style>
  .no-scrollbar::-webkit-scrollbar { display: none; }
  .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>
HTML;

include __DIR__.'/../../inc/header.php';
?>

<main class="max-w-7xl mx-auto px-6 py-28">
    <section class="mb-10">
        <h2 class="font-display text-xl font-bold text-slate-900 dark:text-white mb-4">Resume Learning</h2>
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
            <div class="flex items-center gap-6 w-full md:w-auto">
                <div class="w-24 h-16 rounded-lg bg-slate-100 overflow-hidden flex-shrink-0">
                    <img alt="Current Course" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCmx_9yoAVaoh0ejp3li3JjmawgE21tiFaw6SrDy6hXrOtl-rFWjc3t27wVoVZ4AovX5RIRpks6cbcYCOiPCyqUgXQ8zJMjAqTMSN7o0mPMsdhcI-KUGkC6WuzFnleWhArKQDVqE3K5Icok1fMIYjKQ5MrRGpI0Fvd5sZwkhfxHeLyhE_Zvw2MJFrk7Xeq7OA0p6fnUN_iTiLIcroPOUh72k6QjnM7UjNDkMwBd30L6kk2zR7jmjgA_0tQWNtXAr_BiJL1rPHbUxw"/>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 dark:text-white">Mastering IoT with ESP32</h3>
                    <p class="text-sm text-slate-500">Lesson 12: Interfacing with MQTT Broker</p>
                    <div class="w-48 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full mt-2">
                        <div class="w-3/4 h-full bg-accent rounded-full"></div>
                    </div>
                </div>
            </div>
            <button class="w-full md:w-auto px-6 py-3 bg-accent text-white font-bold rounded-xl hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined">play_circle</span>
                Continue Lesson
            </button>
        </div>
    </section>

    <section class="mb-12">
        <div class="flex flex-col md:flex-row gap-6 items-center justify-between">
            <div class="relative w-full md:w-1/2">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
                <input class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl focus:ring-2 focus:ring-accent focus:border-transparent outline-none transition-all" placeholder="Search for engineering courses, skills, or instructors..." type="text"/>
            </div>
            <div class="flex overflow-x-auto pb-2 w-full md:w-auto gap-3 no-scrollbar">
                <button class="px-5 py-2.5 bg-accent text-white rounded-xl text-sm font-semibold whitespace-nowrap">All Courses</button>
                <button class="px-5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-accent text-slate-600 dark:text-slate-400 rounded-xl text-sm font-semibold transition-all whitespace-nowrap">Artificial Intelligence</button>
                <button class="px-5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-accent text-slate-600 dark:text-slate-400 rounded-xl text-sm font-semibold transition-all whitespace-nowrap">IoT &amp; Embedded</button>
                <button class="px-5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-accent text-slate-600 dark:text-slate-400 rounded-xl text-sm font-semibold transition-all whitespace-nowrap">Web Dev</button>
                <button class="px-5 py-2.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-accent text-slate-600 dark:text-slate-400 rounded-xl text-sm font-semibold transition-all whitespace-nowrap">Network Eng</button>
            </div>
        </div>
    </section>

    <div class="flex flex-col lg:flex-row gap-8">
        <aside class="w-full lg:w-64 space-y-8 flex-shrink-0">
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white mb-4 uppercase text-xs tracking-widest">Skill Level</h3>
                <div class="space-y-1">
                    <label class="sidebar-filter-item">
                        <input class="rounded text-accent focus:ring-accent border-slate-300 dark:border-slate-700 bg-transparent" type="checkbox"/>
                        <span>Beginner</span>
                    </label>
                    <label class="sidebar-filter-item">
                        <input checked="" class="rounded text-accent focus:ring-accent border-slate-300 dark:border-slate-700 bg-transparent" type="checkbox"/>
                        <span>Intermediate</span>
                    </label>
                    <label class="sidebar-filter-item">
                        <input class="rounded text-accent focus:ring-accent border-slate-300 dark:border-slate-700 bg-transparent" type="checkbox"/>
                        <span>Advanced</span>
                    </label>
                </div>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 dark:text-white mb-4 uppercase text-xs tracking-widest">Duration</h3>
                <div class="space-y-1">
                    <label class="sidebar-filter-item">
                        <input class="rounded text-accent focus:ring-accent border-slate-300 dark:border-slate-700 bg-transparent" type="checkbox"/>
                        <span>0 - 2 Hours</span>
                    </label>
                    <label class="sidebar-filter-item">
                        <input class="rounded text-accent focus:ring-accent border-slate-300 dark:border-slate-700 bg-transparent" type="checkbox"/>
                        <span>3 - 6 Hours</span>
                    </label>
                    <label class="sidebar-filter-item">
                        <input class="rounded text-accent focus:ring-accent border-slate-300 dark:border-slate-700 bg-transparent" type="checkbox"/>
                        <span>6+ Hours</span>
                    </label>
                </div>
            </div>
            <div class="p-6 bg-blue-50 dark:bg-blue-900/20 rounded-2xl border border-blue-100 dark:border-blue-800/30">
                <h4 class="font-bold text-blue-900 dark:text-blue-300 mb-2">Corporate Training?</h4>
                <p class="text-sm text-blue-800/70 dark:text-blue-400/70 mb-4">Custom curriculum for your engineering team.</p>
                <a class="text-sm font-bold text-accent hover:underline" href="<?php echo $basePath; ?>/#konsultasi">Contact Sales →</a>
            </div>
        </aside>

        <div class="flex-1">
            <div class="flex items-center justify-between mb-8">
                <h2 class="font-display text-2xl font-bold text-slate-900 dark:text-white">Online Courses</h2>
                <span class="text-sm font-medium text-slate-500">Showing 48 results</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <div class="course-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
                    <div class="relative aspect-video overflow-hidden">
                        <img alt="IoT Course" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCmx_9yoAVaoh0ejp3li3JjmawgE21tiFaw6SrDy6hXrOtl-rFWjc3t27wVoVZ4AovX5RIRpks6cbcYCOiPCyqUgXQ8zJMjAqTMSN7o0mPMsdhcI-KUGkC6WuzFnleWhArKQDVqE3K5Icok1fMIYjKQ5MrRGpI0Fvd5sZwkhfxHeLyhE_Zvw2MJFrk7Xeq7OA0p6fnUN_iTiLIcroPOUh72k6QjnM7UjNDkMwBd30L6kk2zR7jmjgA_0tQWNtXAr_BiJL1rPHbUxw"/>
                        <div class="absolute top-3 right-3 px-2 py-1 bg-white/90 dark:bg-slate-900/90 backdrop-blur rounded-lg text-xs font-bold text-accent">IoT &amp; Embedded</div>
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-bold text-slate-900 dark:text-white mb-2">Mastering IoT with ESP32 &amp; Cloud Integration</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Dr. Aris Santoso</p>
                        <div class="mt-auto flex items-center justify-between">
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-amber-400 text-sm fill-current">star</span>
                                <span class="text-sm font-bold">4.9</span>
                                <span class="text-xs text-slate-400">(1.2k)</span>
                            </div>
                            <div class="text-lg font-bold text-slate-900 dark:text-white">Rp 450.000</div>
                        </div>
                    </div>
                </div>

                <div class="course-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
                    <div class="relative aspect-video overflow-hidden">
                        <img alt="AI Course" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDDgPbs-C7tdFYJEQGyajye-KQtw9fyewb6m-ycyH6URmCtVSczU1H0Merefh2HepriwdY-dKaWFRXy9SIi88F1FbvSMIoIgdiLh5hYgFyYDRua-HPoZNj2JZ0pxxfv2N6fRD6kjGaSJYDJ-wWt5-ZVuCiOoi1ucY9H47fFiYvVVwbQs0HnwHsQd50Na9Zen_NMHLOyu1wgTVpLoKT4grDNoCQLF3IXOYuRfn9U8Qd2Hqglu9t4pRZRdifIWLaLJPB-GjnTF53vpw"/>
                        <div class="absolute top-3 right-3 px-2 py-1 bg-white/90 dark:bg-slate-900/90 backdrop-blur rounded-lg text-xs font-bold text-accent">AI &amp; ML</div>
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-bold text-slate-900 dark:text-white mb-2">Practical Machine Learning for Engineering Prototyping</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Sarah Wijaya, M.Eng</p>
                        <div class="mt-auto flex items-center justify-between">
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-amber-400 text-sm fill-current">star</span>
                                <span class="text-sm font-bold">4.8</span>
                                <span class="text-xs text-slate-400">(850)</span>
                            </div>
                            <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400 uppercase text-sm">Free</div>
                        </div>
                    </div>
                </div>

                <div class="course-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
                    <div class="relative aspect-video overflow-hidden">
                        <img alt="Embedded Course" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCmx_9yoAVaoh0ejp3li3JjmawgE21tiFaw6SrDy6hXrOtl-rFWjc3t27wVoVZ4AovX5RIRpks6cbcYCOiPCyqUgXQ8zJMjAqTMSN7o0mPMsdhcI-KUGkC6WuzFnleWhArKQDVqE3K5Icok1fMIYjKQ5MrRGpI0Fvd5sZwkhfxHeLyhE_Zvw2MJFrk7Xeq7OA0p6fnUN_iTiLIcroPOUh72k6QjnM7UjNDkMwBd30L6kk2zR7jmjgA_0tQWNtXAr_BiJL1rPHbUxw"/>
                        <div class="absolute top-3 right-3 px-2 py-1 bg-white/90 dark:bg-slate-900/90 backdrop-blur rounded-lg text-xs font-bold text-accent">Embedded</div>
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-bold text-slate-900 dark:text-white mb-2">Advanced PCB Design &amp; RF Optimization</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Ir. Bambang Hermawan</p>
                        <div class="mt-auto flex items-center justify-between">
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-amber-400 text-sm fill-current">star</span>
                                <span class="text-sm font-bold">5.0</span>
                                <span class="text-xs text-slate-400">(420)</span>
                            </div>
                            <div class="text-lg font-bold text-slate-900 dark:text-white">Rp 750.000</div>
                        </div>
                    </div>
                </div>

                <div class="course-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
                    <div class="relative aspect-video overflow-hidden">
                        <img alt="Web Course" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDDgPbs-C7tdFYJEQGyajye-KQtw9fyewb6m-ycyH6URmCtVSczU1H0Merefh2HepriwdY-dKaWFRXy9SIi88F1FbvSMIoIgdiLh5hYgFyYDRua-HPoZNj2JZ0pxxfv2N6fRD6kjGaSJYDJ-wWt5-ZVuCiOoi1ucY9H47fFiYvVVwbQs0HnwHsQd50Na9Zen_NMHLOyu1wgTVpLoKT4grDNoCQLF3IXOYuRfn9U8Qd2Hqglu9t4pRZRdifIWLaLJPB-GjnTF53vpw"/>
                        <div class="absolute top-3 right-3 px-2 py-1 bg-white/90 dark:bg-slate-900/90 backdrop-blur rounded-lg text-xs font-bold text-accent">Web Dev</div>
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-bold text-slate-900 dark:text-white mb-2">Full-stack Dashboard Development for Industrial Sensors</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Ahmad Fauzi</p>
                        <div class="mt-auto flex items-center justify-between">
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-amber-400 text-sm fill-current">star</span>
                                <span class="text-sm font-bold">4.7</span>
                                <span class="text-xs text-slate-400">(1.5k)</span>
                            </div>
                            <div class="text-lg font-bold text-slate-900 dark:text-white">Rp 320.000</div>
                        </div>
                    </div>
                </div>

                <div class="course-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
                    <div class="relative aspect-video overflow-hidden">
                        <img alt="Networking Course" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCmx_9yoAVaoh0ejp3li3JjmawgE21tiFaw6SrDy6hXrOtl-rFWjc3t27wVoVZ4AovX5RIRpks6cbcYCOiPCyqUgXQ8zJMjAqTMSN7o0mPMsdhcI-KUGkC6WuzFnleWhArKQDVqE3K5Icok1fMIYjKQ5MrRGpI0Fvd5sZwkhfxHeLyhE_Zvw2MJFrk7Xeq7OA0p6fnUN_iTiLIcroPOUh72k6QjnM7UjNDkMwBd30L6kk2zR7jmjgA_0tQWNtXAr_BiJL1rPHbUxw"/>
                        <div class="absolute top-3 right-3 px-2 py-1 bg-white/90 dark:bg-slate-900/90 backdrop-blur rounded-lg text-xs font-bold text-accent">Networking</div>
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-bold text-slate-900 dark:text-white mb-2">Network Security Fundamentals for Engineering Hubs</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Prof. Dian Sastro</p>
                        <div class="mt-auto flex items-center justify-between">
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-amber-400 text-sm fill-current">star</span>
                                <span class="text-sm font-bold">4.6</span>
                                <span class="text-xs text-slate-400">(680)</span>
                            </div>
                            <div class="text-lg font-bold text-slate-900 dark:text-white">Rp 290.000</div>
                        </div>
                    </div>
                </div>

                <div class="course-card bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
                    <div class="relative aspect-video overflow-hidden">
                        <img alt="Energy Course" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDDgPbs-C7tdFYJEQGyajye-KQtw9fyewb6m-ycyH6URmCtVSczU1H0Merefh2HepriwdY-dKaWFRXy9SIi88F1FbvSMIoIgdiLh5hYgFyYDRua-HPoZNj2JZ0pxxfv2N6fRD6kjGaSJYDJ-wWt5-ZVuCiOoi1ucY9H47fFiYvVVwbQs0HnwHsQd50Na9Zen_NMHLOyu1wgTVpLoKT4grDNoCQLF3IXOYuRfn9U8Qd2Hqglu9t4pRZRdifIWLaLJPB-GjnTF53vpw"/>
                        <div class="absolute top-3 right-3 px-2 py-1 bg-white/90 dark:bg-slate-900/90 backdrop-blur rounded-lg text-xs font-bold text-accent">Renewable</div>
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-bold text-slate-900 dark:text-white mb-2">Solar PV System Design &amp; Energy Auditing</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Budi Rahardjo, PhD</p>
                        <div class="mt-auto flex items-center justify-between">
                            <div class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-amber-400 text-sm fill-current">star</span>
                                <span class="text-sm font-bold">4.9</span>
                                <span class="text-xs text-slate-400">(920)</span>
                            </div>
                            <div class="text-lg font-bold text-slate-900 dark:text-white">Rp 550.000</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 flex justify-center">
                <button class="px-8 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Load More Courses
                </button>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__.'/../../inc/footer.php'; ?>
