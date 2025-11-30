<div class="min-h-screen bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    @if (session('error'))
        <div role="alert" class="max-w-md mx-auto mt-6 px-4">
            <div
                class="rounded-md bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 p-3 text-red-700 dark:text-red-200 text-sm shadow-sm">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <main class="w-full max-w-md">
            <section
                class="bg-white/90 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-2xl p-8 sm:p-10 border border-gray-200 dark:border-gray-700">
                <header class="flex flex-col items-center mb-6">
                    <a href="#"
                        class="flex items-center space-x-3 mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-sm">
                        <div
                            class="flex items-center justify-center w-10 h-10  shadow-sm">
                       <img src="{{ Storage::disk('central_public')->url('images/logo.png') }}" alt="Central Logo">
</div>
                        <div class="text-left">
                            <span class="block text-lg font-bold leading-none">sanaGo</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-300 -mt-0.5">AIHMS</span>
                        </div>
                    </a>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white text-center">Welcome
                        back</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 text-center">Sign in to your account to
                        continue</p>
                </header>

                <form wire:submit.prevent="authenticate" class="space-y-5" novalidate>
                    <!-- Email -->
                    <div>
                        <label for="email"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email
                            address</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-400">
                                <x-heroicon-o-envelope class="w-5 h-5" />
                            </span>
                            <input id="email" name="email" type="email" wire:model.live="email"
                                aria-describedby="email-note @error('email') email-error @enderror"
                                aria-invalid="@error('email') true @else false @enderror" required autocomplete="email"
                                placeholder="you@example.com"
                                class="block w-full pl-12 pr-4 py-3 border rounded-lg shadow-sm placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-500 transition-colors duration-200" />
                        </div>
                        @error('email')
                            <p id="email-error" class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @else
                            <p id="email-note" class="mt-2 text-xs text-gray-500 dark:text-gray-400">We'll never share your
                                email with anyone else.</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div x-data="{ show: false }" class="relative">
                        <label for="password"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-400">
                            <x-heroicon-o-lock-closed class="w-5 h-5" />
                        </span>

                        <input id="password" name="password" :type="show ? 'text' : 'password'"
                            wire:model.live="password" aria-describedby="@error('password') password-error @enderror"
                            aria-invalid="@error('password') true @else false @enderror" required
                            autocomplete="current-password" placeholder="••••••••"
                            class="block w-full pl-12 pr-14 py-3 border rounded-lg shadow-sm placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-500 transition-colors duration-200" />

                        <button type="button" @click="show = !show" :aria-pressed="show"
                            aria-label="Toggle password visibility"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
                            <template x-if="!show">
                                <x-heroicon-o-eye-slash class="w-5 h-5" />
                            </template>
                            <template x-if="show">
                                <x-heroicon-o-eye class="w-5 h-5" />
                            </template>
                        </button>
                    </div>
                    @error('password')
                        <p id="password-error" class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror

                    <!-- Remember / Forgot -->
                    <div class="flex items-center justify-between">
                        <label for="remember"
                            class="flex items-center text-sm text-gray-700 dark:text-gray-300 cursor-pointer select-none">
                            <input id="remember" name="remember" type="checkbox" wire:model.live="remember"
                                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600" />
                            <span class="ml-2">Remember me</span>
                        </label>

                        <a href="{{ route('password.request') }}"
                            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Submit -->
                    <div>
                        <button type="submit" wire:loading.attr="disabled" aria-live="polite"
                            class="w-full inline-flex justify-center items-center gap-3 px-6 py-3 border border-transparent text-base font-semibold rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-transform duration-200 transform hover:-translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-offset-gray-800">
                            <span wire:loading.remove wire:target="authenticate">Log in</span>
                            <span wire:loading wire:target="authenticate" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                                </svg>
                                Logging in...
                            </span>
                        </button>
                    </div>
                </form>

                <footer class="mt-6 text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Don't have an account?
                        <a href="#"
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-semibold transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded">
                            Register here
                        </a>
                    </p>
                </footer>
            </section>
        </main>
    </div>



    {{--
<div lang="en" x-data="{
    mobileMenuOpen: false,
    darkMode: localStorage.getItem('darkMode') === 'true',
    showPassword: false,
    isLoading: false,

    init() {
        if (this.darkMode || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            this.darkMode = true;
        }
    },
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        this.darkMode ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark');
    },
    submitForm() {
        this.isLoading = true;
        // Simulate form submission
        setTimeout(() => {
            this.isLoading = false;
            // In a real app, you would redirect or show success message
        }, 2000);
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



    <main class="flex items-center justify-center min-h-screen pt-[72px]">
        <!-- Error Alert -->
        <div x-show="false" x-transition class="fixed top-24 left-1/2 transform -translate-x-1/2 z-50 max-w-md w-full mx-auto px-4">
            <div class="rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 p-4 text-red-700 dark:text-red-200 text-sm shadow-lg backdrop-blur-md">
                Invalid credentials. Please try again.
            </div>
        </div>

        <div class="w-full max-w-md mx-auto px-6 py-12">
            <div class="bg-white/90 dark:bg-slate-800/60 backdrop-blur-xl rounded-3xl shadow-2xl p-8 sm:p-10 border border-slate-200/50 dark:border-slate-700/50">
                <!-- Logo and Header -->
                <div class="flex flex-col items-center mb-8">
                      <img class="h-10 w-12" src="{{ asset('images/logo.png') }}" alt="Sana Go Health System Logo">

                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">Welcome Back</h1>
                    <p class="text-slate-600 dark:text-slate-400 text-center">Sign in to your account to continue</p>
                </div>

                <!-- Login Form -->
                <form wire:submit.prevent="authenticate" class="space-y-6" novalidate>
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email Address</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 018 0zM12 14v7m-3 3h.01M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <input id="email" name="email" type="email" required autocomplete="email" placeholder="you@example.com"
                                class="block w-full pl-12 pr-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        </div>
                        <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">We'll never share your email with anyone else.</p>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Password</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                            </div>
                            <input id="password" name="password" :type="showPassword ? 'text' : 'password'" required autocomplete="current-password" placeholder="•••••••••"
                                class="block w-full pl-12 pr-12 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-500 dark:placeholder-slate-400 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <svg x-show="!showPassword" class="h-5 w-5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.929m5.858.908a3 3 0 114.243 0 3 3 0 00-4.243 0M9.878 9.878a3 3 0 114.242 0 3 3 0 00-4.242 0M9.88 9.88l3.24-3.24"></path></svg>
                                <svg x-show="showPassword" x-cloak class="h-5 w-5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" class="h-4 w-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500 dark:bg-slate-700 dark:border-slate-600">
                            <span class="ml-2 text-sm text-slate-700 dark:text-slate-300">Remember me</span>
                        </label>
                        <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Forgot password?</a>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" :disabled="isLoading" class="w-full flex justify-center items-center px-6 py-3 border border-transparent text-base font-semibold rounded-xl shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:-translate-y-0.5 disabled:opacity-60 disabled:cursor-not-allowed disabled:transform-none">
                            <span x-show="!isLoading">Sign in</span>
                            <span x-show="isLoading" x-cloak class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Signing in...
                            </span>
                        </button>
                    </div>
                </form>

                <!-- Register Link -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        Don't have an account?
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Register here</a>
                    </p>
                </div>
            </div>
        </div>
    </main>

    <style>
        [x-cloak] { display: none !important; }

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
    </style>
</div>
 --}}
</div>
