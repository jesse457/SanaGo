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
    class="min-h-screen grid lg:grid-cols-2 font-sans antialiased text-slate-900 dark:text-slate-50 transition-colors duration-300">

    <!-- Global Error Toast -->
    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white dark:bg-slate-800 border-l-4 border-red-500 shadow-lg rounded-r-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <x-heroicon-s-x-circle class="h-5 w-5 text-red-400" />
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-slate-900 dark:text-white">Authentication Failed</p>
                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ session('error') }}</p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="show = false"
                            class="bg-white dark:bg-slate-800 rounded-md inline-flex text-slate-400 hover:text-slate-500 focus:outline-none">
                            <x-heroicon-s-x-mark class="h-5 w-5" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Left Side: Login Form -->
    <div class="flex flex-col justify-center px-4 sm:px-6 lg:px-20 xl:px-24 bg-white dark:bg-slate-950 relative">
        <div class="w-full max-w-sm mx-auto">
            <!-- Mobile Logo -->
            <div class="lg:hidden mb-8">
                <img class="h-8 w-auto" src="{{ Storage::disk('central_public')->url('images/logo.png') }}"
                    alt="Logo">
            </div>

            <div class="mb-10">
                <h2 class="mt-6 text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    {{ __('auth_login.title') }}</h2>
                <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ __('auth_login.subtitle') }}</p>
            </div>

            <form wire:submit.prevent="authenticate" class="space-y-6" novalidate>
                <div>
                    <label for="email"
                        class="block text-sm font-semibold text-slate-700 dark:text-slate-300">{{ __('auth_login.email_label') }}</label>
                    <div class="mt-2 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-m-envelope class="h-5 w-5 text-slate-400" />
                        </div>
                        <input id="email" wire:model.live="email" type="email" required autocomplete="email"
                            class="block w-full pl-10 sm:text-sm rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-blue-600 focus:border-blue-600 py-2.5 transition-colors
                            @error('email') border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                    </div>
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div x-data="{ show: false }">
                    <label for="password"
                        class="block text-sm font-semibold text-slate-700 dark:text-slate-300">{{ __('auth_login.password_label') }}</label>
                    <div class="mt-2 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-m-lock-closed class="h-5 w-5 text-slate-400" />
                        </div>
                        <input id="password" wire:model.live="password" :type="show ? 'text' : 'password'" required
                            autocomplete="current-password"
                            class="block w-full pl-10 pr-10 sm:text-sm rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-blue-600 focus:border-blue-600 py-2.5 transition-colors
                            @error('password') border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" @click="show = !show"
                                class="text-slate-400 hover:text-slate-500 focus:outline-none">
                                <x-heroicon-m-eye x-show="!show" class="h-5 w-5" />
                                <x-heroicon-m-eye-slash x-show="show" x-cloak class="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" wire:model="remember" type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded cursor-pointer">
                        <label for="remember-me"
                            class="ml-2 block text-sm text-slate-900 dark:text-slate-300 cursor-pointer">{{ __('auth_login.remember_me') }}</label>
                    </div>
                    @if (tenant('id'))
                        <div class="text-sm">
                            <a href="{{ route('tenant.password.request') }}"
                                class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">{{ __('auth_login.forgot_password') }}</a>
                        </div>
                    @else
                        <div class="text-sm">
                            <a href="{{ route('password.request') }}"
                                class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400">{{ __('auth_login.forgot_password') }}</a>
                        </div>
                    @endif

                </div>

                <div>
                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-70 disabled:cursor-not-allowed transition-all">
                        <span wire:loading.remove wire:target="authenticate">{{ __('auth_login.sign_in_btn') }}</span>
                        <span wire:loading wire:target="authenticate" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            {{ __('auth_login.signing_in') }}
                        </span>
                    </button>
                </div>
            </form>

            <div class="mt-8">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200 dark:border-slate-800"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span
                            class="px-2 bg-white dark:bg-slate-950 text-slate-500">{{ __('auth_login.no_account_prefix') }}</span>
                    </div>
                </div>
               
            </div>
        </div>

        <!-- Footer Info -->
        <div class="absolute bottom-6 left-0 right-0 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Protected by Enterprise Grade Security.
        </div>
    </div>

    <!-- Right Side: Corporate Visuals (Hidden on Mobile) -->
    <div class="hidden lg:block relative bg-slate-900 flex-1 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/40 to-slate-900/10 z-10"></div>
        <!-- Replace with a high-quality medical or tech image -->
        <img class="absolute inset-0 h-full w-full object-cover"
            src="https://images.unsplash.com/photo-1576091160399-112ba8d25d1d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80"
            alt="Hospital Technology">

        <div class="absolute bottom-0 left-0 z-20 p-20 text-white">
            <img class="h-12 w-auto mb-8" src="{{ Storage::disk('central_public')->url('images/logo.png') }}"
                onerror="this.style.display='none'" alt="Logo">
            <h3 class="text-4xl font-bold mb-4">Streamline Your<br>Hospital Operations</h3>
            <p class="text-lg text-slate-300 max-w-md">Our multi-tenant platform manages patient records, billing, and
                staff schedules with uncompromised security.</p>
        </div>
    </div>
</div>
