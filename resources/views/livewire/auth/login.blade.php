<div lang="en" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    showPassword: false,
    init() {
        if (this.darkMode || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            this.darkMode = true;
        }
    }
}" class="min-h-screen relative overflow-x-hidden font-sans antialiased selection:bg-blue-500 selection:text-white text-slate-900 dark:text-slate-50 transition-colors duration-300">

    {{-- Background Effects --}}
    <div class="fixed inset-0 bg-[#f8fafc] -z-50 transition-colors duration-500 dark:hidden">
        <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-blue-50/50 via-purple-50/30 to-white"></div>
    </div>
    <div class="fixed inset-0 bg-[#0B1120] -z-50 transition-colors duration-500 hidden dark:block"></div>

    {{-- Animated Circles --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-40">
        <ul class="circles">
            <li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li>
        </ul>
    </div>

    {{-- Noise Texture --}}
    <div class="fixed inset-0 opacity-[0.04] dark:opacity-[0.07] pointer-events-none -z-30 mix-blend-overlay"
         style="background-image: url('https://grainy-gradients.vercel.app/noise.svg')"></div>

    <main class="flex items-center justify-center min-h-screen pt-10 pb-10 sm:pt-[72px]">

        <!-- Error Alert (Global Session Errors) -->
        @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-transition
             class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 max-w-md w-full px-4">
            <div class="flex items-center justify-between rounded-xl bg-red-50 dark:bg-red-900/40 border border-red-200 dark:border-red-800 p-4 text-red-700 dark:text-red-200 text-sm shadow-lg backdrop-blur-md">
                <div class="flex items-center gap-3">
                    <x-heroicon-s-exclamation-circle class="w-5 h-5 shrink-0" />
                    <span>{{ session('error') }}</span>
                </div>
                <button @click="show = false" class="ml-4 text-red-500 hover:text-red-700 dark:hover:text-red-100">
                    <x-heroicon-s-x-mark class="w-5 h-5" />
                </button>
            </div>
        </div>
        @endif

        <div class="w-full max-w-md mx-auto px-6">
            <div class="bg-white/80 dark:bg-slate-800/60 backdrop-blur-xl rounded-3xl shadow-2xl p-8 sm:p-10 border border-slate-200/50 dark:border-slate-700/50 relative overflow-hidden">

                <!-- Logo and Header -->
                <div class="flex flex-col items-center mb-8 relative z-10">
                    <div class="h-16 w-16  dark:bg-slate-700 rounded-2xl  flex items-center justify-center mb-4">
                        {{-- Ensure you have this logo, or fallback to an icon --}}
                        <img class="h-10 w-auto" src="{{Storage::disk('central_public')->url('images/logo.png') }}"
                             onerror="this.onerror=null; this.parentElement.innerHTML='<svg class=\'w-8 h-8 text-blue-600\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M13 10V3L4 14h7v7l9-11h-7z\'/></svg>'"
                             alt="Logo">
                    </div>

                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2 tracking-tight">{{ __('auth_login.title') }}</h1>
                    <p class="text-slate-600 dark:text-slate-400 text-center text-sm">{{ __('auth_login.subtitle') }}</p>
                </div>

                <!-- Login Form -->
                <form wire:submit.prevent="authenticate" class="space-y-6 relative z-10" novalidate>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5 ml-1">{{ __('auth_login.email_label') }}</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-heroicon-o-envelope class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" />
                            </div>
                            <input id="email" wire:model.live="email" type="email" required autocomplete="email" placeholder="{{ __('auth_login.email_placeholder') }}"
                                class="block w-full pl-10 pr-4 py-3 border rounded-xl bg-white/50 dark:bg-slate-900/50 text-slate-900 dark:text-white placeholder-slate-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200
                                @error('email') border-red-500 focus:ring-red-500 @else border-slate-300 dark:border-slate-600 @enderror">
                        </div>
                        @error('email')
                            <p class="mt-1.5 ml-1 text-xs text-red-500 font-medium flex items-center gap-1">
                                <x-heroicon-s-exclamation-circle class="w-3 h-3"/> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div x-data="{ show: false }">
                        <div class="flex items-center justify-between mb-1.5 ml-1">
                            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('auth_login.password_label') }}</label>
                        </div>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-heroicon-o-lock-closed class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" />
                            </div>
                            <input id="password" wire:model.live="password" :type="show ? 'text' : 'password'" required autocomplete="current-password" placeholder="{{ __('auth_login.password_placeholder') }}"
                                class="block w-full pl-10 pr-12 py-3 border rounded-xl bg-white/50 dark:bg-slate-900/50 text-slate-900 dark:text-white placeholder-slate-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200
                                @error('password') border-red-500 focus:ring-red-500 @else border-slate-300 dark:border-slate-600 @enderror">

                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors focus:outline-none">
                                <x-heroicon-s-eye x-show="!show" class="h-5 w-5" />
                                <x-heroicon-s-eye-slash x-show="show" x-cloak class="h-5 w-5" />
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1.5 ml-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" wire:model="remember" class="h-4 w-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:focus:ring-offset-slate-800 transition">
                            <span class="ml-2 text-sm text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-slate-200 transition-colors">{{ __('auth_login.remember_me') }}</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium transition-colors">{{ __('auth_login.forgot_password') }}</a>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" wire:loading.attr="disabled" class="w-full flex justify-center items-center px-6 py-3.5 border border-transparent text-base font-semibold rounded-xl shadow-lg shadow-blue-500/30 text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-70 disabled:cursor-not-allowed disabled:transform-none dark:focus:ring-offset-slate-800">
                            <span wire:loading.remove wire:target="authenticate">{{ __('auth_login.sign_in_btn') }}</span>
                            <span wire:loading wire:target="authenticate" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('auth_login.signing_in') }}
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Footer -->
                <div class="mt-8 text-center relative z-10">
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        {{ __('auth_login.no_account_prefix') }}
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-bold transition-colors">{{ __('auth_login.register_link') }}</a>
                    </p>
                </div>
            </div>
        </div>
    </main>

    <style>
        [x-cloak] { display: none !important; }

        /* Animation Keyframes */
        @keyframes floatUp {
            0% { transform: translateY(0) rotate(0deg) scale(1); opacity: 0.8; }
            100% { transform: translateY(-100vh) rotate(720deg) scale(1.5); opacity: 0; }
        }

        .circles {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; margin: 0; padding: 0;
        }
        .circles li {
            position: absolute; display: block; list-style: none; width: 20px; height: 20px;
            /* Light Mode Bubbles */
            background: linear-gradient(to top right, rgba(59, 130, 246, 0.4), rgba(168, 85, 247, 0.4));
            backdrop-filter: blur(4px);
            animation: floatUp 25s linear infinite;
            bottom: -150px; border-radius: 50%;
        }
        /* Dark Mode Bubbles */
        .dark .circles li {
            background: linear-gradient(to top right, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.02));
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
    </style>
</div>
