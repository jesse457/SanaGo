<div class=" bg-white  mx-auto space-y-8 font-sans p-6">


    {{-- 2. Main Content --}}
    <main class=" sm:px-2 lg:px-2  space-y-6">
        <nav class="flex mb-5 mt-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('landlord.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-slate-700 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-white">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" />
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-slate-400 mx-1" />
                        <a href="{{ route('landlord.manage-tenants') }}" wire:navigate
                            class="ms-1 text-sm font-medium text-slate-700 md:ms-2 dark:text-slate-400 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-white">
                            Tenant Management
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-slate-400 mx-1" />
                        <span class="ms-1 text-sm font-medium text-slate-500 md:ms-2 dark:text-slate-400">Create
                            Tenant
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Tenant Subscription</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Overview of all registered hospitals and clinics.</p>
        </div>
        @if (!$subscription)
            {{-- Empty State if no subscription exists --}}
            <div
                class="bg-white dark:bg-slate-800 p-8 rounded-xl border border-dashed border-slate-300 dark:border-slate-700 text-center">
                <x-heroicon-o-document-plus class="w-12 h-12 mx-auto text-slate-400" />
                <h3 class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">No Subscription Found</h3>
                <p class="mt-1 text-sm text-slate-500">This tenant does not have an active subscription record.</p>
                <button class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm">Create Subscription</button>
            </div>
        @else
            {{-- Top Stats Row --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Status --}}
                <div
                    class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700/60 shadow-sm flex flex-col justify-between">
                    <span class="text-xs font-semibold uppercase text-slate-500 tracking-wider">Subscription
                        Status</span>
                    <div class="mt-2 flex items-center gap-2">
                        @if ($subscription->isActive())
                            <span class="relative flex h-3 w-3">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span>
                            </span>
                            <span
                                class="text-lg font-bold text-emerald-700 dark:text-emerald-400">{{ $subscription->getStatusDisplayName() }}</span>
                        @elseif($subscription->isCancelled())
                            <span class="h-3 w-3 rounded-full bg-amber-500"></span>
                            <span class="text-lg font-bold text-amber-700 dark:text-amber-400">Cancelled</span>
                        @else
                            <span class="h-3 w-3 rounded-full bg-rose-500"></span>
                            <span
                                class="text-lg font-bold text-rose-700 dark:text-rose-400">{{ $subscription->getStatusDisplayName() }}</span>
                        @endif
                    </div>
                </div>

                {{-- Revenue --}}
                <div
                    class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700/60 shadow-sm flex flex-col justify-between">
                    <span class="text-xs font-semibold uppercase text-slate-500 tracking-wider">Plan Value</span>
                    <div class="mt-2 text-lg font-bold text-slate-900 dark:text-white">
                        {{ number_format($subscription->amount) }} <span
                            class="text-sm font-normal text-slate-500">{{ $subscription->currency ?? 'FCFA' }}</span>
                        <span
                            class="text-xs text-slate-400 block font-normal capitalize">{{ $subscription->billing_cycle }}</span>
                    </div>
                </div>

                {{-- Join Date --}}
                <div
                    class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700/60 shadow-sm flex flex-col justify-between">
                    <span class="text-xs font-semibold uppercase text-slate-500 tracking-wider">Tenant Since</span>
                    <div class="mt-2 text-lg font-bold text-slate-900 dark:text-white">
                        {{ $tenant->created_at->format('M d, Y') }}
                    </div>
                </div>

                {{-- Admin Info --}}
                <div
                    class="bg-white dark:bg-slate-800 p-4 rounded-xl border border-slate-200 dark:border-slate-700/60 shadow-sm flex flex-col justify-between">
                    <span class="text-xs font-semibold uppercase text-slate-500 tracking-wider">Admin Contact</span>
                    <div class="mt-2 overflow-hidden">
                        <p class="text-sm font-bold text-slate-900 dark:text-white truncate">
                            {{ $tenant->admin_name ?? 'N/A' }}</p>
                        <p class="text-xs text-slate-500 truncate" title="{{ $tenant->admin_email ?? '' }}">
                            {{ $tenant->admin_email ?? 'No email' }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Left Column: Plan & Limits --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Subscription Details --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden">
                        <div
                            class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/60 flex justify-between items-center bg-slate-50/50 dark:bg-slate-900/20">
                            <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <x-heroicon-o-credit-card class="w-5 h-5 text-indigo-500" />
                                Details & Limits
                            </h3>
                            @if ($subscription->isCancelled())
                                <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded">Ends
                                    {{ $subscription->ends_at->format('M d') }}</span>
                            @endif
                        </div>

                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- Plan Info --}}
                            <div>
                                <p class="text-xs text-slate-500 uppercase tracking-wide mb-1">Current Plan</p>
                                <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-4">
                                    {{ $subscription->getPlanDisplayName() }}</h2>

                                <div class="space-y-3">
                                    <div
                                        class="flex justify-between text-sm py-2 border-b border-slate-100 dark:border-slate-700/50">
                                        <span class="text-slate-500">Billing Cycle</span>
                                        <span
                                            class="font-medium text-slate-900 dark:text-white capitalize">{{ $subscription->billing_cycle }}</span>
                                    </div>
                                    <div
                                        class="flex justify-between text-sm py-2 border-b border-slate-100 dark:border-slate-700/50">
                                        <span class="text-slate-500">Started On</span>
                                        <span
                                            class="font-medium text-slate-900 dark:text-white">{{ $subscription->starts_at ? $subscription->starts_at->format('M d, Y') : '-' }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm py-2">
                                        <span class="text-slate-500">Renews / Expires</span>
                                        <span
                                            class="font-medium {{ $subscription->isCancelled() ? 'text-rose-500' : 'text-indigo-600 dark:text-indigo-400' }}">
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
                                    $uPerc = $maxUsers > 0 ? min(($currentUsers / $maxUsers) * 100, 100) : 0;
                                @endphp
                                <div>
                                    <div class="flex justify-between items-center mb-2">
                                        <span
                                            class="text-sm font-medium text-slate-700 dark:text-slate-300">Users</span>
                                        <span
                                            class="text-xs font-bold bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded text-slate-600 dark:text-slate-300">
                                            {{ $currentUsers }} / {{ $maxUsers == -1 ? '∞' : $maxUsers }}
                                        </span>
                                    </div>
                                    <div
                                        class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                                        @if ($maxUsers == -1)
                                            <div
                                                class="w-full h-full bg-indigo-50 dark:bg-indigo-900/30 flex justify-center items-center">
                                                <span
                                                    class="text-[8px] uppercase tracking-widest text-indigo-400">Unlimited</span>
                                            </div>
                                        @else
                                            <div class="bg-indigo-500 h-2.5 rounded-full transition-all duration-500"
                                                style="width: {{ $uPerc }}%"></div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Storage Usage -->
                                <div class="mt-4">
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm font-medium">Storage Usage</span>
                                        <span class="text-sm font-medium">{{ $formattedStorage }} /
                                            {{ $subscription->max_storage == -1 ? 'Unlimited' : $subscription->getPlanFeatures()['max_storage'] . ' MB' }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                        <div class="bg-blue-600 h-2.5 rounded-full"
                                            style="width: {{ $storagePercentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Invoices (Placeholder based on structure) --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden">
                        <div
                            class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/60 flex justify-between items-center">
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
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 p-6">
                        <h3 class="font-bold text-slate-900 dark:text-white mb-4">Admin Notes</h3>
                        <textarea
                            class="w-full rounded-xl border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-sm p-3 focus:ring-indigo-500 focus:border-indigo-500"
                            rows="4" placeholder="Add a note about this tenant..."></textarea>
                        <button
                            class="mt-3 w-full bg-slate-900 dark:bg-slate-700 text-white py-2 rounded-lg text-sm font-medium hover:bg-slate-800 transition">Save
                            Note</button>
                    </div>

                    {{-- Danger Zone --}}
                    <div
                        class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-rose-100 dark:border-rose-900/30 overflow-hidden">
                        <div
                            class="px-6 py-4 bg-rose-50 dark:bg-rose-900/10 border-b border-rose-100 dark:border-rose-900/30">
                            <h3 class="font-bold text-rose-700 dark:text-rose-400 flex items-center gap-2">
                                <x-heroicon-s-exclamation-triangle class="w-5 h-5" />
                                Danger Zone
                            </h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @if (!$subscription->isCancelled())
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-medium text-slate-900 dark:text-white">Cancel
                                        Subscription</span>
                                    <span class="text-xs text-slate-500">Stops billing immediately. Access remains
                                        until period ends.</span>
                                    <button wire:click="cancelSubscription"
                                        wire:confirm="Are you sure you want to cancel this tenant's subscription?"
                                        class="mt-2 w-full border border-rose-200 text-rose-600 hover:bg-rose-50 dark:border-rose-800 dark:text-rose-400 dark:hover:bg-rose-900/20 py-2 rounded-lg text-sm font-medium transition">
                                        Cancel Subscription
                                    </button>
                                </div>
                            @else
                                <div class="flex flex-col gap-1">
                                    <span class="text-sm font-medium text-slate-900 dark:text-white">Resume
                                        Subscription</span>
                                    <span class="text-xs text-slate-500">Reactivates billing and recurring
                                        payments.</span>
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
