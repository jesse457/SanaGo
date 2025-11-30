
<div x-data="{
    mobileMenuOpen: false,
    darkMode: localStorage.getItem('darkMode') === 'true',
    teamOpen: null,
    valuesOpen: null,

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
                    Healthcare Management Revolution
                </div>

                <h1 class="reveal-on-scroll delay-100 text-5xl md:text-7xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-8 max-w-5xl">
                    About <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-500 to-blue-400">SanaGo</span>
                </h1>

                <p class="reveal-on-scroll delay-200 mt-4 text-lg md:text-xl text-slate-600 dark:text-slate-300 max-w-2xl mb-12 leading-relaxed">
                    Transforming healthcare management through innovative technology solutions designed specifically for the unique challenges of modern hospitals and healthcare facilities.
                </p>

                <div class="reveal-on-scroll delay-300 flex flex-col sm:flex-row gap-5 items-center w-full justify-center">
                    <a href="#mission" class="group relative inline-flex h-14 items-center justify-center overflow-hidden rounded-full bg-blue-600 px-10 font-bold text-white duration-300 hover:bg-blue-700 shadow-[0_10px_40px_-10px_rgba(37,99,235,0.5)] hover:shadow-[0_20px_40px_-10px_rgba(37,99,235,0.7)] hover:-translate-y-1 w-full sm:w-auto">
                        <span class="mr-2">Our Mission</span>
                        <svg class="w-5 h-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                    <a href="#team" class="inline-flex h-14 items-center justify-center rounded-full px-10 font-bold text-slate-700 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200 border border-slate-200 dark:border-slate-700 w-full sm:w-auto">
                        Meet Our Team
                    </a>
                </div>
            </div>
        </section>

        <!-- MISSION SECTION -->
        <section id="mission" class="py-24 bg-white/40 dark:bg-black/20 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div class="reveal-on-scroll">
                        <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-6">Our Mission</h2>
                        <p class="text-lg text-slate-600 dark:text-slate-300 mb-6 leading-relaxed">
                            At SanaGo, we're dedicated to revolutionizing healthcare management through innovative technology. Our mission is to empower healthcare providers with tools that streamline operations, enhance patient care, and improve overall efficiency.
                        </p>
                        <p class="text-lg text-slate-600 dark:text-slate-300 mb-8 leading-relaxed">
                            We believe that technology should serve healthcare professionals, not complicate their work. That's why we've developed a comprehensive system that addresses the unique challenges faced by hospitals and healthcare facilities today.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex items-start gap-3">
                                <div class="mt-1 h-6 w-6 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white">Patient-Centered Design</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="mt-1 h-6 w-6 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-900 dark:text-white">Role-Based Access</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="reveal-on-scroll delay-100">
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                            <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" alt="Healthcare professionals using technology" class="w-full h-auto">
                            <div class="absolute inset-0 bg-gradient-to-t from-blue-600/20 to-transparent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- VALUES SECTION -->
        <section class="py-24">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16 reveal-on-scroll">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white">Our Values</h2>
                    <p class="mt-4 text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">The principles that guide everything we do at SanaGo</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="reveal-on-scroll delay-100 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl">
                        <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Security First</h3>
                        <p class="text-slate-600 dark:text-slate-300">We prioritize the security and privacy of patient data above all else, implementing industry-leading encryption and security measures.</p>
                    </div>

                    <div class="reveal-on-scroll delay-200 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl">
                        <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Efficiency</h3>
                        <p class="text-slate-600 dark:text-slate-300">Our solutions are designed to streamline workflows and eliminate redundant tasks, allowing healthcare professionals to focus on patient care.</p>
                    </div>

                    <div class="reveal-on-scroll delay-300 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl">
                        <div class="w-14 h-14 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Patient-Centered</h3>
                        <p class="text-slate-600 dark:text-slate-300">We believe that technology should ultimately improve the patient experience, from registration to discharge and beyond.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- TEAM SECTION -->
        <section id="team" class="py-24 bg-white/40 dark:bg-black/20 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16 reveal-on-scroll">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white">Leadership Team</h2>
                    <p class="mt-4 text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">The experts behind SanaGo's innovative healthcare solutions</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <div class="reveal-on-scroll delay-100 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl text-center">
                        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold">
                            JD
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Dr. James Davidson</h3>
                        <p class="text-blue-600 dark:text-blue-400 mb-4">Chief Executive Officer</p>
                        <p class="text-slate-600 dark:text-slate-300">Former hospital administrator with 15+ years of healthcare management experience. Passionate about technology's potential to transform healthcare delivery.</p>
                    </div>

                    <div class="reveal-on-scroll delay-200 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl text-center">
                        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold">
                            SR
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Sarah Rodriguez</h3>
                        <p class="text-blue-600 dark:text-blue-400 mb-4">Chief Technology Officer</p>
                        <p class="text-slate-600 dark:text-slate-300">Software engineer with expertise in healthcare systems and data security. Leads our development team in creating innovative solutions for healthcare challenges.</p>
                    </div>

                    <div class="reveal-on-scroll delay-300 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl text-center">
                        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white text-3xl font-bold">
                            MC
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Michael Chen</h3>
                        <p class="text-blue-600 dark:text-blue-400 mb-4">Chief Medical Officer</p>
                        <p class="text-slate-600 dark:text-slate-300">Practicing physician with a background in health informatics. Ensures our solutions meet the real-world needs of healthcare providers and their patients.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- STATS SECTION -->
        <section class="py-24">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-4 gap-8 text-center">
                    <div class="reveal-on-scroll delay-100">
                        <div class="text-4xl md:text-5xl font-bold text-blue-600 dark:text-blue-400 mb-2">500+</div>
                        <p class="text-slate-600 dark:text-slate-300">Healthcare Facilities</p>
                    </div>
                    <div class="reveal-on-scroll delay-200">
                        <div class="text-4xl md:text-5xl font-bold text-blue-600 dark:text-blue-400 mb-2">50K+</div>
                        <p class="text-slate-600 dark:text-slate-300">Healthcare Professionals</p>
                    </div>
                    <div class="reveal-on-scroll delay-300">
                        <div class="text-4xl md:text-5xl font-bold text-blue-600 dark:text-blue-400 mb-2">2M+</div>
                        <p class="text-slate-600 dark:text-slate-300">Patients Managed</p>
                    </div>
                    <div class="reveal-on-scroll delay-400">
                        <div class="text-4xl md:text-5xl font-bold text-blue-600 dark:text-blue-400 mb-2">99.9%</div>
                        <p class="text-slate-600 dark:text-slate-300">System Uptime</p>
                    </div>
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

                    <h2 class="relative text-3xl font-bold tracking-tight text-white sm:text-5xl mb-6">Ready to Transform Your Healthcare Facility?</h2>
                    <p class="relative text-xl text-blue-100 max-w-2xl mx-auto mb-10">Join the hundreds of healthcare facilities already using SanaGo to streamline operations and improve patient care.</p>

                    <div class="relative flex flex-col sm:flex-row justify-center gap-4">
                        <a href="#" class="rounded-full bg-white px-10 py-4 text-base font-bold text-blue-600 shadow-xl hover:bg-blue-50 transition-all duration-200 hover:scale-105">
                            Request a Demo
                        </a>
                        <a href="#" class="rounded-full border border-white/30 bg-white/10 px-10 py-4 text-base font-bold text-white backdrop-blur-sm hover:bg-white/20 transition-all duration-200 hover:scale-105">
                            Contact Sales
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
