<div x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    faqOpen: null,

    // Function to re-apply animations after Livewire updates
    applyAnimations() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.reveal-on-scroll').forEach((el) => observer.observe(el));
    },

    init() {
        this.applyAnimations();
        window.addEventListener('theme-changed', (e) => { this.darkMode = e.detail; });

        // CRITICAL: Re-run when Livewire updates the page (prevents fading/disappearing)
        Livewire.hook('morph.updated', (el, component) => { this.applyAnimations(); });
    }
}" class="min-h-screen relative overflow-x-hidden font-sans antialiased text-slate-900 dark:text-slate-50">

    <!-- 1. BACKGROUNDS -->
    <div class="fixed inset-0 bg-[#f8fafc] -z-50 dark:hidden">
        <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-blue-50/50 via-indigo-50/20 to-white"></div>
    </div>
    <div class="fixed inset-0 bg-[#0B1120] -z-50 hidden dark:block"></div>
    <div class="fixed inset-0 opacity-[0.03] dark:opacity-[0.05] pointer-events-none -z-30 mix-blend-overlay" style="background-image: url('https://grainy-gradients.vercel.app/noise.svg')"></div>

    <main class="relative">

        <!-- 2. HERO & FORM SECTION -->
        <section class="pt-16 pb-24">
            <div class="max-w-7xl mx-auto px-6 flex flex-col lg:flex-row items-start gap-16">

                <!-- Left: Content -->
                <div class="flex-1 reveal-on-scroll">
                    <div class="inline-flex items-center rounded-full border border-blue-200 dark:border-blue-900/50 bg-blue-50/80 dark:bg-blue-900/20 px-4 py-1.5 text-sm font-semibold text-blue-700 dark:text-blue-300 mb-8 backdrop-blur-md">
                        <span class="flex h-2 w-2 rounded-full bg-blue-500 mr-2 animate-pulse"></span>
                        {{ __('book_demo.badge') }}
                    </div>
                    <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white mb-6 leading-tight">
                        {{ __('book_demo.title_prefix') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-500">{{ __('book_demo.title_suffix') }}</span>
                    </h1>
                    <p class="text-lg md:text-xl text-slate-600 dark:text-slate-400 mb-8 leading-relaxed">
                        {{ __('book_demo.subtitle') }}
                    </p>
                    <div class="flex items-center gap-3 text-sm text-slate-500 font-medium">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        {{ __('book_demo.secure_note') }}
                    </div>
                </div>

                <!-- Right: Form -->
                <div class="flex-1 w-full max-w-2xl reveal-on-scroll delay-200" wire:key="main-form-wrapper">
                    <div class="bg-white dark:bg-slate-900/80 backdrop-blur-2xl p-8 rounded-[2.5rem] border border-slate-200 dark:border-slate-800 shadow-2xl relative overflow-hidden">

                        @if ($success)
                            <div wire:key="success-state" class="py-12 text-center">
                                <div class="w-20 h-20 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">{{ __('book_demo.success_message') }}</h2>
                                <button type="button" wire:click="$set('success', false)" class="mt-8 text-blue-600 font-bold hover:underline">Book another demo</button>
                            </div>
                        @else
                            <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-8">{{ __('book_demo.form_title') }}</h2>

                            <form wire:submit.prevent="submit" class="space-y-5">
                                <!-- Row 1: Name -->
                                <div>
                                    <label class="form-label">{{ __('book_demo.label_name') }}</label>
                                    <input type="text" wire:model="full_name" class="form-input" placeholder="{{ __('book_demo.placeholder_name') }}">
                                    @error('full_name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Row 2: Email & Phone -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="form-label">{{ __('book_demo.label_email') }}</label>
                                        <input type="email" wire:model="email" class="form-input" placeholder="{{ __('book_demo.placeholder_email') }}">
                                        @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="form-label">{{ __('book_demo.label_phone') }}</label>
                                        <input type="tel" wire:model="phone_number" class="form-input" placeholder="{{ __('book_demo.placeholder_phone') }}">
                                    </div>
                                </div>

                                <!-- Row 3: Facility Name -->
                                <div>
                                    <label class="form-label">{{ __('book_demo.label_facility') }}</label>
                                    <input type="text" wire:model="facility_name" class="form-input" placeholder="{{ __('book_demo.placeholder_facility') }}">
                                    @error('facility_name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Row 4: Type & Region -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                    <div>
                                        <label class="form-label">Facility Type</label>
                                        <select wire:model="facility_type" class="form-input">
                                            <option value="">Select Type</option>
                                            <option value="Hospital">Hospital</option>
                                            <option value="Clinic">Clinic</option>
                                            <option value="Pharmacy">Pharmacy</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="form-label">Region</label>
                                        <select wire:model="region" class="form-input">
                                            <option value="">Select Region</option>
                                            <option value="Center">Center</option>
                                            <option value="Littoral">Littoral</option>
                                            <option value="North">North</option>
                                            <option value="West">West</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Row 5: Job Title -->
                                <div>
                                    <label class="form-label">{{ __('book_demo.label_job') }}</label>
                                    <input type="text" wire:model="job_title" class="form-input" placeholder="{{ __('book_demo.placeholder_job') }}">
                                </div>

                                <!-- Row 6: WhatsApp Check -->
                                <div class="flex items-center gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-100 dark:border-slate-700">
                                    <input type="checkbox" wire:model="has_whatsapp" id="whatsapp_check" class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <label for="whatsapp_check" class="text-sm font-medium text-slate-600 dark:text-slate-400">This number is available on WhatsApp</label>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" wire:loading.attr="disabled" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-all shadow-xl shadow-blue-500/25 flex items-center justify-center gap-3 group">
                                    <span wire:loading.remove wire:target="submit">{{ __('book_demo.submit_btn') }}</span>
                                    <span wire:loading wire:target="submit" class="animate-spin h-5 w-5 border-2 border-white border-t-transparent rounded-full"></span>
                                    <svg wire:loading.remove class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </button>

                                <p class="text-[11px] text-center text-slate-500 mt-4 leading-relaxed">
                                    {{ __('book_demo.terms_text') }}
                                    <a href="#" class="underline hover:text-blue-600">{{ __('book_demo.privacy_policy') }}</a> &
                                    <a href="#" class="underline hover:text-blue-600">{{ __('book_demo.terms_service') }}</a>
                                </p>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. FEATURES (What to Expect) -->
        <section class="py-24 border-y border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/40">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center mb-16 reveal-on-scroll">
                    <h2 class="text-3xl font-bold mb-4">{{ __('book_demo.expect_title') }}</h2>
                    <p class="text-slate-600 dark:text-slate-400 max-w-2xl mx-auto">{{ __('book_demo.expect_subtitle') }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="p-8 bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 reveal-on-scroll delay-100">
                        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 text-blue-600 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ __('book_demo.feature_personalized_title') }}</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">{{ __('book_demo.feature_personalized_desc') }}</p>
                    </div>
                    <!-- Feature 2 -->
                    <div class="p-8 bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 reveal-on-scroll delay-200">
                        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 text-purple-600 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ __('book_demo.feature_walkthrough_title') }}</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">{{ __('book_demo.feature_walkthrough_desc') }}</p>
                    </div>
                    <!-- Feature 3 -->
                    <div class="p-8 bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 reveal-on-scroll delay-300">
                        <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 rounded-2xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-3">{{ __('book_demo.feature_qa_title') }}</h3>
                        <p class="text-slate-600 dark:text-slate-400 leading-relaxed">{{ __('book_demo.feature_qa_desc') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. FAQ SECTION -->
        <section class="py-24 max-w-4xl mx-auto px-6 reveal-on-scroll">
            <h2 class="text-3xl font-bold text-center mb-12">{{ __('book_demo.faq_title') }}</h2>
            <div class="space-y-4">
                @foreach([1,2,3,4] as $i)
                <div class="border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden bg-white/50 dark:bg-slate-900/50">
                    <button @click="faqOpen === {{ $i }} ? faqOpen = null : faqOpen = {{ $i }}" class="w-full flex items-center justify-between p-6 text-left hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <span class="font-semibold">{{ __("book_demo.faq_{$i}_q") }}</span>
                        <svg class="w-5 h-5 transition-transform duration-300" :class="faqOpen === {{ $i }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="faqOpen === {{ $i }}" x-collapse x-cloak>
                        <div class="px-6 pb-6 text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ __("book_demo.faq_{$i}_a") }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- 5. BOTTOM CTA -->
        <section class="py-20 px-6 reveal-on-scroll">
            <div class="max-w-5xl mx-auto bg-gradient-to-br from-blue-600 to-indigo-700 rounded-[3rem] p-12 text-center text-white relative overflow-hidden shadow-2xl">
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <h2 class="text-3xl md:text-4xl font-extrabold mb-6 relative z-10">{{ __('book_demo.cta_title') }}</h2>
                <p class="text-blue-100 mb-10 max-w-2xl mx-auto relative z-10">{{ __('book_demo.cta_subtitle') }}</p>
                <button @click="window.scrollTo({top: 0, behavior: 'smooth'})" class="px-10 py-4 bg-white text-blue-600 font-bold rounded-xl hover:bg-blue-50 transition-colors shadow-lg relative z-10">
                    {{ __('book_demo.cta_btn') }}
                </button>
            </div>
        </section>
    </main>
    <style>
    /* Prevent layout shifting & invisible elements */
    [x-cloak] { display: none !important; }

    .reveal-on-scroll {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease-out, transform 0.6s ease-out;
    }

    .reveal-on-scroll.animate-in {
        opacity: 1 !important;
        transform: translateY(0) !important;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #475569; /* slate-700 */
        margin-bottom: 0.5rem;
    }
    .dark .form-label { color: #cbd5e1; } /* slate-300 */

    .form-input {
        width: 100%;
        padding: 0.875rem 1rem;
        border-radius: 0.75rem;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
        outline: none;
    }
    .dark .form-input {
        background-color: rgba(30, 41, 59, 0.5);
        border-color: #334155;
        color: white;
    }
    .form-input:focus {
        border-color: #3b82f6;
        background-color: white;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }
</style>
</div>


