<div x-data="{
    mobileMenuOpen: false,
    darkMode: localStorage.getItem('darkMode') === 'true',
    billingCycle: 'monthly',
    // FAQ State
    activeFaq: null,

    // Pricing Data
    prices: {
        starter: 10000,
        standard: 15000,
        enterprise: 100000
    },

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

    formatPrice(plan) {
        let amount = this.prices[plan];
        // Annual discount: Pay for 10 months, get 12 (roughly 17% off)
        if (this.billingCycle === 'annual') {
            amount = amount * 10;
        }
        return new Intl.NumberFormat('fr-FR').format(amount);
    },

    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        this.darkMode ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark');
    }
}"
    class="min-h-screen relative overflow-x-hidden font-sans antialiased selection:bg-blue-500 selection:text-white text-slate-900 dark:text-slate-50">

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

    <livewire:component.navbar />

    <main class="pt-[72px]">
        <!-- HERO SECTION -->
        <section class="relative pt-20 pb-24 overflow-hidden">
            <div class="relative max-w-7xl mx-auto px-6 flex flex-col items-center text-center z-10">
                <div class="reveal-on-scroll inline-flex items-center rounded-full border border-blue-100 dark:border-blue-900 bg-blue-50/80 dark:bg-blue-900/30 px-4 py-1.5 text-sm font-medium text-blue-700 dark:text-blue-200 mb-8 backdrop-blur-md shadow-sm">
                    <span class="flex h-2 w-2 rounded-full bg-blue-500 mr-2 animate-pulse"></span>
                    {{ __('pricing.badge') }}
                </div>

                <h1 class="reveal-on-scroll delay-100 text-5xl md:text-7xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-8 max-w-5xl">
                    {{ __('pricing.title') }}
                </h1>

                <!-- Billing Toggle -->
                <div class="reveal-on-scroll delay-300 flex flex-col items-center gap-4 mb-16">
                    <div class="flex items-center justify-center gap-4">
                        <span class="text-slate-700 dark:text-slate-300 font-medium">{{ __('pricing.monthly') }}</span>
                        <button @click="billingCycle = billingCycle === 'monthly' ? 'annual' : 'monthly'"
                            class="relative inline-flex h-8 w-14 items-center rounded-full bg-slate-200 dark:bg-slate-700 transition-colors">
                            <span class="inline-block h-6 w-6 transform rounded-full bg-white shadow-lg transition-transform"
                                :class="billingCycle === 'annual' ? 'translate-x-7' : 'translate-x-1'"></span>
                        </button>
                        <span class="text-slate-700 dark:text-slate-300 font-medium">{{ __('pricing.annual') }}</span>
                    </div>
                    <div x-show="billingCycle === 'annual'" x-transition class="text-emerald-500 font-bold text-sm">
                        🎉 2 Months Free applied!
                    </div>
                </div>
            </div>
        </section>

        <!-- PRICING CARDS SECTION -->
        <section class="pb-24">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-3 gap-8 items-stretch">

                    <!-- Starter Plan (10,000) -->
                    <div class="reveal-on-scroll delay-100 bg-white dark:bg-slate-800/60 p-8 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-xl hover:shadow-2xl transition-all duration-300 flex flex-col">
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Starter</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-6 text-sm">Essential tools for small clinics.</p>
                        <div class="flex items-baseline mb-8">
                            <span class="text-4xl font-extrabold text-slate-900 dark:text-white" x-text="formatPrice('starter')"></span>
                            <span class="ml-2 text-slate-500 dark:text-slate-400 font-bold">FCFA</span>
                            <span class="text-slate-500 text-sm" x-text="billingCycle === 'monthly' ? '/mo' : '/yr'"></span>
                        </div>
                        <ul class="space-y-4 mb-8 flex-grow">
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-slate-700 dark:text-slate-300"><strong>6 Users</strong> Included</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-slate-700 dark:text-slate-300"><strong>1GB</strong> Cloud Storage</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-slate-700 dark:text-slate-300">Basic Patient Management</span>
                            </li>
                        </ul>
                        <a href="#" class="w-full block text-center px-6 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-colors">
                            {{ __('pricing.start_trial') }}
                        </a>
                    </div>

                    <!-- Standard Plan (15,000 - Popular) -->
                    <div class="reveal-on-scroll delay-200 relative bg-white dark:bg-slate-800/60 p-8 rounded-3xl border-2 border-blue-500 shadow-2xl hover:shadow-3xl transition-all duration-300 scale-105 flex flex-col z-10">
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <span class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-1 rounded-full text-sm font-bold uppercase tracking-tighter">Most Popular</span>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Standard</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-6 text-sm">Advanced features for growing practices.</p>
                        <div class="flex items-baseline mb-8">
                            <span class="text-4xl font-extrabold text-slate-900 dark:text-white" x-text="formatPrice('standard')"></span>
                            <span class="ml-2 text-slate-500 dark:text-slate-400 font-bold">FCFA</span>
                            <span class="text-slate-500 text-sm" x-text="billingCycle === 'monthly' ? '/mo' : '/yr'"></span>
                        </div>
                        <ul class="space-y-4 mb-8 flex-grow">
                            <li class="flex items-start gap-3 text-blue-600 dark:text-blue-400 font-semibold">
                                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                <span>AI Capability Included</span>
                            </li>
                            <li class="flex items-start gap-3 text-blue-600 dark:text-blue-400 font-semibold">
                                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                <span>Revenue Dashboard</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-slate-700 dark:text-slate-300"><strong>20 Users</strong> Included</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-slate-700 dark:text-slate-300"><strong>5GB</strong> Cloud Storage</span>
                            </li>
                        </ul>
                        <a href="#" class="w-full block text-center px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-colors shadow-lg shadow-blue-500/30">
                            {{ __('pricing.start_trial') }}
                        </a>
                    </div>

                    <!-- Enterprise Plan (100,000) -->
                    <div class="reveal-on-scroll delay-300 bg-white dark:bg-slate-800/60 p-8 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-xl hover:shadow-2xl transition-all duration-300 flex flex-col">
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Enterprise</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-6 text-sm">Full power for large medical institutions.</p>
                        <div class="flex items-baseline mb-8">
                            <span class="text-4xl font-extrabold text-slate-900 dark:text-white" x-text="formatPrice('enterprise')"></span>
                            <span class="ml-2 text-slate-500 dark:text-slate-400 font-bold">FCFA</span>
                        </div>
                        <ul class="space-y-4 mb-8 flex-grow">
                            <li class="flex items-start gap-3 text-purple-600 dark:text-purple-400 font-semibold">
                                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                                <span>Advanced AI (Doctor & Admin)</span>
                            </li>
                            <li class="flex items-start gap-3 text-purple-600 dark:text-purple-400 font-semibold">
                                <svg class="w-6 h-6 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a2 2 0 11-4 0V4zM4 13a2 2 0 012-2h12a2 2 0 012 2v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5z"></path></svg>
                                <span>Custom Capabilities</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-slate-700 dark:text-slate-300"><strong>100 Users</strong> Included</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span class="text-slate-700 dark:text-slate-300"><strong>10GB</strong> Cloud Storage</span>
                            </li>
                        </ul>
                        <a href="#" class="w-full block text-center px-6 py-3 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-white text-white dark:text-slate-900 font-bold rounded-xl transition-colors">
                            {{ __('pricing.contact_sales') }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ SECTION (FIXED) -->
        <section class="py-24 bg-white/40 dark:bg-black/20 backdrop-blur-sm">
            <div class="max-w-3xl mx-auto px-6">
                <h2 class="text-3xl font-bold text-center mb-12 text-slate-900 dark:text-white">{{ __('pricing.faq_title') }}</h2>
                <div class="space-y-4">
                    <!-- FAQ 1 -->
                    <div class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                        <button @click="activeFaq = activeFaq === 1 ? null : 1" class="w-full p-6 flex justify-between items-center text-left">
                            <span class="font-semibold text-slate-900 dark:text-white">{{ __('pricing.faq_1_q') }}</span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform duration-300" :class="activeFaq === 1 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="activeFaq === 1" x-collapse x-cloak class="px-6 pb-6 text-slate-600 dark:text-slate-300">
                            {{ __('pricing.faq_1_a') }}
                        </div>
                    </div>
                    <!-- FAQ 2 -->
                    <div class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                        <button @click="activeFaq = activeFaq === 2 ? null : 2" class="w-full p-6 flex justify-between items-center text-left">
                            <span class="font-semibold text-slate-900 dark:text-white">{{ __('pricing.faq_2_q') }}</span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform duration-300" :class="activeFaq === 2 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="activeFaq === 2" x-collapse x-cloak class="px-6 pb-6 text-slate-600 dark:text-slate-300">
                            {{ __('pricing.faq_2_a') }}
                        </div>
                    </div>
                    <!-- FAQ 3 -->
                    <div class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                        <button @click="activeFaq = activeFaq === 3 ? null : 3" class="w-full p-6 flex justify-between items-center text-left">
                            <span class="font-semibold text-slate-900 dark:text-white">{{ __('pricing.faq_3_q') }}</span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform duration-300" :class="activeFaq === 3 ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="activeFaq === 3" x-collapse x-cloak class="px-6 pb-6 text-slate-600 dark:text-slate-300">
                            {{ __('pricing.faq_3_a') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA SECTION -->
        <section class="py-24">
            <div class="max-w-5xl mx-auto px-6">
                <div class="relative rounded-[2.5rem] bg-gradient-to-br from-blue-600 to-indigo-700 overflow-hidden px-6 py-16 sm:px-12 sm:py-20 text-center shadow-2xl shadow-blue-900/40 reveal-on-scroll">
                    <div class="absolute top-0 left-0 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-pulse"></div>
                    <div class="absolute bottom-0 right-0 translate-x-1/2 translate-y-1/2 w-96 h-96 bg-purple-500/30 rounded-full blur-3xl"></div>

                    <h2 class="relative text-3xl font-bold tracking-tight text-white sm:text-5xl mb-6">{{ __('pricing.cta_title') }}</h2>
                    <p class="relative text-xl text-blue-100 max-w-2xl mx-auto mb-10">{{ __('pricing.cta_subtitle') }}</p>

                    <div class="relative flex flex-col sm:flex-row justify-center gap-4">
                        <a href="#" class="rounded-full bg-white px-10 py-4 text-base font-bold text-blue-600 shadow-xl hover:bg-blue-50 transition-all duration-200 hover:scale-105">
                            {{ __('pricing.cta_trial') }}
                        </a>
                        <a href="#" class="rounded-full border border-white/30 bg-white/10 px-10 py-4 text-base font-bold text-white backdrop-blur-sm hover:bg-white/20 transition-all duration-200 hover:scale-105">
                            {{ __('pricing.cta_demo') }}
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
        .circles li:nth-child(2) { left: 10%; width: 20px; height: 20px; animation-delay: 2s; animation-duration: 12s; }
        .circles li:nth-child(6) { left: 75%; width: 110px; height: 110px; animation-delay: 3s; }
        @keyframes animate { 0% { transform: translateY(0) rotate(0deg) scale(1); opacity: 0.8; } 100% { transform: translateY(-1000px) rotate(720deg) scale(1.5); opacity: 0; } }
        .reveal-on-scroll { opacity: 0; transform: translateY(30px); transition: all 0.8s ease-out; }
        .reveal-on-scroll.animate-in { opacity: 1; transform: translateY(0); }
    </style>
</div>
