
<div  x-data="{
    mobileMenuOpen: false,
    darkMode: localStorage.getItem('darkMode') === 'true',
    activeRole: null,

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

    <livewire:component.navbar />

    <main class="pt-[72px]">
        <!-- HERO SECTION -->
        <section class="relative pt-20 pb-32 overflow-hidden">
            <div class="relative max-w-7xl mx-auto px-6 flex flex-col items-center text-center z-10">
                <div class="reveal-on-scroll inline-flex items-center rounded-full border border-blue-100 dark:border-blue-900 bg-blue-50/80 dark:bg-blue-900/30 px-4 py-1.5 text-sm font-medium text-blue-700 dark:text-blue-200 mb-8 backdrop-blur-md shadow-sm">
                    <span class="flex h-2 w-2 rounded-full bg-blue-500 mr-2 animate-pulse"></span>
                    Comprehensive & Role-Based
                </div>

                <h1 class="reveal-on-scroll delay-100 text-5xl md:text-7xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-8 max-w-5xl">
                    A Feature for Every <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-500 to-blue-400">Healthcare Role</span>
                </h1>

                <p class="reveal-on-scroll delay-200 mt-4 text-lg md:text-xl text-slate-600 dark:text-slate-300 max-w-2xl mb-12 leading-relaxed">
                    Explore the powerful, specialized tools designed for each role within your hospital, all working together in one unified, secure system.
                </p>
            </div>
        </section>

        <!-- ROLE-BASED FEATURES SECTION -->
        <section class="py-24 bg-white/40 dark:bg-black/20 backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16 reveal-on-scroll">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white">Role-Based Dashboards</h2>
                    <p class="mt-4 text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">Click on a role to see its specific features and capabilities.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Administrator -->
                    <div @click="activeRole = activeRole === 'admin' ? null : 'admin'" class="reveal-on-scroll delay-100 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl cursor-pointer transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="p-3 rounded-xl bg-purple-100 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Administrator</h3>
                        </div>
                        <p class="text-slate-600 dark:text-slate-300">Full oversight of hospital operations, finance, staff, and system configuration.</p>
                        <div x-show="activeRole === 'admin'" x-transition class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <ul class="space-y-2 text-sm text-slate-700 dark:text-slate-300">
                                <li>• Revenue Dashboard & Analytics</li>
                                <li>• Staff Shift Management</li>
                                <li>• User & Role Management</li>
                                <li>• Department, Ward & Bed Configuration</li>
                                <li>• Comprehensive Activity Logs</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Doctor -->
                    <div @click="activeRole = activeRole === 'doctor' ? null : 'doctor'" class="reveal-on-scroll delay-200 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl cursor-pointer transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="p-3 rounded-xl bg-blue-100 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Doctor</h3>
                        </div>
                        <p class="text-slate-600 dark:text-slate-300">Tools for managing patient consultations, records, prescriptions, and lab orders.</p>
                        <div x-show="activeRole === 'doctor'" x-transition class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <ul class="space-y-2 text-sm text-slate-700 dark:text-slate-300">
                                <li>• Patient Dashboard & History</li>
                                <li>• Digital Consultation Notes</li>
                                <li>• e-Prescribing Module</li>
                                <li>• Lab Test Requests & Results</li>
                                <li>• Appointment Management</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Receptionist -->
                    <div @click="activeRole = activeRole === 'receptionist' ? null : 'receptionist'" class="reveal-on-scroll delay-300 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl cursor-pointer transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="p-3 rounded-xl bg-emerald-100 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Receptionist</h3>
                        </div>
                        <p class="text-slate-600 dark:text-slate-300">Manages the front desk, appointments, patient registration, and admissions.</p>
                        <div x-show="activeRole === 'receptionist'" x-transition class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <ul class="space-y-2 text-sm text-slate-700 dark:text-slate-300">
                                <li>• Appointment Scheduling</li>
                                <li>• Patient Registration</li>
                                <li>• Admission & Discharge Workflow</li>
                                <li>• Bed Assignment</li>
                                <li>• Billing & Payments</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Lab Technician -->
                    <div @click="activeRole = activeRole === 'lab' ? null : 'lab'" class="reveal-on-scroll delay-100 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl cursor-pointer transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="p-3 rounded-xl bg-yellow-100 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Lab Technician</h3>
                        </div>
                        <p class="text-slate-600 dark:text-slate-300">Handles lab test requests, records results, and manages the test catalog.</p>
                        <div x-show="activeRole === 'lab'" x-transition class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <ul class="space-y-2 text-sm text-slate-700 dark:text-slate-300">
                                <li>• Test Request Queue</li>
                                <li>• Result Entry & Management</li>
                                <li>• Lab Test Catalog</li>
                                <li>• Sample Tracking</li>
                                <li>• Automated Result Notifications</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Pharmacist -->
                    <div @click="activeRole = activeRole === 'pharmacist' ? null : 'pharmacist'" class="reveal-on-scroll delay-200 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl cursor-pointer transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="p-3 rounded-xl bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Pharmacist</h3>
                        </div>
                        <p class="text-slate-600 dark:text-slate-300">Dispenses medications, manages inventory, and processes prescriptions.</p>
                        <div x-show="activeRole === 'pharmacist'" x-transition class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <ul class="space-y-2 text-sm text-slate-700 dark:text-slate-300">
                                <li>• Prescription Fulfillment</li>
                                <li>• Drug Inventory Management</li>
                                <li>• Stock Level Alerts</li>
                                <li>• Expiry Tracking</li>
                                <li>• Sales Reports</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Nurse -->
                    <div @click="activeRole = activeRole === 'nurse' ? null : 'nurse'" class="reveal-on-scroll delay-300 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl cursor-pointer transition-all duration-300 hover:shadow-2xl hover:-translate-y-2">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="p-3 rounded-xl bg-indigo-100 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Nurse</h3>
                        </div>
                        <p class="text-slate-600 dark:text-slate-300">Records patient vitals, manages supplies, and provides direct patient care support.</p>
                        <div x-show="activeRole === 'nurse'" x-transition class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
                            <ul class="space-y-2 text-sm text-slate-700 dark:text-slate-300">
                                <li>• Vitals Recording & History</li>
                                <li>• Supply Usage Tracking</li>
                                <li>• Patient Care Management</li>
                                <li>• Shift Schedule View</li>
                                <li>• Ward Management</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SYSTEM-WIDE FEATURES SECTION -->
        <section class="py-24">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16 reveal-on-scroll">
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white">System-Wide Features</h2>
                    <p class="mt-4 text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">Powerful features that benefit everyone in your facility.</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="reveal-on-scroll delay-100 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl">
                        <div class="w-14 h-14 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Multi-language Support</h3>
                        <p class="text-slate-600 dark:text-slate-300">A translation button on each dashboard allows users to toggle between English and French, ensuring accessibility for a diverse staff.</p>
                    </div>

                    <div class="reveal-on-scroll delay-200 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl">
                        <div class="w-14 h-14 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Advanced Security</h3>
                        <p class="text-slate-600 dark:text-slate-300">Patient names and lab test names are encrypted. Role-based access control ensures users only see the information they need.</p>
                    </div>

                    <div class="reveal-on-scroll delay-300 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl">
                        <div class="w-14 h-14 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Instant Notifications</h3>
                        <p class="text-slate-600 dark:text-slate-300">Users receive a text message (SMS) when their lab results are completed, speeding up communication and patient care.</p>
                    </div>

                    <div class="reveal-on-scroll delay-100 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl">
                        <div class="w-14 h-14 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">User Profiles</h3>
                        <p class="text-slate-600 dark:text-slate-300">All users have a profile page to update personal information and passwords. Nurse profiles also include their assigned shifts.</p>
                    </div>

                    <div class="reveal-on-scroll delay-200 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl">
                        <div class="w-14 h-14 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Feedback System</h3>
                        <p class="text-slate-600 dark:text-slate-300">A built-in system for users to submit feedback, report issues, and view responses, fostering continuous improvement.</p>
                    </div>

                    <div class="reveal-on-scroll delay-300 bg-white dark:bg-slate-800/60 p-8 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-xl">
                        <div class="w-14 h-14 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-3">Comprehensive Audit Logs</h3>
                        <p class="text-slate-600 dark:text-slate-300">Track every action within the system for security, compliance, and accountability. Searchable by user, date, and activity type.</p>
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

                    <h2 class="relative text-3xl font-bold tracking-tight text-white sm:text-5xl mb-6">See All Features in Action</h2>
                    <p class="relative text-xl text-blue-100 max-w-2xl mx-auto mb-10">Schedule a personalized demo to see how SanaGo's features can be tailored to your hospital's unique needs.</p>

                    <div class="relative flex flex-col sm:flex-row justify-center gap-4">
                        <a href="#" class="rounded-full bg-white px-10 py-4 text-base font-bold text-blue-600 shadow-xl hover:bg-blue-50 transition-all duration-200 hover:scale-105">
                            Schedule a Demo
                        </a>
                        <a href="#" class="rounded-full border border-white/30 bg-white/10 px-10 py-4 text-base font-bold text-white backdrop-blur-sm hover:bg-white/20 transition-all duration-200 hover:scale-105">
                            Start Free Trial
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

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
