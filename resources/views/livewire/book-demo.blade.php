
<div  x-data="{
    mobileMenuOpen: false,
    darkMode: localStorage.getItem('darkMode') === 'true',
    faqOpen: null,
    formSubmitted: false,

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
    },
    submitForm() {
        // Simulate form submission
        this.formSubmitted = true;
        // In a real app, you would send data to a server here
        setTimeout(() => {
            this.formSubmitted = false;
        }, 5000); // Hide message after 5 seconds
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
        <!-- HERO SECTION WITH FORM -->
        <section class="relative pt-20 pb-32 overflow-hidden">
            <div class="relative max-w-7xl mx-auto px-6 flex flex-col lg:flex-row items-center gap-16 z-10">
                <!-- Left Column: Text -->
                <div class="flex-1 reveal-on-scroll">
                    <div class="inline-flex items-center rounded-full border border-blue-100 dark:border-blue-900 bg-blue-50/80 dark:bg-blue-900/30 px-4 py-1.5 text-sm font-medium text-blue-700 dark:text-blue-200 mb-8 backdrop-blur-md shadow-sm">
                        <span class="flex h-2 w-2 rounded-full bg-blue-500 mr-2 animate-pulse"></span>
                        {{ __('book_demo.badge') }}
                    </div>
                    <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-6">
                        {{ __('book_demo.title_prefix') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-500 to-blue-400">{{ __('book_demo.title_suffix') }}</span>
                    </h1>
                    <p class="text-lg md:text-xl text-slate-600 dark:text-slate-300 mb-8 leading-relaxed">
                        {{ __('book_demo.subtitle') }}
                    </p>
                    <div class="flex items-center gap-4 text-sm text-slate-500 dark:text-slate-400">
                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span>{{ __('book_demo.secure_note') }}</span>
                    </div>
                </div>

                <!-- Right Column: Form -->
                <div class="flex-1 w-full max-w-md mx-auto lg:mx-0 reveal-on-scroll delay-200">
                    <div class="bg-white dark:bg-slate-800/60 backdrop-blur-xl p-8 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-2xl">
                        <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-6">{{ __('book_demo.form_title') }}</h2>

                        <div x-show="formSubmitted" class="p-4 mb-6 bg-emerald-100 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 rounded-xl text-emerald-700 dark:text-emerald-300 text-center">
                            <svg class="w-6 h-6 mx-auto mb-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            {{ __('book_demo.success_message') }}
                        </div>

                        <form @submit.prevent="submitForm()" class="space-y-5">
                            <div>
                                <label for="fullName" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('book_demo.label_name') }}</label>
                                <input type="text" id="fullName" name="fullName" required class="w-full px-4 py-3 rounded-xl bg-white/80 dark:bg-slate-700/80 backdrop-blur-md border border-slate-200 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400" placeholder="{{ __('book_demo.placeholder_name') }}">
                            </div>
                            <div>
                                <label for="workEmail" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('book_demo.label_email') }}</label>
                                <input type="email" id="workEmail" name="workEmail" required class="w-full px-4 py-3 rounded-xl bg-white/80 dark:bg-slate-700/80 backdrop-blur-md border border-slate-200 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400" placeholder="{{ __('book_demo.placeholder_email') }}">
                            </div>
                            <div>
                                <label for="facilityName" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('book_demo.label_facility') }}</label>
                                <input type="text" id="facilityName" name="facilityName" required class="w-full px-4 py-3 rounded-xl bg-white/80 dark:bg-slate-700/80 backdrop-blur-md border border-slate-200 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400" placeholder="{{ __('book_demo.placeholder_facility') }}">
                            </div>
                            <div>
                                <label for="jobTitle" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('book_demo.label_job') }}</label>
                                <input type="text" id="jobTitle" name="jobTitle" required class="w-full px-4 py-3 rounded-xl bg-white/80 dark:bg-slate-700/80 backdrop-blur-md border border-slate-200 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400" placeholder="{{ __('book_demo.placeholder_job') }}">
                            </div>
                            <div>
                                <label for="phoneNumber" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('book_demo.label_phone') }}</label>
                                <input type="tel" id="phoneNumber" name="phoneNumber" class="w-full px-4 py-3 rounded-xl bg-white/80 dark:bg-slate-700/80 backdrop-blur-md border border-slate-200 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400" placeholder="{{ __('book_demo.placeholder_phone') }}">
                            </div>
                            <button type="submit" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-colors shadow-lg hover:shadow-xl hover:-translate-y-1">
                                {{ __('book_demo.submit_btn') }}
                            </button>
                        </form>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-6 text-center">
                            {{ __('book_demo.terms_text') }} <a href="#" class="underline hover:text-blue-600">{{ __('book_demo.privacy_policy') }}</a> and <a href="#" class="underline hover:text-blue-600">{{ __('book_demo.terms_service') }}</a>.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- WHAT TO EXPECT SECTION -->
        <section class="py-24 bg-white/40 dark:bg-black/20 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16 reveal-on-scroll">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white">{{ __('book_demo.expect_title') }}</h2>
                    <p class="mt-4 text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">{{ __('book_demo.expect_subtitle') }}</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="reveal-on-scroll delay-100 text-center">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-6">
                            🎯
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">{{ __('book_demo.feature_personalized_title') }}</h3>
                        <p class="text-slate-600 dark:text-slate-300">{{ __('book_demo.feature_personalized_desc') }}</p>
                    </div>
                    <div class="reveal-on-scroll delay-200 text-center">
                        <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-6">
                            🖥️
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">{{ __('book_demo.feature_walkthrough_title') }}</h3>
                        <p class="text-slate-600 dark:text-slate-300">{{ __('book_demo.feature_walkthrough_desc') }}</p>
                    </div>
                    <div class="reveal-on-scroll delay-300 text-center">
                        <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-6">
                            💬
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">{{ __('book_demo.feature_qa_title') }}</h3>
                        <p class="text-slate-600 dark:text-slate-300">{{ __('book_demo.feature_qa_desc') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ SECTION -->
        <section class="py-24">
            <div class="max-w-3xl mx-auto px-6">
                <h2 class="text-3xl font-bold text-center mb-12 text-slate-900 dark:text-white">{{ __('book_demo.faq_title') }}</h2>
                <div class="space-y-4">
                    <div class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                        <button @click="faqOpen = faqOpen === 1 ? null : 1" class="w-full p-6 flex justify-between items-center text-left">
                            <span class="font-semibold text-slate-900 dark:text-white">{{ __('book_demo.faq_1_q') }}</span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform duration-300" :class="{'rotate-180': faqOpen === 1}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="faqOpen === 1" x-collapse>
                            <div class="px-6 pb-6 text-slate-600 dark:text-slate-300">
                                {{ __('book_demo.faq_1_a') }}
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                        <button @click="faqOpen = faqOpen === 2 ? null : 2" class="w-full p-6 flex justify-between items-center text-left">
                            <span class="font-semibold text-slate-900 dark:text-white">{{ __('book_demo.faq_2_q') }}</span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform duration-300" :class="{'rotate-180': faqOpen === 2}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="faqOpen === 2" x-collapse>
                            <div class="px-6 pb-6 text-slate-600 dark:text-slate-300">
                                {{ __('book_demo.faq_2_a') }}
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                        <button @click="faqOpen = faqOpen === 3 ? null : 3" class="w-full p-6 flex justify-between items-center text-left">
                            <span class="font-semibold text-slate-900 dark:text-white">{{ __('book_demo.faq_3_q') }}</span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform duration-300" :class="{'rotate-180': faqOpen === 3}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="faqOpen === 3" x-collapse>
                            <div class="px-6 pb-6 text-slate-600 dark:text-slate-300">
                                {{ __('book_demo.faq_3_a') }}
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                        <button @click="faqOpen = faqOpen === 4 ? null : 4" class="w-full p-6 flex justify-between items-center text-left">
                            <span class="font-semibold text-slate-900 dark:text-white">{{ __('book_demo.faq_4_q') }}</span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform duration-300" :class="{'rotate-180': faqOpen === 4}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="faqOpen === 4" x-collapse>
                            <div class="px-6 pb-6 text-slate-600 dark:text-slate-300">
                                {{ __('book_demo.faq_4_a') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FINAL CTA SECTION -->
        <section class="py-24 bg-white/40 dark:bg-black/20 backdrop-blur-sm">
            <div class="max-w-5xl mx-auto px-6 text-center">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-6">{{ __('book_demo.cta_title') }}</h2>
                <p class="text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto mb-10">
                    {{ __('book_demo.cta_subtitle') }}
                </p>
                <a href="#book-demo" class="inline-flex items-center gap-2 px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all duration-200 hover:scale-105 shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    {{ __('book_demo.cta_btn') }}
                </a>
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
