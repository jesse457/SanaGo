<main class="flex-1 p-4 md:p-6 lg:ml-64 bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">
    {{-- Breadcrumbs --}}
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('pharmacist.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors duration-150">
                        <x-heroicon-s-home class="h-4 w-4 me-2.5" />
                        {{ __('pharmacist.manage_drugs_page.breadcrumb_home') }}
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-4 h-4 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm text-gray-400 md:ms-2 dark:text-gray-200">{{ __('pharmacist.manage_drugs_page.breadcrumb_manage_drugs') }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    {{-- Header --}}
    <header class="flex items-center justify-between mb-8 pb-4 border-b border-gray-200 dark:border-gray-700">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-1 flex items-center gap-3">
                <x-heroicon-s-beaker class="w-8 h-8 text-indigo-600" />
                {{ __('pharmacist.manage_drugs_page.manage_drugs_title') }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">{{ __('pharmacist.manage_drugs_page.manage_drugs_description') }}</p>
        </div>
        <a href="{{ route('pharmacist.create-drugs') }}" wire:navigate
            class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition duration-150 ease-in-out shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <x-heroicon-s-plus class="w-5 h-5" />
            {{ __('pharmacist.manage_drugs_page.add_new_drug_button') }}
        </a>
    </header>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">

        <div class="p-6 lg:p-8 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="relative w-full md:flex-grow">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <x-heroicon-s-magnifying-glass class="w-5 h-5 text-gray-400 dark:text-gray-500" />
                    </div>
                    <input type="text" wire:model.live.debounce.400ms="search" placeholder="{{ __('pharmacist.manage_drugs_page.search_by_drug_name') }}"
                        class="w-full pl-12 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 text-gray-900 dark:text-gray-200">
                </div>
                <div class="relative w-full md:w-auto md:min-w-[200px]">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <x-heroicon-s-funnel class="w-5 h-5 text-gray-400 dark:text-gray-500" />
                    </div>
                    <select wire:model.live="statusFilter"
                        class="w-full appearance-none pl-12 pr-10 py-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 text-gray-900 dark:text-gray-200">
                        <option value="">{{ __('pharmacist.manage_drugs_page.filter_all_drugs') }}</option>
                        <option value="low-stock">{{ __('pharmacist.manage_drugs_page.filter_low_stock') }}</option>
                        <option value="in-stock">{{ __('pharmacist.manage_drugs_page.filter_in_stock') }}</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                            {{ __('pharmacist.manage_drugs_page.drug_name') }}</th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                            {{ __('pharmacist.manage_drugs_page.stock_quantity') }}</th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                            {{ __('pharmacist.manage_drugs_page.price') }}</th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                            {{ __('pharmacist.manage_drugs_page.min_stock_level') }}</th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">{{ __('pharmacist.manage_drugs_page.actions') }}</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($drugs as $drug)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $drug->name }}</td>
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm
                                @if ($drug->stock_quantity <= $drug->min_stock_level) text-red-500 dark:text-red-400 font-semibold
                                @else
                                    text-gray-500 dark:text-gray-400 @endif
                                ">
                                {{ $drug->stock_quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                ${{ number_format($drug->unit_price_purchase, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $drug->min_stock_level }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center justify-center space-x-2">
                                    <button wire:click="viewEditDrug({{ $drug->id }})" title="{{ __('pharmacist.manage_drugs_page.edit') }}"
                                        class="ms-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium text-blue-700 bg-blue-100 hover:bg-blue-600 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400 dark:bg-blue-900/40 dark:text-blue-300 dark:hover:bg-blue-900/60 transition-all duration-200 transform hover:scale-105">
                                        <x-heroicon-s-pencil-square class="w-5 h-5" />
                                        <span>{{ __('pharmacist.manage_drugs_page.edit') }}</span>
                                    </button>

                                    <button wire:click="viewDeleteDrug({{ $drug->id }})" title="{{ __('pharmacist.manage_drugs_page.delete') }}"
                                        class="ms-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium text-red-700 bg-red-100 hover:bg-red-600 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 dark:bg-red-900/40 dark:text-red-300 dark:hover:bg-red-900/60 transition-all duration-200 transform hover:scale-105">
                                        <x-heroicon-s-trash class="w-5 h-5" />
                                        <span>{{ __('pharmacist.manage_drugs_page.delete') }}</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                    <x-heroicon-o-archive-box class="w-16 h-16 mb-4 text-gray-300 dark:text-gray-600" />
                                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-1">{{ __('pharmacist.manage_drugs_page.no_drugs_found') }}</p>
                                    @if ($search || $statusFilter)
                                        <p class="text-sm">{{ __('pharmacist.manage_drugs_page.try_adjusting_search_filter') }}</p>
                                    @else
                                        <p class="text-sm">{{ __('pharmacist.manage_drugs_page.get_started_adding_drug') }}</p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- pagination --}}
            <div class="p-4">
                {{ $drugs->links() }}
            </div>
        </div>
    </div>

    {{-- Edit Modal (Drug) --}}
    <div x-data x-show="$wire.showDrugEditModal" x-cloak x-trap.noscroll
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50">
        <div @click.away="$wire.set('showDrugEditModal', false)"
             class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-2xl w-full mx-auto transform transition-all
                    max-h-[90vh] overflow-auto">
            <div class="flex justify-between items-start">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('pharmacist.manage_drugs_page.edit_drug_title') }}</h3>
                <button type="button" @click="$wire.set('showDrugEditModal', false)"
                        class="text-gray-400 hover:bg-gray-200 hover:text-gray-900 rounded-lg p-1.5 inline-flex items-center focus:outline-none">
                    <x-heroicon-s-x-mark class="w-5 h-5" />
                </button>
            </div>

            <form wire:submit.prevent="updateDrug" class="mt-4 space-y-4">
                <div>
                    <label for="editing-drug-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('pharmacist.create_drugs_page.drug_name') }}</label>
                    <input id="editing-drug-name" type="text" wire:model.defer="drug_name"
                           class="w-full mt-1 px-3 py-2 rounded-md border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100" />
                    @error('drug_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="editing-units-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('pharmacist.manage_drugs_page.dosage_units') }}</label>
                    <input id="editing-units-name" type="text" wire:model.defer="dosage_units"
                           class="w-full mt-1 px-3 py-2 rounded-md border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100" />
                    @error('dosage_units') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="editing-price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('pharmacist.manage_drugs_page.price_usd') }}</label>
                        <input id="editing-price" type="number" step="0.01" min="0" wire:model.defer="price"
                               class="w-full mt-1 px-3 py-2 rounded-md border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100" />
                        @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="editing-stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('pharmacist.create_drugs_page.stock_quantity') }}</label>
                        <input id="editing-stock" type="number" min="0" wire:model.defer="stock_quantity"
                               class="w-full mt-1 px-3 py-2 rounded-md border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100" />
                        @error('stock_quantity') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div>
                    <label for="editing-min-stock" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('pharmacist.manage_drugs_page.minimum_stock_level') }}</label>
                    <input id="editing-min-stock" type="number" min="0" wire:model.defer="min_stock_level"
                           class="w-full mt-1 px-3 py-2 rounded-md border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100" />
                    @error('min_stock_level') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="editing-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('pharmacist.create_drugs_page.description') }}</label>
                    <textarea id="editing-description" rows="3" wire:model.defer="description"
                              class="w-full mt-1 px-3 py-2 rounded-md border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100"></textarea>
                    @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mt-2 flex justify-end gap-3">
                    <button type="button" @click="$wire.set('showDrugEditModal', false)"
                            class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200">
                        {{ __('pharmacist.manage_drugs_page.cancel') }}
                    </button>

                    <button type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        {{ __('pharmacist.manage_drugs_page.save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
