
<div x-data="{
    mobileMenuOpen: false,
    darkMode: localStorage.getItem('darkMode') === 'true',
    selectedCategory: 'all',
    searchQuery: '',

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
                    {{ __('blog.badge') }}
                </div>

                <h1 class="reveal-on-scroll delay-100 text-5xl md:text-7xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-8 max-w-5xl">
                    {{ __('blog.title_prefix') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-500 to-blue-400">{{ __('blog.title_suffix') }}</span>
                </h1>

                <p class="reveal-on-scroll delay-200 mt-4 text-lg md:text-xl text-slate-600 dark:text-slate-300 max-w-2xl mb-12 leading-relaxed">
                    {{ __('blog.subtitle') }}
                </p>

                <div class="reveal-on-scroll delay-300 w-full max-w-md">
                    <div class="relative">
                        <input type="text" x-model="searchQuery" placeholder="{{ __('blog.search_placeholder') }}" class="w-full px-5 py-3 pl-12 rounded-full bg-white/80 dark:bg-slate-800/80 backdrop-blur-md border border-slate-200 dark:border-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400">
                        <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
            </div>
        </section>

        <!-- CATEGORIES SECTION -->
        <section class="py-8">
            <div class="max-w-7xl mx-auto px-6">
                <div class="flex flex-wrap justify-center gap-3 reveal-on-scroll">
                    <button @click="selectedCategory = 'all'" class="px-5 py-2 rounded-full font-medium transition-colors" :class="selectedCategory === 'all' ? 'bg-blue-600 text-white' : 'bg-white/80 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'">
                        {{ __('blog.cat_all') }}
                    </button>
                    <button @click="selectedCategory = 'healthcare-tech'" class="px-5 py-2 rounded-full font-medium transition-colors" :class="selectedCategory === 'healthcare-tech' ? 'bg-blue-600 text-white' : 'bg-white/80 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'">
                        {{ __('blog.cat_tech') }}
                    </button>
                    <button @click="selectedCategory = 'patient-care'" class="px-5 py-2 rounded-full font-medium transition-colors" :class="selectedCategory === 'patient-care' ? 'bg-blue-600 text-white' : 'bg-white/80 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'">
                        {{ __('blog.cat_patient') }}
                    </button>
                    <button @click="selectedCategory = 'hospital-management'" class="px-5 py-2 rounded-full font-medium transition-colors" :class="selectedCategory === 'hospital-management' ? 'bg-blue-600 text-white' : 'bg-white/80 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'">
                        {{ __('blog.cat_management') }}
                    </button>
                    <button @click="selectedCategory = 'company-news'" class="px-5 py-2 rounded-full font-medium transition-colors" :class="selectedCategory === 'company-news' ? 'bg-blue-600 text-white' : 'bg-white/80 dark:bg-slate-800/80 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'">
                        {{ __('blog.cat_news') }}
                    </button>
                </div>
            </div>
        </section>

        <!-- FEATURED POST SECTION -->
        <section class="py-12 bg-white/40 dark:bg-black/20 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div class="reveal-on-scroll order-2 md:order-1">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-sm font-medium">{{ __('blog.featured_badge') }}</span>
                            <span class="text-slate-500 dark:text-slate-400 text-sm">June 15, 2023</span>
                        </div>
                        <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-4">The Future of Hospital Management: AI and Automation</h2>
                        <p class="text-lg text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
                            Explore how artificial intelligence and automation are transforming hospital operations, from patient scheduling to resource allocation, and what this means for healthcare providers.
                        </p>
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                JD
                            </div>
                            <div>
                                <p class="font-medium text-slate-900 dark:text-white">Dr. James Davidson</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">Chief Executive Officer</p>
                            </div>
                        </div>
                        <a href="#" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl transition-colors">
                            {{ __('blog.read_article') }}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                    <div class="reveal-on-scroll order-1 md:order-2">
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                            <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" alt="AI in healthcare" class="w-full h-auto">
                            <div class="absolute inset-0 bg-gradient-to-t from-blue-600/20 to-transparent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <!-- RECENT POSTS SECTION -->
    <section class="py-24">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16 reveal-on-scroll">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white">{{ __('blog.recent_posts_title') }}</h2>
                <p class="mt-4 text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">{{ __('blog.recent_posts_subtitle') }}</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Blog Post 1 -->
                <div class="reveal-on-scroll delay-100 bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                    <div class="aspect-w-16 aspect-h-9">
                        <img src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" alt="Data security in healthcare" class="w-full h-48 object-cover">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-full text-xs font-medium">{{ __('blog.cat_tech') }}</span>
                            <span class="text-slate-500 dark:text-slate-400 text-xs">June 10, 2023</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Ensuring Data Security in Hospital Management Systems</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-4">Best practices for protecting sensitive patient data in digital hospital environments, including encryption and access control measures.</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                    SR
                                </div>
                                <span class="text-sm text-slate-600 dark:text-slate-400">Sarah Rodriguez</span>
                            </div>
                            <a href="#" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-sm">{{ __('blog.read_more') }}</a>
                        </div>
                    </div>
                </div>

                <!-- Blog Post 2 -->
                <div class="reveal-on-scroll delay-200 bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                    <div class="aspect-w-16 aspect-h-9">
                        <img src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" alt="Patient experience" class="w-full h-48 object-cover">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full text-xs font-medium">{{ __('blog.cat_patient') }}</span>
                            <span class="text-slate-500 dark:text-slate-400 text-xs">June 5, 2023</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Improving Patient Experience Through Digital Solutions</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-4">How technology can streamline patient journeys from registration to discharge, leading to higher satisfaction and better outcomes.</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                    MC
                                </div>
                                <span class="text-sm text-slate-600 dark:text-slate-400">Michael Chen</span>
                            </div>
                            <a href="#" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-sm">{{ __('blog.read_more') }}</a>
                        </div>
                    </div>
                </div>

                <!-- Blog Post 3 -->
                <div class="reveal-on-scroll delay-300 bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                    <div class="aspect-w-16 aspect-h-9">
                        <img src="https://images.unsplash.com/photo-1551190822-a9333d879b1f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" alt="Role-based access control" class="w-full h-48 object-cover">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-xs font-medium">{{ __('blog.cat_management') }}</span>
                            <span class="text-slate-500 dark:text-slate-400 text-xs">May 28, 2023</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">The Benefits of Role-Based Access in Healthcare Settings</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-4">How role-based access control systems enhance security, streamline workflows, and ensure compliance in healthcare organizations.</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                    JD
                                </div>
                                <span class="text-sm text-slate-600 dark:text-slate-400">Dr. James Davidson</span>
                            </div>
                            <a href="#" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-sm">{{ __('blog.read_more') }}</a>
                        </div>
                    </div>
                </div>

                <!-- Blog Post 4 -->
                <div class="reveal-on-scroll delay-100 bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                    <div class="aspect-w-16 aspect-h-9">
                        <img src="https://images.unsplash.com/photo-1516549655169-df83a0774514?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" alt="Mobile healthcare" class="w-full h-48 object-cover">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="px-3 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 rounded-full text-xs font-medium">{{ __('blog.cat_tech') }}</span>
                            <span class="text-slate-500 dark:text-slate-400 text-xs">May 20, 2023</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Mobile-First Design for Healthcare Applications</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-4">Why mobile-first design is crucial for modern healthcare applications and how it improves accessibility for healthcare professionals.</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                    SR
                                </div>
                                <span class="text-sm text-slate-600 dark:text-slate-400">Sarah Rodriguez</span>
                            </div>
                            <a href="#" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-sm">{{ __('blog.read_more') }}</a>
                        </div>
                    </div>
                </div>

                <!-- Blog Post 5 -->
                <div class="reveal-on-scroll delay-200 bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                    <div class="aspect-w-16 aspect-h-9">
                        <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" alt="Interoperability in healthcare" class="w-full h-48 object-cover">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-xs font-medium">{{ __('blog.cat_management') }}</span>
                            <span class="text-slate-500 dark:text-slate-400 text-xs">May 15, 2023</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Interoperability Challenges in Healthcare Systems</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-4">The importance of interoperability between different healthcare systems and how to overcome common implementation challenges.</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                    MC
                                </div>
                                <span class="text-sm text-slate-600 dark:text-slate-400">Michael Chen</span>
                            </div>
                            <a href="#" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-sm">{{ __('blog.read_more') }}</a>
                        </div>
                    </div>
                </div>

                <!-- Blog Post 6 -->
                <div class="reveal-on-scroll delay-300 bg-white dark:bg-slate-800/60 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden hover:shadow-2xl transition-shadow duration-300">
                    <div class="aspect-w-16 aspect-h-9">
                        <img src="https://images.unsplash.com/photo-1515377905703-c4788e51af15?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" alt="Company news" class="w-full h-48 object-cover">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-3">
                            <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 rounded-full text-xs font-medium">{{ __('blog.cat_news') }}</span>
                            <span class="text-slate-500 dark:text-slate-400 text-xs">May 10, 2023</span>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">SanaGo Announces New Partnership with Leading Hospital Chain</h3>
                        <p class="text-slate-600 dark:text-slate-300 mb-4">We're excited to announce our partnership with HealthFirst Hospitals to implement our hospital management system across their 15 facilities.</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-xs font-bold">
                                    JD
                                </div>
                                <span class="text-sm text-slate-600 dark:text-slate-400">Dr. James Davidson</span>
                            </div>
                            <a href="#" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium text-sm">{{ __('blog.read_more') }}</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-12 text-center reveal-on-scroll">
                <button class="inline-flex items-center gap-2 px-8 py-3 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-medium rounded-xl transition-colors">
                    {{ __('blog.load_more') }}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
            </div>
        </div>
    </section>

    <!-- NEWSLETTER SECTION -->
    <section class="py-24 bg-white/40 dark:bg-black/20 backdrop-blur-sm">
        <div class="max-w-4xl mx-auto px-6">
            <div class="bg-white dark:bg-slate-800/60 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-2xl p-8 md:p-12 text-center reveal-on-scroll">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-6">
                    ✉️
                </div>
                <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-4">{{ __('blog.newsletter_title') }}</h2>
                <p class="text-lg text-slate-600 dark:text-slate-300 mb-8">{{ __('blog.newsletter_desc') }}</p>
                <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                    <input type="email" placeholder="{{ __('blog.email_placeholder') }}" class="flex-1 px-5 py-3 rounded-full bg-white/80 dark:bg-slate-700/80 backdrop-blur-md border border-slate-200 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400">
                    <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-full transition-colors">
                        {{ __('blog.subscribe_btn') }}
                    </button>
                </form>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-4">{{ __('blog.privacy_note') }}</p>
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
</style></div>
