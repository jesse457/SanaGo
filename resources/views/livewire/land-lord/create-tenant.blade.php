<div class="bg-white  mx-auto space-y-8 font-sans p-6">
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
    <!-- Header Section -->
    <div class="mx-auto mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">

        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3 tracking-tight">
                {{ __('ui.create_tenant_account') }}</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Setup a new workspace and configure
                    subscription
                    details.</p>
        </div>

    </div>

    <!-- Main Form Card -->
    <div
        class="mx-auto bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden relative">

        <!-- Loading Overlay -->
        <div wire:loading wire:target="createTenant"
            class="absolute inset-0 bg-white/50 dark:bg-slate-900/50 z-50 backdrop-blur-sm flex items-center justify-center">
            <div class="flex items-center gap-2 text-indigo-600 font-semibold animate-pulse">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Processing...
            </div>
        </div>

        <form wire:submit.prevent="createTenant" class="p-6 md:p-8 space-y-10">

            <!-- Section 1: Tenant Details -->
            <section>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white mb-6 flex items-center gap-2">
                    <div
                        class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600">
                        <x-heroicon-o-building-office class="w-5 h-5" />
                    </div>
                    Organization Details
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <!-- Tenant Name -->
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Tenant Name</label>
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                                <x-heroicon-o-user-group class="w-5 h-5" />
                            </div>
                            <input type="text" wire:model.live.debounce.500ms="tenantName"
                                placeholder="e.g. Acme Healthcare"
                                class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all dark:text-white placeholder-slate-400 sm:text-sm">
                        </div>
                        @error('tenantName')
                            <span
                                class="text-rose-500 text-xs font-medium flex items-center mt-1"><x-heroicon-s-exclamation-circle
                                    class="w-3 h-3 mr-1" />{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Domain Preview (Input Group) -->
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">System Domain</label>
                        <div
                            class="flex rounded-xl shadow-sm ring-1 ring-inset ring-slate-200 dark:ring-slate-700 overflow-hidden bg-slate-50 dark:bg-slate-900/50">
                            <span class="flex select-none items-center pl-3 text-slate-400 sm:text-sm">https://</span>
                            <input type="text" wire:model="generatedDomain" readonly
                                class="block flex-1 border-0 bg-transparent py-2.5 pl-1 text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-0 sm:text-sm cursor-not-allowed">
                            <span
                                class="flex select-none items-center pr-3 text-slate-400 sm:text-sm border-l border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 px-3">.app.com</span>
                        </div>
                        <p class="text-xs text-slate-500">Auto-generated based on name.</p>
                    </div>

                    <!-- Admin Name -->
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Administrator Name</label>
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                                <x-heroicon-o-user class="w-5 h-5" />
                            </div>
                            <input type="text" wire:model="adminName" placeholder="John Doe"
                                class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all dark:text-white placeholder-slate-400 sm:text-sm">
                        </div>
                    </div>

                    <!-- Admin Email -->
                    <div class="space-y-1.5">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Administrator
                            Email</label>
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                                <x-heroicon-o-envelope class="w-5 h-5" />
                            </div>
                            <input type="email" wire:model="adminEmail" placeholder="admin@company.com"
                                class="block w-full pl-10 pr-3 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all dark:text-white placeholder-slate-400 sm:text-sm">
                        </div>
                        @error('adminEmail')
                            <span
                                class="text-rose-500 text-xs font-medium flex items-center mt-1"><x-heroicon-s-exclamation-circle
                                    class="w-3 h-3 mr-1" />{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </section>

            <hr class="border-slate-100 dark:border-slate-700">

            <!-- Section 2: Subscription -->
            <section>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white flex items-center gap-2">
                            <div
                                class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600">
                                <x-heroicon-o-credit-card class="w-5 h-5" />
                            </div>
                            Subscription Plan
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1 ml-10">Choose the best plan for the
                            organization.</p>
                    </div>

                    <!-- Modern Toggle Switch -->
                    <div
                        class="bg-slate-100 dark:bg-slate-900 p-1.5 rounded-xl flex items-center self-start sm:self-center ring-1 ring-inset ring-slate-200 dark:ring-slate-700">
                        <button type="button" wire:click="$set('billingCycle', 'monthly')"
                            class="relative px-6 py-2 text-sm font-medium rounded-lg transition-all duration-200 focus:outline-none z-10
                            {{ $billingCycle === 'monthly' ? 'text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700' }}">
                            @if ($billingCycle === 'monthly')
                                <div class="absolute inset-0 bg-white dark:bg-slate-700 rounded-lg shadow-sm -z-10"
                                    layoutId="highlight"></div>
                            @endif
                            Monthly
                        </button>
                        <button type="button" wire:click="$set('billingCycle', 'yearly')"
                            class="relative px-6 py-2 text-sm font-medium rounded-lg transition-all duration-200 focus:outline-none z-10
                            {{ $billingCycle === 'yearly' ? 'text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700' }}">
                            @if ($billingCycle === 'yearly')
                                <div class="absolute inset-0 bg-white dark:bg-slate-700 rounded-lg shadow-sm -z-10"
                                    layoutId="highlight"></div>
                            @endif
                            Yearly <span
                                class="ml-1 text-[10px] text-emerald-600 font-bold bg-emerald-100 dark:bg-emerald-500/20 px-1.5 py-0.5 rounded-full">-20%</span>
                        </button>
                    </div>
                </div>

                <!-- Pricing Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                    @foreach ($plans ?? [] as $plan)
                        @php
                            $isSelected = $subscriptionTier === $plan['id'];
                            $isPopular = isset($plan['is_popular']) && $plan['is_popular']; // Assuming you might add this flag later
                        @endphp

                        <div wire:click="$set('subscriptionTier', '{{ $plan['id'] }}')"
                            class="relative cursor-pointer group rounded-2xl border transition-all duration-300 flex flex-col
                            {{ $isSelected
                                ? 'border-indigo-600 bg-indigo-50/60 dark:bg-indigo-600/10 ring-1 ring-indigo-600 shadow-md transform scale-[1.02]'
                                : 'border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:border-indigo-300 dark:hover:border-indigo-700 hover:shadow-lg' }}">

                            @if ($isSelected)
                                <div
                                    class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-indigo-600 text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-sm uppercase tracking-wider">
                                    Selected
                                </div>
                            @endif

                            <div class="p-5 flex-1">
                                <h4
                                    class="font-bold text-base {{ $isSelected ? 'text-indigo-900 dark:text-indigo-100' : 'text-slate-900 dark:text-white' }}">
                                    {{ $plan['name'] }}
                                </h4>

                                <div class="mt-3 mb-6">
                                    <span class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">
                                        {{ number_format($plan['price']) }}
                                    </span>
                                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">FCFA /
                                        {{ $billingCycle == 'monthly' ? 'mo' : 'yr' }}</span>
                                </div>

                                <div class="space-y-3">
                                    <!-- Feature: Users -->
                                    <div class="flex items-start text-xs text-slate-600 dark:text-slate-300">
                                        <div
                                            class="mt-0.5 mr-2 p-1 rounded-full {{ $isSelected ? 'bg-indigo-200 dark:bg-indigo-500/30 text-indigo-700 dark:text-indigo-300' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }}">
                                            <x-heroicon-s-users class="w-3 h-3" />
                                        </div>
                                        <span class="leading-5">
                                            <strong
                                                class="font-semibold text-slate-900 dark:text-white">{{ $plan['features']['max_users'] == -1 ? 'Unlimited' : $plan['features']['max_users'] }}</strong>
                                            Users
                                        </span>
                                    </div>

                                    <!-- Feature: Storage -->
                                    <div class="flex items-start text-xs text-slate-600 dark:text-slate-300">
                                        <div
                                            class="mt-0.5 mr-2 p-1 rounded-full {{ $isSelected ? 'bg-indigo-200 dark:bg-indigo-500/30 text-indigo-700 dark:text-indigo-300' : 'bg-slate-100 dark:bg-slate-700 text-slate-500' }}">
                                            <x-heroicon-s-circle-stack class="w-3 h-3" />
                                        </div>
                                        <span class="leading-5">
                                            <strong
                                                class="font-semibold text-slate-900 dark:text-white">{{ $plan['features']['max_storage'] == -1 ? 'Unlimited' : $plan['features']['max_storage'] / 1024 . ' GB' }}</strong>
                                            Storage
                                        </span>
                                    </div>

                                    <!-- Feature: API -->
                                    <div class="flex items-start text-xs text-slate-600 dark:text-slate-300">
                                        <div
                                            class="mt-0.5 mr-2 p-1 rounded-full {{ $plan['features']['api_access'] ? ($isSelected ? 'bg-indigo-200 dark:bg-indigo-500/30 text-indigo-700 dark:text-indigo-300' : 'bg-slate-100 dark:bg-slate-700 text-slate-500') : 'bg-slate-50 dark:bg-slate-800 text-slate-300' }}">
                                            @if ($plan['features']['api_access'])
                                                <x-heroicon-s-check class="w-3 h-3" />
                                            @else
                                                <x-heroicon-s-x-mark class="w-3 h-3" />
                                            @endif
                                        </div>
                                        <span
                                            class="leading-5 {{ !$plan['features']['api_access'] ? 'text-slate-400 dark:text-slate-600 line-through' : '' }}">
                                            API Access
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Radio Indicator -->
                            <div
                                class="px-5 py-3 border-t {{ $isSelected ? 'border-indigo-200 dark:border-indigo-500/30 bg-indigo-50/50 dark:bg-indigo-600/5' : 'border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50' }}">
                                <div class="flex items-center justify-center">
                                    <div
                                        class="h-4 w-4 rounded-full border {{ $isSelected ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300' }} flex items-center justify-center">
                                        @if ($isSelected)
                                            <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                                        @endif
                                    </div>
                                    <span
                                        class="ml-2 text-xs font-semibold {{ $isSelected ? 'text-indigo-700 dark:text-indigo-300' : 'text-slate-500' }}">
                                        {{ $isSelected ? 'Selected Plan' : 'Select Plan' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('subscriptionTier')
                    <p class="text-rose-500 mt-3 text-sm flex items-center justify-center animate-bounce">
                        <x-heroicon-s-exclamation-triangle class="w-4 h-4 mr-1" />
                        Please select a subscription plan to proceed.
                    </p>
                @enderror
            </section>

            <!-- Actions -->
            <div class="pt-6 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-700">
                <button type="button"
                    class="px-5 py-2.5 text-sm font-medium text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-xl shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                    Cancel
                </button>

                <button type="submit" wire:loading.attr="disabled"
                    class="group relative inline-flex items-center justify-center px-8 py-2.5 text-sm font-semibold text-white transition-all duration-200 bg-indigo-600 border border-transparent rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 shadow-md shadow-indigo-500/20 hover:shadow-lg hover:shadow-indigo-500/30 disabled:opacity-70 disabled:cursor-not-allowed">
                    <span wire:loading.remove>Create Tenant Account</span>
                    <span wire:loading class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Creating...
                    </span>
                    <x-heroicon-s-arrow-right class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform"
                        wire:loading.remove />
                </button>
            </div>
        </form>
    </div>
</div>
