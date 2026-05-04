<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">

    {{-- Toast Notification --}}
    <div x-data="{ show: false, title: '', message: '', type: 'success' }"
        x-on:notify.window="show = true; title = $event.detail.title; message = $event.detail.message; type = $event.detail.type || 'success'; setTimeout(() => show = false, 3000)"
        class="fixed top-6 right-6 z-[100] w-full max-w-sm" x-cloak>
        <div x-show="show" x-transition.opacity.duration.300ms
            :class="{ 'bg-emerald-50 border-emerald-200': type === 'success', 'bg-red-50 border-red-200': type === 'error' }"
            class="p-4 border rounded-xl shadow-lg flex items-start gap-3 relative backdrop-blur-sm">
            <div x-show="type === 'success'" class="text-emerald-500"><x-heroicon-s-check-circle class="w-6 h-6" /></div>
            <div x-show="type === 'error'" class="text-red-500"><x-heroicon-s-x-circle class="w-6 h-6" /></div>
            <div>
                <h4 class="font-bold text-gray-900" x-text="title"></h4>
                <p class="text-sm text-gray-600" x-text="message"></p>
            </div>
            <button @click="show = false" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                <x-heroicon-s-x-mark class="w-4 h-4" />
            </button>
        </div>
    </div>

    <div class="max-w-7xl mx-auto">

        {{-- 1. STICKY HEADER SECTION --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4 md:flex md:items-center md:justify-between space-y-3 md:space-y-0">

                {{-- Title & Breadcrumbs --}}
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
                                    <span class="text-gray-900 dark:text-white">Tenant Management</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                        Tenant Management
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Overview of all registered hospitals and clinics.
                    </p>
                </div>

                {{-- Right Actions --}}
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <a href="{{ route('landlord.create-tenants') }}" wire:navigate
                            class="group relative inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl shadow-md hover:bg-indigo-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all overflow-hidden dark:focus:ring-offset-gray-900">
                            <div
                                class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                            </div>
                            <x-heroicon-o-plus class="w-5 h-5" />
                            <span>{{ __('ui.add_tenant') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Filter Bar --}}
            <div
                class="px-4 sm:px-6 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex flex-col md:flex-row gap-3 items-center justify-between">
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto flex-1">
                    {{-- Search --}}
                    <div class="relative w-full sm:max-w-xs group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-m-magnifying-glass
                                class="h-4 w-4 text-gray-400 group-focus-within:text-indigo-500 transition-colors" />
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150 ease-in-out"
                            placeholder="Search tenants...">
                    </div>

                    {{-- Filters --}}
                    <div class="flex gap-3 w-full sm:w-auto">
                        <select wire:model.live="filterPlan"
                            class="block w-full sm:w-40 border border-gray-300 dark:border-gray-600 rounded-lg py-2 pl-3 pr-8 leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Plans</option>
                            <option value="basic">Basic</option>
                            <option value="standard">Standard</option>
                            <option value="premium">Premium</option>
                        </select>
                    </div>
                </div>

                {{-- Active Filters Badges --}}
                @if ($search || $filterPlan)
                    <div class="flex items-center justify-end w-full md:w-auto">
                        <button wire:click="$set('search', ''); $set('filterPlan', '')"
                            class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium hover:underline transition-colors flex items-center gap-1">
                            <x-heroicon-m-trash class="w-3 h-3" /> Clear Filters
                        </button>
                    </div>
                @endif
            </div>
        </header>

        {{-- 2. CONTENT AREA --}}
        <div class="relative min-h-[400px] p-4 sm:p-6 space-y-6">

            {{-- Loading Overlay --}}
            <div wire:loading.flex
                class="absolute inset-0 bg-white/60 dark:bg-gray-900/60 backdrop-blur-[2px] z-20 flex items-start justify-center rounded-2xl pt-20 transition-all">
                <div
                    class="flex items-center gap-3 px-6 py-3 bg-white dark:bg-gray-800 rounded-full shadow-xl border border-slate-100 dark:border-gray-700 animate-bounce">
                    <x-heroicon-o-arrow-path class="animate-spin h-5 w-5 text-indigo-600" />
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Updating...</span>
                </div>
            </div>

            {{-- Stats Overview (Styled like Reference Cards) --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Tenants</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $tenants->total() }}</p>
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl text-indigo-600 dark:text-indigo-400">
                        <x-heroicon-o-building-office-2 class="w-6 h-6" />
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Active Subs</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                            {{-- Placeholder Logic --}}
                            {{ $tenants->where('subscription.status', 'active')->count() }}
                        </p>
                    </div>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl text-emerald-600 dark:text-emerald-400">
                        <x-heroicon-o-credit-card class="w-6 h-6" />
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Expiring Soon</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">0</p>
                    </div>
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl text-amber-600 dark:text-amber-400">
                        <x-heroicon-o-clock class="w-6 h-6" />
                    </div>
                </div>
            </div>

            {{-- Tenants Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($tenants as $tenant)
                    <div wire:key="tenant-{{ $tenant->id }}"
                        class="group bg-white dark:bg-gray-900 rounded-2xl shadow-sm hover:shadow-lg border border-slate-200 dark:border-gray-800 transition-all duration-200 flex flex-col h-full overflow-hidden">

                        {{-- Card Header --}}
                        <div class="p-5 border-b border-slate-100 dark:border-gray-800/50 flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-inner">
                                    {{ substr($tenant->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white truncate max-w-[140px]" title="{{ $tenant->name }}">
                                        {{ $tenant->name }}
                                    </h3>
                                    <a href="http://{{ $tenant->domains->first()?->domain }}" target="_blank"
                                        class="text-xs text-slate-500 hover:text-indigo-600 hover:underline flex items-center gap-1">
                                        {{ $tenant->domains->first()?->domain }}
                                        <x-heroicon-m-arrow-top-right-on-square class="w-3 h-3" />
                                    </a>
                                </div>
                            </div>

                            {{-- Status Dot --}}
                            @php $isActive = $tenant->subscription && $tenant->subscription->isActive(); @endphp
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold {{ $isActive ? 'bg-emerald-50 text-emerald-700 border border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-900' : 'bg-slate-100 text-slate-600 border border-slate-200 dark:bg-gray-800 dark:text-slate-400 dark:border-gray-700' }}">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $isActive ? 'bg-emerald-400' : 'hidden' }} opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 {{ $isActive ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                </span>
                                {{ $isActive ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-5 flex-1 space-y-4">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500 dark:text-slate-400 font-medium">Current Plan</span>
                                @php
                                    $tier = $tenant->subscription_tier;
                                    $badgeClass = match ($tier) {
                                        'enterprise' => 'bg-purple-50 text-purple-700 border-purple-100 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800',
                                        'premium' => 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800',
                                        'standard' => 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800',
                                        default => 'bg-slate-50 text-slate-700 border-slate-100 dark:bg-gray-800 dark:text-slate-300 dark:border-gray-700',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold border capitalize {{ $badgeClass }}">
                                    {{ ucfirst($tier) }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500 dark:text-slate-400 font-medium">Renewal Date</span>
                                <span class="font-bold text-slate-700 dark:text-slate-200">
                                    {{ $tenant->subscription?->ends_at?->format('M d, Y') ?? 'N/A' }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center text-sm">
                                <span class="text-slate-500 dark:text-slate-400 font-medium">Admin Contact</span>
                                <span class="font-medium text-slate-900 dark:text-white truncate max-w-[140px]" title="{{ $tenant->contact_email }}">
                                    {{ $tenant->contact_email }}
                                </span>
                            </div>
                        </div>

                        {{-- Card Actions --}}
                        <div class="p-4 bg-slate-50 dark:bg-gray-800/50 border-t border-slate-200 dark:border-gray-800 flex items-center justify-between gap-2">
                            <a href="{{ route('landlord.manage-subscription', $tenant->id) }}" wire:navigate
                                class="flex-1 inline-flex justify-center items-center gap-2 px-3 py-2 text-xs font-bold text-indigo-700 bg-white border border-indigo-100 rounded-lg hover:bg-indigo-50 hover:border-indigo-200 shadow-sm transition-all dark:bg-gray-800 dark:text-indigo-400 dark:border-gray-700 dark:hover:bg-gray-700">
                                <x-heroicon-s-credit-card class="w-4 h-4" />
                                Manage Sub
                            </a>

                            <div class="flex gap-1">
                                <button wire:click="editTenant({{ $tenant->id }})"
                                    class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-white border border-transparent hover:border-slate-200 hover:shadow-sm dark:hover:bg-gray-700 dark:hover:border-gray-600 rounded-lg transition-all"
                                    title="Edit Details">
                                    <x-heroicon-s-pencil-square class="w-4 h-4" />
                                </button>
                                <button wire:click="viewDeleteTenant({{ $tenant->id }})"
                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-white border border-transparent hover:border-red-100 hover:shadow-sm dark:hover:bg-red-900/20 dark:hover:border-red-900 rounded-lg transition-all"
                                    title="Delete Tenant">
                                    <x-heroicon-s-trash class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-16 flex flex-col items-center justify-center text-center bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-slate-300 dark:border-gray-700">
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-full mb-4">
                            <x-heroicon-o-building-storefront class="w-10 h-10 text-indigo-500" />
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">No tenants found</h3>
                        <p class="text-slate-500 dark:text-slate-400 max-w-sm mt-1 mb-6 text-sm">
                            Try adjusting your search terms or create your first tenant to get started.
                        </p>
                        <a href="{{ route('landlord.create-tenants') }}" wire:navigate
                            class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors text-sm font-bold shadow-md">
                            Create New Tenant
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $tenants->links() }}
            </div>
        </div>
    </div>

    {{-- ================= MODALS ================= --}}

    {{-- Edit Modal (Styled like Reference) --}}
    <div x-cloak x-show="$wire.showEditModal" class="relative z-50" role="dialog" aria-modal="true">
        <div x-show="$wire.showEditModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="$wire.showEditModal" @click.away="$wire.closeEditModal()"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all w-full max-w-lg border border-slate-100 dark:border-gray-800">

                    {{-- Header --}}
                    <div class="bg-white dark:bg-gray-900 px-6 py-4 border-b border-slate-100 dark:border-gray-800 flex items-center justify-between sticky top-0 z-10">
                        <div>
                            <h3 class="text-lg font-bold leading-6 text-slate-900 dark:text-white">Edit Tenant Details</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Update basic information.</p>
                        </div>
                        <button wire:click="closeEditModal" class="rounded-lg bg-slate-50 dark:bg-gray-800 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors focus:outline-none">
                            <x-heroicon-o-x-mark class="h-5 w-5" />
                        </button>
                    </div>

                    <div class="px-6 py-6 space-y-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Tenant Name</label>
                            <input type="text" wire:model.defer="tenantName"
                                class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5">
                            @error('tenantName') <span class="text-xs text-red-500 mt-1 font-medium block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Contact Email</label>
                            <input type="email" wire:model.defer="contactEmail"
                                class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2.5">
                            @error('contactEmail') <span class="text-xs text-red-500 mt-1 font-medium block">{{ $message }}</span> @enderror
                        </div>
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-xl flex gap-3 border border-indigo-100 dark:border-indigo-800">
                            <x-heroicon-s-information-circle class="w-5 h-5 text-indigo-600 dark:text-indigo-400 flex-shrink-0" />
                            <p class="text-xs text-indigo-700 dark:text-indigo-300 leading-relaxed">
                                To change the Subscription Plan or Billing Cycle, please use the <span class="font-bold">Manage Sub</span> page.
                            </p>
                        </div>
                    </div>

                    <div class="bg-slate-50 dark:bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 rounded-b-2xl border-t border-slate-100 dark:border-gray-800">
                        <button type="button" wire:click="saveTenant"
                            class="inline-flex w-full sm:w-auto justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-indigo-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-all">
                            Save Changes
                        </button>
                        <button type="button" wire:click="closeEditModal"
                            class="inline-flex w-full sm:w-auto justify-center rounded-xl bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-gray-700 hover:bg-slate-50 dark:hover:bg-gray-700 transition-all">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-cloak x-show="$wire.showDeleteModal" class="relative z-50">
        <div x-show="$wire.showDeleteModal"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="$wire.showDeleteModal" @click.away="$wire.closeDeleteModal()"
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100 dark:border-gray-800">
                    <div class="p-6 text-center">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 mb-5">
                            <x-heroicon-o-exclamation-triangle class="h-8 w-8 text-red-600 dark:text-red-500" />
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Delete Tenant?</h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                            Are you sure you want to delete <span class="font-bold text-slate-900 dark:text-white">{{ $selectedTenantDomain }}</span>? <br>
                            This action involves deleting databases and <span class="font-bold text-red-600">cannot be undone</span>.
                        </p>
                    </div>
                    <div class="bg-slate-50 dark:bg-gray-800/50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100 dark:border-gray-800">
                        <button wire:click="deleteTenant"
                            class="inline-flex w-full sm:w-auto justify-center rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-red-700 hover:shadow-lg transition-all">
                            Yes, Delete
                        </button>
                        <button wire:click="closeDeleteModal"
                            class="inline-flex w-full sm:w-auto justify-center rounded-xl bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-gray-700 hover:bg-slate-50 dark:hover:bg-gray-700 transition-all">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
