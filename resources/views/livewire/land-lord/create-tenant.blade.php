<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- STICKY HEADER --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4 md:flex md:items-center md:justify-between space-y-3 md:space-y-0">
                <div class="flex-1 min-w-0">
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-1" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('landlord.dashboard') }}" wire:navigate
                                    class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    Home
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <a href="{{ route('landlord.manage-tenants') }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                        Tenants
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">Create New</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                        {{ __('ui.create_tenant_account') }}
                    </h2>
                </div>
            </div>
        </header>

        {{-- CONTENT --}}
        <div class="p-4 sm:p-6">
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                <form wire:submit.prevent="createTenant">

                    <!-- Section 1: Tenant Details -->
                    <div class="p-6 md:p-8 space-y-8 border-b border-slate-100 dark:border-gray-800">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                                <div class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                                    <x-heroicon-o-building-office class="w-5 h-5" />
                                </div>
                                Organization Details
                            </h3>
                            <p class="mt-1 ml-11 text-sm text-slate-500 dark:text-slate-400">Configure the subdomain and assign an administrator.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6 ml-11">
                            <!-- Tenant Name -->
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Organization Name</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                                        <x-heroicon-o-user-group class="w-5 h-5" />
                                    </div>
                                    <input type="text" wire:model.live.debounce.500ms="tenantName" placeholder="e.g. Acme Healthcare"
                                        class="block w-full pl-10 pr-3 py-2.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all dark:text-white placeholder-slate-400 sm:text-sm shadow-sm">
                                </div>
                                @error('tenantName') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                            </div>

                            <!-- Domain Preview -->
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">System Domain</label>
                                <div class="flex items-center w-full rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-900 overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500 focus-within:border-indigo-500 transition-all">
                                    <span class="pl-3 text-slate-500 dark:text-slate-400 sm:text-sm font-mono select-none">https://</span>
                                    <input type="text" wire:model="generatedDomain" readonly
                                        class="block flex-1 border-0 bg-transparent py-2.5 pl-1 pr-1 text-slate-900 dark:text-white placeholder:text-slate-400 focus:ring-0 sm:text-sm font-bold font-mono cursor-default">
                                    <span class="pr-3 text-slate-500 dark:text-slate-400 sm:text-sm font-mono select-none bg-slate-100 dark:bg-gray-800 h-full py-2.5 border-l border-slate-200 dark:border-gray-700 px-3">.app.com</span>
                                </div>
                            </div>

                            <!-- Admin Name -->
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Administrator Name</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                                        <x-heroicon-o-user class="w-5 h-5" />
                                    </div>
                                    <input type="text" wire:model="adminName" placeholder="John Doe"
                                        class="block w-full pl-10 pr-3 py-2.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all dark:text-white placeholder-slate-400 sm:text-sm shadow-sm">
                                </div>
                            </div>

                            <!-- Admin Email -->
                            <div class="space-y-1.5">
                                <label class="text-sm font-bold text-slate-700 dark:text-slate-300">Administrator Email</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 group-focus-within:text-indigo-500 transition-colors">
                                        <x-heroicon-o-envelope class="w-5 h-5" />
                                    </div>
                                    <input type="email" wire:model="adminEmail" placeholder="admin@company.com"
                                        class="block w-full pl-10 pr-3 py-2.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-xl focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all dark:text-white placeholder-slate-400 sm:text-sm shadow-sm">
                                </div>
                                @error('adminEmail') <span class="text-xs text-red-500 font-bold">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Subscription -->
                    <div class="p-6 md:p-8 bg-slate-50/50 dark:bg-gray-900/50">
                        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2.5">
                                    <div class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                                        <x-heroicon-o-credit-card class="w-5 h-5" />
                                    </div>
                                    Subscription Plan
                                </h3>
                                <p class="mt-1 ml-11 text-sm text-slate-500 dark:text-slate-400">Select a billing frequency and tier.</p>
                            </div>

                            <!-- Toggle Switch -->
                            <div class="bg-white dark:bg-gray-800 p-1 rounded-xl inline-flex items-center border border-slate-200 dark:border-gray-700 shadow-sm">
                                <button type="button" wire:click="$set('billingCycle', 'monthly')"
                                    class="relative px-4 py-1.5 text-sm font-bold rounded-lg transition-all duration-200 focus:outline-none flex items-center gap-2
                                    {{ $billingCycle === 'monthly' ? 'bg-slate-100 dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                                    Monthly
                                </button>
                                <button type="button" wire:click="$set('billingCycle', 'yearly')"
                                    class="relative px-4 py-1.5 text-sm font-bold rounded-lg transition-all duration-200 focus:outline-none flex items-center gap-2
                                    {{ $billingCycle === 'yearly' ? 'bg-slate-100 dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                                    Yearly
                                    <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-extrabold bg-emerald-100 dark:bg-emerald-500/20 px-1.5 rounded border border-emerald-200 dark:border-emerald-800">-20%</span>
                                </button>
                            </div>
                        </div>

                        <!-- Plans Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 ml-11">
                            @foreach ($plans ?? [] as $plan)
                                @php $isSelected = $subscriptionTier === $plan['id']; @endphp
                                <div wire:click="$set('subscriptionTier', '{{ $plan['id'] }}')"
                                    class="relative group cursor-pointer rounded-2xl border transition-all duration-200 flex flex-col h-full
                                    {{ $isSelected
                                        ? 'border-indigo-600 bg-white dark:bg-gray-800 ring-1 ring-indigo-600 shadow-lg scale-[1.02] z-10'
                                        : 'border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-indigo-300 dark:hover:border-indigo-700 hover:shadow-md' }}">

                                    @if ($isSelected)
                                        <div class="absolute -top-3 inset-x-0 flex justify-center">
                                            <span class="bg-indigo-600 text-white text-[10px] font-bold px-3 py-1 rounded-full shadow-sm uppercase tracking-wider ring-4 ring-slate-50 dark:ring-gray-900">Selected</span>
                                        </div>
                                    @endif

                                    <div class="p-5 flex-1 flex flex-col">
                                        <h4 class="font-bold text-lg {{ $isSelected ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-900 dark:text-white' }}">
                                            {{ $plan['name'] }}
                                        </h4>
                                        <div class="mt-2 mb-4">
                                            <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ number_format($plan['price']) }}</span>
                                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">FCFA / {{ $billingCycle == 'monthly' ? 'mo' : 'yr' }}</span>
                                        </div>
                                        <ul class="space-y-3 mb-4 flex-1">
                                            <li class="flex items-center text-xs font-medium text-slate-600 dark:text-slate-300">
                                                <x-heroicon-s-users class="w-4 h-4 mr-2 text-indigo-500" />
                                                {{ $plan['features']['max_users'] == -1 ? 'Unlimited' : $plan['features']['max_users'] }} Users
                                            </li>
                                            <li class="flex items-center text-xs font-medium text-slate-600 dark:text-slate-300">
                                                <x-heroicon-s-circle-stack class="w-4 h-4 mr-2 text-indigo-500" />
                                                {{ $plan['features']['max_storage'] == -1 ? 'Unlimited' : $plan['features']['max_storage'] / 1024 . ' GB' }} Storage
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="px-5 py-3 border-t border-slate-100 dark:border-gray-700/50 bg-slate-50 dark:bg-gray-900/50 rounded-b-2xl">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="h-4 w-4 rounded-full border flex items-center justify-center transition-colors {{ $isSelected ? 'border-indigo-600 bg-indigo-600' : 'border-slate-300 dark:border-slate-600' }}">
                                                @if ($isSelected) <div class="h-1.5 w-1.5 rounded-full bg-white"></div> @endif
                                            </div>
                                            <span class="text-xs font-bold uppercase {{ $isSelected ? 'text-indigo-700 dark:text-indigo-300' : 'text-slate-500' }}">Select</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('subscriptionTier')
                            <p class="mt-4 ml-11 text-sm text-red-600 font-medium animate-pulse">Please select a subscription plan.</p>
                        @enderror
                    </div>

                    <!-- Footer Actions -->
                    <div class="px-6 py-4 md:px-8 bg-slate-50 dark:bg-gray-950 border-t border-slate-200 dark:border-gray-800 flex items-center justify-end gap-3 rounded-b-2xl">
                        <a href="{{ route('landlord.manage-tenants') }}" class="px-5 py-2.5 text-sm font-bold text-slate-700 bg-white border border-slate-300 rounded-xl shadow-sm hover:bg-slate-50 transition-all dark:bg-gray-800 dark:text-slate-300 dark:border-gray-700">
                            Cancel
                        </a>
                        <button type="submit" wire:loading.attr="disabled" class="px-8 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-xl shadow-md hover:bg-indigo-700 hover:shadow-lg focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all flex items-center gap-2">
                            <span wire:loading.remove>Create Tenant</span>
                            <span wire:loading class="flex items-center gap-2"><x-heroicon-o-arrow-path class="animate-spin h-4 w-4"/> Processing...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
