<div x-data="{ activeTab: 'general' }" class="min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300 pb-20">

    {{-- STICKY HEADER --}}
    <header class="sticky top-0 z-30 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <a href="{{ route('landlord.dashboard') }}" class="text-slate-400 dark:text-slate-500 text-sm font-bold hover:text-indigo-600 transition-colors">Landlord</a>
                <x-heroicon-m-chevron-right class="w-4 h-4 text-slate-400" />
                <h1 class="text-slate-900 dark:text-white text-lg font-bold">Platform Settings</h1>
            </div>
            <div class="flex items-center gap-4">
                <span class="hidden md:block text-xs font-medium text-slate-500 bg-slate-100 dark:bg-gray-800 px-2 py-1 rounded">v2.4.0</span>
                <button wire:click="saveSettings" wire:loading.attr="disabled"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-5 py-2 rounded-xl shadow-md hover:shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-2">
                    <span wire:loading.remove>Save Changes</span>
                    <span wire:loading class="flex items-center gap-2">
                        <x-heroicon-o-arrow-path class="animate-spin h-4 w-4"/> Saving...
                    </span>
                </button>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Success Toast --}}
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms x-init="setTimeout(() => show = false, 4000)"
                class="fixed top-20 right-6 z-50 bg-emerald-500 text-white px-6 py-4 rounded-xl shadow-xl flex items-center gap-3 border border-emerald-400/50 backdrop-blur-sm">
                <x-heroicon-s-check-circle class="w-6 h-6 text-white" />
                <div>
                    <h4 class="font-bold text-sm">Success</h4>
                    <p class="text-xs text-emerald-50 opacity-90 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- Sidebar Navigation --}}
            <nav class="lg:col-span-3">
                <div class="sticky top-24 space-y-1">
                    @php
                        $tabs = [
                            ['id' => 'general', 'label' => 'General & Branding', 'icon' => 'adjustments-horizontal'],
                            ['id' => 'billing', 'label' => 'Billing & Gateways', 'icon' => 'credit-card'],
                            ['id' => 'defaults', 'label' => 'Tenant Defaults', 'icon' => 'building-library'],
                            ['id' => 'mail', 'label' => 'Email & SMTP', 'icon' => 'envelope'],
                            ['id' => 'security', 'label' => 'Security & Access', 'icon' => 'shield-check'],
                        ];
                    @endphp

                    @foreach($tabs as $tab)
                        <button @click="activeTab = '{{ $tab['id'] }}'"
                            :class="activeTab === '{{ $tab['id'] }}'
                                ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow-sm ring-1 ring-slate-200 dark:ring-gray-700'
                                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-gray-800/50'"
                            class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all">
                            @svg('heroicon-o-'.$tab['icon'], 'w-5 h-5')
                            {{ $tab['label'] }}
                        </button>
                    @endforeach

                    <div class="h-px bg-slate-200 dark:bg-gray-800 my-4 mx-4"></div>

                    <button @click="activeTab = 'maintenance'"
                        :class="activeTab === 'maintenance' ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 shadow-sm ring-1 ring-rose-200 dark:ring-rose-800' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-gray-800/50'"
                        class="w-full flex items-center gap-3 px-4 py-3 text-sm font-bold rounded-xl transition-all group">
                        <x-heroicon-o-wrench-screwdriver class="w-5 h-5 group-hover:text-rose-500" />
                        System Maintenance
                    </button>
                </div>
            </nav>

            {{-- Main Content Area --}}
            <div class="lg:col-span-9 space-y-6">

                {{-- Tab: General --}}
                <div x-show="activeTab === 'general'" x-transition:enter.duration.300ms class="space-y-6">
                    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-slate-200 dark:border-gray-800 p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Platform Identity</h3>
                        <p class="text-sm text-slate-500 mb-6">White-label configuration for tenant dashboards.</p>

                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="col-span-2">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">Platform Logo</label>
                                <div class="flex items-center gap-6">
                                    <div class="h-24 w-24 rounded-2xl bg-slate-50 dark:bg-gray-800 border border-dashed border-slate-300 dark:border-gray-700 flex items-center justify-center overflow-hidden relative group cursor-pointer hover:border-indigo-500 transition-colors">
                                        @if($logo)
                                            <img src="{{ $logo->temporaryUrl() }}" class="object-contain p-2 h-full w-full">
                                        @else
                                            <x-heroicon-o-photo class="w-8 h-8 text-slate-400" />
                                        @endif
                                        <input type="file" wire:model="logo" class="absolute inset-0 opacity-0 cursor-pointer">
                                    </div>
                                    <div>
                                        <button class="px-4 py-2 bg-white dark:bg-gray-800 border border-slate-300 dark:border-gray-700 rounded-xl text-xs font-bold uppercase tracking-wide hover:bg-slate-50 dark:hover:bg-gray-700 transition relative">
                                            Upload New
                                            <input type="file" wire:model="logo" class="absolute inset-0 opacity-0 cursor-pointer">
                                        </button>
                                        <p class="mt-2 text-xs text-slate-500">Recommended: 500x500px PNG (Transparent)</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">App Name</label>
                                <input type="text" wire:model="platformName" class="w-full rounded-xl border-slate-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2.5">
                            </div>
                            <div class="col-span-1">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Support Email</label>
                                <input type="email" wire:model="supportEmail" class="w-full rounded-xl border-slate-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-slate-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 text-sm py-2.5">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab: Billing & Gateways (NEW) --}}
                <div x-cloak x-show="activeTab === 'billing'" x-transition:enter.duration.300ms class="space-y-6">
                    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-slate-200 dark:border-gray-800 p-6 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Payment Gateways</h3>
                                <p class="text-sm text-slate-500">Configure keys for Stripe and PayPal.</p>
                            </div>
                            <x-heroicon-o-credit-card class="w-8 h-8 text-indigo-100 dark:text-indigo-900/50" />
                        </div>

                        <div class="space-y-6">
                            {{-- Stripe --}}
                            <div class="p-5 rounded-xl border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-800/50">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded-full {{ $stripeEnabled ? 'bg-emerald-500' : 'bg-slate-300' }}"></div>
                                        <span class="font-bold text-slate-700 dark:text-white">Stripe Integration</span>
                                    </div>
                                    <button wire:click="$toggle('stripeEnabled')" class="text-xs font-bold text-indigo-600 hover:underline">{{ $stripeEnabled ? 'Disable' : 'Enable' }}</button>
                                </div>
                                <div class="grid md:grid-cols-2 gap-4 {{ !$stripeEnabled ? 'opacity-50 pointer-events-none' : '' }}">
                                    <div>
                                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Publishable Key</label>
                                        <input type="password" wire:model="stripeKey" class="w-full rounded-lg border-slate-200 dark:border-gray-700 text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold uppercase text-slate-500 mb-1">Secret Key</label>
                                        <input type="password" wire:model="stripeSecret" class="w-full rounded-lg border-slate-200 dark:border-gray-700 text-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab: Tenant Defaults (NEW) --}}
                <div x-cloak x-show="activeTab === 'defaults'" x-transition:enter.duration.300ms class="space-y-6">
                    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-slate-200 dark:border-gray-800 p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Tenant Provisioning</h3>
                        <p class="text-sm text-slate-500 mb-6">Default settings applied to newly created tenants.</p>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="col-span-1">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Free Trial Period (Days)</label>
                                <div class="flex">
                                    <input type="number" wire:model="trialDays" class="w-full rounded-l-xl border-slate-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-2.5">
                                    <span class="inline-flex items-center px-4 rounded-r-xl border border-l-0 border-slate-300 dark:border-gray-700 bg-slate-50 dark:bg-gray-900 text-slate-500 text-sm font-bold">Days</span>
                                </div>
                            </div>

                            <div class="col-span-1">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Default DB Connection</label>
                                <select wire:model="dbConnection" class="w-full rounded-xl border-slate-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm py-2.5">
                                    <option value="mysql">MySQL (System Default)</option>
                                    <option value="pgsql">PostgreSQL</option>
                                    <option value="sqlite">SQLite (Dev Only)</option>
                                </select>
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
                                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4 leading-relaxed">
                                    Enabling this will prevent tenants from logging in. Only Super Admins will have access.
                                    Use this for database migrations or critical updates.
                                </p>

                                <div class="flex items-center gap-3">
                                    <button type="button" wire:click="$toggle('maintenanceMode')"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-rose-600 {{ $maintenanceMode ? 'bg-rose-600' : 'bg-slate-300 dark:bg-slate-700' }}">
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $maintenanceMode ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                    </button>
                                    <span class="text-sm font-bold {{ $maintenanceMode ? 'text-rose-600' : 'text-slate-500' }}">
                                        {{ $maintenanceMode ? 'System is DOWN' : 'System is LIVE' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
