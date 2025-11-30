<div x-data="{
    mobileMenuOpen: false,
    darkMode: localStorage.getItem('darkMode') === 'true',
    billingCycle: 'monthly',

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
}"
    class="min-h-screen relative overflow-x-hidden font-sans antialiased selection:bg-blue-500 selection:text-white text-slate-900 dark:text-slate-50">

    <!-- BACKGROUNDS & BUBBLE ANIMATION -->
    <div class="fixed inset-0 bg-[#f8fafc] -z-50 transition-colors duration-500 dark:hidden">
        <div
            class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-blue-50/50 via-purple-50/30 to-white">
        </div>
    </div>
    <div class="fixed inset-0 bg-[#0B1120] -z-50 transition-colors duration-500 hidden dark:block"></div>
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
    <div class="fixed inset-0 opacity-[0.04] dark:opacity-[0.07] pointer-events-none -z-30 mix-blend-overlay"
        style="background-image: url('https://grainy-gradients.vercel.app/noise.svg')"></div>

    <livewire:component.navbar />

    <main class="pt-[72px]">
        <!-- HERO SECTION -->
        <section class="relative pt-20 pb-32 overflow-hidden">
            <div class="relative max-w-7xl mx-auto px-6 flex flex-col items-center text-center z-10">
                <div
                    class="reveal-on-scroll inline-flex items-center rounded-full border border-blue-100 dark:border-blue-900 bg-blue-50/80 dark:bg-blue-900/30 px-4 py-1.5 text-sm font-medium text-blue-700 dark:text-blue-200 mb-8 backdrop-blur-md shadow-sm">
                    <span class="flex h-2 w-2 rounded-full bg-blue-500 mr-2 animate-pulse"></span>
                    Simple, Transparent Pricing
                </div>

                <h1
                    class="reveal-on-scroll delay-100 text-5xl md:text-7xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-8 max-w-5xl">
                    Plans for Every <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-500 to-blue-400">Healthcare
                        Facility</span>
                </h1>

                <p
                    class="reveal-on-scroll delay-200 mt-4 text-lg md:text-xl text-slate-600 dark:text-slate-300 max-w-2xl mb-12 leading-relaxed">
                    Choose the perfect plan for your clinic or hospital. All plans include our core features with
                    role-based access for your entire team.
                </p>

                <!-- Billing Toggle -->
                <div class="reveal-on-scroll delay-300 flex items-center justify-center gap-4 mb-16">
                    <span class="text-slate-700 dark:text-slate-300 font-medium">Monthly</span>
                    <button @click="billingCycle = billingCycle === 'monthly' ? 'annual' : 'monthly'"
                        class="relative inline-flex h-8 w-14 items-center rounded-full bg-slate-200 dark:bg-slate-700 transition-colors">
                        <span
                            class="inline-block h-6 w-6 transform rounded-full bg-white shadow-lg transition-transform"
                            :class="billingCycle === 'annual' ? 'translate-x-7' : 'translate-x-1'"></span>
                    </button>
                    <span class="text-slate-700 dark:text-slate-300 font-medium">Annual (Save 20%)</span>
                </div>
            </div>
        </section>

        <!-- PRICING CARDS SECTION -->
        <section class="pb-24">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-3 gap-8 items-start">
                    <!-- Starter Plan -->
                    <div
                        class="reveal-on-scroll delay-100 bg-white dark:bg-slate-800/60 p-8 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Starter</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-6">Perfect for small clinics and private
                            practices.</p>
                        <div class="flex items-baseline mb-8">
                            <span class="text-5xl font-extrabold text-slate-900 dark:text-white"
                                x-text="billingCycle === 'monthly' ? '$199' : '$159'"></span>
                            <span class="text-slate-500 dark:text-slate-400 font-medium">/month</span>
                        </div>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-slate-700 dark:text-slate-300">Up to 10 Users</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-slate-700 dark:text-slate-300">All 6 Role-Based Dashboards</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-slate-700 dark:text-slate-300">Patient & Appointment Management</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-slate-700 dark:text-slate-300">Basic Reporting & Analytics</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-slate-700 dark:text-slate-300">Email Support</span>
                            </li>
                        </ul>
                        <a href="#"
                            class="w-full block text-center px-6 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-bold rounded-xl transition-colors">
                            Start Free Trial
                        </a>
                    </div>

                    <!-- Professional Plan (Popular) -->
                    <div
                        class="reveal-on-scroll delay-200 relative bg-white dark:bg-slate-800/60 p-8 rounded-3xl border-2 border-blue-500 shadow-2xl hover:shadow-3xl transition-all duration-300 hover:-translate-y-2 scale-105">
                        <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                            <span
                                class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-4 py-1 rounded-full text-sm font-bold">Most
                                Popular</span>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Professional</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-6">Ideal for medium-sized hospitals and medical
                            centers.</p>
                        <div class="flex items-baseline mb-8">
                            <span class="text-5xl font-extrabold text-slate-900 dark:text-white"
                                x-text="billingCycle === 'monthly' ? '$499' : '$399'"></span>
                            <span class="text-slate-500 dark:text-slate-400 font-medium">/month</span>
                        </div>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-slate-700 dark:text-slate-300">Up to 50 Users</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-slate-700 dark:text-slate-300">Everything in Starter, plus:</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-slate-700 dark:text-slate-300">Advanced Revenue Dashboard</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-slate-700 dark:text-slate-300">Shift & Bed Management</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-slate-700 dark:text-slate-300">Inventory & Supply Tracking</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-slate-700 dark:text-slate-300">Priority Phone Support</span>
                            </li>
                        </ul>
                        <a href="#"
                            class="w-full block text-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-colors shadow-lg">
                            Start Free Trial
                        </a>
                    </div>

                    <!-- Enterprise Plan -->
                    <div
                        class="reveal-on-scroll delay-300 bg-white dark:bg-slate-800/60 p-8 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-2">
                        <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Enterprise</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-6">Tailored for large hospital networks and
                            healthcare enterprises.</p>
                        <div class="flex items-baseline mb-8">
                            <span class="text-4xl font-extrabold text-slate-900 dark:text-white">Custom</span>
                        </div>
                        <ul class="space-y-4 mb-8">
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-slate-700 dark:text-slate-300">Unlimited Users</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-slate-700 dark:text-slate-300">Everything in Professional,
                                    plus:</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-slate-700 dark:text-slate-300">Custom Feature Development</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-slate-700 dark:text-slate-300">Advanced API Access</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-slate-700 dark:text-slate-300">Dedicated Account Manager</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-6 h-6 text-emerald-500 mt-0.5 shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-slate-700 dark:text-slate-300">24/7 Dedicated Support</span>
                            </li>
                        </ul>
                        <a href="#"
                            class="w-full block text-center px-6 py-3 bg-slate-900 dark:bg-slate-100 hover:bg-slate-800 dark:hover:bg-white text-white dark:text-slate-900 font-bold rounded-xl transition-colors">
                            Contact Sales
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ SECTION -->
        <section class="py-24 bg-white/40 dark:bg-black/20 backdrop-blur-sm">
            <div class="max-w-3xl mx-auto px-6">
                <h2 class="text-3xl font-bold text-center mb-12 text-slate-900 dark:text-white">Frequently Asked
                    Questions</h2>
                <div class="space-y-4">
                    <div
                        class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                        <button class="w-full p-6 flex justify-between items-center text-left">
                            <span class="font-semibold text-slate-900 dark:text-white">Is there a setup fee?</span>
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="px-6 pb-6 text-slate-600 dark:text-slate-300">
                            No, there are no setup fees for any of our plans. You can start your free trial instantly
                            without any hidden charges.
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                        <button class="w-full p-6 flex justify-between items-center text-left">
                            <span class="font-semibold text-slate-900 dark:text-white">Can I change my plan
                                later?</span>
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="px-6 pb-6 text-slate-600 dark:text-slate-300">
                            Absolutely! You can upgrade or downgrade your plan at any time from your account settings.
                            Changes will be reflected in your next billing cycle.
                        </div>
                    </div>
                    <div
                        class="bg-white dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                        <button class="w-full p-6 flex justify-between items-center text-left">
                            <span class="font-semibold text-slate-900 dark:text-white">How secure is my patient
                                data?</span>
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="px-6 pb-6 text-slate-600 dark:text-slate-300">
                            Security is our top priority. We use industry-standard encryption for data at rest and in
                            transit. Our system is HIPAA compliant and we conduct regular security audits.
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA SECTION -->
        <section class="py-24">
            <div class="max-w-5xl mx-auto px-6">
                <div
                    class="relative rounded-[2.5rem] bg-gradient-to-br from-blue-600 to-indigo-700 overflow-hidden px-6 py-16 sm:px-12 sm:py-20 text-center shadow-2xl shadow-blue-900/40 reveal-on-scroll">
                    <div
                        class="absolute top-0 left-0 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-white/10 rounded-full blur-3xl animate-pulse">
                    </div>
                    <div
                        class="absolute bottom-0 right-0 translate-x-1/2 translate-y-1/2 w-96 h-96 bg-purple-500/30 rounded-full blur-3xl">
                    </div>

                    <h2 class="relative text-3xl font-bold tracking-tight text-white sm:text-5xl mb-6">Ready to Get
                        Started?</h2>
                    <p class="relative text-xl text-blue-100 max-w-2xl mx-auto mb-10">Join hundreds of healthcare
                        facilities that trust SanaGo to manage their operations efficiently and securely.</p>

                    <div class="relative flex flex-col sm:flex-row justify-center gap-4">
                        <a href="#"
                            class="rounded-full bg-white px-10 py-4 text-base font-bold text-blue-600 shadow-xl hover:bg-blue-50 transition-all duration-200 hover:scale-105">
                            Start Your Free Trial
                        </a>
                        <a href="#"
                            class="rounded-full border border-white/30 bg-white/10 px-10 py-4 text-base font-bold text-white backdrop-blur-sm hover:bg-white/20 transition-all duration-200 hover:scale-105">
                            Schedule a Demo
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <livewire:component.footer />

    <style>
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
    </style>
</div>
