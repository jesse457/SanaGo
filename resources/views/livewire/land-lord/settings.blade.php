<div class="max-w-full mx-auto space-y-4 p-2 md:p-4  bg-slate-50 dark:bg-slate-900 min-h-screen font-sans">

    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Platform Settings</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Configure global parameters for your SaaS environment.</p>
        </div>

        {{-- Success Message --}}
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                class="mb-6 rounded-xl bg-emerald-50 border border-emerald-200 p-4 flex items-center gap-3 text-emerald-800 dark:bg-emerald-900/30 dark:border-emerald-800 dark:text-emerald-300 shadow-sm transition-all">
                <x-heroicon-s-check-circle class="w-5 h-5 flex-shrink-0" />
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <form wire:submit="saveSettings" class="space-y-6">

            {{-- Card 1: Platform Identity --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700/60">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">General Configuration</h3>
                    <p class="text-sm text-slate-500">Information visible to tenants and in system emails.</p>
                </div>

                <div class="p-6 grid gap-6 md:grid-cols-2">
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Platform Name</label>
                        <input type="text" wire:model="platformName"
                            class="block w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/50 text-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5">
                        @error('platformName') <span class="text-xs text-rose-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Support Email</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-s-envelope class="h-4 w-4 text-slate-400" />
                            </div>
                            <input type="email" wire:model="supportEmail"
                                class="block w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/50 text-slate-900 dark:text-white pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5">
                        </div>
                        <p class="mt-1 text-xs text-slate-500">Used for outgoing system notifications.</p>
                    </div>
                </div>
            </div>

            {{-- Card 2: Localization --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700/60">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Regional Settings</h3>
                    <p class="text-sm text-slate-500">Default timezone and currency for new tenants.</p>
                </div>

                <div class="p-6 grid gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">System Timezone</label>
                        <select wire:model="timezone" class="block w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/50 text-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5">
                            <option value="America/New_York">America/New York (EST)</option>
                            <option value="Europe/London">Europe/London (GMT)</option>
                            <option value="Africa/Douala">Africa/Douala (WAT)</option>
                            <option value="Asia/Tokyo">Asia/Tokyo (JST)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Default Currency</label>
                        <select wire:model="currency" class="block w-full rounded-xl border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/50 text-slate-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5">
                            <option value="USD">USD ($)</option>
                            <option value="EUR">EUR (€)</option>
                            <option value="XAF">XAF (FCFA)</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Card 3: Notifications --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-700/60">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">Admin Notifications</h3>
                    <p class="text-sm text-slate-500">Manage what alerts you receive via email.</p>
                </div>

                <div class="p-6 space-y-5">
                    <div class="flex items-start">
                        <div class="flex h-6 items-center">
                            <input id="notifyNewTenant" type="checkbox" wire:model="notifyNewTenant" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600 cursor-pointer">
                        </div>
                        <div class="ml-3">
                            <label for="notifyNewTenant" class="text-sm font-medium text-slate-900 dark:text-white cursor-pointer">New Tenant Registrations</label>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Receive an email whenever a new hospital or clinic signs up.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="flex h-6 items-center">
                            <input id="notifyTicketCreated" type="checkbox" wire:model="notifyTicketCreated" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-600 cursor-pointer">
                        </div>
                        <div class="ml-3">
                            <label for="notifyTicketCreated" class="text-sm font-medium text-slate-900 dark:text-white cursor-pointer">New Support Tickets</label>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Get notified immediately when a tenant submits a complaint.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end pt-4 pb-8">
                <button type="submit" wire:loading.attr="disabled" class="bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-xl font-bold shadow-lg shadow-indigo-500/20 transition-all transform hover:-translate-y-0.5 disabled:opacity-70 disabled:cursor-not-allowed">
                    <span wire:loading.remove>Save Changes</span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Saving...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
