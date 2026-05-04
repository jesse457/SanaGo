<div class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- STICKY HEADER --}}
        <header class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4 md:flex md:items-center md:justify-between space-y-3 md:space-y-0">
                <div class="flex-1 min-w-0">
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-1" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('landlord.dashboard') }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors flex items-center"><x-heroicon-s-home class="w-3 h-3 mr-1.5" /> Home</a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <a href="{{ route('landlord.manage-tenants') }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Tenants</a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">Subscription Details</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">Tenant Subscription</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Managing {{ $tenant->name }} ({{ $tenant->domains->first()->domain ?? 'No Domain' }})</p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2">
                     <button wire:click="loginAsTenant" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition-all dark:bg-gray-800 dark:border-gray-700 dark:text-slate-300">
                        <x-heroicon-m-arrow-right-end-on-rectangle class="w-4 h-4"/> Impersonate
                    </button>
                </div>
            </div>
        </header>

        <div class="p-4 sm:p-6 space-y-6">
            @if (!$subscription)
                <div class="bg-white dark:bg-gray-900 p-12 rounded-2xl border border-dashed border-slate-300 dark:border-gray-700 text-center">
                    <div class="mx-auto h-16 w-16 bg-slate-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                        <x-heroicon-o-document-plus class="w-8 h-8 text-slate-400" />
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">No Subscription Found</h3>
                    <p class="mt-2 text-sm text-slate-500 dark:text-slate-400 max-w-sm mx-auto">This tenant does not have an active billing record. Create one to enable access.</p>
                    <button class="mt-6 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-md transition-all">Create Subscription</button>
                </div>
            @else

                {{-- Top Stats Row --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    {{-- Status --}}
                    <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 flex flex-col justify-between h-full">
                        <span class="text-xs font-bold uppercase text-slate-500 tracking-wider">Status</span>
                        <div class="mt-2 flex items-center gap-2">
                            @if ($subscription->isActive())
                                <span class="relative flex h-3 w-3"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span></span>
                                <span class="text-xl font-bold text-emerald-700 dark:text-emerald-400">{{ $subscription->getStatusDisplayName() }}</span>
                            @elseif($subscription->isCancelled())
                                <span class="h-3 w-3 rounded-full bg-amber-500"></span>
                                <span class="text-xl font-bold text-amber-700 dark:text-amber-400">Cancelled</span>
                            @else
                                <span class="h-3 w-3 rounded-full bg-rose-500"></span>
                                <span class="text-xl font-bold text-rose-700 dark:text-rose-400">{{ $subscription->getStatusDisplayName() }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Plan Value --}}
                    <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 flex flex-col justify-between h-full">
                        <span class="text-xs font-bold uppercase text-slate-500 tracking-wider">Recurring Revenue</span>
                        <div class="mt-2 flex items-baseline gap-1">
                            <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($subscription->amount) }}</span>
                            <span class="text-sm font-semibold text-slate-500">{{ $subscription->currency ?? 'FCFA' }}</span>
                            <span class="text-xs font-medium text-slate-400 bg-slate-100 dark:bg-gray-800 px-1.5 py-0.5 rounded ml-2 capitalize">{{ $subscription->billing_cycle }}</span>
                        </div>
                    </div>

                    {{-- Join Date --}}
                    <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 flex flex-col justify-between h-full">
                        <span class="text-xs font-bold uppercase text-slate-500 tracking-wider">Tenant Since</span>
                        <div class="mt-2 text-xl font-bold text-slate-900 dark:text-white">{{ $tenant->created_at->format('M d, Y') }}</div>
                    </div>

                    {{-- Admin Info --}}
                    <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 flex flex-col justify-between h-full">
                        <span class="text-xs font-bold uppercase text-slate-500 tracking-wider">Admin Contact</span>
                        <div class="mt-2">
                            <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $tenant->admin_name ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $tenant->admin_email ?? 'No email' }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Left Column: Details & Limits --}}
                    <div class="lg:col-span-2 space-y-6">
                        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-100 dark:border-gray-800 flex justify-between items-center bg-slate-50/50 dark:bg-gray-800/50">
                                <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                    <x-heroicon-s-credit-card class="w-5 h-5 text-indigo-500" /> Subscription & Usage
                                </h3>
                                @if ($subscription->isCancelled())
                                    <span class="text-xs font-bold text-rose-600 bg-rose-50 border border-rose-100 px-2 py-1 rounded-lg">Ends {{ $subscription->ends_at->format('M d') }}</span>
                                @endif
                            </div>

                            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                                {{-- Plan Details --}}
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3">Current Plan</p>
                                    <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-6">{{ $subscription->getPlanDisplayName() }}</h2>

                                    <div class="space-y-4">
                                        <div class="flex justify-between text-sm pb-3 border-b border-slate-50 dark:border-gray-800">
                                            <span class="text-slate-500 font-medium">Next Billing Date</span>
                                            <span class="font-bold {{ $subscription->isCancelled() ? 'text-rose-500' : 'text-indigo-600 dark:text-indigo-400' }}">
                                                {{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y') : 'Lifetime' }}
                                            </span>
                                        </div>
                                        <div class="flex justify-between text-sm pb-3 border-b border-slate-50 dark:border-gray-800">
                                            <span class="text-slate-500 font-medium">Start Date</span>
                                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $subscription->starts_at ? $subscription->starts_at->format('M d, Y') : '-' }}</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Usage Bars --}}
                                <div class="space-y-6 bg-slate-50 dark:bg-gray-800/30 p-5 rounded-xl border border-slate-100 dark:border-gray-800">
                                    {{-- Users --}}
                                    @php
                                        $maxUsers = $subscription->max_users;
                                        $uPerc = $maxUsers > 0 ? min(($currentUsers / $maxUsers) * 100, 100) : 0;
                                    @endphp
                                    <div>
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1"><x-heroicon-s-users class="w-3 h-3"/> Active Users</span>
                                            <span class="text-xs font-bold text-slate-500">{{ $currentUsers }} <span class="text-slate-300">/</span> {{ $maxUsers == -1 ? '∞' : $maxUsers }}</span>
                                        </div>
                                        <div class="w-full bg-slate-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                            @if ($maxUsers == -1)
                                                <div class="w-full h-full bg-indigo-100 dark:bg-indigo-900/30 flex justify-center items-center"><span class="text-[6px] uppercase tracking-widest text-indigo-400">Unlimited</span></div>
                                            @else
                                                <div class="bg-indigo-600 h-2 rounded-full transition-all duration-500 shadow-sm shadow-indigo-500/50" style="width: {{ $uPerc }}%"></div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Storage --}}
                                    <div>
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1"><x-heroicon-s-circle-stack class="w-3 h-3"/> Storage Usage</span>
                                            <span class="text-xs font-bold text-slate-500">{{ $formattedStorage }} <span class="text-slate-300">/</span> {{ $subscription->max_storage == -1 ? 'Unlimited' : $subscription->getPlanFeatures()['max_storage'] . ' MB' }}</span>
                                        </div>
                                        <div class="w-full bg-slate-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                            <div class="bg-blue-500 h-2 rounded-full transition-all duration-500 shadow-sm shadow-blue-500/50" style="width: {{ $storagePercentage }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Actions --}}
                    <div class="space-y-6">
                        {{-- Admin Notes --}}
                        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 p-6">
                            <h3 class="font-bold text-slate-900 dark:text-white mb-4 text-sm uppercase tracking-wide">Internal Notes</h3>
                            <textarea class="w-full rounded-xl border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-800 text-sm p-3 focus:ring-indigo-500 focus:border-indigo-500 min-h-[120px]" placeholder="Add a note about this tenant..."></textarea>
                            <button class="mt-3 w-full bg-slate-900 dark:bg-slate-700 text-white py-2 rounded-xl text-sm font-bold hover:bg-slate-800 transition shadow-sm">Save Note</button>
                        </div>

                        {{-- Danger Zone --}}
                        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-rose-200 dark:border-rose-900/50 overflow-hidden">
                            <div class="px-6 py-4 bg-rose-50 dark:bg-rose-900/10 border-b border-rose-100 dark:border-rose-900/50">
                                <h3 class="font-bold text-rose-700 dark:text-rose-400 flex items-center gap-2 text-sm">
                                    <x-heroicon-s-exclamation-triangle class="w-4 h-4" /> Danger Zone
                                </h3>
                            </div>
                            <div class="p-6">
                                @if (!$subscription->isCancelled())
                                    <div class="flex flex-col gap-2">
                                        <p class="text-xs text-slate-500 leading-relaxed mb-2">Cancelling stops billing immediately. The tenant will retain access until the current period ends.</p>
                                        <button wire:click="cancelSubscription" wire:confirm="Are you sure?"
                                            class="w-full border border-rose-200 text-rose-600 hover:bg-rose-50 dark:border-rose-800 dark:text-rose-400 dark:hover:bg-rose-900/20 py-2.5 rounded-xl text-sm font-bold transition-colors">
                                            Cancel Subscription
                                        </button>
                                    </div>
                                @else
                                    <div class="flex flex-col gap-2">
                                        <p class="text-xs text-slate-500 leading-relaxed mb-2">Resuming will reactivate recurring billing immediately.</p>
                                        <button wire:click="resumeSubscription"
                                            class="w-full bg-emerald-600 text-white hover:bg-emerald-700 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-md">
                                            Resume Subscription
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
