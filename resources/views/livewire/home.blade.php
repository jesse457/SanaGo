<div x-data="{
    activeTab: 0,
    openFaq: null,
    demoOpen: false,
    darkMode: localStorage.getItem('darkMode') === 'true',

    // Data from PHP
    tabs: {{ json_encode(__('home.tabs_data')) }},
    deptCards: {{ json_encode(__('home.solutions_data')) }},
    testimonials: {{ json_encode(__('home.testimonials_data')) }},
    faqs: {{ json_encode(__('home.faqs_data')) }},

    autoRotate: null,
    progress: 0,

    // Typewriter Logic
    typewriter: {
        text: '',
        words: ['Hospital Operations', 'Patient Care', 'Clinical Workflows', 'Lab Diagnostics'],
        wordIndex: 0,
        charIndex: 0,
        isDeleting: false,
        speed: 100,
        init() { this.type(); },
        type() {
            const currentWord = this.words[this.wordIndex];
            if (this.isDeleting) {
                this.text = currentWord.substring(0, this.charIndex - 1);
                this.charIndex--;
                this.speed = 50;
            } else {
                this.text = currentWord.substring(0, this.charIndex + 1);
                this.charIndex++;
                this.speed = 100;
            }
            if (!this.isDeleting && this.charIndex === currentWord.length) {
                this.isDeleting = true;
                this.speed = 2000;
            } else if (this.isDeleting && this.charIndex === 0) {
                this.isDeleting = false;
                this.wordIndex = (this.wordIndex + 1) % this.words.length;
                this.speed = 500;
            }
            setTimeout(() => this.type(), this.speed);
        }
    },

    init() {
        // Listen for theme changes from Layout/Navbar
        window.addEventListener('theme-changed', (e) => {
            this.darkMode = e.detail;
        });

        this.startAutoRotate();
        this.typewriter.init();

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        document.querySelectorAll('.reveal-on-scroll').forEach((el) => observer.observe(el));
    },

    startAutoRotate() {
        clearInterval(this.autoRotate);
        this.progress = 0;
        this.autoRotate = setInterval(() => {
            this.progress += 1;
            if (this.progress >= 100) {
                this.progress = 0;
                this.activeTab = (this.activeTab + 1) % this.tabs.length;
            }
        }, 60);
    },
    setTab(index) {
        this.activeTab = index;
        this.progress = 0;
        this.startAutoRotate();
    },
    handleMouseMove(e) {
        if (window.innerWidth < 768) return;
        const card = e.currentTarget;
        const rect = card.getBoundingClientRect();
        card.style.setProperty('--mouse-x', `${e.clientX - rect.left}px`);
        card.style.setProperty('--mouse-y', `${e.clientY - rect.top}px`);
    }
}" class="min-h-screen relative overflow-x-hidden font-sans antialiased selection:bg-blue-500 selection:text-white text-slate-900 dark:text-slate-50">

    <!-- BACKGROUNDS -->
    <div class="fixed inset-0 bg-[#f8fafc] -z-50 transition-colors duration-500 dark:hidden">
        <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-blue-50/50 via-purple-50/30 to-white"></div>
    </div>
    <div class="fixed inset-0 bg-[#0B1120] -z-50 transition-colors duration-500 hidden dark:block"></div>

    <!-- BUBBLES -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-40">
        <ul class="circles">
            <li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li>
        </ul>
    </div>

    <!-- Noise Overlay -->
    <div class="fixed inset-0 opacity-[0.04] dark:opacity-[0.07] pointer-events-none -z-30 mix-blend-overlay"
        style="background-image: url('https://grainy-gradients.vercel.app/noise.svg')"></div>

    <!-- HERO SECTION -->
    <section class="relative pt-12 pb-20 md:pt-20 md:pb-32 overflow-hidden">
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 flex flex-col items-center text-center z-10">

            <div class="reveal-on-scroll inline-flex items-center rounded-full border border-blue-100 dark:border-blue-900 bg-blue-50/80 dark:bg-blue-900/30 px-4 py-1.5 text-xs md:text-sm font-medium text-blue-700 dark:text-blue-200 mb-6 md:mb-8 backdrop-blur-md shadow-sm">
                <span class="flex h-2 w-2 rounded-full bg-blue-500 mr-2 animate-pulse"></span>
                {{ __('home.hero_explore') }}
            </div>

            <h1 class="reveal-on-scroll delay-100 text-3xl sm:text-5xl md:text-7xl lg:text-8xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-6 md:mb-8 max-w-5xl leading-tight min-h-[160px] md:min-h-0">
                {{ __('home.hero_title_p1') }}
                <br class="hidden md:block" />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-500 to-blue-400 animate-gradient pb-2 inline-block">
                    <span x-text="typewriter.text"></span><span class="animate-blink text-blue-600 ml-1">|</span>
                </span>
            </h1>

            <p class="reveal-on-scroll delay-200 text-base md:text-xl text-slate-600 dark:text-slate-300 max-w-2xl mb-10 md:mb-12 leading-relaxed px-2">
                {{ __('home.hero_subtitle') }}
            </p>

            <div class="reveal-on-scroll delay-300 flex flex-col sm:flex-row gap-4 items-center w-full justify-center px-4">
                <a href="#pricing" class="group relative inline-flex h-12 md:h-14 items-center justify-center overflow-hidden rounded-full bg-blue-600 px-8 md:px-10 font-bold text-white duration-300 hover:bg-blue-700 shadow-lg w-full sm:w-auto">
                    <span class="mr-2">{{ __('home.hero_cta') }}</span>
                    <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
                <a href="#features" class="inline-flex h-12 md:h-14 items-center justify-center rounded-full px-8 md:px-10 font-bold text-slate-700 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200 border border-slate-200 dark:border-slate-700 w-full sm:w-auto">
                    Learn More
                </a>
            </div>

            <!-- 3D Tilt Card (Dashboard Preview) -->
            <div class="reveal-on-scroll delay-500 mt-12 md:mt-20 relative w-full max-w-5xl mx-auto px-4 sm:px-6 lg:perspective-2000"
                x-data="{
                    rotateX: 0, rotateY: 0,
                    isDark: document.documentElement.classList.contains('dark'),
                    canTilt: window.matchMedia('(hover: hover) and (pointer: fine)').matches,
                    handleMouseMove(e) {
                        if (!this.canTilt) return;
                        const rect = this.$el.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;
                        const centerX = rect.width / 2;
                        const centerY = rect.height / 2;
                        this.rotateX = -((y - centerY) / 50);
                        this.rotateY = ((x - centerX) / 50);
                    },
                    reset() { this.rotateX = 0; this.rotateY = 0; }
                }"
                @theme-changed.window="isDark = $event.detail"
                @mousemove="handleMouseMove($event)" @mouseleave="reset()">

                <div class="tilt-card relative w-full rounded-xl md:rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-xl md:shadow-[0_20px_50px_rgba(0,0,0,0.1)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] transition-transform ease-out duration-300 will-change-transform overflow-hidden"
                    :style="canTilt ? `transform: rotateX(${rotateX}deg) rotateY(${rotateY}deg)` : ''">

                    <!-- Browser UI Header -->
                    <div class="h-8 md:h-11 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 flex items-center px-3 md:px-4 gap-2 md:gap-4">
                        <div class="flex gap-1.5 shrink-0">
                            <div class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-red-400/80"></div>
                            <div class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-amber-400/80"></div>
                            <div class="w-2 h-2 md:w-3 md:h-3 rounded-full bg-green-400/80"></div>
                        </div>
                        <div class="flex-1 flex justify-center">
                            <div class="w-full max-w-[140px] xs:max-w-[200px] sm:max-w-md h-5 md:h-7 bg-white dark:bg-slate-900/40 rounded md:rounded-md border border-slate-200 dark:border-slate-700 flex items-center px-3">
                                <div class="text-[8px] md:text-[10px] text-slate-400 dark:text-slate-500 truncate mx-auto font-mono">sanago.site/admin/dashboard</div>
                            </div>
                        </div>
                        <div class="w-8 md:w-12 shrink-0 hidden sm:block"></div>
                    </div>

                    <!-- Dashboard Image -->
                    <div class="relative w-full bg-slate-50 dark:bg-[#0B1120] aspect-[16/10] sm:aspect-auto">
                        <img x-show="!isDark" class="w-full h-auto block antialiased shadow-inner" src="{{ asset('images/dashboard-light.webp') }}" alt="Dashboard Light">
                        <img x-show="isDark" x-cloak class="w-full h-auto block antialiased shadow-inner" src="{{ asset('images/dashboard-dark.webp') }}" alt="Dashboard Dark">
                        <div class="absolute inset-0 pointer-events-none rounded-b-xl md:rounded-b-2xl shadow-[inset_0_0_40px_rgba(0,0,0,0.02)] dark:shadow-[inset_0_0_40px_rgba(255,255,255,0.01)]"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FEATURES SECTION -->
    <section id="features" class="py-16 md:py-24 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-12 md:mb-16 reveal-on-scroll">
                <h2 class="text-3xl md:text-5xl font-bold text-slate-900 dark:text-white">{{ __('home.features_title') }}</h2>
                <p class="mt-4 text-lg md:text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">{{ __('home.features_subtitle') }}</p>
            </div>

            <div class="grid lg:grid-cols-12 gap-8 lg:gap-12">
                <!-- Sidebar Tabs -->
                <div class="lg:col-span-4 flex flex-col gap-3">
                    <template x-for="(tab, i) in tabs" :key="i">
                        <button @click="setTab(i)" class="group relative w-full text-left p-4 md:p-5 rounded-2xl transition-all duration-300 border backdrop-blur-sm overflow-hidden" :class="activeTab === i ? 'bg-white dark:bg-slate-800 border-blue-200 dark:border-blue-500/50 shadow-lg' : 'bg-white/50 dark:bg-slate-800/30 border-transparent'">
                            <div class="absolute bottom-0 left-0 h-0.5 bg-blue-500/40 transition-all duration-100 ease-linear" :style="activeTab === i ? `width: ${progress}%` : 'width: 0%'"></div>
                            <div class="flex items-center gap-4 relative z-10">
                                <div class="h-10 w-10 md:h-12 md:w-12 rounded-xl flex items-center justify-center transition-all duration-300" :class="activeTab === i ? 'bg-blue-600 text-white shadow-lg' : 'bg-slate-100 dark:bg-slate-700 text-slate-400'">
                                    <div class="w-5 h-5 md:w-6 md:h-6">
                                        <template x-if="tab.icon === 'admin'"><x-hugeicons-manager /></template>
                                        <template x-if="tab.icon === 'doctor'"><x-hugeicons-doctor-01 /></template>
                                        <template x-if="tab.icon === 'receptionist'"><x-hugeicons-appointment-01 /></template>
                                        <template x-if="tab.icon === 'lab'"><x-hugeicons-labs /></template>
                                        <template x-if="tab.icon === 'pharmacist'"><x-hugeicons-medicine-02 /></template>
                                        <template x-if="tab.icon === 'nurse'"><x-hugeicons-pulse-01 /></template>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="font-bold text-slate-900 dark:text-white text-base md:text-lg" x-text="tab.title"></h3>
                                    <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400" x-text="tab.subtitle"></p>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>

                <!-- Detail Panel -->
                <div class="lg:col-span-8">
                    <div class="relative min-h-[400px] md:min-h-[550px] bg-white/70 dark:bg-slate-800/50 backdrop-blur-xl rounded-3xl border border-white/40 dark:border-slate-700 p-6 md:p-12 shadow-2xl overflow-hidden">
                        <template x-for="(tab, i) in tabs" :key="'panel-' + i">
                            <div x-show="activeTab === i" x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-4 md:translate-x-8 md:translate-y-0" x-transition:enter-end="opacity-100 translate-x-0 translate-y-0" class="h-full flex flex-col relative z-10">
                                <h3 class="text-2xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4" x-text="tab.heading"></h3>
                                <p class="text-base md:text-lg text-slate-600 dark:text-slate-300 leading-relaxed mb-8 md:mb-12" x-text="tab.copy"></p>
                                <div class="grid sm:grid-cols-2 gap-4 mt-auto">
                                    <template x-for="(b, bi) in tab.bullets" :key="bi">
                                        <div class="p-4 rounded-xl bg-white/60 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700">
                                            <div class="flex gap-3">
                                                <div class="mt-1 h-5 w-5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                                                    <x-heroicon-s-check class="w-3 h-3 text-emerald-600 dark:text-emerald-400" />
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-900 dark:text-white text-sm" x-text="b.title"></p>
                                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1" x-text="b.desc"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SOLUTIONS GRID -->
    <section id="solutions" class="py-16 md:py-24 bg-white/40 dark:bg-black/20 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center mb-12 reveal-on-scroll">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white">{{ __('home.solutions_title') }}</h2>
                <p class="mt-4 text-slate-600 dark:text-slate-400">{{ __('home.solutions_subtitle') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 reveal-on-scroll delay-200">
                <template x-for="(card, index) in deptCards" :key="index">
                    <div @mousemove="handleMouseMove($event)" class="group relative rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-6 md:p-8 overflow-hidden transition-all duration-300 hover:shadow-2xl">
                        <div class="pointer-events-none absolute -inset-px rounded-2xl opacity-0 transition duration-300 group-hover:opacity-100 hidden md:block" style="background: radial-gradient(600px circle at var(--mouse-x) var(--mouse-y), rgba(59, 130, 246, 0.05), transparent 40%);"></div>
                        <div class="relative z-10">
                            <div class="flex justify-between items-center mb-6">
                                <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600">
                                    <div class="w-6 h-6">
                                        <template x-if="card.badge === 'Laboratory'"><x-hugeicons-labs /></template>
                                        <template x-if="card.badge === 'Pharmacy'"><x-hugeicons-medicine-01 /></template>
                                        <template x-if="card.badge === 'System Control'"><x-hugeicons-microsoft-admin /></template>
                                        <template x-if="card.badge === 'Front Desk'"><x-heroicon-m-computer-desktop /></template>
                                        <template x-if="card.badge === 'Clinical'"><x-hugeicons-doctor-03 /></template>
                                        <template x-if="card.badge === 'Patient Care'"><x-hugeicons-patient /></template>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold uppercase px-2.5 py-1 rounded-full" :class="card.badgeClass" x-text="card.badge"></span>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3" x-text="card.title"></h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm mb-6" x-text="card.desc"></p>
                            <ul class="space-y-3 mb-4">
                                <template x-for="li in card.points" :key="li">
                                    <li class="flex items-start gap-2 text-sm text-slate-700 dark:text-slate-300">
                                        <svg class="w-4 h-4 mt-0.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <span x-text="li"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- TESTIMONIALS -->
    <section class="py-16 md:py-24 overflow-hidden relative bg-slate-50/50 dark:bg-black/20">
        <div class="max-w-7xl mx-auto px-4 text-center mb-12 reveal-on-scroll">
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white">{{ __('home.testimonials_title') }}</h2>
            <p class="mt-4 text-slate-600 dark:text-slate-400">{{ __('home.testimonials_subtitle') }}</p>
        </div>
        <div class="absolute left-0 top-0 bottom-0 w-12 md:w-40 bg-gradient-to-r from-[#f8fafc] dark:from-[#0B1120] to-transparent z-20 pointer-events-none"></div>
        <div class="absolute right-0 top-0 bottom-0 w-12 md:w-40 bg-gradient-to-l from-[#f8fafc] dark:from-[#0B1120] to-transparent z-20 pointer-events-none"></div>

        <div class="flex overflow-hidden group">
            <div class="flex animate-marquee shrink-0 items-center gap-6 md:gap-8 py-8 px-4 group-hover:[animation-play-state:paused]">
                <template x-for="t in testimonials" :key="t.name">
                    <div class="w-[280px] md:w-[350px] flex-shrink-0 p-6 md:p-8 rounded-2xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 shadow-xl">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="h-10 w-10 md:h-12 md:w-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg" x-text="t.name.charAt(0)"></div>
                            <div class="text-left">
                                <p class="font-bold text-slate-900 dark:text-white text-sm md:text-base" x-text="t.name"></p>
                                <p class="text-[10px] md:text-xs font-medium text-blue-600 uppercase" x-text="t.role"></p>
                            </div>
                        </div>
                        <p class="text-slate-600 dark:text-slate-300 text-xs md:text-sm italic leading-relaxed" x-text="t.quote"></p>
                    </div>
                </template>
            </div>
            <div class="flex animate-marquee shrink-0 items-center gap-6 md:gap-8 py-8 px-4 group-hover:[animation-play-state:paused]" aria-hidden="true">
                <template x-for="t in testimonials" :key="t.name + '-dup'">
                    <div class="w-[280px] md:w-[350px] flex-shrink-0 p-6 md:p-8 rounded-2xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 shadow-xl">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="h-10 w-10 md:h-12 md:w-12 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg" x-text="t.name.charAt(0)"></div>
                            <div class="text-left">
                                <p class="font-bold text-slate-900 dark:text-white text-sm md:text-base" x-text="t.name"></p>
                                <p class="text-[10px] md:text-xs font-medium text-blue-600 uppercase" x-text="t.role"></p>
                            </div>
                        </div>
                        <p class="text-slate-600 dark:text-slate-300 text-xs md:text-sm italic leading-relaxed" x-text="t.quote"></p>
                    </div>
                </template>
            </div>
        </div>
    </section>

    <!-- PRICING / BETA -->
    <section id="pricing" class="py-16 md:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 lg:gap-8 items-center">
                <div class="reveal-on-scroll lg:col-span-1 text-center lg:text-left">
                    <h2 class="text-3xl md:text-5xl font-bold text-slate-900 dark:text-white mb-6 leading-tight">{{ __('home.pricing_title') }}</h2>
                    <p class="text-slate-600 dark:text-slate-400 mb-8 text-lg">{{ __('home.pricing_subtitle') }}</p>
                    <div class="flex items-center justify-center lg:justify-start gap-2 text-blue-600 font-bold uppercase tracking-wider text-sm">
                        <span class="flex h-3 w-3 rounded-full bg-blue-500 animate-pulse"></span> {{ __('home.limited_time') }}
                    </div>
                </div>
                <div class="reveal-on-scroll delay-100 lg:col-span-2">
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-purple-600 rounded-[2rem] blur opacity-20 group-hover:opacity-40 transition duration-1000"></div>
                        <div class="relative bg-white dark:bg-[#0f172a] rounded-[1.8rem] p-6 md:p-12 border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div class="absolute top-0 right-0 bg-blue-600 text-white text-[10px] md:text-xs font-bold px-4 py-2 rounded-bl-xl shadow-lg">{{ __('home.beta_program') }}</div>
                            <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                                <div class="space-y-4 text-center md:text-left">
                                    <h3 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-white">{{ __('home.exclusive_access') }}</h3>
                                    <ul class="space-y-2 inline-block text-left">
                                        <li class="flex items-center gap-2 text-slate-600 dark:text-slate-300 text-sm md:text-base"><x-heroicon-s-check class="w-5 h-5 text-green-500" /> Unlimited Users</li>
                                        <li class="flex items-center gap-2 text-slate-600 dark:text-slate-300 text-sm md:text-base"><x-heroicon-s-check class="w-5 h-5 text-green-500" /> Full Module Suite</li>
                                    </ul>
                                    <div class="flex items-baseline justify-center md:justify-start gap-2 pt-2">
                                        <span class="text-5xl md:text-6xl font-extrabold text-slate-900 dark:text-white">{{ __('home.free') }}</span>
                                        <span class="text-slate-500 font-medium">/ consultation</span>
                                    </div>
                                </div>
                                <button @click="demoOpen = true" class="w-full md:w-auto px-10 py-5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg shadow-xl shadow-blue-600/30 transition-all active:scale-95">{{ __('home.apply_beta') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ SECTION -->
    <section id="faq" class="py-16 md:py-24 max-w-3xl mx-auto px-4 sm:px-6">
        <h2 class="text-2xl md:text-3xl font-bold text-center mb-8 md:mb-12 text-slate-900 dark:text-white">{{ __('home.faq_title') }}</h2>
        <div class="space-y-4">
            <template x-for="(f, i) in faqs" :key="i">
                <div class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                    <button @click="openFaq = openFaq === i ? null : i" class="w-full p-5 md:p-6 flex justify-between items-start text-left gap-4">
                        <span class="font-semibold text-slate-900 dark:text-white text-sm md:text-base" x-text="f.q"></span>
                        <svg class="w-5 h-5 text-slate-400 transition-transform shrink-0" :class="{ 'rotate-180': openFaq === i }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="openFaq === i" x-collapse class="px-5 md:px-6 pb-6 text-slate-600 dark:text-slate-300 text-sm md:text-base" x-text="f.a"></div>
                </div>
            </template>
        </div>
    </section>

    <!-- FINAL CTA -->
    <section class="py-16 md:py-24">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="relative rounded-[2rem] md:rounded-[2.5rem] bg-gradient-to-br from-blue-600 to-indigo-700 overflow-hidden px-6 py-12 md:px-12 md:py-20 text-center shadow-2xl reveal-on-scroll">
                <div class="absolute top-0 left-0 -translate-x-1/2 -translate-y-1/2 w-64 h-64 md:w-96 md:h-96 bg-white/10 rounded-full blur-3xl"></div>
                <h2 class="relative text-2xl md:text-5xl font-bold text-white mb-6 leading-tight">{{ __('home.cta_title') }}</h2>
                <p class="relative text-base md:text-xl text-blue-100 max-w-2xl mx-auto mb-10">{{ __('home.cta_subtitle') }}</p>
                <div class="relative flex flex-col sm:flex-row justify-center gap-4 px-4">
                    <button @click="demoOpen = true" class="rounded-full bg-white px-8 md:px-10 py-4 text-base font-bold text-blue-600 shadow-xl hover:bg-blue-50 transition-all active:scale-95">{{ __('home.book_demo') }}</button>
                    <a href="#features" class="rounded-full border border-white/30 bg-white/10 px-8 md:px-10 py-4 text-base font-bold text-white backdrop-blur-sm hover:bg-white/20 transition-all active:scale-95">Explore Features</a>
                </div>
            </div>
        </div>
    </section>

    <style>
        @keyframes blink { 0%, 100% { opacity: 1; } 50% { opacity: 0; } }
        .animate-blink { animation: blink 1s step-end infinite; }
        .tilt-card { transform-style: preserve-3d; backface-visibility: hidden; }
        @keyframes marquee { from { transform: translateX(0); } to { transform: translateX(-100%); } }
        .animate-marquee { animation: marquee 35s linear infinite; }
        @media (max-width: 640px) { .animate-marquee { animation-duration: 20s; } }
    </style>
</div>
