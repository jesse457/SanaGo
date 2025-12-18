<div lang="en" x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true',
    init() {
        if (this.darkMode || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
            this.darkMode = true;
        }
    }
}" class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-950 font-sans antialiased text-slate-900 dark:text-slate-50 transition-colors duration-300 relative overflow-hidden">

    <!-- Subtle Corporate Background Pattern -->
    <div class="absolute inset-0 z-0 opacity-[0.4] dark:opacity-[0.1]"
        style="background-image: radial-gradient(#cbd5e1 1px, transparent 1px); background-size: 24px 24px;"></div>

    <div class="w-full max-w-md bg-white dark:bg-slate-900 shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none border border-slate-200 dark:border-slate-800 rounded-2xl p-8 sm:p-10 relative z-10">

        <div class="text-center mb-8">
            <div class="mx-auto h-12 w-12 bg-blue-50 dark:bg-blue-900/20 rounded-xl flex items-center justify-center mb-4">
                <x-heroicon-o-shield-check class="h-6 w-6 text-blue-600 dark:text-blue-400" />
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Set New Password</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-2">Ensure your account stays secure with a strong password.</p>
        </div>

        <form wire:submit.prevent="resetPassword" class="space-y-5">
            <input type="hidden" wire:model="token">

            <!-- Read-only Email -->
            <div class="group">
                <label class="block text-xs uppercase font-bold text-slate-500 dark:text-slate-400 mb-1.5 tracking-wider">Account Email</label>
                <div class="flex items-center w-full px-4 py-3 border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-800/50 text-slate-500 dark:text-slate-400 select-none">
                    <x-heroicon-s-envelope class="w-4 h-4 mr-3 text-slate-400" />
                    <span class="text-sm font-medium" x-text="'{{ $email }}'"></span>
                    <x-heroicon-s-lock-closed class="w-4 h-4 ml-auto text-slate-400" />
                </div>
            </div>

            <!-- Password with Professional Meter -->
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
                <label class="block text-xs uppercase font-bold text-slate-500 dark:text-slate-400 mb-1.5 tracking-wider">New Password</label>
                <div class="relative">
                    <input wire:model.live="password" x-model="password" @input="checkStrength()" :type="show ? 'text' : 'password'"
                        class="block w-full px-4 py-3 border rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all sm:text-sm
                        @error('password') border-red-500 focus:ring-red-500 @else border-slate-300 dark:border-slate-700 @enderror"
                        placeholder="••••••••">

                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 focus:outline-none">
                        <x-heroicon-s-eye x-show="!show" class="h-4 w-4" />
                        <x-heroicon-s-eye-slash x-show="show" x-cloak class="h-4 w-4" />
                    </button>
                </div>
                @error('password') <p class="mt-1.5 text-xs text-red-500 font-medium flex items-center gap-1"><x-heroicon-s-exclamation-circle class="w-3 h-3"/> {{ $message }}</p> @enderror

                <!-- Corporate Strength Meter -->
                <div class="mt-3 flex gap-1 h-1" x-show="password.length > 0" x-transition>
                    <div class="flex-1 rounded-full transition-colors duration-300" :class="strength > 0 ? (strength == 1 ? 'bg-red-500' : (strength <= 3 ? 'bg-yellow-500' : 'bg-green-500')) : 'bg-slate-200 dark:bg-slate-700'"></div>
                    <div class="flex-1 rounded-full transition-colors duration-300" :class="strength > 1 ? (strength <= 3 ? 'bg-yellow-500' : 'bg-green-500') : 'bg-slate-200 dark:bg-slate-700'"></div>
                    <div class="flex-1 rounded-full transition-colors duration-300" :class="strength > 2 ? (strength == 3 ? 'bg-yellow-500' : 'bg-green-500') : 'bg-slate-200 dark:bg-slate-700'"></div>
                    <div class="flex-1 rounded-full transition-colors duration-300" :class="strength > 3 ? 'bg-green-500' : 'bg-slate-200 dark:bg-slate-700'"></div>
                </div>
                <div class="flex justify-between items-center mt-2" x-show="password.length > 0">
                    <span class="text-[10px] text-slate-400">Must contain uppercase & number</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider"
                          :class="{
                            'text-red-500': strength <= 1,
                            'text-yellow-500': strength > 1 && strength <= 3,
                            'text-green-500': strength >= 4
                          }"
                          x-text="['Very Weak', 'Weak', 'Good', 'Strong', 'Excellent'][strength] || 'Very Weak'"></span>
                </div>
            </div>

            <!-- Confirmation -->
            <div x-data="{ confirm: '' }">
                <label class="block text-xs uppercase font-bold text-slate-500 dark:text-slate-400 mb-1.5 tracking-wider">Confirm Password</label>
                <div class="relative">
                    <input wire:model.live="password_confirmation" x-model="confirm" type="password"
                        class="block w-full px-4 py-3 border rounded-lg bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-blue-600 focus:border-blue-600 transition-all sm:text-sm border-slate-300 dark:border-slate-700">

                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none transition-all duration-200 scale-0"
                         :class="{ 'scale-100 opacity-100': confirm.length > 0 && confirm === $wire.password, 'scale-0 opacity-0': confirm !== $wire.password }">
                        <x-heroicon-s-check-circle class="h-5 w-5 text-green-500" />
                    </div>
                </div>
            </div>

            <button type="submit" wire:loading.attr="disabled" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-slate-900 dark:bg-blue-600 hover:bg-slate-800 dark:hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 dark:focus:ring-blue-600 disabled:opacity-70 disabled:cursor-not-allowed transition-all mt-4">
                <span wire:loading.remove>Update Password</span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Updating...
                </span>
            </button>
        </form>
    </div>

    <div class="absolute bottom-6 text-xs text-slate-400 z-10">
        &copy; {{ date('Y') }} Enterprise Health Systems
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</div>
