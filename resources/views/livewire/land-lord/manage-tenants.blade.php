<main class="bg-white max-w-7xl mx-auto font-sans p-6">

    {{-- Toast Notification (Alpine.js) --}}
    <div x-data="{ show: false, title: '', message: '', type: 'success' }"
        x-on:notify.window="show = true; title = $event.detail.title; message = $event.detail.message; type = $event.detail.type || 'success'; setTimeout(() => show = false, 3000)"
        class="fixed top-6 right-6 z-[100] w-full max-w-sm" x-cloak>
        <div x-show="show" x-transition.opacity.duration.300ms
            :class="{ 'bg-green-50 border-green-200': type === 'success', 'bg-red-50 border-red-200': type === 'error' }"
            class="p-4 border rounded-xl shadow-lg flex items-start gap-3 relative backdrop-blur-sm">
            <div x-show="type === 'success'" class="text-green-500"><x-heroicon-s-check-circle class="w-6 h-6" /></div>
            <div x-show="type === 'error'" class="text-red-500"><x-heroicon-s-x-circle class="w-6 h-6" /></div>
            <div>
                <h4 class="font-bold text-gray-900" x-text="title"></h4>
                <p class="text-sm text-gray-600" x-text="message"></p>
            </div>
            <button @click="show = false"
                class="absolute top-2 right-2 text-gray-400 hover:text-gray-600"><x-heroicon-s-x-mark
                    class="w-4 h-4" /></button>
        </div>
    </div>

    {{-- Breadcrumb --}}
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
                    <span class="ms-1 text-sm font-medium text-slate-500 md:ms-2 dark:text-slate-400">Tenant
                        Complaints</span>
                </div>
            </li>
        </ol>
    </nav>

    {{-- Header & Actions --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-slate-900 dark:text-white tracking-tight">Tenant Management</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Overview of all registered hospitals and clinics.</p>
        </div>
        <a href="{{ route('landlord.create-tenants') }}" wire:navigate
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-all hover:-translate-y-0.5 focus:ring-4 focus:ring-indigo-500/20">
            <x-heroicon-m-plus class="w-5 h-5" />
            <span>{{ __('ui.add_tenant') }}</span>
        </a>
    </div>

    {{-- Stats Overview (Optional but recommended) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div
            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Tenants</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $tenants->total() }}</p>
            </div>
            <div class="p-3 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400">
                <x-heroicon-o-building-office-2 class="w-6 h-6" />
            </div>
        </div>
        <div
            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Active Subscriptions</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">
                    {{-- Assuming you add a scopeActive to your component query --}}
                    {{ $tenants->where('subscription.status', 'active')->count() }}
                </p>
            </div>
            <div class="p-3 bg-green-50 dark:bg-green-900/30 rounded-lg text-green-600 dark:text-green-400">
                <x-heroicon-o-credit-card class="w-6 h-6" />
            </div>
        </div>
        <div
            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Expiring Soon</p>
                <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1">0</p>
            </div>
            <div class="p-3 bg-amber-50 dark:bg-amber-900/30 rounded-lg text-amber-600 dark:text-amber-400">
                <x-heroicon-o-clock class="w-6 h-6" />
            </div>
        </div>
    </div>

    {{-- Filters & Search --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-heroicon-o-magnifying-glass class="h-5 w-5 text-slate-400" />
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Search by name, domain, or email..."
                    class="block w-full pl-10 pr-3 py-2.5 border border-slate-300 dark:border-gray-600 rounded-lg bg-slate-50 dark:bg-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition-shadow">
            </div>
            <div class="flex gap-2">
                <select wire:model.live="filterPlan"
                    class="py-2.5 pl-3 pr-8 border border-slate-300 dark:border-gray-600 rounded-lg bg-slate-50 dark:bg-gray-900 text-slate-700 dark:text-gray-200 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Plans</option>
                    <option value="basic">Basic</option>
                    <option value="standard">Standard</option>
                    <option value="premium">Premium</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Tenants Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @forelse ($tenants as $tenant)
            <div
                class="group bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md border border-slate-200 dark:border-gray-700 transition-all duration-200 flex flex-col h-full">

                {{-- Card Header --}}
                <div class="p-5 border-b border-slate-100 dark:border-gray-700/50 flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        {{-- Avatar / Logo Placeholder --}}
                        <div
                            class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-lg">
                            {{ substr($tenant->name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="text-base font-semibold text-slate-900 dark:text-white truncate max-w-[140px]"
                                title="{{ $tenant->name }}">
                                {{ $tenant->name }}
                            </h3>
                            <a href="http://{{ $tenant->domains->first()?->domain }}" target="_blank"
                                class="text-xs text-slate-500 hover:text-indigo-600 hover:underline flex items-center gap-1">
                                {{ $tenant->domains->first()?->domain }}
                                <x-heroicon-m-arrow-top-right-on-square class="w-3 h-3" />
                            </a>
                        </div>
                    </div>
                    {{-- Live Status Dot --}}
                    @php $isActive = $tenant->subscription && $tenant->subscription->isActive(); @endphp
                    <div class="flex flex-col items-end">
                        <span
                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $isActive ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                            <span
                                class="w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            {{ $isActive ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                {{-- Card Body --}}
                <div class="p-5 flex-1 space-y-4">
                    {{-- Plan Badge --}}
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 dark:text-slate-400">Plan</span>
                        @php
                            $tier = $tenant->subscription_tier;
                            $badgeClass = match ($tier) {
                                'enterprise' => 'bg-purple-100 text-purple-700 border-purple-200',
                                'premium' => 'bg-amber-100 text-amber-700 border-amber-200',
                                'standard' => 'bg-blue-100 text-blue-700 border-blue-200',
                                default => 'bg-slate-100 text-slate-700 border-slate-200',
                            };
                        @endphp
                        <span class="px-2 py-0.5 rounded text-xs font-semibold border {{ $badgeClass }}">
                            {{ ucfirst($tier) }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 dark:text-slate-400">Renewal</span>
                        <span class="font-medium text-slate-900 dark:text-white">
                            {{ $tenant->subscription?->ends_at?->format('M d, Y') ?? 'N/A' }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 dark:text-slate-400">Contact</span>
                        <span class="font-medium text-slate-900 dark:text-white truncate max-w-[150px]"
                            title="{{ $tenant->contact_email }}">
                            {{ $tenant->contact_email }}
                        </span>
                    </div>
                </div>

                {{-- Card Actions --}}
                <div
                    class="p-4 bg-slate-50 dark:bg-gray-800/50 border-t border-slate-200 dark:border-gray-700 rounded-b-xl flex justify-between gap-2">
                    {{-- Main Link Requested: Manage Subscription --}}
                    <a href="{{ route('landlord.manage-subscription', $tenant->id) }}" wire:navigate
                        class="flex-1 inline-flex justify-center items-center gap-2 px-3 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 rounded-lg dark:bg-indigo-900/20 dark:text-indigo-300 dark:hover:bg-indigo-900/40 transition-colors">
                        <x-heroicon-s-credit-card class="w-4 h-4" />
                        {{ __('Manage Sub') }}
                    </a>

                    {{-- Edit Button (Triggers Modal) --}}
                    <button wire:click="editTenant({{ $tenant->id }})"
                        class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-200 dark:hover:bg-gray-700 rounded-lg transition-colors"
                        title="Edit Details">
                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                    </button>

                    {{-- Delete Button --}}
                    <button wire:click="viewDeleteTenant({{ $tenant->id }})"
                        class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                        title="Delete Tenant">
                        <x-heroicon-o-trash class="w-5 h-5" />
                    </button>
                </div>
            </div>
        @empty
            <div
                class="col-span-full py-12 flex flex-col items-center justify-center text-center bg-white dark:bg-gray-800 rounded-xl border border-dashed border-slate-300 dark:border-gray-700">
                <div class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-full mb-4">
                    <x-heroicon-o-building-storefront class="w-8 h-8 text-indigo-500" />
                </div>
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">No tenants found</h3>
                <p class="text-slate-500 dark:text-slate-400 max-w-sm mt-1 mb-6">
                    Try adjusting your search terms or create your first tenant to get started.
                </p>
                <a href="{{ route('landlord.create-tenants') }}" wire:navigate
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                    Create New Tenant
                </a>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $tenants->links() }}
    </div>

    {{-- ================= MODALS ================= --}}

    {{-- Edit Modal --}}
    <div x-cloak x-show="$wire.showEditModal" class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="$wire.showEditModal" x-transition.opacity
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="$wire.showEditModal" @click.away="$wire.closeEditModal()"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                class="relative transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-gray-700">

                <div
                    class="px-4 py-5 sm:px-6 bg-slate-50 dark:bg-gray-700/50 border-b border-slate-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-semibold leading-6 text-slate-900 dark:text-white" id="modal-title">Edit
                        Tenant Details</h3>
                    <button wire:click="closeEditModal"
                        class="text-slate-400 hover:text-slate-500"><x-heroicon-m-x-mark class="w-5 h-5" /></button>
                </div>

                <div class="px-4 py-5 sm:p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium leading-6 text-slate-900 dark:text-white">Tenant
                            Name</label>
                        <input type="text" wire:model.defer="tenantName"
                            class="mt-2 block w-full rounded-md border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-gray-900 dark:ring-gray-600 dark:text-white">
                        @error('tenantName')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium leading-6 text-slate-900 dark:text-white">Contact
                            Email</label>
                        <input type="email" wire:model.defer="contactEmail"
                            class="mt-2 block w-full rounded-md border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 dark:bg-gray-900 dark:ring-gray-600 dark:text-white">
                        @error('contactEmail')
                            <span class="text-xs text-red-500">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg flex gap-3">
                        <x-heroicon-s-information-circle
                            class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" />
                        <p class="text-xs text-blue-700 dark:text-blue-300">
                            To change the Subscription Plan or Billing Cycle, please use the <span
                                class="font-bold">Manage Sub</span> page.
                        </p>
                    </div>
                </div>

                <div
                    class="bg-slate-50 dark:bg-gray-700/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-200 dark:border-gray-700">
                    <button type="button" wire:click="saveTenant"
                        class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">Save
                        Changes</button>
                    <button type="button" wire:click="closeEditModal"
                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto dark:bg-gray-800 dark:text-slate-300 dark:ring-gray-600 dark:hover:bg-gray-700">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div x-cloak x-show="$wire.showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto">
        <div x-show="$wire.showDeleteModal" x-transition.opacity
            class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="$wire.showDeleteModal" @click.away="$wire.closeDeleteModal()"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                class="relative transform overflow-hidden rounded-xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="p-6 text-center">
                    <div
                        class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                        <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-red-600" />
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Delete Tenant?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">
                        Are you sure you want to delete <span
                            class="font-bold text-slate-900 dark:text-white">{{ $selectedTenantDomain }}</span>? This
                        action involves deleting databases and cannot be undone.
                    </p>
                </div>
                <div class="bg-slate-50 dark:bg-gray-700/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button wire:click="deleteTenant"
                        class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">Yes,
                        Delete</button>
                    <button wire:click="closeDeleteModal"
                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto dark:bg-gray-800 dark:text-slate-300 dark:ring-gray-600">Cancel</button>
                </div>
            </div>
        </div>
    </div>

</main>
