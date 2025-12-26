
<div x-data="{
    mobileMenuOpen: false,
    darkMode: localStorage.getItem('darkMode') === 'true',
    jobOpen: null,

    init() {
        // Global Dark Mode Sync
        if (this.darkMode || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            this.darkMode = true;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });

        document.querySelectorAll('.reveal-on-scroll').forEach((el) => observer.observe(el));
    },

    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    }
}" class="min-h-screen relative overflow-x-hidden font-sans antialiased text-slate-900 dark:text-slate-50">

    <!-- BACKGROUNDS -->
    <div class="fixed inset-0 bg-[#f8fafc] -z-50 dark:hidden">
        <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-blue-50/50 via-purple-50/30 to-white"></div>
    </div>
    <div class="fixed inset-0 bg-[#0B1120] -z-50 hidden dark:block"></div>

    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-40">
        <ul class="circles">
            <li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li>
        </ul>
    </div>

    <livewire:component.navbar />

    <main class="pt-[72px]">
        <!-- HERO SECTION -->
        <section class="relative pt-20 pb-32">
            <div class="relative max-w-7xl mx-auto px-6 flex flex-col items-center text-center z-10">
                <div class="reveal-on-scroll inline-flex items-center rounded-full border border-blue-100 dark:border-blue-900 bg-blue-50/80 dark:bg-blue-900/30 px-4 py-1.5 text-sm font-medium text-blue-700 dark:text-blue-200 mb-8 backdrop-blur-md">
                    <span class="flex h-2 w-2 rounded-full bg-blue-500 mr-2 animate-pulse"></span>
                    {{ __('career.badge') }}
                </div>

                <h1 class="reveal-on-scroll delay-100 text-5xl md:text-7xl font-extrabold tracking-tight mb-8 max-w-5xl">
                    {{ __('career.title_prefix') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-500 to-blue-400">{{ __('career.title_suffix') }}</span>
                </h1>

                <p class="reveal-on-scroll delay-200 mt-4 text-lg md:text-xl text-slate-600 dark:text-slate-300 max-w-2xl mb-12 leading-relaxed">
                    {{ __('career.subtitle') }}
                </p>

                <div class="reveal-on-scroll delay-300 flex flex-col sm:flex-row gap-4 w-full justify-center">
                    <a href="#openings" class="inline-flex items-center justify-center px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-full transition-all hover:scale-105 shadow-lg shadow-blue-500/20">
                        {{ __('career.view_openings') }}
                    </a>
                    <a href="#culture" class="inline-flex items-center justify-center px-8 py-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-full transition-all">
                        {{ __('career.our_culture') }}
                    </a>
                </div>
            </div>
        </section>

        <!-- CULTURE SECTION -->
        <section id="culture" class="py-24 bg-white/40 dark:bg-black/20 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-2 gap-16 items-center">
                    <div class="reveal-on-scroll order-2 md:order-1">
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl rotate-2 hover:rotate-0 transition-transform duration-500">
                            <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1170&q=80" alt="Team" class="w-full">
                            <div class="absolute inset-0 bg-gradient-to-t from-blue-600/20 to-transparent"></div>
                        </div>
                    </div>
                    <div class="reveal-on-scroll order-1 md:order-2">
                        <h2 class="text-3xl md:text-4xl font-bold mb-6 text-slate-900 dark:text-white">{{ __('career.culture_title') }}</h2>
                        <p class="text-lg text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">{{ __('career.culture_desc_1') }}</p>
                        <div class="grid grid-cols-2 gap-6">
                            <div class="flex items-center gap-3">
                                <span class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg text-blue-600 dark:text-blue-400">✓</span>
                                <span class="font-medium">{{ __('career.culture_point_1') }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg text-purple-600 dark:text-purple-400">✓</span>
                                <span class="font-medium">{{ __('career.culture_point_2') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- OPEN POSITIONS SECTION -->
        <section id="openings" class="py-24">
            <div class="max-w-5xl mx-auto px-6">
                <div class="text-center mb-16 reveal-on-scroll">
                    <h2 class="text-3xl md:text-4xl font-bold">{{ __('career.positions_title') }}</h2>
                    <p class="mt-4 text-slate-600 dark:text-slate-400">{{ __('career.positions_subtitle') }}</p>
                </div>

                <div class="space-y-6">
                    <!-- Job Card 1 -->
                    <div class="reveal-on-scroll bg-white dark:bg-slate-800/60 p-8 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-xl transition-all">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                            <div class="flex-grow">
                                <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">UX/UI Designer</h3>
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-xs font-bold uppercase">Full-time</span>
                                    <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full text-xs font-bold uppercase">Remote</span>
                                </div>
                                <p class="text-slate-600 dark:text-slate-300 max-w-2xl">We're looking for a talented designer to create intuitive interfaces for our healthcare systems.</p>
                            </div>
                            <button @click="jobOpen = jobOpen === 'designer' ? null : 'designer'"
                                    class="w-full md:w-auto px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-blue-500/25">
                                <span x-text="jobOpen === 'designer' ? 'Close Details' : 'Apply Now'"></span>
                            </button>
                        </div>

                        <!-- Expandable Details -->
                        <div x-show="jobOpen === 'designer'" x-collapse x-cloak class="mt-8 pt-8 border-t border-slate-200 dark:border-slate-700">
                            <div class="grid md:grid-cols-2 gap-8">
                                <div>
                                    <h4 class="font-bold text-slate-900 dark:text-white mb-4">What we are looking for:</h4>
                                    <ul class="space-y-3">
                                        <li class="flex items-center gap-2 text-slate-600 dark:text-slate-300 text-sm">
                                            <span class="text-blue-500">●</span> 3+ years of experience in Figma/Adobe XD
                                        </li>
                                        <li class="flex items-center gap-2 text-slate-600 dark:text-slate-300 text-sm">
                                            <span class="text-blue-500">●</span> Strong portfolio of dashboard designs
                                        </li>
                                    </ul>
                                </div>
                                <div class="flex flex-col justify-end">
                                    <a href="#" class="inline-flex items-center justify-center gap-2 px-6 py-4 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold rounded-2xl hover:opacity-90 transition-all">
                                        Submit Application via LinkedIn
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Add more job cards here... -->
                </div>

                <div class="mt-12 text-center reveal-on-scroll">
                    <p class="text-slate-600 dark:text-slate-300 mb-6">Don't see a position that matches your skills?</p>
                    <a href="mailto:careers@sanago.com" class="inline-flex items-center gap-2 px-8 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-all">
                        Send us your CV
                    </a>
                </div>
            </div>
        </section>

        <!-- CTA SECTION -->
        <section class="py-24">
            <div class="max-w-5xl mx-auto px-6">
                <div class="relative rounded-[3rem] bg-gradient-to-br from-blue-600 to-indigo-800 overflow-hidden px-6 py-20 text-center shadow-2xl reveal-on-scroll">
                    <h2 class="relative text-3xl font-bold text-white sm:text-5xl mb-6">Ready to Join Our Team?</h2>
                    <p class="relative text-xl text-blue-100 max-w-2xl mx-auto mb-10">Help us transform healthcare management through innovative technology solutions.</p>
                    <div class="relative flex flex-col sm:flex-row justify-center gap-4">
                        <a href="#openings" class="rounded-full bg-white px-10 py-4 text-base font-bold text-blue-600 shadow-xl hover:bg-blue-50 transition-all">
                            View Open Positions
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <livewire:component.footer />

    <style>
        [x-cloak] { display: none !important; }
        .circles { position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; margin: 0; padding: 0; }
        .circles li { position: absolute; display: block; list-style: none; width: 20px; height: 20px; background: linear-gradient(to top right, rgba(59, 130, 246, 0.3), rgba(168, 85, 247, 0.3)); filter: blur(8px); animation: animate 25s linear infinite; bottom: -150px; border-radius: 50%; }
        .dark .circles li { background: linear-gradient(to top right, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0)); filter: blur(10px); }
        .circles li:nth-child(1) { left: 25%; width: 80px; height: 80px; animation-delay: 0s; }
        @keyframes animate { 0% { transform: translateY(0) rotate(0deg) scale(1); opacity: 0.8; } 100% { transform: translateY(-1000px) rotate(720deg) scale(1.5); opacity: 0; } }
        .reveal-on-scroll { opacity: 0; transform: translateY(30px); transition: all 0.8s ease-out; }
        .reveal-on-scroll.animate-in { opacity: 1; transform: translateY(0); }
    </style>
</div>
