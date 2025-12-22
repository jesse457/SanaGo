<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 font-sans space-y-8">

    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="{{ route('landlord.dashboard') }}" wire:navigate
                    class="inline-flex items-center text-sm font-medium text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 transition-colors">
                    <x-heroicon-m-home class="w-4 h-4 me-2" />
                    Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <x-heroicon-m-chevron-right class="rtl:rotate-180 w-4 h-4 text-slate-400 mx-1" />
                    <a href="{{ route('landlord.manage-tenants') }}" wire:navigate
                        class="ms-1 text-sm font-medium text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400 transition-colors">
                        Tenants
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <x-heroicon-m-chevron-right class="rtl:rotate-180 w-4 h-4 text-slate-400 mx-1" />
                    <span class="ms-1 text-sm font-medium text-slate-900 dark:text-slate-200">Create New</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                {{ __('ui.create_tenant_account') }}
            </h1>
            <p class="mt-2 text-base text-slate-600 dark:text-slate-400 max-w-2xl">
                Setup a new workspace environment. Configure the subdomain, assign an administrator, and select a billing plan.
            </p>
        </div>
    </div>

    <!-- Main Form Card (No Overlay) -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm ring-1 ring-slate-900/5 dark:ring-white/10 overflow-hidden relative">

        <form wire:submit.prevent="createTenant" class="divide-y divide-slate-100 dark:divide-slate-700/50">

            <!-- Section 1: Tenant Details -->
            <div class="p-6 md:p-8 space-y-8">
                <div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2.5">
                        <div class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
                            <x-heroicon-o-building-office class="w-5 h-5" />
                        </div>
                        Organization Details
                    </h3>
                    <p class="mt-1 ml-11 text-sm text-slate-500 dark:text-slate-400">Basic information to identify the tenant.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Tenant Name -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-900 dark:text-slate-200">Organization Name</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                                <x-heroicon-o-user-group class="w-5 h-5" />
                            </div>
                            <input type="text" wire:model.live.debounce.500ms="tenantName" placeholder="e.g. Acme Healthcare"
                                class="block w-full pl-10 pr-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all dark:text-white placeholder-slate-400 sm:text-sm shadow-sm hover:border-slate-400 dark:hover:border-slate-600">
                        </div>
                        @error('tenantName')
                            <p class="text-rose-500 text-xs font-medium flex items-center mt-1 animate-pulse">
                                <x-heroicon-s-exclamation-circle class="w-3 h-3 mr-1" />{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Domain Preview -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-900 dark:text-slate-200">System Domain</label>
                        <div class="flex items-center w-full rounded-lg shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 bg-slate-50 dark:bg-slate-900 overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-inset transition-all">
                            <span class="pl-3 text-slate-500 dark:text-slate-400 sm:text-sm font-mono select-none">https://</span>
                            <input type="text" wire:model="generatedDomain" readonly
                                class="block flex-1 border-0 bg-transparent py-2.5 pl-1 pr-1 text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-0 sm:text-sm font-semibold cursor-default">
                            <span class="pr-3 text-slate-500 dark:text-slate-400 sm:text-sm font-mono select-none bg-slate-100 dark:bg-slate-800/50 h-full py-2.5 border-l border-slate-200 dark:border-slate-700 px-3">.app.com</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-500">Auto-generated based on organization name.</p>
                    </div>

                    <!-- Admin Name -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-900 dark:text-slate-200">Administrator Name</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                                <x-heroicon-o-user class="w-5 h-5" />
                            </div>
                            <input type="text" wire:model="adminName" placeholder="John Doe"
                                class="block w-full pl-10 pr-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all dark:text-white placeholder-slate-400 sm:text-sm shadow-sm hover:border-slate-400 dark:hover:border-slate-600">
                        </div>
                    </div>

                    <!-- Admin Email -->
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-900 dark:text-slate-200">Administrator Email</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                                <x-heroicon-o-envelope class="w-5 h-5" />
                            </div>
                            <input type="email" wire:model="adminEmail" placeholder="admin@company.com"
                                class="block w-full pl-10 pr-3 py-2.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all dark:text-white placeholder-slate-400 sm:text-sm shadow-sm hover:border-slate-400 dark:hover:border-slate-600">
                        </div>
                        @error('adminEmail')
                            <p class="text-rose-500 text-xs font-medium flex items-center mt-1 animate-pulse">
                                <x-heroicon-s-exclamation-circle class="w-3 h-3 mr-1" />{{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Subscription -->
            <div class="p-6 md:p-8 bg-slate-50/50 dark:bg-slate-800/50">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2.5">
                            <div class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                <x-heroicon-o-credit-card class="w-5 h-5" />
                            </div>
                            Subscription Plan
                        </h3>
                        <p class="mt-1 ml-11 text-sm text-slate-500 dark:text-slate-400">Select a billing frequency and tier.</p>
                    </div>

                    <!-- Modern Toggle Switch -->
                    <div class="bg-white dark:bg-slate-900 p-1 rounded-lg inline-flex items-center ring-1 ring-slate-200 dark:ring-slate-700 shadow-sm">
                        <button type="button" wire:click="$set('billingCycle', 'monthly')"
                            class="relative px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 focus:outline-none flex items-center gap-2
                            {{ $billingCycle === 'monthly' ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                            Monthly
                        </button>
                        <button type="button" wire:click="$set('billingCycle', 'yearly')"
                            class="relative px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 focus:outline-none flex items-center gap-2
                            {{ $billingCycle === 'yearly' ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                            Yearly
                            <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-bold bg-emerald-100 dark:bg-emerald-500/20 px-1.5 py-0.5 rounded-full ring-1 ring-inset ring-emerald-600/20">-20%</span>
                        </button>
                    </div>
                </div>

                <!-- Pricing Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($plans ?? [] as $plan)
                        @php
                            $isSelected = $subscriptionTier === $plan['id'];
                        @endphp

                        <div wire:click="$set('subscriptionTier', '{{ $plan['id'] }}')"
                            class="relative group cursor-pointer rounded-2xl border transition-all duration-200 flex flex-col h-full
                            {{ $isSelected
                                ? 'border-indigo-600 bg-white dark:bg-slate-800 ring-2 ring-indigo-600 shadow-lg scale-[1.02] z-10'
                                : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-indigo-300 dark:hover:border-indigo-700 hover:shadow-md' }}">

                            @if ($isSelected)
                                <div class="absolute -top-3 inset-x-0 flex justify-center">
                                    <span class="bg-indigo-600 text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-sm uppercase tracking-wider ring-2 ring-white dark:ring-slate-900">
                                        Selected
                                    </span>
                                </div>
                            @endif

                            <div class="p-6 flex-1 flex flex-col">
                                <h4 class="font-bold text-lg {{ $isSelected ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-900 dark:text-white' }}">
                                    {{ $plan['name'] }}
                                </h4>

                                <div class="mt-4 mb-6">
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                                            {{ number_format($plan['price']) }}
                                        </span>
                                        <span class="text-sm font-semibold text-slate-500 dark:text-slate-400">
                                            FCFA
                                        </span>
                                    </div>
                                    <span class="text-xs text-slate-500 dark:text-slate-400">
                                        Billed {{ $billingCycle == 'monthly' ? 'monthly' : 'annually' }}
                                    </span>
                                </div>

                                <ul class="space-y-4 mb-4 flex-1">
                                    <!-- Feature: Users -->
                                    <li class="flex items-start text-sm text-slate-600 dark:text-slate-300">
                                        <x-heroicon-o-users class="w-5 h-5 mr-3 text-indigo-500 shrink-0" />
                                        <span class="leading-tight">
                                            <strong class="font-semibold text-slate-900 dark:text-white">{{ $plan['features']['max_users'] == -1 ? 'Unlimited' : $plan['features']['max_users'] }}</strong>
                                            Team Members
                                        </span>
                                    </li>

                                    <!-- Feature: Storage -->
                                    <li class="flex items-start text-sm text-slate-600 dark:text-slate-300">
                                        <x-heroicon-o-circle-stack class="w-5 h-5 mr-3 text-indigo-500 shrink-0" />
                                        <span class="leading-tight">
                                            <strong class="font-semibold text-slate-900 dark:text-white">{{ $plan['features']['max_storage'] == -1 ? 'Unlimited' : $plan['features']['max_storage'] / 1024 . ' GB' }}</strong>
                                            Storage
                                        </span>
                                    </li>

                                    <!-- Feature: API -->
                                    <li class="flex items-start text-sm text-slate-600 dark:text-slate-300">
                                        @if ($plan['features']['api_access'])
                                            <x-heroicon-o-check-circle class="w-5 h-5 mr-3 text-emerald-500 shrink-0" />
                                            <span class="leading-tight">API Access</span>
                                        @else
                                            <x-heroicon-o-x-circle class="w-5 h-5 mr-3 text-slate-400 shrink-0" />
                                            <span class="leading-tight text-slate-400 decoration-slate-400">No API Access</span>
                                        @endif
                                    </li>
                                </ul>
                            </div>

                            <!-- Selection Indicator -->
                            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700/50 bg-slate-50 dark:bg-slate-800/50 rounded-b-2xl">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="h-4 w-4 rounded-full border flex items-center justify-center transition-colors
                                        {{ $isSelected ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300 dark:border-slate-600' }}">
                                        @if ($isSelected)
                                            <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                                        @endif
                                    </div>
                                    <span class="text-xs font-semibold uppercase tracking-wide {{ $isSelected ? 'text-indigo-700 dark:text-indigo-300' : 'text-slate-500' }}">
                                        {{ $isSelected ? 'Selected' : 'Click to select' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('subscriptionTier')
                    <div class="mt-6 p-4 rounded-lg bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-900/50 flex items-center justify-center gap-2 text-rose-600 dark:text-rose-400">
                        <x-heroicon-s-exclamation-triangle class="w-5 h-5" />
                        <span class="text-sm font-medium">Please select a subscription plan to proceed.</span>
                    </div>
                @enderror
            </div>

            <!-- Footer Actions -->
            <div class="px-6 py-4 md:px-8 bg-slate-50 dark:bg-slate-900 border-t border-slate-200 dark:border-slate-700 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 rounded-b-2xl">
                <a href="{{ route('landlord.manage-tenants') }}"
                    class="w-full sm:w-auto px-5 py-2.5 text-sm font-medium text-center text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-lg shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                    Cancel
                </a>

                <!-- BUTTON WITH INTERNAL SPINNER -->
                <button type="submit"
                    wire:loading.attr="disabled"
                    wire:target="createTenant"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-2.5 text-sm font-semibold text-white transition-all duration-200 bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 shadow-md shadow-indigo-500/20 disabled:opacity-70 disabled:cursor-not-allowed">

                    <!-- Default State -->
                    <span wire:loading.remove wire:target="createTenant">Create Tenant</span>

                    <!-- Loading State -->
                    <span wire:loading wire:target="createTenant" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
