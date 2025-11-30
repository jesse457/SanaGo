<main class="flex-1 p-4 md:p-6 lg:ml-64 bg-slate-50 dark:bg-gray-900 overflow-y-auto min-h-screen" role="main">
    {{-- Removed Animated Background Elements for a simpler, professional look --}}

    <div x-data="toastStore()" x-on:show-toast.window="showToast($event.detail)"
        class="fixed top-5 right-5 z-[100] w-full max-w-xs" role="alert" aria-live="polite">
        <div x-show="show" x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg flex items-center gap-4">
            <div class="flex-shrink-0">
                {{-- Standardized Success color to a primary blue --}}
                <x-heroicon-o-check-circle class="w-8 h-8 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <p class="font-bold text-gray-900 dark:text-white" x-text="title"></p>
                <p class="text-sm text-gray-600 dark:text-gray-400" x-text="message"></p>
            </div>
        </div>
    </div>

    <button @click="openSidebar = true" aria-label="{{ __('ui.open_menu') }}"
        class="lg:hidden p-2 rounded-lg text-gray-700 bg-white shadow-md hover:bg-gray-100 transition-all duration-200 mb-6 dark:bg-gray-800 dark:text-gray-200">
        <x-heroicon-o-bars-3 class="w-6 h-6" />
    </button>

    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('landlord.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors dark:text-gray-400 dark:hover:text-indigo-400">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" />
                        {{ __('ui.home') }}
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-500">
                            {{ __('ui.manage_tenants') }}
                        </span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="mb-8">
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2 flex items-center gap-3">
                    <div class="p-2 bg-indigo-600 rounded-lg shadow-md">
                        <x-heroicon-s-user-group class="w-8 h-8 text-white" />
                    </div>
                    {{ __('ui.manage_tenants') }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400">{{ __('ui.tenants_header_subtext') }}</p>
            </div>
            <a href="{{ route('landlord.create-tenants') }}" wire:navigate
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <x-heroicon-o-plus class="w-5 h-5" />
                {{ __('ui.add_tenant') }}
            </a>
        </header>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-4 sm:p-6 mb-6 relative z-10">
        <div class="flex flex-col md:flex-row items-start md:items-center gap-4">
            <div class="relative w-full md:flex-1">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" />
                </div>
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('ui.search_tenant_placeholder') }}"
                    class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
            </div>
            <div class="flex gap-2">
                <button class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" aria-label="{{ __('ui.filter') }}">
                    <x-heroicon-o-adjustments-horizontal class="w-5 h-5" />
                </button>
                <button class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors" aria-label="{{ __('ui.export') }}">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5" />
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 relative z-10">
        @forelse ($tenants as $tenant)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden group">
                <div class="h-2 bg-indigo-600"></div>
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-lg mr-3">
                                {{ substr($tenant->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $tenant->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $tenant->id }}</p>
                            </div>
                        </div>
                        @php $tier = $tenant->subscription_tier ?? 'Basic'; @endphp
                        @if ($tier === 'Premium')
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300">
                                {{ __('ui.premium') }}
                            </span>
                        @elseif ($tier === 'Standard')
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-sky-100 text-sky-800 dark:bg-sky-900/50 dark:text-sky-300">
                                {{ __('ui.standard') }}
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300">
                                {{ __('ui.basic') }}
                            </span>
                        @endif
                    </div>

                    <div class="space-y-3 mb-4">
                        <div class="flex items-center text-sm">
                            <x-heroicon-o-globe-alt class="w-4 h-4 text-gray-400 mr-2" />
                            @php
                                $firstDomain = $tenant->domains()->first()?->domain;
                            @endphp
                            @if ($firstDomain)
                                <a href="{{ $firstDomain }}:8000" target="_blank" rel="noopener"
                                    class="text-indigo-600 dark:text-indigo-400 hover:underline font-mono break-all">
                                    {{ $firstDomain }}
                                </a>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </div>
                        <div class="flex items-center text-sm">
                            <x-heroicon-o-envelope class="w-4 h-4 text-gray-400 mr-2" />
                            <span class="text-gray-600 dark:text-gray-400 truncate">{{ $tenant->contact_email }}</span>
                        </div>

                         <div class="flex items-center text-sm">
                            <x-heroicon-o-calendar class="w-4 h-4 text-gray-400 mr-2" />
                            <span class="text-gray-600 dark:text-gray-400 truncate">{{ __('ui.starts_at') }}: {{ $tenant->subscription?->starts_at }}</span>
                        </div>
                                                 <div class="flex items-center text-sm">
                            <x-heroicon-o-calendar class="w-4 h-4 text-gray-400 mr-2" />
                            <span class="text-gray-600 dark:text-gray-400 truncate">{{ __('ui.ends_at') }}: {{ $tenant->subscription?->ends_at }}</span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-4 border-t border-gray-100 dark:border-gray-700">
                        <button type="button" wire:click="editTenant({{ $tenant->id }})"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors">
                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                            {{ __('ui.edit') }}
                        </button>
                        <div class="flex gap-2">
                            <button type="button" wire:click="viewTenant({{ $tenant->id }})"
                                class="p-1.5 rounded-lg text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors" aria-label="{{ __('ui.view') }}">
                                <x-heroicon-o-eye class="w-4 h-4" />
                            </button>
                            <button type="button" wire:click="viewDeleteTenant({{ $tenant->id }})"
                                class="p-1.5 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors" aria-label="{{ __('ui.delete') }}">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-12 text-center">
                    <x-heroicon-o-user-group class="w-16 h-16 mx-auto text-gray-400 mb-4" />
                    <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">{{ __('ui.no_tenants_found') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">
                        {{ __('ui.tenants_search_tip') }}
                    </p>
                    <a href="{{ route('landlord.create-tenants') }}" wire:navigate
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <x-heroicon-o-plus class="w-5 h-5" />
                        {{ __('ui.add_new_tenant') }}
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <div class="flex justify-center">
        {{ $tenants->links() }}
    </div>

    <!-- View Modal -->
    <div x-cloak x-show="$wire.showViewModal" x-transition.opacity
        class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div @click="$wire.closeViewModal()" class="absolute inset-0 bg-black/50 backdrop-blur-sm" aria-hidden="true"></div>
        <div @keydown.escape.window="$wire.closeViewModal()"
            class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden transform transition-all"
            role="dialog" aria-modal="true" aria-labelledby="view-modal-title">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 id="view-modal-title" class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('ui.tenant_details') }}</h3>
                <button class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    @click="$wire.closeViewModal()" aria-label="{{ __('ui.close') }}">
                    <x-heroicon-o-x-mark class="w-5 h-5 text-gray-500" />
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-xl">
                        <span x-text="$wire.viewing?.name ? $wire.viewing.name.charAt(0).toUpperCase() : ''"></span>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="$wire.viewing?.name"></h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">ID: <span x-text="$wire.viewing?.id"></span></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <p class="text-xs uppercase text-gray-500 dark:text-gray-400 mb-1">{{ __('ui.domain') }}</p>
                        <p class="text-sm font-mono text-indigo-600 dark:text-indigo-400 break-all"
                            x-text="$wire.viewing?.domain"></p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3">
                        <p class="text-xs uppercase text-gray-500 dark:text-gray-400 mb-1">{{ __('ui.subscription') }}</p>
                        <p class="text-sm text-gray-900 dark:text-white" x-text="$wire.viewing?.subscription_tier"></p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 sm:col-span-2">
                        <p class="text-xs uppercase text-gray-500 dark:text-gray-400 mb-1">{{ __('ui.email') }}</p>
                        <p class="text-sm text-gray-900 dark:text-white" x-text="$wire.viewing?.contact_email"></p>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <button
                    class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                    @click="$wire.closeViewModal()">
                    {{ __('ui.close') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-cloak x-show="$wire.showEditModal" x-transition.opacity
        class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div @click="$wire.closeEditModal()" class="absolute inset-0 bg-black/50 backdrop-blur-sm" aria-hidden="true"></div>
        <div @keydown.escape.window="$wire.closeEditModal()"
            class="relative w-full max-w-xl bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden transform transition-all"
            role="dialog" aria-modal="true" aria-labelledby="edit-modal-title">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h3 id="edit-modal-title" class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('ui.edit_tenant') }}</h3>
                <button class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    @click="$wire.closeEditModal()" aria-label="{{ __('ui.close') }}">
                    <x-heroicon-o-x-mark class="w-5 h-5 text-gray-500" />
                </button>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="tenant-name" class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ui.tenant_name') }}</label>
                        <input id="tenant-name" type="text" wire:model.defer="tenantName"
                            class="w-full px-3 py-2 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        @error('tenantName')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="contact-email" class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ui.contact_email') }}</label>
                        <input id="contact-email" type="email" wire:model.defer="contactEmail"
                            class="w-full px-3 py-2 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        @error('contactEmail')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="subscription-tier" class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ui.subscription') }}</label>
                        <select id="subscription-tier" wire:model.defer="subscriptionTier"
                            class="w-full px-3 py-2 rounded-lg border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="Basic">{{ __('ui.basic') }}</option>
                            <option value="Standard">{{ __('ui.standard') }}</option>
                            <option value="Premium">{{ __('ui.premium') }}</option>
                        </select>
                        @error('subscriptionTier')
                            <p class="text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2 sm:col-span-2">
                        <label for="generated-domain" class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ui.generated_domain') }}</label>
                        <input id="generated-domain" type="text" wire:model="generatedDomain" readonly
                            class="w-full px-3 py-2 rounded-lg border-gray-300 bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-2">
                <button
                    class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                    @click="$wire.closeEditModal()">
                    {{ __('ui.cancel') }}
                </button>
                <button wire:click="saveTenant"
                    class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                    {{ __('ui.save_changes') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div x-cloak x-show="$wire.showDeleteModal" x-transition.opacity
        class="fixed inset-0 z-40 flex items-center justify-center p-4">
        <div @click="$wire.closeDeleteModal()" class="absolute inset-0 bg-black/50 backdrop-blur-sm" aria-hidden="true"></div>
        <div @keydown.escape.window="$wire.closeDeleteModal()"
            class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden transform transition-all"
            role="dialog" aria-modal="true" aria-labelledby="delete-modal-title">
            <div class="px-6 py-5">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                        <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>
                    <div>
                        <h3 id="delete-modal-title" class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('ui.delete_tenant') }}</h3>
                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                            {{ __('ui.delete_tenant_confirm', ['domain' => '<span class="font-semibold" x-text="$wire.selectedTenantDomain"></span>']) }}
                            {{ __('ui.action_cannot_be_undone') }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="px-6 py-4 bg-gray-50 dark:bg-gray-700/40 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-2">
                <button
                    class="px-4 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                    @click="$wire.closeDeleteModal()">
                    {{ __('ui.cancel') }}
                </button>
                <button wire:click="deleteTenant" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition-colors">
                    {{ __('ui.delete') }}
                </button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('toastStore', () => ({
                show: false,
                title: '{{ __('ui.success') }}',
                message: '',
                showToast(detail) {
                    this.title = detail.title || '{{ __('ui.success') }}';
                    this.message = detail.message || '';
                    this.show = true;
                    clearTimeout(this._t);
                    this._t = setTimeout(() => this.show = false, detail.timeout || 3000);
                }
            }));

            // Listen for Livewire notify events
            Livewire.on('notify', (event) => {
                Alpine.store('toastStore').showToast(event);
            });
        });
    </script>
</main>
