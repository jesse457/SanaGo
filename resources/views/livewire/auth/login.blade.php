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
            class="fixed top-6 right-6 z-[60] max-w-sm w-full bg-white dark:bg-slate-900 border border-red-100 dark:border-red-900/30 shadow-2xl rounded-2xl overflow-hidden">
            <div class="p-4 flex items-center gap-4">
                <div class="flex-shrink-0 w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                    <x-heroicon-s-x-circle class="h-6 w-6 text-red-600 dark:text-red-400" />
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold">Access Denied</p>
                    <p class="text-xs text-slate-500 mt-0.5">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Left Side: Login Form -->
    <div class="flex flex-col justify-center px-6 py-12 lg:px-16 xl:px-24 relative overflow-hidden">
        <!-- Theme Toggle -->
        <div class="absolute top-8 right-8">
            <button @click="darkMode = !darkMode; document.documentElement.classList.toggle('dark'); localStorage.setItem('darkMode', darkMode)"
                class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white/50 dark:bg-slate-900/50 backdrop-blur-md">
                <x-heroicon-o-sun x-show="darkMode" class="w-5 h-5 text-yellow-500" />
                <x-heroicon-o-moon x-show="!darkMode" class="w-5 h-5 text-slate-600" />
            </button>
        </div>

        <div class="w-full max-w-md mx-auto relative z-10">
            <div class="mb-10">
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">System Login</h2>
                <p class="mt-3 text-slate-500 dark:text-slate-400 font-medium">Please enter your credentials to access the clinical dashboard.</p>
            </div>

            <form wire:submit.prevent="authenticate" class="space-y-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 ml-1">Email / Staff ID</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-heroicon-m-user class="h-5 w-5 text-slate-400 transition-colors" />
                        </div>
                        <input id="email" wire:model="email" type="text" required
                            class="block w-full pl-11 pr-4 py-3.5 rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:ring-blue-600 focus:border-blue-600 transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2 ml-1">Password</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-heroicon-m-lock-closed class="h-5 w-5 text-slate-400 transition-colors" />
                        </div>
                        <input id="password" wire:model="password" :type="showPassword ? 'text' : 'password'" required
                            class="block w-full pl-11 pr-12 py-3.5 rounded-xl border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50 text-slate-900 dark:text-white focus:ring-blue-600 focus:border-blue-600 transition-all">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button type="button" @click="showPassword = !showPassword" class="text-slate-400 hover:text-slate-600">
                                <x-heroicon-m-eye x-show="!showPassword" class="h-5 w-5" />
                                <x-heroicon-m-eye-slash x-show="showPassword" x-cloak class="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between py-1">
                    <label class="flex items-center cursor-pointer">
                        <input wire:model="remember" type="checkbox" class="h-5 w-5 border-slate-300 rounded text-blue-600">
                        <span class="ml-3 text-sm text-slate-600 dark:text-slate-400">Keep me logged in</span>
                    </label>
                    <a href="#" class="text-sm font-bold text-blue-600">Forgot Password?</a>
                </div>

                <div class="pt-2">
                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full flex justify-center items-center py-4 px-4 rounded-xl shadow-lg text-sm font-extrabold text-white bg-blue-600 hover:bg-blue-700 transition-all">
                        <span wire:loading.remove wire:target="authenticate">Sign In to Dashboard</span>
                        <span wire:loading wire:target="authenticate">Verifying Security...</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="absolute bottom-8 left-0 right-0 text-center">
            <p class="text-[10px] uppercase tracking-widest font-bold text-slate-400 dark:text-slate-600">
                &copy; {{ date('Y') }} Secure Clinical Access Portal
            </p>
        </div>
    </div>

    <!-- Right Side -->
    <div class="hidden lg:block relative overflow-hidden bg-slate-900">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600/30 to-slate-950 z-10"></div>
        <img class="absolute inset-0 h-full w-full object-cover animate-soft-zoom"
            src="https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1950&q=80">

        <div class="absolute inset-0 z-20 flex flex-col justify-between p-20">
            <div class="flex items-center gap-2">
                <span class="text-2xl font-bold tracking-tight text-white tracking-widest uppercase">Sana<span class="text-blue-400">Go</span></span>
            </div>
            <div class="max-w-xl">
                <h3 class="text-4xl font-extrabold text-white mb-6 leading-tight">Precision Healthcare <br><span class="text-blue-400">Enterprise Management</span></h3>
                <p class="text-lg text-slate-300 border-l-4 border-blue-500 pl-8">Unified operations for medical professionals and facility management.</p>
            </div>
            <div class="flex items-center gap-8 text-white/50 text-sm font-semibold uppercase tracking-widest">
                <div class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div> Secure Node Active</div>
            </div>
        </div>
    </div>
</div>
