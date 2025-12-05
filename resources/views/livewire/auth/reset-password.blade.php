<div lang="en" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    init() {
        if (this.darkMode || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            this.darkMode = true;
        }
    }
}" class="min-h-screen relative overflow-x-hidden font-sans antialiased text-slate-900 dark:text-slate-50 transition-colors duration-300">

    {{-- Background Effects --}}
    <div class="fixed inset-0 bg-[#f8fafc] -z-50 transition-colors duration-500 dark:hidden">
        <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(ellipse_at_top_left,_var(--tw-gradient-stops))] from-blue-50/50 via-purple-50/30 to-white"></div>
    </div>
    <div class="fixed inset-0 bg-[#0B1120] -z-50 transition-colors duration-500 hidden dark:block"></div>
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-40"><ul class="circles"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul></div>
    <div class="fixed inset-0 opacity-[0.04] dark:opacity-[0.07] pointer-events-none -z-30 mix-blend-overlay" style="background-image: url('https://grainy-gradients.vercel.app/noise.svg')"></div>

    <main class="flex items-center justify-center min-h-screen pt-10 pb-10">
        <div class="w-full max-w-md mx-auto px-6">
            <div class="bg-white/80 dark:bg-slate-800/60 backdrop-blur-xl rounded-3xl shadow-2xl p-8 border border-slate-200/50 dark:border-slate-700/50 relative">

                <div class="flex flex-col items-center mb-8">
                    <div class="h-14 w-14 bg-gradient-to-tr from-blue-500 to-purple-500 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-blue-500/30">
                        <x-heroicon-o-lock-closed class="h-8 w-8 text-white" />
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Set New Password</h1>
                    <p class="text-slate-600 dark:text-slate-400 text-sm mt-1">Create a strong password for your account.</p>
                </div>

                <form wire:submit.prevent="resetPassword" class="space-y-5">
                    <!-- Hidden Token -->
                    <input type="hidden" wire:model="token">

                    <!-- Email Field (Readonly) -->
                    <div>
                        <label class="block text-xs uppercase font-bold text-slate-500 dark:text-slate-400 mb-1 ml-1 tracking-wider">Email Address</label>
                        <div class="relative">
                            <input wire:model="email" type="email" readonly
                                class="block w-full px-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-100/50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 cursor-not-allowed">
                            <x-heroicon-s-check-circle class="absolute right-3 top-3.5 h-5 w-5 text-green-500" />
                        </div>
                    </div>

                    <!-- Password Field with Strength Meter -->
                    <div x-data="{
                        password: '',
                        show: false,
                        strength: 0,
                        checkStrength() {
                            let score = 0;
                            if (this.password.length > 7) score++;
                            if (this.password.match(/[A-Z]/)) score++;
                            if (this.password.match(/[0-9]/)) score++;
                            if (this.password.match(/[^a-zA-Z0-9]/)) score++;
                            this.strength = score;
                        }
                    }">
                        <label class="block text-xs uppercase font-bold text-slate-500 dark:text-slate-400 mb-1 ml-1 tracking-wider">New Password</label>
                        <div class="relative group">
                            <input wire:model.live="password" x-model="password" @input="checkStrength()" :type="show ? 'text' : 'password'"
                                class="block w-full px-4 py-3 border rounded-xl bg-white/50 dark:bg-slate-900/50 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all
                                @error('password') border-red-500 @else border-slate-300 dark:border-slate-600 @enderror"
                                placeholder="Min. 8 characters">

                            <button type="button" @click="show = !show" class="absolute right-3 top-3.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                <x-heroicon-s-eye x-show="!show" class="h-5 w-5" />
                                <x-heroicon-s-eye-slash x-show="show" x-cloak class="h-5 w-5" />
                            </button>
                        </div>
                        @error('password') <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p> @enderror

                        <!-- Dynamic Strength Meter -->
                        <div class="mt-2 h-1.5 w-full bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden flex" x-show="password.length > 0" x-transition>
                            <div class="h-full transition-all duration-500 ease-out"
                                 :class="{
                                    'w-1/4 bg-red-500': strength <= 1,
                                    'w-2/4 bg-orange-500': strength == 2,
                                    'w-3/4 bg-yellow-500': strength == 3,
                                    'w-full bg-green-500': strength >= 4
                                 }"></div>
                        </div>
                        <p class="text-[10px] mt-1 text-right text-slate-400" x-show="password.length > 0">
                            <span x-show="strength <= 1">Weak</span>
                            <span x-show="strength == 2">Fair</span>
                            <span x-show="strength == 3">Good</span>
                            <span x-show="strength >= 4" class="text-green-500 font-bold">Strong</span>
                        </p>
                    </div>

                    <!-- Confirm Password with Match Check -->
                    <div x-data="{ confirm: '' }">
                        <label class="block text-xs uppercase font-bold text-slate-500 dark:text-slate-400 mb-1 ml-1 tracking-wider">Confirm Password</label>
                        <div class="relative">
                            <input wire:model.live="password_confirmation" x-model="confirm" type="password"
                                class="block w-full px-4 py-3 border rounded-xl bg-white/50 dark:bg-slate-900/50 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all border-slate-300 dark:border-slate-600">

                            <!-- Visual Match Indicator -->
                            <div class="absolute right-3 top-3.5 transition-all duration-300 transform scale-0"
                                 :class="{ 'scale-100': confirm.length > 0 && confirm === $wire.password }">
                                <x-heroicon-s-check-circle class="h-5 w-5 text-green-500" />
                            </div>
                        </div>
                    </div>

                    <button type="submit" wire:loading.attr="disabled" class="w-full flex justify-center items-center px-6 py-3.5 border border-transparent text-base font-semibold rounded-xl shadow-lg shadow-blue-500/30 text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-70 disabled:cursor-not-allowed">
                        <span wire:loading.remove>Reset Password</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Updating...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </main>

    <style>
        [x-cloak] { display: none !important; }
        /* Same bubble animation styles as before */
        @keyframes floatUp { 0% { transform: translateY(0) rotate(0deg) scale(1); opacity: 0.8; } 100% { transform: translateY(-100vh) rotate(720deg) scale(1.5); opacity: 0; } }
        .circles { position: absolute; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; margin: 0; padding: 0; }
        .circles li { position: absolute; display: block; list-style: none; width: 20px; height: 20px; background: linear-gradient(to top right, rgba(59, 130, 246, 0.4), rgba(168, 85, 247, 0.4)); backdrop-filter: blur(4px); animation: floatUp 25s linear infinite; bottom: -150px; border-radius: 50%; }
        .dark .circles li { background: linear-gradient(to top right, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.02)); }
        .circles li:nth-child(1) { left: 25%; width: 80px; height: 80px; animation-delay: 0s; }
        .circles li:nth-child(2) { left: 10%; width: 20px; height: 20px; animation-delay: 2s; animation-duration: 12s; }
    </style>
</div>