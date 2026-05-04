<div lang="en" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    emailSent: @entangle('emailSent'),
    init() {
        if (this.darkMode || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            this.darkMode = true;
        }
    }
}" class="min-h-screen grid lg:grid-cols-2 font-sans antialiased text-slate-900 dark:text-slate-50 transition-colors duration-300 bg-slate-50 dark:bg-slate-950">

    <!-- Left Side: Brand & Visuals -->
    <div class="hidden lg:flex relative flex-col justify-between p-16 bg-slate-900 overflow-hidden">
        <!-- Background Layer -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 via-slate-900/90 to-slate-950 z-10"></div>
            <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop"
                 class="absolute inset-0 w-full h-full object-cover grayscale opacity-30" alt="Building">
            <!-- Animated Glow -->
            <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-blue-500/10 rounded-full blur-[120px] z-10"></div>
        </div>

        <div class="relative z-20">
             <div class="flex items-center gap-3">
                <div class="h-10 w-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <x-heroicon-s-shield-check class="h-6 w-6 text-white" />
                </div>
                <span class="text-xl font-bold tracking-tight text-white">Health<span class="text-blue-400">Systems</span></span>
             </div>
        </div>

        <div class="relative z-20 max-w-md">
            <h2 class="text-4xl font-extrabold text-white mb-6 tracking-tight leading-tight">
                Secure Account <br><span class="text-blue-400">Recovery.</span>
            </h2>
            <p class="text-slate-300 leading-relaxed text-lg border-l-2 border-blue-500 pl-6">
                Enter your verified work email to receive encrypted reset instructions. We prioritize the security of your medical data.
            </p>
        </div>

        <div class="relative z-20 flex items-center gap-4 text-slate-500 text-xs uppercase tracking-widest font-semibold">
            <span>&copy; {{ date('Y') }} Enterprise Health</span>
            <span class="h-px w-8 bg-slate-700"></span>
            <span>Privacy Policy</span>
        </div>
    </div>

    <!-- Right Side: The Form -->
    <div class="flex items-center justify-center p-6 relative">
        <!-- Decorative background elements -->
        <div class="absolute top-0 right-0 p-8">
            <button @click="darkMode = !darkMode; $dispatch('toggle-dark-mode')" class="p-2 rounded-full hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors">
                <x-heroicon-o-sun x-show="darkMode" class="w-5 h-5 text-yellow-500" />
                <x-heroicon-o-moon x-show="!darkMode" class="w-5 h-5 text-slate-600" />
            </button>
        </div>

        <!-- Card Container -->
        <div class="w-full max-w-md bg-white dark:bg-slate-900 shadow-2xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-slate-800 rounded-3xl p-8 sm:p-12 relative z-10">

            <!-- State 1: Request Form -->
            <div x-show="!emailSent" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <!-- Mobile Logo -->
                <div class="lg:hidden flex justify-center mb-10">
                    <div class="h-12 w-12 bg-blue-600 rounded-2xl flex items-center justify-center">
                        <x-heroicon-s-shield-check class="h-7 w-7 text-white" />
                    </div>
                </div>

                <div class="mb-10 text-center lg:text-left">
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Forgot password?</h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-3 text-base">No worries, we'll send you reset instructions.</p>
                </div>

                <!-- Fix: use wire:model instead of blur to prevent "double click" issue -->
                <!-- Added wire:loading.attr="disabled" and wire:target to the whole form -->
                <form wire:submit.prevent="sendResetLink" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 ml-1">Work Email</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <x-heroicon-m-envelope class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" />
                            </div>
                            <input id="email" wire:model="email" type="email" required autofocus
                                class="block w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-4 focus:ring-blue-600/10 focus:border-blue-600 transition-all duration-200
                                @error('email') border-red-500 focus:ring-red-500/10 focus:border-red-500 @enderror"
                                placeholder="name@hospital.com">
                        </div>

                        @error('email')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1.5 px-1">
                                <x-heroicon-s-exclamation-circle class="w-4 h-4"/> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button type="submit"
                        wire:loading.attr="disabled"
                        wire:target="sendResetLink"
                        class="w-full flex justify-center items-center py-4 px-4 rounded-xl shadow-lg shadow-blue-600/20 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600 disabled:opacity-80 disabled:cursor-not-allowed transition-all transform active:scale-[0.98]">

                        <!-- Ensure target is set for loading so it triggers immediately -->
                        <span wire:loading.remove wire:target="sendResetLink">Send Reset Link</span>

                        <span wire:loading wire:target="sendResetLink" class="flex items-center gap-3">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Verifying...
                        </span>
                    </button>
                </form>

                <div class="mt-10 pt-8 border-t border-slate-100 dark:border-slate-800 text-center">
                    <a href="{{ tenant('id') ? route('tenant.login') : route('login') }}"
                       class="text-sm font-semibold text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 inline-flex items-center gap-2 transition-all group">
                        <x-heroicon-s-arrow-left class="w-4 h-4 transition-transform group-hover:-translate-x-1" />
                        Back to Sign In
                    </a>
                </div>
            </div>

            <!-- State 2: Success Message -->
            <div x-show="emailSent" x-cloak
                 class="py-4 text-center"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                <div class="relative mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-50 dark:bg-green-500/10 mb-8">
                    <div class="absolute inset-0 rounded-full animate-ping bg-green-200 dark:bg-green-500/20 opacity-20"></div>
                    <x-heroicon-o-check-circle class="h-12 w-12 text-green-500" />
                </div>

                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-3">Check your inbox</h2>
                <p class="text-slate-600 dark:text-slate-400 mb-8 text-base leading-relaxed">
                    We've sent a secure reset link to: <br>
                    <span class="font-bold text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded mt-2 inline-block" x-text="$wire.email"></span>
                </p>

                <div class="space-y-4">
                    <a href="{{ tenant('id') ? route('tenant.login') : route('login') }}"
                       class="block w-full py-4 px-4 rounded-xl bg-slate-900 dark:bg-white text-white dark:text-slate-900 text-sm font-bold hover:bg-slate-800 dark:hover:bg-slate-100 transition-all shadow-lg">
                        Return to Login
                    </a>

                    <div class="pt-4">
                        <p class="text-sm text-slate-500 mb-2">Didn't receive the email?</p>
                        <button wire:click="sendResetLink" wire:loading.attr="disabled"
                            class="text-sm text-blue-600 dark:text-blue-400 font-bold hover:underline disabled:opacity-50">
                            <span wire:loading.remove wire:target="sendResetLink">Click to resend</span>
                            <span wire:loading wire:target="sendResetLink">Resending link...</span>
                        </button>
                    </div>
                </div>

                <div class="mt-8 p-4 bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-900/20 rounded-xl text-left">
                    <div class="flex gap-3">
                        <x-heroicon-s-information-circle class="h-5 w-5 text-amber-600 flex-shrink-0" />
                        <p class="text-xs text-amber-800 dark:text-amber-400 leading-normal">
                            Note: If you don't see the email within 2 minutes, please check your <span class="font-bold uppercase">Spam</span> folder or contact IT Support.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
