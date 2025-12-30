<div x-data="{
    mobileMenuOpen: false,
    darkMode: localStorage.getItem('darkMode') === 'true',
    billingCycle: 'monthly',
    activeFaq: null,
    prices: { starter: 10000, standard: 15000, enterprise: 100000 },

    init() {
        window.addEventListener('theme-changed', (e) => { this.darkMode = e.detail; });

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
        if (this.billingCycle === 'annual') { amount = amount * 10; }
        return new Intl.NumberFormat('fr-FR').format(amount);
    },

    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        this.darkMode ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark');
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: this.darkMode }));
    }
}" class="min-h-screen relative overflow-x-hidden font-sans antialiased selection:bg-blue-500 selection:text-white text-slate-900 dark:text-slate-50">

    <div class="fixed inset-0 bg-[#f8fafc] -z-50 transition-colors duration-500 dark:hidden">
        <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-blue-50/50 via-purple-50/30 to-white"></div>
    </div>
    <div class="fixed inset-0 bg-[#0B1120] -z-50 transition-colors duration-500 hidden dark:block"></div>
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-40">
        <ul class="circles"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul>
    </div>
    <div class="fixed inset-0 opacity-[0.04] dark:opacity-[0.07] pointer-events-none -z-30 mix-blend-overlay" style="background-image: url('https://grainy-gradients.vercel.app/noise.svg')"></div>



    <main class="pt-[72px]">
        <!-- HERO SECTION -->
        <section class="relative pt-20 pb-24 overflow-hidden">
            <div class="relative max-w-7xl mx-auto px-6 flex flex-col items-center text-center z-10">
                <div class="reveal-on-scroll inline-flex items-center rounded-full border border-blue-100 dark:border-blue-900 bg-blue-50/80 dark:bg-blue-900/30 px-4 py-1.5 text-sm font-medium text-blue-700 dark:text-blue-200 mb-8 backdrop-blur-md shadow-sm">
                    <span class="flex h-2 w-2 rounded-full bg-blue-500 mr-2 animate-pulse"></span> {{ __('pricing.badge') }}
                </div>
                <h1 class="reveal-on-scroll delay-100 text-5xl md:text-7xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-8 max-w-5xl">{{ __('pricing.title') }}</h1>

                <!-- Billing Toggle -->
                <div class="reveal-on-scroll delay-300 flex flex-col items-center gap-4 mb-16">
                    <div class="flex items-center justify-center gap-4">
                        <span class="text-slate-700 dark:text-slate-300 font-medium">{{ __('pricing.monthly') }}</span>
                        <button @click="billingCycle = billingCycle === 'monthly' ? 'annual' : 'monthly'" class="relative inline-flex h-8 w-14 items-center rounded-full bg-slate-200 dark:bg-slate-700 transition-colors">
                            <span class="inline-block h-6 w-6 transform rounded-full bg-white shadow-lg transition-transform" :class="billingCycle === 'annual' ? 'translate-x-7' : 'translate-x-1'"></span>
                        </button>
                        <span class="text-slate-700 dark:text-slate-300 font-medium">{{ __('pricing.annual') }}</span>
                    </div>
                    <div x-show="billingCycle === 'annual'" x-transition class="text-emerald-500 font-bold text-sm">🎉 2 Months Free applied!</div>
                </div>
            </div>
        </section>

        <!-- PRICING CARDS -->
        <section class="pb-24">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-3 gap-8 items-stretch">
                    <!-- Starter -->
                    <div class="reveal-on-scroll delay-100 bg-white dark:bg-slate-800/60 p-8 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-xl hover:shadow-2xl transition-all duration-300 flex flex-col">
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ __('pricing.starter_title') }}</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-6 text-sm">{{ __('pricing.starter_desc') }}</p>
                        <div class="flex items-baseline mb-8">
                            <span class="text-4xl font-extrabold text-slate-900 dark:text-white" x-text="formatPrice('starter')"></span>
                            <span class="ml-2 text-slate-500 dark:text-slate-400 font-bold">{{ __('pricing.currency') }}</span>
                            <span class="text-slate-500 text-sm" x-text="billingCycle === 'monthly' ? '{{ __('pricing.per_month') }}' : '{{ __('pricing.per_year') }}'"></span>
                        </div>
                        <ul class="space-y-4 mb-8 flex-grow">
                            <li class="flex items-start gap-3"><x-heroicon-s-check-circle class="w-6 h-6 text-emerald-500 shrink-0" /><span class="text-slate-700 dark:text-slate-300">{!! __('pricing.starter_users') !!}</span></li>
                            <li class="flex items-start gap-3"><x-heroicon-s-check-circle class="w-6 h-6 text-emerald-500 shrink-0" /><span class="text-slate-700 dark:text-slate-300">{!! __('pricing.starter_storage') !!}</span></li>
                            @foreach (__('pricing.starter_features') as $feature)
                                <li class="flex items-start gap-3"><x-heroicon-s-check-circle class="w-6 h-6 text-emerald-500 shrink-0" /><span class="text-slate-700 dark:text-slate-300">{{ $feature }}</span></li>
                            @endforeach
                        </ul>
                        <a href="#" class="w-full block text-center px-6 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-colors">{{ __('pricing.start_trial') }}</a>
                    </div>

                    <!-- Standard -->
                    <div class="reveal-on-scroll delay-200 relative bg-white dark:bg-slate-800/60 p-8 rounded-3xl border-2 border-blue-500 shadow-2xl hover:shadow-3xl transition-all duration-300 scale-105 flex flex-col z-10">
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <span class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-1 rounded-full text-sm font-bold uppercase tracking-tighter">{{ __('pricing.most_popular') }}</span>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ __('pricing.standard_title') }}</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-6 text-sm">{{ __('pricing.standard_desc') }}</p>
                        <div class="flex items-baseline mb-8">
                            <span class="text-4xl font-extrabold text-slate-900 dark:text-white" x-text="formatPrice('standard')"></span>
                            <span class="ml-2 text-slate-500 dark:text-slate-400 font-bold">{{ __('pricing.currency') }}</span>
                            <span class="text-slate-500 text-sm" x-text="billingCycle === 'monthly' ? '{{ __('pricing.per_month') }}' : '{{ __('pricing.per_year') }}'"></span>
                        </div>
                        <ul class="space-y-4 mb-8 flex-grow">
                            <li class="flex items-start gap-3 text-blue-600 dark:text-blue-400 font-semibold"><x-heroicon-s-sparkles class="w-6 h-6 shrink-0" /><span>AI Capability Included</span></li>
                            <li class="flex items-start gap-3"><x-heroicon-s-check-circle class="w-6 h-6 text-emerald-500 shrink-0" /><span class="text-slate-700 dark:text-slate-300">{!! __('pricing.standard_users') !!}</span></li>
                            <li class="flex items-start gap-3"><x-heroicon-s-check-circle class="w-6 h-6 text-emerald-500 shrink-0" /><span class="text-slate-700 dark:text-slate-300">{!! __('pricing.standard_storage') !!}</span></li>
                            @foreach (__('pricing.standard_features') as $feature)
                                <li class="flex items-start gap-3"><x-heroicon-s-check-circle class="w-6 h-6 text-emerald-500 shrink-0" /><span class="text-slate-700 dark:text-slate-300">{{ $feature }}</span></li>
                            @endforeach
                        </ul>
                        <a href="#" class="w-full block text-center px-6 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-colors shadow-lg shadow-blue-500/30">{{ __('pricing.start_trial') }}</a>
                    </div>

                    <!-- Enterprise -->
                    <div class="reveal-on-scroll delay-300 bg-white dark:bg-slate-800/60 p-8 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-xl hover:shadow-2xl transition-all duration-300 flex flex-col">
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ __('pricing.enterprise_title') }}</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-6 text-sm">{{ __('pricing.enterprise_desc') }}</p>
                        <div class="flex items-baseline mb-8">
                            <span class="text-4xl font-extrabold text-slate-900 dark:text-white" x-text="formatPrice('enterprise')"></span>
                            <span class="ml-2 text-slate-500 dark:text-slate-400 font-bold">{{ __('pricing.currency') }}</span>
                            <span class="text-slate-500 text-sm" x-text="billingCycle === 'monthly' ? '{{ __('pricing.per_month') }}' : '{{ __('pricing.per_year') }}'"></span>
                        </div>
                        <ul class="space-y-4 mb-8 flex-grow">
                            <li class="flex items-start gap-3 text-purple-600 dark:text-purple-400 font-semibold"><x-heroicon-s-cpu-chip class="w-6 h-6 shrink-0" /><span>Custom AI Diagnostics</span></li>
                            <li class="flex items-start gap-3"><x-heroicon-s-check-circle class="w-6 h-6 text-emerald-500 shrink-0" /><span class="text-slate-700 dark:text-slate-300">{!! __('pricing.enterprise_users') !!}</span></li>
                            @foreach (__('pricing.enterprise_features') as $feature)
                                <li class="flex items-start gap-3"><x-heroicon-s-check-circle class="w-6 h-6 text-emerald-500 shrink-0" /><span class="text-slate-700 dark:text-slate-300">{{ $feature }}</span></li>
                            @endforeach
                        </ul>
                        <a href="#" class="w-full block text-center px-6 py-3 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-white text-white dark:text-slate-900 font-bold rounded-xl transition-colors">{{ __('pricing.contact_sales') }}</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ SECTION -->
        <section class="py-24 bg-white/40 dark:bg-black/20 backdrop-blur-sm">
            <div class="max-w-3xl mx-auto px-6">
                <h2 class="text-3xl font-bold text-center mb-12 text-slate-900 dark:text-white">{{ __('pricing.faq_title') }}</h2>
                <div class="space-y-4">
                    @for($i = 1; $i <= 3; $i++)
                    <div class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                        <button @click="activeFaq = activeFaq === {{ $i }} ? null : {{ $i }}" class="w-full p-6 flex justify-between items-center text-left">
                            <span class="font-semibold text-slate-900 dark:text-white">{{ __('pricing.faq_'.$i.'_q') }}</span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform duration-300" :class="activeFaq === {{ $i }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="activeFaq === {{ $i }}" x-collapse x-cloak class="px-6 pb-6 text-slate-600 dark:text-slate-300">{{ __('pricing.faq_'.$i.'_a') }}</div>
                    </div>
                    @endfor
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
                        <a href="#" class="rounded-full bg-white px-10 py-4 text-base font-bold text-blue-600 shadow-xl hover:bg-blue-50 transition-all duration-200 hover:scale-105">{{ __('pricing.cta_trial') }}</a>
                        <a href="#" class="rounded-full border border-white/30 bg-white/10 px-10 py-4 text-base font-bold text-white backdrop-blur-sm hover:bg-white/20 transition-all duration-200 hover:scale-105">{{ __('pricing.cta_demo') }}</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

  
</div>
