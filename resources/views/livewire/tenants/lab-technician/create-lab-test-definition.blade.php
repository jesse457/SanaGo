<div class="flex-1 p-4 sm:p-6 bg-white dark:bg-gray-900 overflow-y-auto min-h-screen">

    {{-- Hero/Header Section --}}
    <div class="mb-8 relative">
        {{-- Breadcrumbs --}}
        <nav class="flex items-center text-sm font-medium text-slate-500 dark:text-slate-400 mb-8 mt-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('lab-technician.dashboard') }}" wire:navigate
                        class="inline-flex items-center hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                        <x-heroicon-s-home class="w-4 h-4 mr-1.5" />
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-4 h-4 text-slate-300 dark:text-slate-600 mx-1" />
                        <a href="{{ route('lab-technician.manage-lab-definitions') }}" wire:navigate
                            class="ms-1 hover:text-indigo-600 md:ms-2 dark:hover:text-indigo-400 transition-colors">
                            Lab Tests
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-4 h-4 text-slate-300 dark:text-slate-600 mx-1" />
                        <span class="ms-1 font-semibold text-slate-900 md:ms-2 dark:text-slate-200">
                            Create Lab Test
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Title & Description --}}
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3 tracking-tight">

                Create New Lab Test
            </h1>
            <p class="text-slate-600 dark:text-slate-400 mt-2 text-sm max-w-2xl">
                Define the parameters, pricing, and units for a new diagnostic test.
            </p>
        </div>
    </div>

    {{-- Main Form Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <form wire:submit.prevent="saveTest" class="p-6 sm:p-8">

            {{-- Success/Error Alerts --}}
            @if (session()->has('success'))
                <div x-data="{ open: true }" x-show="open" x-transition
                    class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 flex justify-between items-start"
                    role="alert">
                    <div class="flex items-center gap-3">
                        <x-heroicon-s-check-circle class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0" />
                        <div>
                            <h3 class="text-sm font-medium text-green-800 dark:text-green-300">Success</h3>
                            <div class="text-sm text-green-700 dark:text-green-400 mt-1">{{ session('success') }}</div>
                        </div>
                    </div>
                    <button type="button" @click="open = false"
                        class="text-green-500 hover:text-green-700 dark:text-green-400 dark:hover:text-green-200 transition">
                        <x-heroicon-s-x-mark class="w-5 h-5" />
                    </button>
                </div>
            @endif

            @if (session()->has('error'))
                <div x-data="{ open: true }" x-show="open" x-transition
                    class="mb-6 p-4 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 flex justify-between items-start"
                    role="alert">
                    <div class="flex items-center gap-3">
                        <x-heroicon-s-exclamation-triangle class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0" />
                        <div>
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-300">Error</h3>
                            <div class="text-sm text-red-700 dark:text-red-400 mt-1">{{ session('error') }}</div>
                        </div>
                    </div>
                    <button type="button" @click="open = false"
                        class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-200 transition">
                        <x-heroicon-s-x-mark class="w-5 h-5" />
                    </button>
                </div>
            @endif

            {{-- Section: Test Details --}}
            <div class="space-y-8">
                <div>
                    <h3 class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <x-heroicon-m-clipboard-document-list class="w-4 h-4" />
                        Test Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-6">
                        {{-- Test Name (Full Width on mobile, span 2 on large if needed, but here parallel works) --}}
                        <div class="md:col-span-2">
                            <label for="test_name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Test Name</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <x-heroicon-m-beaker class="h-5 w-5 text-slate-400" />
                                </div>
                                <input type="text" id="test_name" wire:model.live.debounce.300ms="test_name"
                                    placeholder="e.g., Complete Blood Count (CBC)"
                                    class="block w-full rounded-lg border-slate-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white py-2.5 pl-10 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200
                                    @error('test_name') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
                            @error('test_name')
                                <p class="text-red-600 text-xs mt-1.5 flex items-center gap-1">
                                    <x-heroicon-s-exclamation-circle class="w-4 h-4" /> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Price --}}
                        <div>
                            <label for="price" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Price (USD)</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" id="price" wire:model="price"
                                    placeholder="0.00" step="0.01" min="0"
                                    class="block w-full rounded-lg border-slate-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white py-2.5 pl-7 pr-4 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200
                                    @error('price') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
                            @error('price')
                                <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Units --}}
                        <div>
                            <label for="units" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Units of Measurement</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <x-heroicon-m-scale class="h-5 w-5 text-slate-400" />
                                </div>
                                <input type="text" id="units" wire:model="units"
                                    placeholder="e.g., mg/dL, mmol/L"
                                    class="block w-full rounded-lg border-slate-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white py-2.5 pl-10 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200
                                    @error('units') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror">
                            </div>
                            @error('units')
                                <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Description (Optional)</label>
                            <div class="relative">
                                <textarea id="description" wire:model="description"
                                    placeholder="Provide details about what this test analyzes..."
                                    rows="4"
                                    class="block w-full rounded-lg border-slate-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200
                                    @error('description') border-red-500 focus:border-red-500 focus:ring-red-500 @enderror"></textarea>
                                <div class="absolute bottom-3 right-3 pointer-events-none">
                                    <x-heroicon-m-pencil-square class="h-5 w-5 text-gray-400" />
                                </div>
                            </div>
                            @error('description')
                                <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end gap-4 mt-10 pt-6 border-t border-slate-200 dark:border-gray-700">
                <a href="{{ route('lab-technician.manage-lab-definitions') }}" wire:navigate
                    class="rounded-lg bg-white dark:bg-gray-800 px-6 py-2.5 text-sm font-semibold text-slate-900 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-gray-600 hover:bg-slate-50 dark:hover:bg-gray-700 transition-all duration-200">
                    Cancel
                </a>

                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-8 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-200 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed"
                    wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-not-allowed">

                    <span wire:loading.remove wire:target="saveTest" class="flex items-center gap-2">
                        <x-heroicon-m-check class="w-5 h-5" />
                        Create Test
                    </span>

                    <span wire:loading wire:target="saveTest" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
