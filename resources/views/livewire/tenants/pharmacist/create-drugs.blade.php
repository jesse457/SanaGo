<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4">
                <div class="flex flex-col gap-2">
                    {{-- Breadcrumbs --}}
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-1" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('pharmacist.dashboard') }}" wire:navigate
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    {{ __('Home') }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <a href="{{ route('pharmacist.manage-drugs') }}" wire:navigate
                                        class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        {{ __('Manage Drugs') }}
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">{{ __('Create Drugs') }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            {{ __('Create New Drug') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                            {{ __('Define the name, pricing, stock levels, and dosage for a new drug item.') }}
                        </p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="p-4 sm:p-6 pb-20">
            <div class="max-w-4xl mx-auto">
                <div id="addUserFormContainer" class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                    <form wire:submit.prevent="saveDrug" class="p-6 sm:p-8">

                        {{-- Success/Error Alerts --}}
                        @if (session()->has('success'))
                            <div x-data="{ open: true }" x-show="open" x-transition
                                class="mb-6 p-4 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800 flex justify-between items-start shadow-sm" role="alert">
                                <div class="flex items-center gap-2">
                                    <x-heroicon-s-check-circle class="w-5 h-5 flex-shrink-0" />
                                    <span class="font-bold">{{ session('success') }}</span>
                                </div>
                                <button @click="open = false" class="text-emerald-800 hover:text-emerald-900 dark:text-emerald-400 dark:hover:text-emerald-300 p-1.5 rounded-lg">
                                    <x-heroicon-s-x-mark class="w-5 h-5" />
                                </button>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div x-data="{ open: true }" x-show="open" x-transition
                                class="mb-6 p-4 text-sm text-red-800 rounded-xl bg-red-50 border border-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800 flex justify-between items-start shadow-sm" role="alert">
                                <div class="flex items-center gap-2">
                                    <x-heroicon-s-exclamation-triangle class="w-5 h-5 flex-shrink-0" />
                                    <span class="font-bold">{{ session('error') }}</span>
                                </div>
                                <button @click="open = false" class="text-red-800 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1.5 rounded-lg">
                                    <x-heroicon-s-x-mark class="w-5 h-5" />
                                </button>
                            </div>
                        @endif

                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                {{-- Drug Name --}}
                                <div>
                                    <label for="drugName" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Drug Name') }} <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <x-heroicon-s-document-text class="h-5 w-5 text-slate-400 dark:text-slate-500" />
                                        </div>
                                        <input type="text" id="drugName" wire:model.live.debounce.300ms="name"
                                            placeholder="{{ __('e.g., Paracetamol') }}"
                                            class="block w-full rounded-xl border py-2.5 pl-10 pr-4 shadow-sm sm:text-sm transition-all duration-200 dark:bg-gray-800 dark:text-white
                                            @error('name') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-slate-300 dark:border-gray-700 focus:border-blue-600 focus:ring-blue-600 @enderror">
                                    </div>
                                    @error('name')
                                        <p class="text-red-600 text-xs font-medium mt-1.5 flex items-center gap-1.5">
                                            <x-heroicon-s-exclamation-circle class="w-4 h-4" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Unit Price --}}
                                <div>
                                    <label for="price" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Unit Price') }} {{ __('(Purchase)') }} <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-slate-500 sm:text-sm font-bold">$</span>
                                        </div>
                                        <input type="number" id="price" wire:model="unit_price_purchase"
                                            placeholder="{{ __('e.g., 2.50') }}" step="0.01"
                                            class="block w-full rounded-xl border py-2.5 pl-8 pr-4 shadow-sm sm:text-sm transition-all duration-200 dark:bg-gray-800 dark:text-white
                                            @error('unit_price_purchase') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-slate-300 dark:border-gray-700 focus:border-blue-600 focus:ring-blue-600 @enderror">
                                    </div>
                                    @error('unit_price_purchase')
                                        <p class="text-red-600 text-xs font-medium mt-1.5 flex items-center gap-1.5">
                                            <x-heroicon-s-exclamation-circle class="w-4 h-4" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Stock Quantity --}}
                                <div>
                                    <label for="stock_quantity" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Stock Quantity') }} <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <x-heroicon-s-scale class="h-5 w-5 text-slate-400 dark:text-slate-500" />
                                        </div>
                                        <input type="number" id="stock_quantity" wire:model="stock_quantity"
                                            placeholder="{{ __('e.g., 500') }}"
                                            class="block w-full rounded-xl border py-2.5 pl-10 pr-4 shadow-sm sm:text-sm transition-all duration-200 dark:bg-gray-800 dark:text-white
                                            @error('stock_quantity') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-slate-300 dark:border-gray-700 focus:border-blue-600 focus:ring-blue-600 @enderror">
                                    </div>
                                    @error('stock_quantity')
                                        <p class="text-red-600 text-xs font-medium mt-1.5 flex items-center gap-1.5">
                                            <x-heroicon-s-exclamation-circle class="w-4 h-4" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Min Stock Level --}}
                                <div>
                                    <label for="min_stock_level" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Min Stock Level') }} <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <x-heroicon-s-archive-box-arrow-down class="h-5 w-5 text-slate-400 dark:text-slate-500" />
                                        </div>
                                        <input type="number" id="min_stock_level" wire:model="min_stock_level"
                                            placeholder="{{ __('e.g., 50') }}"
                                            class="block w-full rounded-xl border py-2.5 pl-10 pr-4 shadow-sm sm:text-sm transition-all duration-200 dark:bg-gray-800 dark:text-white
                                            @error('min_stock_level') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-slate-300 dark:border-gray-700 focus:border-blue-600 focus:ring-blue-600 @enderror">
                                    </div>
                                    @error('min_stock_level')
                                        <p class="text-red-600 text-xs font-medium mt-1.5 flex items-center gap-1.5">
                                            <x-heroicon-s-exclamation-circle class="w-4 h-4" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Dosage Unit --}}
                                <div>
                                    <label for="dosage_unit" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Dosage Unit') }} <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <x-heroicon-s-adjustments-horizontal class="h-5 w-5 text-slate-400 dark:text-slate-500" />
                                        </div>
                                        <input type="text" id="dosage_unit" wire:model="dosage_unit"
                                            placeholder="{{ __('e.g., mg, tablets, ml') }}"
                                            class="block w-full rounded-xl border py-2.5 pl-10 pr-4 shadow-sm sm:text-sm transition-all duration-200 dark:bg-gray-800 dark:text-white
                                            @error('dosage_unit') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-slate-300 dark:border-gray-700 focus:border-blue-600 focus:ring-blue-600 @enderror">
                                    </div>
                                    @error('dosage_unit')
                                        <p class="text-red-600 text-xs font-medium mt-1.5 flex items-center gap-1.5">
                                            <x-heroicon-s-exclamation-circle class="w-4 h-4" />
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <div class="border-t border-slate-100 dark:border-gray-800 pt-8 mt-8">
                            <div>
                                <label for="description" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('Description') }} <span class="text-slate-400 font-normal ml-1 text-xs">{{ __('(Optional)') }}</span></label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 top-3 flex items-start pl-3">
                                        <x-heroicon-s-tag class="h-5 w-5 text-slate-400 dark:text-slate-500" />
                                    </div>
                                    <textarea id="description" wire:model="description" placeholder="{{ __('Provide a detailed description of the drug, its uses, and side effects.') }}"
                                        rows="4"
                                        class="block w-full rounded-xl border py-2.5 pl-10 pr-4 shadow-sm sm:text-sm transition-all duration-200 dark:bg-gray-800 dark:text-white
                                        @error('description') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-slate-300 dark:border-gray-700 focus:border-blue-600 focus:ring-blue-600 @enderror"></textarea>
                                </div>
                                @error('description')
                                    <p class="text-red-600 text-xs font-medium mt-1.5 flex items-center gap-1.5">
                                        <x-heroicon-s-exclamation-circle class="w-4 h-4" />
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="flex items-center justify-end gap-4 mt-10 pt-6 border-t border-slate-200 dark:border-gray-800">
                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2.5 rounded-xl bg-blue-600 px-8 py-3 text-sm font-semibold text-white shadow-md hover:bg-blue-700 hover:shadow-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all duration-200 disabled:opacity-75 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-not-allowed">
                                <span wire:loading wire:target="saveDrug" class="flex items-center gap-2">
                                    <x-heroicon-o-arrow-path class="animate-spin h-5 w-5 text-white" />
                                    <span>{{ __('Creating...') }}</span>
                                </span>
                                <span wire:loading.remove wire:target="saveDrug" class="flex items-center gap-2">
                                    <x-heroicon-s-plus-circle class="w-5 h-5" />
                                    {{ __('Create Drug') }}
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
