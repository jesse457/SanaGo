<div lang="en" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    showPassword: false,
    init() {
        if (this.darkMode || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            this.darkMode = true;
        }
    }
}"
    class="min-h-screen grid lg:grid-cols-2 font-sans antialiased text-slate-900 dark:text-slate-50 transition-colors duration-300 bg-white dark:bg-slate-950">

    <!-- Global Error Toast -->
    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            class="fixed top-6 right-6 z-[60] max-w-sm w-full bg-white dark:bg-slate-900 border border-red-100 dark:border-red-900/30 shadow-2xl rounded-2xl pointer-events-auto overflow-hidden">
            <div class="p-4 flex items-center gap-4">
                <div class="flex-shrink-0 w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                    <x-heroicon-s-x-circle class="h-6 w-6 text-red-600 dark:text-red-400" />
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-slate-900 dark:text-white">Access Denied</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                    <x-heroicon-s-x-mark class="h-5 w-5" />
                </button>
            </div>
        </div>
    @endif

    <!-- Left Side: Login Form -->
    <div class="flex flex-col justify-center px-6 py-12 lg:px-16 xl:px-24 relative overflow-hidden">
        <!-- Background Decor -->
        <div class="absolute top-0 left-0 w-full h-32 bg-gradient-to-b from-blue-50/50 dark:from-blue-900/10 to-transparent opacity-50"></div>

        <!-- Theme Toggle -->
        <div class="absolute top-8 right-8">
            <button @click="darkMode = !darkMode; document.documentElement.classList.toggle('dark'); localStorage.setItem('darkMode', darkMode)"
                class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white/50 dark:bg-slate-900/50 backdrop-blur-md hover:bg-slate-100 dark:hover:bg-slate-800 transition-all">
                <x-heroicon-o-sun x-show="darkMode" class="w-5 h-5 text-yellow-500" />
                <x-heroicon-o-moon x-show="!darkMode" class="w-5 h-5 text-slate-600" />
            </button>
        </div>

        <div class="w-full max-w-md mx-auto relative z-10">
            <!-- Mobile Logo -->
            <div class="lg:hidden mb-10">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <x-heroicon-s-shield-check class="h-6 w-6 text-white" />
                    </div>
                    <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">Sana<span class="text-blue-600">Go</span></span>
                </div>
            </div>

            <div class="mb-10">
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
                    Welcome Back
                </h2>
                <p class="mt-3 text-slate-500 dark:text-slate-400 font-medium">
                    Secure access for Hospital Staff & Patients.
                </p>
            </div>

            <form wire:submit.prevent="authenticate" class="space-y-5" novalidate>
                <!-- Email/ID Field -->
                <div>
                    <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 ml-1">
                        Email Address or Staff ID
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-heroicon-m-user class="h-5 w-5 text-slate-400 group-focus-within:text-blue-600 transition-colors" />
                        </div>
                        <input id="email" wire:model="email" type="text" required autocomplete="email"
                            placeholder="doctor@sanago.com / patient@email.com"
                            class="block w-full pl-11 pr-4 py-3.5 rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-4 focus:ring-blue-600/10 focus:border-blue-600 transition-all
                            @error('email') border-red-500 focus:ring-red-500/10 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 ml-1">
                        Password
                    </label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-heroicon-m-lock-closed class="h-5 w-5 text-slate-400 group-focus-within:text-blue-600 transition-colors" />
                        </div>
                        <input id="password" wire:model="password" :type="showPassword ? 'text' : 'password'" required
                            autocomplete="current-password"
                            placeholder="Enter your secure credentials"
                            class="block w-full pl-11 pr-12 py-3.5 rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-4 focus:ring-blue-600/10 focus:border-blue-600 transition-all
                            @error('password') border-red-500 focus:ring-red-500/10 @enderror">

                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" @click="showPassword = !showPassword"
                                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-200/50 dark:hover:bg-slate-800 transition-all focus:outline-none">
                                <x-heroicon-m-eye x-show="!showPassword" class="h-5 w-5" />
                                <x-heroicon-m-eye-slash x-show="showPassword" x-cloak class="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                    @error('password')
                        <p class="mt-2 text-xs font-medium text-red-600 dark:text-red-400 ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between py-1">
                    <label class="relative flex items-center cursor-pointer group">
                        <input id="remember-me" wire:model="remember" type="checkbox"
                            class="peer h-5 w-5 border-slate-300 dark:border-slate-700 rounded-md text-blue-600 focus:ring-blue-600/20 bg-white dark:bg-slate-900 transition-all">
                        <span class="ml-3 text-sm font-medium text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-slate-200 transition-colors">
                            Remember me
                        </span>
                    </label>

                    <a href="{{ tenant('id') ? route('tenant.password.request') : route('password.request') }}"
                        class="text-sm font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                        Trouble signing in?
                    </a>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="authenticate"
                        class="w-full flex justify-center items-center py-4 px-4 rounded-xl shadow-lg shadow-blue-600/20 text-sm font-extrabold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-slate-950 focus:ring-blue-600 disabled:opacity-80 disabled:cursor-not-allowed transition-all transform active:scale-[0.98]">

                        <span wire:loading.remove wire:target="authenticate">Access Dashboard</span>

                        <span wire:loading wire:target="authenticate" class="flex items-center gap-3">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Verifying Credentials...
                        </span>
                    </button>
                </div>
            </form>

            <div class="mt-10">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-100 dark:border-slate-800"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-white dark:bg-slate-950 text-slate-400 font-medium italic text-center">
                            New Staff? Contact IT Support.
                            <br>
                            <span class="text-xs opacity-80">Patients: Please contact Admissions.</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="absolute bottom-8 left-0 right-0 text-center">
            <p class="text-[10px] uppercase tracking-[0.2em] font-bold text-slate-400 dark:text-slate-600">
                &copy; {{ date('Y') }} SanaGo HMS • Unified Access Portal
            </p>
        </div>
    </div>

    <!-- Right Side: Corporate Visuals -->
    <div class="hidden lg:block relative overflow-hidden bg-slate-900">
        <!-- Abstract Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/30 via-slate-900/90 to-slate-950 z-10"></div>

        <!-- Image -->
        <img class="absolute inset-0 h-full w-full object-cover scale-105 animate-soft-zoom"
            src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80"
            alt="Hospital Environment">

        <!-- Decorative Grid -->
        <div class="absolute inset-0 z-10 opacity-20" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>

        <div class="absolute inset-0 z-20 flex flex-col justify-between p-20">
            <div class="flex items-center gap-2">
              <img class="h-7 w-auto flex items-center" src="{{ asset('images/logo.webp') }}" alt="Sana Go Logo">
                <span class="text-2xl font-bold tracking-tight text-white tracking-widest">Sana<span class="text-blue-400">Go</span></span>
            </div>

            <div class="max-w-xl">
                <h3 class="text-4xl font-extrabold text-white mb-6 leading-[1.1] tracking-tighter">
                    One System. <br>
                    <span class="text-blue-400">Complete Care.</span>
                </h3>

                <div class="border-l-4 border-blue-500 pl-8 space-y-4">
                    <p class="text-lg text-slate-300 leading-relaxed font-light">
                        Unified access point for all hospital operations and patient services.
                    </p>

                    <ul class="text-sm font-semibold text-white/70 space-y-1">
                        <li class="flex items-center gap-2">
                            <x-heroicon-m-check class="w-4 h-4 text-blue-400"/> Medical Staff (Doctors, Nurses, Technicians)
                        </li>
                        <li class="flex items-center gap-2">
                            <x-heroicon-m-check class="w-4 h-4 text-blue-400"/> Administration & Management
                        </li>
                        <li class="flex items-center gap-2">
                            <x-heroicon-m-check class="w-4 h-4 text-blue-400"/> Patient Portal & Appointments
                        </li>
                    </ul>
                </div>
            </div>

            <div class="flex items-center gap-8 text-white/50 text-sm font-semibold tracking-widest uppercase">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                    System Operational
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                    Secure Connection
                </div>
            </div>
        </div>
    </div>
    <style>
    @keyframes soft-zoom {
        from { transform: scale(1); }
        to { transform: scale(1.1); }
    }
    .animate-soft-zoom {
        animation: soft-zoom 20s alternate infinite ease-in-out;
    }
    </style>

</div>
