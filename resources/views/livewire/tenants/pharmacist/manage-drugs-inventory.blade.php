<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4 md:flex md:items-center md:justify-between space-y-3 md:space-y-0">

                {{-- Title & Breadcrumbs --}}
                <div class="flex-1 min-w-0">
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 mt-2" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('pharmacist.dashboard') }}" wire:navigate
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    {{ __('pharmacist.manage_drugs_page.breadcrumb_home') }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">{{ __('pharmacist.manage_drugs_page.breadcrumb_manage_drugs') }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            {{ __('pharmacist.manage_drugs_page.manage_drugs_title') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('pharmacist.manage_drugs_page.manage_drugs_description') }}
                        </p>
                    </div>
                </div>

                {{-- Action Toolbar --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('pharmacist.create-drugs') }}" wire:navigate
                        class="group relative inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl shadow-md hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all overflow-hidden dark:focus:ring-offset-gray-900">
                        <div
                            class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                        </div>
                        <x-heroicon-s-plus class="w-5 h-5" />
                        <span>{{ __('pharmacist.manage_drugs_page.add_new_drug_button') }}</span>
                    </a>
                </div>
            </div>

            {{-- Filters Bar --}}
            <div class="px-4 sm:px-6 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row gap-3 items-center justify-between">
                    {{-- Search Input --}}
                    <div class="relative w-full md:flex-grow group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-m-magnifying-glass class="h-4 w-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                        </div>
                        <input type="text" wire:model.live.debounce.400ms="search" placeholder="{{ __('pharmacist.manage_drugs_page.search_by_drug_name') }}"
                            class="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out">
                    </div>

                    {{-- Status Filter --}}
                    <div class="relative w-full md:w-auto md:min-w-[200px]">
                        <select wire:model.live="statusFilter"
                            class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg py-2 pl-3 pr-8 leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out">
                            <option value="">{{ __('pharmacist.manage_drugs_page.filter_all_drugs') }}</option>
                            <option value="low-stock">{{ __('pharmacist.manage_drugs_page.filter_low_stock') }}</option>
                            <option value="in-stock">{{ __('pharmacist.manage_drugs_page.filter_in_stock') }}</option>
                        </select>
                    </div>

                    {{-- Clear Filters --}}
                    @if ($search || $statusFilter)
                        <div class="flex items-center justify-end w-full md:w-auto">
                            <button wire:click="$set('search', ''); $set('statusFilter', '')"
                                class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium hover:underline transition-colors flex items-center gap-1">
                                <x-heroicon-m-trash class="w-3 h-3" /> Clear Filters
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="relative min-h-[400px] p-4 sm:p-6 pb-20">

            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-gray-800">
                        <thead class="bg-slate-50 dark:bg-gray-950">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ __('pharmacist.manage_drugs_page.drug_name') }}
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ __('pharmacist.manage_drugs_page.stock_quantity') }}
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ __('pharmacist.manage_drugs_page.price') }}
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ __('pharmacist.manage_drugs_page.min_stock_level') }}
                                </th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ __('pharmacist.manage_drugs_page.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                            @forelse ($drugs as $drug)
                                @php
                                    $isLowStock = $drug->stock_quantity <= $drug->min_stock_level;
                                    $stockClasses = $isLowStock
                                        ? 'bg-red-50 text-red-700 border-red-100 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800'
                                        : 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800';
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $drug->name }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            {{ \Illuminate\Support\Str::limit($drug->description, 50) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border shadow-sm {{ $stockClasses }}">
                                            {{ $drug->stock_quantity }}
                                            <span class="ml-1 text-[10px] font-medium">{{ $drug->dosage_unit }}</span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-900 dark:text-white">
                                        FCFA {{ number_format($drug->unit_price_purchase, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                        {{ $drug->min_stock_level }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button wire:click="viewEditDrug({{ $drug->id }})" title="{{ __('pharmacist.manage_drugs_page.edit') }}"
                                                class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                                                <x-heroicon-m-pencil-square class="w-4 h-4" />
                                            </button>

                                            <button wire:click="viewDeleteDrug({{ $drug->id }})" title="{{ __('pharmacist.manage_drugs_page.delete') }}"
                                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                                <x-heroicon-m-trash class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 bg-slate-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4 border border-slate-100 dark:border-gray-700">
                                                <x-heroicon-o-archive-box class="w-8 h-8 text-slate-400" />
                                            </div>
                                            <p class="text-base font-bold text-slate-900 dark:text-white mb-1">{{ __('pharmacist.manage_drugs_page.no_drugs_found') }}</p>
                                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                                @if ($search || $statusFilter)
                                                    {{ __('pharmacist.manage_drugs_page.try_adjusting_search_filter') }}
                                                @else
                                                    {{ __('pharmacist.manage_drugs_page.get_started_adding_drug') }}
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- pagination --}}
                    @if ($drugs->hasPages())
                        <div class="p-4 border-t border-slate-100 dark:border-gray-800 bg-slate-50 dark:bg-gray-900/50">
                            {{ $drugs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Edit Modal (Drug) --}}
        <div x-data="{ show: @entangle('showDrugEditModal') }" x-show="show" x-cloak class="relative z-50">
            <template x-teleport="body">
                <div x-show="show" class="fixed inset-0 z-50 overflow-y-auto">
                    {{-- Backdrop --}}
                    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" wire:click="$set('showDrugEditModal', false)"></div>

                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <form wire:submit.prevent="updateDrug"
                            x-show="show"
                            x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-100 dark:border-gray-800"
                        >
                            {{-- Modal Header --}}
                            <div class="px-6 py-5 border-b border-slate-100 dark:border-gray-800 flex items-center justify-between bg-white dark:bg-gray-900">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ __('pharmacist.manage_drugs_page.edit_drug_title') }}</h3>
                                <button type="button" wire:click="$set('showDrugEditModal', false)"
                                        class="rounded-xl bg-slate-50 dark:bg-gray-800 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
                                    <x-heroicon-o-x-mark class="w-5 h-5" />
                                </button>
                            </div>

                            <div class="p-6 space-y-5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                                    <div>
                                        <label for="editing-drug-name" class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('pharmacist.create_drugs_page.drug_name') }}</label>
                                        <input id="editing-drug-name" type="text" wire:model.defer="drug_name"
                                            class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5" />
                                        @error('drug_name') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="editing-units-name" class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('pharmacist.manage_drugs_page.dosage_units') }}</label>
                                        <input id="editing-units-name" type="text" wire:model.defer="dosage_units"
                                            class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5" />
                                        @error('dosage_units') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="editing-price" class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('pharmacist.manage_drugs_page.price_usd') }}</label>
                                        <input id="editing-price" type="number" step="0.01" min="0" wire:model.defer="price"
                                            class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5" />
                                        @error('price') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="editing-stock" class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('pharmacist.create_drugs_page.stock_quantity') }}</label>
                                        <input id="editing-stock" type="number" min="0" wire:model.defer="stock_quantity"
                                            class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5" />
                                        @error('stock_quantity') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="editing-min-stock" class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('pharmacist.manage_drugs_page.minimum_stock_level') }}</label>
                                        <input id="editing-min-stock" type="number" min="0" wire:model.defer="min_stock_level"
                                            class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5" />
                                        @error('min_stock_level') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="mt-5">
                                    <label for="editing-description" class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('pharmacist.create_drugs_page.description') }}</label>
                                    <textarea id="editing-description" rows="2" wire:model.defer="description"
                                        class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5"></textarea>
                                    @error('description') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="px-6 py-4 bg-slate-50 dark:bg-gray-900/50 flex items-center justify-end gap-3 rounded-b-2xl border-t border-slate-100 dark:border-gray-800">
                                <button type="button" wire:click="$set('showDrugEditModal', false)"
                                        class="rounded-xl bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-gray-700 hover:bg-slate-50 dark:hover:bg-gray-700 transition-all">
                                    {{ __('pharmacist.manage_drugs_page.cancel') }}
                                </button>

                                <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all"
                                        wire:loading.attr="disabled"
                                        wire:target="updateDrug">
                                    <span wire:loading.remove wire:target="updateDrug">{{ __('pharmacist.manage_drugs_page.save_changes') }}</span>
                                    <span wire:loading wire:target="updateDrug" class="flex items-center gap-2"><x-heroicon-o-arrow-path class="animate-spin h-4 w-4" /> Saving...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>

        {{-- Delete Modal (Drug) --}}
        <div x-data="{ show: @entangle('showDrugDeleteModal') }" x-show="show" x-cloak class="relative z-50">
            <template x-teleport="body">
                <div x-show="show" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center">
                        {{-- Backdrop --}}
                        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                             class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" wire:click="$set('showDrugDeleteModal', false)"></div>

                        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-sm border border-slate-100 dark:border-gray-800">

                            <div class="p-6">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20 sm:mx-0 sm:h-10 sm:w-10">
                                        <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-red-600 dark:text-red-400" />
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Delete Drug</h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                                Are you sure you want to delete this drug definition? This action cannot be undone.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="px-6 py-4 bg-slate-50 dark:bg-gray-900/50 flex justify-end gap-3 rounded-b-2xl border-t border-slate-100 dark:border-gray-800">
                                <button type="button" wire:click="$set('showDrugDeleteModal', false)"
                                        class="rounded-xl bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-gray-700 hover:bg-slate-50 dark:hover:bg-gray-700 transition-all">
                                    Cancel
                                </button>

                                <button type="button" wire:click="deleteDrug"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-red-700 transition-all"
                                        wire:loading.attr="disabled"
                                        wire:target="deleteDrug">
                                    <span wire:loading.remove wire:target="deleteDrug">Delete</span>
                                    <span wire:loading wire:target="deleteDrug">Deleting...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</main>
