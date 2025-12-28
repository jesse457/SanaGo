<div x-data="{
    mobileMenuOpen: false,
    activeTab: 0,
    openFaq: null,
    demoOpen: false,
    darkMode: localStorage.getItem('darkMode') === 'true',
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
        init() {
            this.type();
        },
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
        if (this.darkMode || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            this.darkMode = true;
        }
        this.startAutoRotate();
        this.typewriter.init();

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
        const card = e.currentTarget;
        const rect = card.getBoundingClientRect();
        card.style.setProperty('--mouse-x', `${e.clientX - rect.left}px`);
        card.style.setProperty('--mouse-y', `${e.clientY - rect.top}px`);
    }
}"
    class="min-h-screen relative overflow-x-hidden font-sans antialiased selection:bg-blue-500 selection:text-white text-slate-900 dark:text-slate-50">

    <!-- ========================================== -->
    <!-- BACKGROUNDS & BUBBLE ANIMATION -->
    <!-- ========================================== -->

    <!-- Light Mode Static Base -->
    <div class="fixed inset-0 bg-[#f8fafc] -z-50 transition-colors duration-500 dark:hidden">
        <div
            class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-blue-50/50 via-purple-50/30 to-white">
        </div>
    </div>

    <!-- Dark Mode Static Base -->
    <div class="fixed inset-0 bg-[#0B1120] -z-50 transition-colors duration-500 hidden dark:block"></div>

    <!-- BUBBLES CONTAINER -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-40">
        <ul class="circles">
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </div>

    <!-- Noise Texture Overlay -->
    <div class="fixed inset-0 opacity-[0.04] dark:opacity-[0.07] pointer-events-none -z-30 mix-blend-overlay"
        style="background-image: url('https://grainy-gradients.vercel.app/noise.svg')"></div>



    <livewire:component.navbar />

    <main class="pt-[72px]">

        <!-- ========================================== -->
        <!-- HERO SECTION WITH TYPEWRITER -->
        <!-- ========================================== -->
        <section class="relative pt-20 pb-32 overflow-hidden">
            <div class="relative max-w-7xl mx-auto px-6 flex flex-col items-center text-center z-10">

                <div
                    class="reveal-on-scroll inline-flex items-center rounded-full border border-blue-100 dark:border-blue-900 bg-blue-50/80 dark:bg-blue-900/30 px-4 py-1.5 text-sm font-medium text-blue-700 dark:text-blue-200 mb-8 backdrop-blur-md shadow-sm hover:scale-105 transition-transform duration-300 cursor-default">
                    <span class="flex h-2 w-2 rounded-full bg-blue-500 mr-2 animate-pulse"></span>
                    {{ __('home.hero_explore') }}
                </div>

                <h1
                    class="reveal-on-scroll delay-100 text-5xl md:text-7xl lg:text-8xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-8 max-w-5xl drop-shadow-sm h-[3.5em] md:h-[2.5em]">
                    {{ __('home.hero_title_p1') }}
                    <br />
                    <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-500 to-blue-400 animate-gradient pb-2">
                        <span x-text="typewriter.text"></span><span class="animate-blink text-blue-600 ml-1">|</span>
                    </span>
                </h1>

                <p
                    class="reveal-on-scroll delay-200 mt-4 text-lg md:text-xl text-slate-600 dark:text-slate-300 max-w-2xl mb-12 leading-relaxed">
                    {{ __('home.hero_subtitle') }}
                </p>

                <div
                    class="reveal-on-scroll delay-300 flex flex-col sm:flex-row gap-5 items-center w-full justify-center">
                    <a href="#pricing"
                        class="group relative inline-flex h-14 items-center justify-center overflow-hidden rounded-full bg-blue-600 px-10 font-bold text-white duration-300 hover:bg-blue-700 shadow-[0_10px_40px_-10px_rgba(37,99,235,0.5)] hover:shadow-[0_20px_40px_-10px_rgba(37,99,235,0.7)] hover:-translate-y-1 w-full sm:w-auto">
                        <span class="mr-2">{{ __('home.hero_cta') }}</span>
                        <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </a>
                    <a href="#features"
                        class="inline-flex h-14 items-center justify-center rounded-full px-10 font-bold text-slate-700 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200 border border-slate-200 dark:border-slate-700 w-full sm:w-auto">
                        Learn More
                    </a>
                </div>

                <!-- 3D Tilt Card Component -->
                <div class="reveal-on-scroll delay-500 mt-20 relative w-full max-w-6xl mx-auto perspective-2000"
                    x-data="{
                        rotateX: 0,
                        rotateY: 0,
                        // Check if dark mode is active on load
                        isDark: localStorage.getItem('theme') === 'dark',

                        handleMouseMove(e) {
                            const rect = this.$el.getBoundingClientRect();
                            const x = e.clientX - rect.left;
                            const y = e.clientY - rect.top;
                            const centerX = rect.width / 2;
                            const centerY = rect.height / 2;

                            this.rotateX = -((y - centerY) / 50);
                            this.rotateY = ((x - centerX) / 50);
                        },

                        reset() {
                            this.rotateX = 0;
                            this.rotateY = 0;
                        }
                    }" @theme-changed.window="isDark = $event.detail"
                    @mousemove="handleMouseMove($event)" @mouseleave="reset()">

                    <div class="tilt-card w-full rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-2xl transition-transform ease-out duration-200 will-change-transform overflow-hidden"
                        :style="`transform: rotateX(${rotateX}deg) rotateY(${rotateY}deg)`">

                        <!-- Browser Header -->
                        <div
                            class="h-11 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700 flex items-center px-4 gap-2">
                            <div class="flex gap-1.5">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                            </div>
                            <div
                                class="mx-auto w-full max-w-md h-6 bg-white dark:bg-slate-900/50 rounded-md border border-slate-200 dark:border-slate-700 flex items-center px-3">
                                <div class="text-[10px] text-slate-400 dark:text-slate-500 truncate">
                                    sanago.system/admin/dashboard</div>
                            </div>
                        </div>

                        <!-- Image Wrapper -->
                        <div class="relative w-full bg-white dark:bg-[#0B1120]">
                            <!-- Light Mode Image -->
                            <img x-show="!isDark" class="w-full h-auto block antialiased shadow-inner"
                                src="{{ asset('images/dashboard-light.webp') }}"
                                style="image-rendering: -webkit-optimize-contrast; image-rendering: crisp-edges;"
                                alt="SanaGo Dashboard Light">

                            <!-- Dark Mode Image -->
                            <img x-show="isDark" x-cloak class="w-full h-auto block antialiased shadow-inner"
                                src="{{ asset('images/dashboard-dark.webp') }}"
                                style="image-rendering: -webkit-optimize-contrast; image-rendering: crisp-edges;"
                                alt="SanaGo Dashboard Dark">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section id="features" class="py-24 relative">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16 reveal-on-scroll">
                    <h2 class="text-3xl md:text-5xl font-bold text-slate-900 dark:text-white">
                        {{ __('home.features_title') }}</h2>
                    <p class="mt-4 text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">
                        {{ __('home.features_subtitle') }}</p>
                </div>

                <div class="grid lg:grid-cols-12 gap-8 lg:gap-12">
                    <div class="lg:col-span-4 flex flex-col gap-3 reveal-on-scroll delay-100">
                        <template x-for="(tab, i) in tabs" :key="i">
                            <button @click="setTab(i)"
                                class="group relative w-full text-left p-5 rounded-2xl transition-all duration-300 border backdrop-blur-sm overflow-hidden"
                                :class="activeTab === i ?
                                    'bg-white dark:bg-slate-800 border-blue-200 dark:border-blue-500/50 shadow-lg shadow-blue-500/5 scale-[1.02]' :
                                    'bg-white/50 dark:bg-slate-800/30 border-transparent hover:bg-white/80 dark:hover:bg-slate-800/50'">
                                <div class="absolute bottom-0 left-0 h-1 bg-gradient-to-r from-blue-500 to-purple-500 transition-all duration-100"
                                    :style="activeTab === i ? `width: ${progress}%` : 'width: 0%'"></div>
                                <div class="flex items-center gap-4 relative z-10">
                                    <div class="h-12 w-12 rounded-xl flex items-center justify-center text-2xl transition-all duration-300"
                                        :class="activeTab === i ?
                                            'bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-lg shadow-blue-500/30' :
                                            'bg-slate-100 dark:bg-slate-700 text-slate-400'">
                                        <span x-text="tab.icon"></span>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 dark:text-white text-lg" x-text="tab.title">
                                        </h3>
                                        <p class="text-sm text-slate-500 dark:text-slate-400" x-text="tab.subtitle">
                                        </p>
                                    </div>
                                </div>
                            </button>
                        </template>
                    </div>
                    <div class="lg:col-span-8 reveal-on-scroll delay-200">
                        <div
                            class="relative h-full min-h-[550px] bg-white/70 dark:bg-slate-800/50 backdrop-blur-xl rounded-3xl border border-white/40 dark:border-slate-700 p-8 md:p-12 shadow-2xl shadow-blue-900/5 overflow-hidden">
                            <template x-for="(tab, i) in tabs" :key="'panel-' + i">
                                <div x-show="activeTab === i" x-transition:enter="transition ease-out duration-500"
                                    x-transition:enter-start="opacity-0 translate-x-8"
                                    x-transition:enter-end="opacity-100 translate-x-0"
                                    class="h-full flex flex-col relative z-10">
                                    <h3 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4"
                                        x-text="tab.heading"></h3>
                                    <p class="text-lg text-slate-600 dark:text-slate-300 leading-relaxed mb-8"
                                        x-text="tab.copy"></p>
                                    <div class="grid sm:grid-cols-2 gap-4 mt-auto">
                                        <template x-for="(b, bi) in tab.bullets" :key="bi">
                                            <div
                                                class="p-4 rounded-xl bg-white/60 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700">
                                                <div class="flex gap-3">
                                                    <div
                                                        class="mt-1 h-6 w-6 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                                                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400"
                                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-900 dark:text-white text-sm"
                                                            x-text="b.title"></p>
                                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1"
                                                            x-text="b.desc"></p>
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

        <!-- Solutions -->
        <section id="solutions" class="py-24 bg-white/40 dark:bg-black/20 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-12 reveal-on-scroll">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white">
                        {{ __('home.solutions_title') }}</h2>
                    <p class="mt-4 text-slate-600 dark:text-slate-400">{{ __('home.solutions_subtitle') }}</p>
                </div>
                <div class="grid md:grid-cols-3 gap-6 reveal-on-scroll delay-200">
                    <template x-for="(card, index) in deptCards" :key="index">
                        <div @mousemove="handleMouseMove($event)"
                            class="group relative rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 p-8 overflow-hidden transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                            <div class="pointer-events-none absolute -inset-px rounded-2xl opacity-0 transition duration-300 group-hover:opacity-100"
                                style="background: radial-gradient(600px circle at var(--mouse-x) var(--mouse-y), rgba(59, 130, 246, 0.05), transparent 40%);">
                            </div>
                            <div class="relative z-10">
                                <div class="flex justify-between items-center mb-6">
                                    <div
                                        class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400">
                                        <template x-if="card.badge === 'Laboratory'">
                                            <x-hugeicons-labs class="w-6 h-6 text-green-500" />
                                        </template>
                                        <template x-if="card.badge === 'Pharmacy'">
                                            <x-hugeicons-medicine-01 class="w-6 h-6 text-green-500" />
                                        </template>
                                        <template x-if="card.badge === 'System Control'">
                                            <x-hugeicons-microsoft-admin class="w-6 h-6 text-green-500" />
                                        </template>
                                        <template x-if="card.badge === 'Front Desk'">
                                            <x-heroicon-m-computer-desktop class="w-6 h-6 text-green-500" />
                                        </template>

                                        <template x-if="card.badge === 'Clinical'">
                                            <x-hugeicons-doctor-03 class="w-6 h-6 text-green-500" />
                                        </template>
                                        <template x-if="card.badge === 'Patient Care'">
                                            <x-hugeicons-patient class="w-6 h-6 text-green-500" />
                                        </template>
                                    </div>
                                    <span class="text-[10px] font-bold uppercase px-2.5 py-1 rounded-full"
                                        :class="card.badgeClass" x-text="card.badge"></span>
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3" x-text="card.title">
                                </h3>
                                <p class="text-slate-500 dark:text-slate-400 text-sm mb-6" x-text="card.desc"></p>
                                <ul class="space-y-3 mb-8">
                                    <template x-for="li in card.points" :key="li">
                                        <li class="flex items-start gap-2 text-sm text-slate-700 dark:text-slate-300">
                                            <svg class="w-4 h-4 mt-0.5 text-blue-500 shrink-0" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
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

        <!-- Infinite Testimonial Loop -->
        <section class="py-24 overflow-hidden relative bg-slate-50/50 dark:bg-black/20 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-6 text-center mb-12 reveal-on-scroll">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white">
                    {{ __('home.testimonials_title') }}</h2>
                <p class="mt-4 text-slate-600 dark:text-slate-400">{{ __('home.testimonials_subtitle') }}</p>
            </div>
            <div
                class="absolute left-0 top-0 bottom-0 w-20 md:w-40 bg-gradient-to-r from-[#f8fafc] dark:from-[#0B1120] to-transparent z-20 pointer-events-none">
            </div>
            <div
                class="absolute right-0 top-0 bottom-0 w-20 md:w-40 bg-gradient-to-l from-[#f8fafc] dark:from-[#0B1120] to-transparent z-20 pointer-events-none">
            </div>
            <div class="flex overflow-hidden group">
                <div
                    class="flex animate-marquee shrink-0 items-center gap-8 py-8 px-4 group-hover:[animation-play-state:paused]">
                    <template x-for="t in testimonials" :key="t.name">
                        <div
                            class="w-[350px] flex-shrink-0 p-8 rounded-2xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 shadow-xl shadow-slate-200/50 dark:shadow-none hover:scale-105 transition-transform duration-300">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg"
                                    x-text="t.name.charAt(0)"></div>
                                <div class="text-left">
                                    <p class="font-bold text-slate-900 dark:text-white" x-text="t.name"></p>
                                    <p class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase"
                                        x-text="t.role"></p>
                                </div>
                            </div>
                            <p class="text-slate-600 dark:text-slate-300 text-sm italic leading-relaxed"
                                x-text="t.quote"></p>
                        </div>
                    </template>
                </div>
                <div class="flex animate-marquee shrink-0 items-center gap-8 py-8 px-4 group-hover:[animation-play-state:paused]"
                    aria-hidden="true">
                    <template x-for="t in testimonials" :key="t.name + '-dup'">
                        <div
                            class="w-[350px] flex-shrink-0 p-8 rounded-2xl bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 shadow-xl shadow-slate-200/50 dark:shadow-none hover:scale-105 transition-transform duration-300">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg"
                                    x-text="t.name.charAt(0)"></div>
                                <div class="text-left">
                                    <p class="font-bold text-slate-900 dark:text-white" x-text="t.name"></p>
                                    <p class="text-xs font-medium text-blue-600 dark:text-blue-400 uppercase"
                                        x-text="t.role"></p>
                                </div>
                            </div>
                            <p class="text-slate-600 dark:text-slate-300 text-sm italic leading-relaxed"
                                x-text="t.quote"></p>
                        </div>
                    </template>
                </div>
            </div>
        </section>

        <!-- Pricing / Beta -->
        <section id="pricing" class="py-24">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-center">
                    <div class="reveal-on-scroll lg:col-span-1 flex flex-col justify-center">
                        <h2 class="text-4xl md:text-5xl font-bold text-slate-900 dark:text-white mb-6">
                            {{ __('home.pricing_title') }}</h2>
                        <p class="text-slate-600 dark:text-slate-400 mb-8 text-lg">{{ __('home.pricing_subtitle') }}
                        </p>
                        <div
                            class="flex items-center gap-2 text-blue-600 dark:text-blue-400 font-bold uppercase tracking-wider text-sm">
                            <span class="flex h-3 w-3 rounded-full bg-blue-500 animate-pulse"></span>
                            {{ __('home.limited_time') }}
                        </div>
                    </div>

                    <div class="reveal-on-scroll delay-100 lg:col-span-2 lg:col-start-2">
                        <!-- Glow Effect Container -->
                        <div class="relative group">
                            <div
                                class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-purple-600 rounded-[2rem] blur opacity-30 group-hover:opacity-60 transition duration-1000 group-hover:duration-200">
                            </div>
                            <div
                                class="relative bg-white dark:bg-[#0f172a] rounded-[1.8rem] p-8 md:p-12 overflow-hidden border border-slate-200 dark:border-slate-700">
                                <!-- Beta Badge -->
                                <div
                                    class="absolute top-0 right-0 bg-gradient-to-bl from-blue-600 to-blue-500 text-white text-xs font-bold px-4 py-2 rounded-bl-xl shadow-lg">
                                    {{ __('home.beta_program') }}</div>

                                <div class="flex flex-col md:flex-row justify-between items-center gap-8">
                                    <div class="space-y-4">
                                        <h3 class="text-3xl font-bold text-slate-900 dark:text-white">
                                            {{ __('home.exclusive_access') }}</h3>
                                        <ul class="space-y-2">
                                            <li class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                                                <svg class="w-5 h-5 text-green-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Unlimited Users
                                            </li>
                                            <li class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                                                <svg class="w-5 h-5 text-green-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Full Module Suite
                                            </li>
                                        </ul>
                                        <div class="flex items-baseline gap-2 pt-2">
                                            <span
                                                class="text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ __('home.free') }}</span>
                                            <span class="text-slate-500 font-medium">/ consultation</span>
                                        </div>
                                    </div>
                                    <button @click="demoOpen = true"
                                        class="w-full md:w-auto px-10 py-5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-lg shadow-xl shadow-blue-600/30 transition-all duration-200 hover:scale-105 hover:-translate-y-1">
                                        {{ __('home.apply_beta') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="faq" class="py-24 max-w-3xl mx-auto px-6">
            <h2 class="text-3xl font-bold text-center mb-12 text-slate-900 dark:text-white">{{ __('home.faq_title') }}
            </h2>
            <div class="space-y-4">
                <template x-for="(f, i) in faqs" :key="i">
                    <div
                        class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                        <button @click="openFaq = openFaq === i ? null : i"
                            class="w-full p-6 flex justify-between items-center text-left">
                            <span class="font-semibold text-slate-900 dark:text-white" x-text="f.q"></span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform duration-300"
                                :class="{ 'rotate-180': openFaq === i }" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="openFaq === i" x-collapse>
                            <div class="px-6 pb-6 text-slate-600 dark:text-slate-300" x-text="f.a"></div>
                        </div>
                    </div>
                </template>
            </div>
        </section>
        <!-- Final CTA -->
        <section class="py-24">
            <div class="max-w-5xl mx-auto px-6">
                <div
                    class="relative rounded-[2.5rem] bg-gradient-to-br from-blue-600 to-indigo-700 overflow-hidden px-6 py-16 sm:px-12 sm:py-20 text-center shadow-2xl shadow-blue-900/40 reveal-on-scroll">
                    <!-- Decorative elements inside CTA -->
                    <div
                        class="absolute top-0 left-0 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-pulse">
                    </div>
                    <div
                        class="absolute bottom-0 right-0 translate-x-1/2 translate-y-1/2 w-96 h-96 bg-purple-500/30 rounded-full blur-3xl">
                    </div>

                    <h2 class="relative text-3xl font-bold tracking-tight text-white sm:text-5xl mb-6">
                        {{ __('home.cta_title') }}</h2>
                    <p class="relative text-xl text-blue-100 max-w-2xl mx-auto mb-10">{{ __('home.cta_subtitle') }}
                    </p>

                    <div class="relative flex flex-col sm:flex-row justify-center gap-4">
                        <button @click="demoOpen = true"
                            class="rounded-full bg-white px-10 py-4 text-base font-bold text-blue-600 shadow-xl hover:bg-blue-50 transition-all duration-200 hover:scale-105">
                            {{ __('home.book_demo') }}
                        </button>
                        <a href="#features"
                            class="rounded-full border border-white/30 bg-white/10 px-10 py-4 text-base font-bold text-white backdrop-blur-sm hover:bg-white/20 transition-all duration-200 hover:scale-105">
                            Explore Features
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <livewire:component.footer />

    <style>
        .perspective-1000 {
            perspective: 1000px;
        }

        [x-cloak] {
            display: none !important;
        }

        @keyframes marquee {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-100%);
            }
        }

        .animate-marquee {
            animation: marquee 35s linear infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }
        }

        .animate-blink {
            animation: blink 1s step-end infinite;
        }

        /* NEW BLURRY GRADIENT BUBBLES CSS */
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

        .circles li:nth-child(1) {
            left: 25%;
            width: 80px;
            height: 80px;
            animation-delay: 0s;
        }

        .circles li:nth-child(2) {
            left: 10%;
            width: 20px;
            height: 20px;
            animation-delay: 2s;
            animation-duration: 12s;
        }

        .circles li:nth-child(3) {
            left: 70%;
            width: 20px;
            height: 20px;
            animation-delay: 4s;
        }

        .circles li:nth-child(4) {
            left: 40%;
            width: 60px;
            height: 60px;
            animation-delay: 0s;
            animation-duration: 18s;
        }

        .circles li:nth-child(5) {
            left: 65%;
            width: 20px;
            height: 20px;
            animation-delay: 0s;
        }

        .circles li:nth-child(6) {
            left: 75%;
            width: 110px;
            height: 110px;
            animation-delay: 3s;
        }

        .circles li:nth-child(7) {
            left: 35%;
            width: 150px;
            height: 150px;
            animation-delay: 7s;
        }

        .circles li:nth-child(8) {
            left: 50%;
            width: 25px;
            height: 25px;
            animation-delay: 15s;
            animation-duration: 45s;
        }

        .circles li:nth-child(9) {
            left: 20%;
            width: 15px;
            height: 15px;
            animation-delay: 2s;
            animation-duration: 35s;
        }

        .circles li:nth-child(10) {
            left: 85%;
            width: 150px;
            height: 150px;
            animation-delay: 0s;
            animation-duration: 11s;
        }

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

        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }

        .reveal-on-scroll.animate-in {
            opacity: 1;
            transform: translateY(0);
        }

        .perspective-2000 {
            perspective: 2000px;
        }

        .tilt-card {
            transform-style: preserve-3d;
        }

        /* This makes the image feel like it's slightly floating inside the frame */
        .tilt-card img {
            transform: translateZ(20px);
        }
    </style>
</div>
