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
                                <a href="{{ route('lab-technician.dashboard') }}" wire:navigate
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    Home
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <a href="{{ route('lab-technician.manage-lab-definitions') }}" wire:navigate
                                        class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        Lab Tests
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">Create Lab Test</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            Create New Lab Test
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                            Define the parameters, pricing, and units for a new diagnostic test.
                        </p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="p-4 sm:p-6 pb-20">
            <div class="max-w-4xl mx-auto">
                {{-- Main Form Card --}}
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                    <form wire:submit.prevent="saveTest" class="p-6 sm:p-8">

                        {{-- Success/Error Alerts --}}
                        @if (session()->has('success'))
                            <div x-data="{ open: true }" x-show="open" x-transition
                                class="mb-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 flex justify-between items-start shadow-sm"
                                role="alert">
                                <div class="flex items-center gap-3">
                                    <x-heroicon-s-check-circle class="w-5 h-5 text-emerald-600 dark:text-emerald-400 flex-shrink-0" />
                                    <div>
                                        <h3 class="text-sm font-bold text-emerald-800 dark:text-emerald-300">Success</h3>
                                        <div class="text-sm text-emerald-700 dark:text-emerald-400 mt-1">{{ session('success') }}</div>
                                    </div>
                                </div>
                                <button type="button" @click="open = false"
                                    class="text-emerald-500 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-200 transition p-1.5 rounded-lg">
                                    <x-heroicon-s-x-mark class="w-5 h-5" />
                                </button>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div x-data="{ open: true }" x-show="open" x-transition
                                class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 flex justify-between items-start shadow-sm"
                                role="alert">
                                <div class="flex items-center gap-3">
                                    <x-heroicon-s-exclamation-triangle class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0" />
                                    <div>
                                        <h3 class="text-sm font-bold text-red-800 dark:text-red-300">Error</h3>
                                        <div class="text-sm text-red-700 dark:text-red-400 mt-1">{{ session('error') }}</div>
                                    </div>
                                </div>
                                <button type="button" @click="open = false"
                                    class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-200 transition p-1.5 rounded-lg">
                                    <x-heroicon-s-x-mark class="w-5 h-5" />
                                </button>
                            </div>
                        @endif

                        {{-- Section: Test Details --}}
                        <div class="space-y-8">
                            <div>
                                <h3 class="text-base font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-6 border-b border-slate-100 dark:border-gray-800 pb-3 flex items-center gap-2">
                                    <x-heroicon-m-clipboard-document-list class="w-5 h-5" />
                                    Test Information
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                    {{-- Test Name --}}
                                    <div class="md:col-span-2 space-y-1.5">
                                        <label for="test_name" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Test Name <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <x-heroicon-m-beaker class="h-5 w-5 text-slate-400" />
                                            </div>
                                            <input type="text" id="test_name" wire:model.live.debounce.300ms="test_name"
                                                placeholder="e.g., Complete Blood Count (CBC)"
                                                class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2.5 pl-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all duration-200
                                                @error('test_name') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                                        </div>
                                        @error('test_name')
                                            <p class="text-red-600 text-xs font-medium mt-1.5 flex items-center gap-1">
                                                <x-heroicon-s-exclamation-circle class="w-4 h-4" /> {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    {{-- Price --}}
                                    <div class="space-y-1.5">
                                        <label for="price" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Price (USD) <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-slate-500 sm:text-sm font-bold">$</span>
                                            </div>
                                            <input type="number" id="price" wire:model="price"
                                                placeholder="0.00" step="0.01" min="0"
                                                class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2.5 pl-7 pr-4 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all duration-200
                                                @error('price') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                                        </div>
                                        @error('price')
                                            <p class="text-red-600 text-xs font-medium mt-1.5">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Units --}}
                                    <div class="space-y-1.5">
                                        <label for="units" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Units of Measurement <span class="text-red-500">*</span></label>
                                        <div class="relative">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <x-heroicon-m-scale class="h-5 w-5 text-slate-400" />
                                            </div>
                                            <input type="text" id="units" wire:model="units"
                                                placeholder="e.g., mg/dL, mmol/L"
                                                class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2.5 pl-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all duration-200
                                                @error('units') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                                        </div>
                                        @error('units')
                                            <p class="text-red-600 text-xs font-medium mt-1.5">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    {{-- Description --}}
                                    <div class="md:col-span-2 space-y-1.5">
                                        <label for="description" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Description <span class="text-slate-400 font-normal ml-1 text-xs">(Optional)</span></label>
                                        <div class="relative">
                                            <textarea id="description" wire:model="description"
                                                placeholder="Provide details about what this test analyzes..."
                                                rows="4"
                                                class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all duration-200 p-3"></textarea>
                                            <div class="absolute bottom-3 right-3 pointer-events-none">
                                                <x-heroicon-m-pencil-square class="h-5 w-5 text-gray-400" />
                                            </div>
                                        </div>
                                        @error('description')
                                            <p class="text-red-600 text-xs font-medium mt-1.5">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="flex items-center justify-end gap-4 mt-10 pt-6 border-t border-slate-200 dark:border-gray-700">
                            <a href="{{ route('lab-technician.manage-lab-definitions') }}" wire:navigate
                                class="rounded-xl bg-white dark:bg-gray-800 px-6 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-gray-600 hover:bg-slate-50 dark:hover:bg-gray-700 transition-all duration-200">
                                Cancel
                            </a>

                            <button type="submit"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-8 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-700 hover:shadow-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-not-allowed">

                                <span wire:loading.remove wire:target="saveTest" class="flex items-center gap-2">
                                    <x-heroicon-m-check class="w-5 h-5" />
                                    Create Test
                                </span>

                                <span wire:loading wire:target="saveTest" class="flex items-center gap-2">
                                    <x-heroicon-o-arrow-path class="animate-spin h-5 w-5 text-white" />
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
