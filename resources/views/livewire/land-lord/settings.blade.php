<div
    x-data="{
        activeTab: 'general',
        maintenanceMode: @entangle('maintenanceMode'),
        toggleMaintenance() { this.maintenanceMode = !this.maintenanceMode }
    }"
    class="min-h-screen bg-slate-50 dark:bg-slate-950 font-sans selection:bg-indigo-500 selection:text-white pb-20"
>
    {{-- Top Navigation / Breadcrumbs --}}
    <header class="sticky top-0 z-30 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-slate-400 dark:text-slate-500 text-sm font-medium">Landlord</span>
                <x-heroicon-m-chevron-right class="w-4 h-4 text-slate-400" />
                <h1 class="text-slate-900 dark:text-white text-lg font-bold">Platform Settings</h1>
            </div>
            <div class="flex items-center gap-4">
                <span class="hidden md:block text-xs text-slate-500">Last updated: {{ now()->format('M d, Y H:i') }}</span>
                <button wire:click="saveSettings" wire:loading.attr="disabled" class="bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium px-5 py-2 rounded-lg shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-2">
                    <span wire:loading.remove>Save Changes</span>
                    <span wire:loading class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Saving...
                    </span>
                </button>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- Success Message --}}
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms x-init="setTimeout(() => show = false, 4000)"
                class="fixed bottom-6 right-6 z-50 bg-emerald-500 text-white px-6 py-4 rounded-xl shadow-xl flex items-center gap-3">
                <x-heroicon-s-check-circle class="w-6 h-6 text-white/90" />
                <div>
                    <h4 class="font-bold text-sm">Success</h4>
                    <p class="text-xs text-emerald-50 opacity-90">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- Sidebar Navigation --}}
            <nav class="lg:col-span-3 space-y-1">
                <div class="sticky top-24 space-y-1">
                    <button @click="activeTab = 'general'"
                        :class="activeTab === 'general' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm ring-1 ring-slate-200 dark:ring-slate-700' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50'"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all">
                        <x-heroicon-o-adjustments-horizontal class="w-5 h-5" />
                        General & Branding
                    </button>

                    <button @click="activeTab = 'localization'"
                        :class="activeTab === 'localization' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm ring-1 ring-slate-200 dark:ring-slate-700' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50'"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all">
                        <x-heroicon-o-globe-alt class="w-5 h-5" />
                        Localization
                    </button>

                    <button @click="activeTab = 'security'"
                        :class="activeTab === 'security' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm ring-1 ring-slate-200 dark:ring-slate-700' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50'"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all">
                        <x-heroicon-o-shield-check class="w-5 h-5" />
                        Security & Access
                    </button>

                    <button @click="activeTab = 'notifications'"
                        :class="activeTab === 'notifications' ? 'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm ring-1 ring-slate-200 dark:ring-slate-700' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50'"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all">
                        <x-heroicon-o-bell class="w-5 h-5" />
                        Notifications
                    </button>

                    <button @click="activeTab = 'maintenance'"
                        :class="activeTab === 'maintenance' ? 'bg-rose-50 dark:bg-rose-900/10 text-rose-600 dark:text-rose-400 shadow-sm ring-1 ring-rose-200 dark:ring-rose-800' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50'"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl transition-all group">
                        <x-heroicon-o-wrench-screwdriver class="w-5 h-5 group-hover:text-rose-500" />
                        System Maintenance
                    </button>
                </div>
            </nav>

            {{-- Main Content Area --}}
            <div class="lg:col-span-9 space-y-6">

                {{-- Tab: General & Branding --}}
                <div x-show="activeTab === 'general'" x-transition:enter.duration.300ms class="space-y-6">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Platform Identity</h3>
                        <p class="text-sm text-slate-500 mb-6">Configure how the SaaS appears to your tenants.</p>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Platform Logo (White Label)</label>
                                <div class="flex items-center gap-6">
                                    <div class="h-20 w-20 rounded-lg bg-slate-100 dark:bg-slate-700 border border-dashed border-slate-300 dark:border-slate-600 flex items-center justify-center overflow-hidden relative group cursor-pointer">
                                        @if($logo)
                                            <img src="{{ $logo->temporaryUrl() }}" class="object-cover h-full w-full">
                                        @else
                                            <x-heroicon-o-photo class="w-8 h-8 text-slate-400" />
                                        @endif
                                        <input type="file" wire:model="logo" class="absolute inset-0 opacity-0 cursor-pointer">
                                        <div class="absolute inset-0 bg-black/40 hidden group-hover:flex items-center justify-center">
                                            <span class="text-xs text-white">Change</span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="relative">
                                            <input type="file" wire:model="logo" class="hidden" id="logo-upload">
                                            <label for="logo-upload" class="cursor-pointer inline-flex items-center px-4 py-2 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg font-semibold text-xs text-slate-700 dark:text-slate-200 uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-600 transition ease-in-out duration-150">
                                                Upload New Logo
                                            </label>
                                            <p class="mt-2 text-xs text-slate-500">PNG, JPG or SVG. Max 2MB. Recommended 500x150px.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Platform Name</label>
                                <input type="text" wire:model="platformName" class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-indigo-500">
                            </div>

                            <div class="col-span-1">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Support Email</label>
                                <input type="email" wire:model="supportEmail" class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-indigo-500">
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Global Announcement Banner</label>
                                <textarea wire:model="globalAnnouncement" rows="2" class="form-textarea w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-indigo-500" placeholder="Message visible to all tenants (e.g., Scheduled maintenance...)"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab: Localization --}}
                <div x-cloak x-show="activeTab === 'localization'" x-transition:enter.duration.300ms class="space-y-6">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Regional Defaults</h3>
                        <p class="text-sm text-slate-500 mb-6">Set the default operational standards for new tenants.</p>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">System Timezone</label>
                                <select wire:model="timezone" class="form-select w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-indigo-500">
                                    <option value="America/New_York">America/New York (EST)</option>
                                    <option value="Europe/London">Europe/London (GMT)</option>
                                    <option value="Africa/Douala">Africa/Douala (WAT)</option>
                                    <option value="Asia/Tokyo">Asia/Tokyo (JST)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Default Currency</label>
                                <select wire:model="currency" class="form-select w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-indigo-500">
                                    <option value="USD">USD ($)</option>
                                    <option value="EUR">EUR (€)</option>
                                    <option value="XAF">XAF (FCFA)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Date Format</label>
                                <select wire:model="dateFormat" class="form-select w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-indigo-500">
                                    <option value="Y-m-d">YYYY-MM-DD (ISO)</option>
                                    <option value="d/m/Y">DD/MM/YYYY</option>
                                    <option value="m/d/Y">MM/DD/YYYY</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab: Security --}}
                <div x-cloak x-show="activeTab === 'security'" x-transition:enter.duration.300ms class="space-y-6">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Security Policies</h3>
                        <p class="text-sm text-slate-500 mb-6">Manage access controls and tenant security requirements.</p>

                        <div class="space-y-6">
                            <div class="flex items-center justify-between p-4 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700">
                                <div>
                                    <h4 class="font-medium text-slate-900 dark:text-white">Enforce 2FA for Hospital Admins</h4>
                                    <p class="text-xs text-slate-500 mt-1">Require Two-Factor Authentication for all tenant admin accounts.</p>
                                </div>
                                {{-- Modern Toggle --}}
                                <button type="button" role="switch" wire:click="$toggle('enforce2fa')"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2 {{ $enforce2fa ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700' }}">
                                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $enforce2fa ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Admin Session Timeout (Minutes)</label>
                                    <input type="number" wire:model="sessionTimeout" class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Min Password Length</label>
                                    <input type="number" wire:model="passwordMinLength" class="form-input w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab: Notifications --}}
                <div x-cloak x-show="activeTab === 'notifications'" x-transition:enter.duration.300ms class="space-y-6">
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Email Alerts</h3>
                        <p class="text-sm text-slate-500 mb-6">Decide what happens in your ecosystem that needs your attention.</p>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between py-3 border-b border-slate-100 dark:border-slate-700/50 last:border-0">
                                <div>
                                    <h4 class="text-sm font-medium text-slate-900 dark:text-white">New Tenant Registration</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">When a new hospital signs up or is created.</p>
                                </div>
                                <button type="button" wire:click="$toggle('notifyNewTenant')"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 {{ $notifyNewTenant ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700' }}">
                                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $notifyNewTenant ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                </button>
                            </div>

                            <div class="flex items-center justify-between py-3 border-b border-slate-100 dark:border-slate-700/50 last:border-0">
                                <div>
                                    <h4 class="text-sm font-medium text-slate-900 dark:text-white">Critical System Errors</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">Server resource warnings or database connection failures.</p>
                                </div>
                                <button type="button" wire:click="$toggle('notifyCriticalErrors')"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 {{ $notifyCriticalErrors ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700' }}">
                                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $notifyCriticalErrors ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                </button>
                            </div>

                            <div class="flex items-center justify-between py-3 border-b border-slate-100 dark:border-slate-700/50 last:border-0">
                                <div>
                                    <h4 class="text-sm font-medium text-slate-900 dark:text-white">Support Tickets</h4>
                                    <p class="text-xs text-slate-500 mt-0.5">New high-priority tickets from tenants.</p>
                                </div>
                                <button type="button" wire:click="$toggle('notifyTicketCreated')"
                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 {{ $notifyTicketCreated ? 'bg-indigo-600' : 'bg-slate-200 dark:bg-slate-700' }}">
                                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $notifyTicketCreated ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab: Maintenance --}}
                <div x-cloak x-show="activeTab === 'maintenance'" x-transition:enter.duration.300ms class="space-y-6">
                    <div class="bg-rose-50 dark:bg-rose-950/20 rounded-2xl border border-rose-100 dark:border-rose-900/50 p-6">
                        <div class="flex items-start gap-4">
                            <div class="p-3 bg-rose-100 dark:bg-rose-900/40 rounded-full text-rose-600 dark:text-rose-400">
                                <x-heroicon-s-exclamation-triangle class="w-6 h-6" />
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Maintenance Mode</h3>
                                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                                    Enabling this will prevent tenants from logging in. Only Super Admins will have access.
                                    <br class="hidden md:block">Use this for database migrations or critical updates.
                                </p>

                                <div class="flex items-center gap-3">
                                    <button type="button" wire:click="$toggle('maintenanceMode')"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-rose-600 {{ $maintenanceMode ? 'bg-rose-600' : 'bg-slate-300 dark:bg-slate-700' }}">
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $maintenanceMode ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                    <span class="text-sm font-semibold {{ $maintenanceMode ? 'text-rose-600' : 'text-slate-500' }}">
                                        {{ $maintenanceMode ? 'System is currently under maintenance' : 'System is Live' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4">System Utilities</h3>
                        <div class="grid md:grid-cols-2 gap-4">
                             <button wire:click="clearCache" class="flex items-center justify-between p-4 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition text-left group">
                                <div>
                                    <p class="font-medium text-slate-700 dark:text-slate-200">Clear Application Cache</p>
                                    <p class="text-xs text-slate-500">Routes, Views, and Config</p>
                                </div>
                                <x-heroicon-o-trash class="w-5 h-5 text-slate-400 group-hover:text-slate-600" />
                            </button>
                             <button wire:click="backupNow" class="flex items-center justify-between p-4 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition text-left group">
                                <div>
                                    <p class="font-medium text-slate-700 dark:text-slate-200">Trigger Database Backup</p>
                                    <p class="text-xs text-slate-500">Run manual backup to S3</p>
                                </div>
                                <x-heroicon-o-server class="w-5 h-5 text-slate-400 group-hover:text-slate-600" />
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
