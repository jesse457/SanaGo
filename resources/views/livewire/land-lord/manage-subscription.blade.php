<div class="   min-h-screen font-sans max-w-full mx-auto space-y-4 p-2 md:p-4 transition-colors duration-300">

    {{-- 1. Navigation / Breadcrumbs --}}
    <nav class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700/60 sticky top-0 z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center text-sm text-slate-500 dark:text-slate-400 mb-2">
                <span class="hover:text-indigo-600 transition cursor-pointer">Tenants</span>
                <x-heroicon-s-chevron-right class="w-3 h-3 mx-2 text-slate-300" />
                <span class="text-slate-900 dark:text-white font-medium truncate">{{ $tenant->id }}</span>
            </div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                {{-- Tenant Identity --}}
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl shadow-md">
                        {{ substr($tenant->name ?? 'T', 0, 1) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900 dark:text-white leading-tight flex items-center gap-2">
                            {{ $tenant->name ?? 'Unnamed Tenant' }}
                            @if(isset($tenant->domains))
                                <a href="https://{{ $tenant->domains->first()->domain }}" target="_blank" class="text-slate-400 hover:text-indigo-500 transition">
                                    <x-heroicon-m-arrow-top-right-on-square class="w-5 h-5" />
                                </a>
                            @endif
                        </h1>
                        <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400">
                            <span class="flex items-center gap-1">
                                <x-heroicon-s-building-office class="w-3.5 h-3.5" /> {{ $tenant->name ?? 'N/A' }}
                            </span>
                            <span class="text-slate-300">|</span>
                            <span class="flex items-center gap-1">
                                <x-heroicon-s-globe-alt class="w-3.5 h-3.5" /> {{ $tenant->domains->first()->domain ?? 'No Domain' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Global Actions --}}
                <div class="flex items-center gap-3">
                    <button wire:click="loginAsTenant" class="px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm hover:bg-slate-50 dark:hover:bg-slate-600 transition-all flex items-center gap-2">
                        <x-heroicon-s-user-circle class="w-4 h-4 text-slate-400" />
                        Impersonate
                    </button>
                    <button class="px-4 py-2 text-sm font-bold text-white bg-slate-900 dark:bg-indigo-600 hover:bg-slate-800 dark:hover:bg-indigo-700 rounded-lg shadow-sm transition-all flex items-center gap-2">
                        <x-heroicon-s-pencil-square class="w-4 h-4" />
                        Edit Details
                    </button>
                </div>
            </div>
        </div>
    </nav>

    {{-- 2. Main Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        @if(!$subscription)
            {{-- Empty State if no subscription exists --}}
            <div class="bg-white dark:bg-slate-800 p-8 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 text-center">
                <x-heroicon-o-document-plus class="w-12 h-12 mx-auto text-slate-400" />
                <h3 class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">No Subscription Found</h3>
                <p class="mt-1 text-sm text-slate-500">This tenant does not have an active subscription record.</p>
                <button class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">Create Subscription</button>
            </div>
        @else

            {{-- Top Stats Row --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Status --}}
                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700/60 shadow-sm flex flex-col justify-between">
                    <span class="text-xs font-semibold uppercase text-slate-500 tracking-wider">Subscription Status</span>
                    <div class="mt-2 flex items-center gap-2">
                        @if($subscription->isActive())
                            <span class="relative flex h-3 w-3">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                            </span>
                            <span class="text-lg font-bold text-emerald-700 dark:text-emerald-400">{{ $subscription->getStatusDisplayName() }}</span>
                        @elseif($subscription->isCancelled())
                            <span class="h-3 w-3 rounded-full bg-amber-500"></span>
                            <span class="text-lg font-bold text-amber-700 dark:text-amber-400">Cancelled</span>
                        @else
                            <span class="h-3 w-3 rounded-full bg-rose-500"></span>
                            <span class="text-lg font-bold text-rose-700 dark:text-rose-400">{{ $subscription->getStatusDisplayName() }}</span>
                        @endif
                    </div>
                </div>

                {{-- Revenue --}}
                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700/60 shadow-sm flex flex-col justify-between">
                    <span class="text-xs font-semibold uppercase text-slate-500 tracking-wider">Plan Value</span>
                    <div class="mt-2 text-lg font-bold text-slate-900 dark:text-white">
                        {{ number_format($subscription->amount) }} <span class="text-sm font-normal text-slate-500">{{ $subscription->currency ?? 'FCFA' }}</span>
                        <span class="text-xs text-slate-400 block font-normal capitalize">{{ $subscription->billing_cycle }}ly</span>
                    </div>
                </div>

                {{-- Join Date --}}
                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700/60 shadow-sm flex flex-col justify-between">
                    <span class="text-xs font-semibold uppercase text-slate-500 tracking-wider">Tenant Since</span>
                    <div class="mt-2 text-lg font-bold text-slate-900 dark:text-white">
                        {{ $tenant->created_at->format('M d, Y') }}
                    </div>
                </div>

                {{-- Admin Info --}}
                <div class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700/60 shadow-sm flex flex-col justify-between">
                    <span class="text-xs font-semibold uppercase text-slate-500 tracking-wider">Admin Contact</span>
                    <div class="mt-2 overflow-hidden">
                        <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $tenant->admin_name ?? 'N/A' }}</p>
                        <p class="text-xs text-slate-500 truncate" title="{{ $tenant->admin_email ?? '' }}">{{ $tenant->admin_email ?? 'No email' }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left Column: Plan & Limits --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Subscription Details --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/60 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/20">
                            <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <x-heroicon-o-credit-card class="w-5 h-5 text-indigo-500" />
                                Details & Limits
                            </h3>
                            @if($subscription->isCancelled())
                                <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded">Ends {{ $subscription->ends_at->format('M d') }}</span>
                            @endif
                        </div>

                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- Plan Info --}}
                            <div>
                                <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Current Plan</p>
                                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">{{ $subscription->getPlanDisplayName() }}</h2>

                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm py-2 border-b border-slate-100 dark:border-slate-700/50">
                                        <span class="text-slate-500">Billing Cycle</span>
                                        <span class="font-medium text-slate-900 dark:text-white capitalize">{{ $subscription->billing_cycle }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm py-2 border-b border-slate-100 dark:border-slate-700/50">
                                        <span class="text-slate-500">Started On</span>
                                        <span class="font-medium text-slate-900 dark:text-white">{{ $subscription->starts_at ? $subscription->starts_at->format('M d, Y') : '-' }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm py-2">
                                        <span class="text-slate-500">Renews / Expires</span>
                                        <span class="font-medium {{ $subscription->isCancelled() ? 'text-rose-500' : 'text-indigo-600 dark:text-indigo-400' }}">
                                            {{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y') : 'Lifetime' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Real Usage Visuals --}}
                            <div class="space-y-6">
                                {{-- Users --}}
                                @php
                                    $maxUsers = $subscription->max_users;
                                    $uPerc = ($maxUsers > 0) ? min(($currentUsers / $maxUsers) * 100, 100) : 0;
                                @endphp
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Users</span>
                                        <span class="text-xs font-bold bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded text-slate-600 dark:text-slate-300">
                                            {{ $currentUsers }} / {{ $maxUsers == -1 ? '∞' : $maxUsers }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                                        @if($maxUsers == -1)
                                            <div class="w-full h-full bg-indigo-50 dark:bg-indigo-900/30 flex justify-center items-center"><span class="text-[8px] uppercase tracking-widest text-indigo-400">Unlimited</span></div>
                                        @else
                                            <div class="bg-indigo-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $uPerc }}%"></div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Storage --}}
                                @php
                                    $maxStorage = $subscription->max_storage;
                                    $sPerc = ($maxStorage > 0) ? min(($currentStorage / $maxStorage) * 100, 100) : 0;
                                @endphp
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Storage (MB)</span>
                                        <span class="text-xs font-bold bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded text-slate-600 dark:text-slate-300">
                                            {{ $currentStorage }} / {{ $maxStorage == -1 ? '∞' : $maxStorage }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                                        @if($maxStorage == -1)
                                            <div class="w-full h-full bg-purple-50 dark:bg-purple-900/30 flex justify-center items-center"><span class="text-[8px] uppercase tracking-widest text-purple-400">Unlimited</span></div>
                                        @else
                                            <div class="bg-purple-500 h-2.5 rounded-full transition-all duration-500" style="width: {{ $sPerc }}%"></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Invoices (Placeholder based on structure) --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/60 flex justify-between items-center">
                            <h3 class="font-bold text-slate-900 dark:text-white">Recent Invoices</h3>
                        </div>
                        <div class="p-6 text-center text-slate-400 text-sm italic">
                            Invoice history not available in this view.
                        </div>
                    </div>
                </div>

                {{-- Right Column: Management Actions --}}
                <div class="space-y-6">

                    {{-- Admin Notes --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 p-6">
                        <h3 class="font-bold text-slate-900 dark:text-white mb-4">Admin Notes</h3>
                        <textarea class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-sm p-3 focus:ring-indigo-500 focus:border-indigo-500" rows="4" placeholder="Add a note about this tenant..."></textarea>
                        <button class="mt-3 w-full bg-slate-900 dark:bg-slate-700 text-white py-2 rounded-lg text-sm font-medium hover:bg-slate-800 transition">Save Note</button>
                    </div>

                    {{-- Danger Zone --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-rose-100 dark:border-rose-900/30 overflow-hidden">
                        <div class="px-6 py-4 bg-rose-50 dark:bg-rose-900/10 border-b border-rose-100 dark:border-rose-900/30">
                            <h3 class="font-bold text-rose-700 dark:text-rose-400 flex items-center gap-2">
                                <x-heroicon-s-exclamation-triangle class="w-5 h-5" />
                                Danger Zone
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @if(!$subscription->isCancelled())
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-medium text-slate-900 dark:text-white">Cancel Subscription</span>
                                    <span class="text-xs text-slate-500">Stops billing immediately. Access remains until period ends.</span>
                                    <button wire:click="cancelSubscription" wire:confirm="Are you sure you want to cancel this tenant's subscription?"
                                            class="mt-2 w-full border border-rose-200 text-rose-600 hover:bg-rose-50 dark:border-rose-800 dark:text-rose-400 dark:hover:bg-rose-900/20 py-2 rounded-lg text-sm font-medium transition">
                                        Cancel Subscription
                                    </button>
                                </div>
                            @else
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-medium text-slate-900 dark:text-white">Resume Subscription</span>
                                    <span class="text-xs text-slate-500">Reactivates billing and recurring payments.</span>
                                    <button wire:click="resumeSubscription"
                                            class="mt-2 w-full bg-emerald-600 text-white hover:bg-emerald-700 py-2 rounded-lg text-sm font-medium transition">
                                        Resume Subscription
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        @endif
    </main>
</div>
