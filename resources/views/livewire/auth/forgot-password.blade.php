<div lang="en" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    emailSent: @entangle('emailSent'),
    init() {
        if (this.darkMode || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            this.darkMode = true;
        }
    }
}" class="min-h-screen grid lg:grid-cols-2 font-sans antialiased text-slate-900 dark:text-slate-50 transition-colors duration-300">

    <!-- Left Side: Brand & Visuals (Hidden on Mobile) -->
    <div class="hidden lg:flex relative flex-col justify-between p-12 bg-slate-900 overflow-hidden">
        <!-- Abstract Corporate Background -->
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/90 to-slate-900/90 z-10"></div>
            <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070&auto=format&fit=crop"
                 class="absolute inset-0 w-full h-full object-cover grayscale opacity-40 mix-blend-overlay" alt="Building">
            <!-- Subtle Grid Pattern -->
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 32px 32px;"></div>
        </div>

        <div class="relative z-20">
             <img class="h-10 w-auto mb-6" src="{{ Storage::disk('central_public')->url('images/logo-white.png') }}"
                  onerror="this.style.display='none'" alt="Logo">
        </div>

        <div class="relative z-20 max-w-lg">
            <h2 class="text-3xl font-bold text-white mb-4">Secure Account Recovery</h2>
            <p class="text-slate-300 leading-relaxed text-lg">We use industry-standard encryption to protect your data. Follow the steps to regain access to your medical portal.</p>
        </div>

        <div class="relative z-20 text-slate-400 text-sm">
            &copy; {{ date('Y') }} Enterprise Health Systems.
        </div>
    </div>

    <!-- Right Side: The Form -->
    <div class="flex items-center justify-center p-6 bg-slate-50 dark:bg-slate-950 relative">
        <!-- Minimal Grid Background for Right Side -->
        <div class="absolute inset-0 opacity-[0.4] dark:opacity-[0.1]"
             style="background-image: linear-gradient(#cbd5e1 1px, transparent 1px), linear-gradient(to right, #cbd5e1 1px, transparent 1px); background-size: 40px 40px; mask-image: linear-gradient(to bottom, transparent, black 10%, black 90%, transparent);"></div>

        <div class="w-full max-w-md bg-white dark:bg-slate-900 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200 dark:border-slate-800 rounded-2xl p-10 relative z-10 transition-all duration-500"
             :class="emailSent ? 'scale-95 opacity-90 blur-[1px] pointer-events-none' : 'scale-100 opacity-100'">

            <!-- Mobile Logo -->
            <div class="lg:hidden flex justify-center mb-8">
                <img class="h-10 w-auto" src="{{ Storage::disk('central_public')->url('images/logo.png') }}" alt="Logo">
            </div>

            <div class="mb-8">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Forgot password?</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">No worries, we'll send you reset instructions.</p>
            </div>

            <form wire:submit.prevent="sendResetLink" class="space-y-6">
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Work Email</label>
                    <input id="email" wire:model.blur="email" type="email" required autofocus
                        class="block w-full px-4 py-3 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition-all duration-200
                        @error('email') border-red-500 focus:ring-red-500/20 focus:border-red-500 @enderror"
                        placeholder="name@hospital.com">

                    @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1.5">
                            <x-heroicon-s-exclamation-circle class="w-4 h-4"/> {{ $message }}
                        </p>
                    @enderror
                </div>

                <button type="submit" wire:loading.attr="disabled" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-slate-900 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 dark:focus:ring-blue-600 disabled:opacity-70 disabled:cursor-not-allowed transition-all">
                    <span wire:loading.remove>Send Instructions</span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Processing...
                    </span>
                </button>
            </form>
@if (tenant('id'))
            <!-- Back to Login Link -->
            <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-center">
                <a href="{{ route('tenant.login') }}" class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white inline-flex items-center gap-2 transition-colors">
                    <x-heroicon-s-arrow-left class="w-4 h-4" />
                    Back to Sign In
                </a>
            </div>
            @else
             <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800 text-center">
                <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white inline-flex items-center gap-2 transition-colors">
                    <x-heroicon-s-arrow-left class="w-4 h-4" />
                    Back to Sign In
                </a>
            </div>
        </div>

@endif


        <!-- Success State Overlay -->
        <div x-show="emailSent" x-cloak
             class="absolute inset-0 flex items-center justify-center z-20 backdrop-blur-sm bg-white/50 dark:bg-slate-950/50"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">

             <div class="w-full max-w-md bg-white dark:bg-slate-900 shadow-2xl rounded-2xl p-10 text-center border border-slate-200 dark:border-slate-800">
                <div class="mx-auto flex items-center justify-center h-14 w-14 rounded-full bg-green-50 dark:bg-green-900/20 mb-6">
                    <x-heroicon-o-check class="h-7 w-7 text-green-600 dark:text-green-400" />
                </div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2">Check your email</h2>
                <p class="text-slate-600 dark:text-slate-400 mb-6 text-sm leading-relaxed">We've sent a password reset link to <br><span class="font-semibold text-slate-900 dark:text-white" x-text="$wire.email"></span></p>

                <div class="space-y-3">
                    <a href="{{ route('login') }}" class="block w-full py-2.5 px-4 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white text-sm font-medium hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                        Return to Login
                    </a>
                    <button wire:click="sendResetLink" class="text-xs text-slate-500 hover:text-blue-600 dark:hover:text-blue-400 font-medium transition-colors">
                        Didn't receive it? Click to resend
                    </button>
                </div>
             </div>
        </div>
    </div>
</div>
