<div lang="en" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    emailSent: @entangle('emailSent'),
    init() {
        if (this.darkMode || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            this.darkMode = true;
        }
    }
}" class="min-h-screen relative overflow-x-hidden font-sans antialiased selection:bg-blue-500 selection:text-white text-slate-900 dark:text-slate-50 transition-colors duration-300">

    {{-- Background Effects (Identical to Login) --}}
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

    <main class="flex items-center justify-center min-h-screen pt-10 pb-10 sm:pt-[72px]">
        <div class="w-full max-w-md mx-auto px-6">
            <div class="bg-white/80 dark:bg-slate-800/60 backdrop-blur-xl rounded-3xl shadow-2xl p-8 sm:p-10 border border-slate-200/50 dark:border-slate-700/50 relative overflow-hidden transition-all duration-500"
                 :class="emailSent ? 'scale-95 opacity-90' : 'scale-100 opacity-100'">

                <!-- Logo -->
                <div class="flex flex-col items-center mb-6 relative z-10">
                    <div class="h-16 w-16 dark:bg-slate-700 rounded-2xl flex items-center justify-center mb-4 transition-transform duration-500" :class="emailSent ? 'rotate-12 scale-110' : ''">
                        <img class="h-10 w-auto" src="{{ Storage::disk('central_public')->url('images/logo.png') }}"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<svg class=\'w-8 h-8 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z\'/></svg>'"
                             alt="Logo">
                    </div>
                </div>

                <!-- STATE 1: FORM -->
                <div x-show="!emailSent" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-10" x-transition:enter-end="opacity-100 translate-x-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0" x-transition:leave-end="opacity-0 translate-x-10">

                    <div class="text-center mb-8">
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2 tracking-tight">Forgot Password?</h1>
                        <p class="text-slate-600 dark:text-slate-400 text-sm">Enter your email and we'll send you instructions to reset your password.</p>
                    </div>

                    <form wire:submit.prevent="sendResetLink" class="space-y-6 relative z-10">
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 ml-1">Email Address</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <x-heroicon-o-envelope class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" />
                                </div>
                                <input id="email" wire:model.blur="email" type="email" required autofocus
                                    class="block w-full pl-10 pr-4 py-3 border rounded-xl bg-white/50 dark:bg-slate-900/50 text-slate-900 dark:text-white placeholder-slate-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200
                                    @error('email') border-red-500 focus:ring-red-500 @else border-slate-300 dark:border-slate-600 @enderror"
                                    placeholder="name@company.com">
                            </div>
                            @error('email')
                                <p class="mt-1.5 ml-1 text-xs text-red-500 font-medium flex items-center gap-1">
                                    <x-heroicon-s-exclamation-circle class="w-3 h-3"/> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <button type="submit" wire:loading.attr="disabled" class="w-full flex justify-center items-center px-6 py-3.5 border border-transparent text-base font-semibold rounded-xl shadow-lg shadow-blue-500/30 text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-70 disabled:cursor-not-allowed">
                            <span wire:loading.remove>Send Reset Link</span>
                            <span wire:loading class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sending...
                            </span>
                        </button>
                    </form>
                </div>

                <!-- STATE 2: SUCCESS MESSAGE -->
                <div x-show="emailSent" x-cloak class="text-center py-4"
                     x-transition:enter="transition ease-out duration-500 delay-200"
                     x-transition:enter-start="opacity-0 translate-y-10"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900/30 mb-6">
                        <x-heroicon-o-check class="h-8 w-8 text-green-600 dark:text-green-400" />
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Check your mail</h2>
                    <p class="text-slate-600 dark:text-slate-400 mb-6">We have sent a password reset link to <br><span class="font-semibold text-slate-900 dark:text-white" x-text="$wire.email"></span></p>

                    <p class="text-sm text-slate-500 dark:text-slate-500">
                        Didn't receive the email? <button wire:click="sendResetLink" class="text-blue-600 hover:text-blue-500 font-medium">Click to resend</button>
                    </p>
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center relative z-10 pt-4 border-t border-slate-200 dark:border-slate-700/50">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-medium transition-colors group">
                        <x-heroicon-s-arrow-left class="w-4 h-4 transition-transform group-hover:-translate-x-1" />
                        Back to Login
                    </a>
                </div>
            </div>
        </div>
    </main>
    <style>
        /* Include the same CSS for circles from your original code here */
        [x-cloak] { display: none !important; }
        @keyframes floatUp { 0% { transform: translateY(0) rotate(0deg) scale(1); opacity: 0.8; } 100% { transform: translateY(-100vh) rotate(720deg) scale(1.5); opacity: 0; } }
        .circles { position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; margin: 0; padding: 0; }
        .circles li { position: absolute; display: block; list-style: none; width: 20px; height: 20px; background: linear-gradient(to top right, rgba(59, 130, 246, 0.4), rgba(168, 85, 247, 0.4)); backdrop-filter: blur(4px); animation: floatUp 25s linear infinite; bottom: -150px; border-radius: 50%; }
        .dark .circles li { background: linear-gradient(to top right, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.02)); }
        .circles li:nth-child(1) { left: 25%; width: 80px; height: 80px; animation-delay: 0s; }
        /* ... rest of bubbles ... */
    </style>
</div>