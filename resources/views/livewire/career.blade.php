
<div  x-data="{
    mobileMenuOpen: false,
    darkMode: localStorage.getItem('darkMode') === 'true',
    jobOpen: null,
    benefitsOpen: null,

    init() {
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
        this.darkMode ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark');
    }
}" class="min-h-screen relative overflow-x-hidden font-sans antialiased selection:bg-blue-500 selection:text-white text-slate-900 dark:text-slate-50">

    <!-- BACKGROUNDS & BUBBLE ANIMATION -->
    <div class="fixed inset-0 bg-[#f8fafc] -z-50 transition-colors duration-500 dark:hidden">
        <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-blue-50/50 via-purple-50/30 to-white"></div>
    </div>
    <div class="fixed inset-0 bg-[#0B1120] -z-50 transition-colors duration-500 hidden dark:block"></div>
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-40">
        <ul class="circles">
            <li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li>
        </ul>
    </div>
    <div class="fixed inset-0 opacity-[0.04] dark:opacity-[0.07] pointer-events-none -z-30 mix-blend-overlay"
         style="background-image: url('https://grainy-gradients.vercel.app/noise.svg')"></div>

    <!-- NAVBAR -->
    <livewire:component.navbar />

    <main class="pt-[72px]">
        <!-- HERO SECTION -->
        <section class="relative pt-20 pb-32 overflow-hidden">
            <div class="relative max-w-7xl mx-auto px-6 flex flex-col items-center text-center z-10">
                <div class="reveal-on-scroll inline-flex items-center rounded-full border border-blue-100 dark:border-blue-900 bg-blue-50/80 dark:bg-blue-900/30 px-4 py-1.5 text-sm font-medium text-blue-700 dark:text-blue-200 mb-8 backdrop-blur-md shadow-sm">
                    <span class="flex h-2 w-2 rounded-full bg-blue-500 mr-2 animate-pulse"></span>
                    {{ __('career.badge') }}
                </div>

                <h1 class="reveal-on-scroll delay-100 text-5xl md:text-7xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-8 max-w-5xl">
                    {{ __('career.title_prefix') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-500 to-blue-400">{{ __('career.title_suffix') }}</span>
                </h1>

                <p class="reveal-on-scroll delay-200 mt-4 text-lg md:text-xl text-slate-600 dark:text-slate-300 max-w-2xl mb-12 leading-relaxed">
                    {{ __('career.subtitle') }}
                </p>

                <div class="reveal-on-scroll delay-300 flex flex-col sm:flex-row gap-4 w-full justify-center">
                    <a href="#openings" class="inline-flex items-center justify-center px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-full transition-all duration-200 hover:scale-105 shadow-lg hover:shadow-blue-500/25">
                        {{ __('career.view_openings') }}
                        <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </a>
                    <a href="#culture" class="inline-flex items-center justify-center px-8 py-4 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold rounded-full transition-all duration-200 hover:scale-105">
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
                            <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" alt="Team collaboration" class="w-full h-auto">
                            <div class="absolute inset-0 bg-gradient-to-t from-blue-600/20 to-transparent"></div>
                        </div>
                    </div>
                    <div class="reveal-on-scroll order-1 md:order-2">
                        <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-6">{{ __('career.culture_title') }}</h2>
                        <p class="text-lg text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
                            {{ __('career.culture_desc_1') }}
                        </p>
                        <p class="text-lg text-slate-600 dark:text-slate-300 mb-8 leading-relaxed">
                            {{ __('career.culture_desc_2') }}
                        </p>
                        <div class="grid grid-cols-2 gap-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <span class="font-medium text-slate-900 dark:text-white">{{ __('career.culture_point_1') }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </div>
                                <span class="font-medium text-slate-900 dark:text-white">{{ __('career.culture_point_2') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- BENEFITS SECTION -->
        <section class="py-24">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16 reveal-on-scroll">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white">{{ __('career.benefits_title') }}</h2>
                    <p class="mt-4 text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">{{ __('career.benefits_subtitle') }}</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="reveal-on-scroll delay-100 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-lg hover:shadow-xl transition-shadow">
                        <div class="w-14 h-14 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center text-3xl mb-6">
                            🏥
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">{{ __('career.benefit_health_title') }}</h3>
                        <p class="text-slate-600 dark:text-slate-300">{{ __('career.benefit_health_desc') }}</p>
                    </div>
                    <div class="reveal-on-scroll delay-200 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-lg hover:shadow-xl transition-shadow">
                        <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-3xl mb-6">
                            📚
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">{{ __('career.benefit_dev_title') }}</h3>
                        <p class="text-slate-600 dark:text-slate-300">{{ __('career.benefit_dev_desc') }}</p>
                    </div>
                    <div class="reveal-on-scroll delay-300 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-lg hover:shadow-xl transition-shadow">
                        <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center text-3xl mb-6">
                            🏡
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">{{ __('career.benefit_flex_title') }}</h3>
                        <p class="text-slate-600 dark:text-slate-300">{{ __('career.benefit_flex_desc') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- OPEN POSITIONS SECTION -->
        <section id="openings" class="py-24 bg-white/40 dark:bg-black/20 backdrop-blur-sm">
            <div class="max-w-5xl mx-auto px-6">
                <div class="text-center mb-16 reveal-on-scroll">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white">{{ __('career.positions_title') }}</h2>
                    <p class="mt-4 text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">{{ __('career.positions_subtitle') }}</p>
                </div>

                <div class="space-y-6">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">UX/UI Designer</h3>
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-sm">Full-time</span>
                                    <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full text-sm">Remote</span>
                                    <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-full text-sm">Design</span>
                                </div>
                                <p class="text-slate-600 dark:text-slate-300">We're looking for a talented UX/UI designer to create intuitive, user-friendly interfaces for our healthcare management system. Experience with complex data visualization is a plus.</p>
                            </div>
                            <button @click="jobOpen = jobOpen === 'design' ? null : 'design'" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors">
                                Apply Now
                            </button>
                        </div>
                        <div x-show="jobOpen === 'design'" x-collapse class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <h4 class="font-bold text-slate-900 dark:text-white mb-3">Requirements:</h4>
                            <ul class="space-y-2 mb-6 text-slate-600 dark:text-slate-300">
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    3+ years of UX/UI design experience
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Portfolio demonstrating experience with complex systems
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Experience with data visualization and dashboard design
                                </li>
                                <li class="flex items-start gap-2">
                                    <svg class="w-5 h-5 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Understanding of accessibility standards and best practices
                                </li>
                            </ul>
                            <a href="#" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                Apply on LinkedIn
                            </a>
                        </div>
                    </div>
                </div>

                <div class="mt-12 text-center reveal-on-scroll">
                    <p class="text-slate-600 dark:text-slate-300 mb-6">Don't see a position that matches your skills?</p>
                    <a href="#" class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium rounded-xl transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Send us your resume
                    </a>
                </div>
            </div>
        </section>

        <!-- CTA SECTION -->
        <section class="py-24">
            <div class="max-w-5xl mx-auto px-6">
                <div class="relative rounded-[2.5rem] bg-gradient-to-br from-blue-600 to-indigo-700 overflow-hidden px-6 py-16 sm:px-12 sm:py-20 text-center shadow-2xl shadow-blue-900/40 reveal-on-scroll">
                    <!-- Decorative elements inside CTA -->
                    <div class="absolute top-0 left-0 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
                    <div class="absolute bottom-0 right-0 translate-x-1/2 translate-y-1/2 w-96 h-96 bg-purple-500/30 rounded-full blur-3xl"></div>

                    <h2 class="relative text-3xl font-bold tracking-tight text-white sm:text-5xl mb-6">Ready to Join Our Team?</h2>
                    <p class="relative text-xl text-blue-100 max-w-2xl mx-auto mb-10">Help us transform healthcare management through innovative technology solutions.</p>

                    <div class="relative flex flex-col sm:flex-row justify-center gap-4">
                        <a href="#openings" class="rounded-full bg-white px-10 py-4 text-base font-bold text-blue-600 shadow-xl hover:bg-blue-50 transition-all duration-200 hover:scale-105">
                            View Open Positions
                        </a>
                        <a href="#" class="rounded-full border border-white/30 bg-white/10 px-10 py-4 text-base font-bold text-white backdrop-blur-sm hover:bg-white/20 transition-all duration-200 hover:scale-105">
                            Learn More
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
   <livewire:component.footer />

    <style>
        [x-cloak] { display: none !important; }
        @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-100%); } }
        .animate-marquee { animation: marquee 35s linear infinite; }
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }
        .animate-blink { animation: blink 1s step-end infinite; }

        /* BLURRY GRADIENT BUBBLES CSS */
        .circles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }
        .circles li {
            position: absolute;
            display: block;
            list-style: none;
            width: 20px;
            height: 20px;
            /* Light Mode Gradient & Blur */
            background: linear-gradient(to top right, rgba(59, 130, 246, 0.3), rgba(168, 85, 247, 0.3));
            filter: blur(8px);
            animation: animate 25s linear infinite;
            bottom: -150px;
            border-radius: 50%;
        }
        /* Dark Mode Gradient & Blur */
        .dark .circles li {
            background: linear-gradient(to top right, rgba(255, 255, 255, 0.08), rgba(255, 255, 255, 0));
            filter: blur(10px);
        }

        .circles li:nth-child(1) { left: 25%; width: 80px; height: 80px; animation-delay: 0s; }
        .circles li:nth-child(2) { left: 10%; width: 20px; height: 20px; animation-delay: 2s; animation-duration: 12s; }
        .circles li:nth-child(3) { left: 70%; width: 20px; height: 20px; animation-delay: 4s; }
        .circles li:nth-child(4) { left: 40%; width: 60px; height: 60px; animation-delay: 0s; animation-duration: 18s; }
        .circles li:nth-child(5) { left: 65%; width: 20px; height: 20px; animation-delay: 0s; }
        .circles li:nth-child(6) { left: 75%; width: 110px; height: 110px; animation-delay: 3s; }
        .circles li:nth-child(7) { left: 35%; width: 150px; height: 150px; animation-delay: 7s; }
        .circles li:nth-child(8) { left: 50%; width: 25px; height: 25px; animation-delay: 15s; animation-duration: 45s; }
        .circles li:nth-child(9) { left: 20%; width: 15px; height: 15px; animation-delay: 2s; animation-duration: 35s; }
        .circles li:nth-child(10) { left: 85%; width: 150px; height: 150px; animation-delay: 0s; animation-duration: 11s; }

        @keyframes animate {
            0% {
                transform: translateY(0) rotate(0deg) scale(1);
                opacity: 0.8;
            }
            100% {
                transform: translateY(-1000px) rotate(720deg) scale(1.5);
                opacity: 0;
            }
        }
        .reveal-on-scroll { opacity: 0; transform: translateY(30px); transition: all 0.8s ease-out; }
        .reveal-on-scroll.animate-in { opacity: 1; transform: translateY(0); }
    </style>
</div>
