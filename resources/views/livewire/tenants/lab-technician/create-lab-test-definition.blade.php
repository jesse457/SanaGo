<div class="flex-1 p-4 md:p-6 lg:ml-64 bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">

    {{-- Breadcrumbs --}}
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('lab-technician.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors duration-150">
                        <x-heroicon-s-home class="h-4 w-4 me-2.5" />
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-4 h-4 text-gray-400 mx-1" />
                        <a href="{{ route('lab-technician.manage-lab-definitions') }}" wire:navigate
                            class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors duration-150">
                            Lab Tests
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-4 h-4 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm text-gray-400 md:ms-2 dark:text-gray-200">Create Lab Test</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    {{-- Header --}}
    <header class="mb-8 pb-4 border-b border-gray-200 dark:border-gray-700">
        <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white flex items-center gap-3">
            <x-heroicon-s-beaker class="w-10 h-10 text-indigo-600" />
            Create New Lab Test
        </h1>
        <p class="text-gray-600 dark:text-gray-400 mt-2">Define a new lab test with all its necessary details.</p>
    </header>

    {{-- Main Form Card --}}
    <div id="addUserFormContainer" class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
        <form wire:submit.prevent="saveTest" class="p-8 sm:p-12">

            {{-- Success/Error Alerts --}}
            @if (session()->has('success'))
                <div x-data="{ open: true }" x-show="open"
                    class="mb-6 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-700 dark:text-green-400 flex justify-between items-center"
                    role="alert">
                    <div class="flex items-center gap-2">
                        <x-heroicon-s-check-circle class="w-5 h-5" />
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                    <button @click="open = false"
                        class="text-green-800 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                        <x-heroicon-s-x-mark class="w-5 h-5" />
                    </button>
                </div>
            @endif

            @if (session()->has('error'))
                <div x-data="{ open: true }" x-show="open"
                    class="mb-6 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-700 dark:text-red-400 flex justify-between items-center"
                    role="alert">
                    <div class="flex items-center gap-2">
                        <x-heroicon-s-exclamation-triangle class="w-5 h-5" />
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                    <button @click="open = false"
                        class="text-red-800 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                        <x-heroicon-s-x-mark class="w-5 h-5" />
                    </button>
                </div>
            @endif

            {{-- Section: Test Information --}}
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    {{-- Test Name --}}
                    <div>
                        <label for="test_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Test Name</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-s-document-text class="h-5 w-5 text-gray-400 dark:text-gray-500" />
                            </div>
                            <input type="text" id="test_name" wire:model.live.debounce.300ms="test_name"
                                placeholder="e.g., Blood Sugar Test"
                                class="block w-full rounded-lg border py-2.5 pl-10 pr-4 shadow-sm sm:text-sm transition-all duration-200
                                @error('test_name') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 dark:border-gray-600 focus:border-indigo-600 focus:ring-indigo-600 dark:bg-gray-700 dark:text-white @enderror">
                        </div>
                        @error('test_name')
                            <p class="text-red-600 text-xs mt-1.5 flex items-center gap-1.5">
                                <x-heroicon-s-exclamation-circle class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Price --}}
                    <div>
                        <label for="price" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Price</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-s-currency-dollar class="h-5 w-5 text-gray-400 dark:text-gray-500" />
                            </div>
                            <input type="number" id="price" wire:model="price"
                                placeholder="e.g., 50.00" step="0.01"
                                class="block w-full rounded-lg border py-2.5 pl-10 pr-4 shadow-sm sm:text-sm transition-all duration-200
                                @error('price') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 dark:border-gray-600 focus:border-indigo-600 focus:ring-indigo-600 dark:bg-gray-700 dark:text-white @enderror">
                        </div>
                        @error('price')
                            <p class="text-red-600 text-xs mt-1.5 flex items-center gap-1.5">
                                <x-heroicon-s-exclamation-circle class="w-4 h-4" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div>
                    {{-- Units --}}
                    <label for="units" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Units</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <x-heroicon-s-scale class="h-5 w-5 text-gray-400 dark:text-gray-500" />
                        </div>
                        <input type="text" id="units" wire:model="units"
                            placeholder="e.g., mg/dL, units"
                            class="block w-full rounded-lg border py-2.5 pl-10 pr-4 shadow-sm sm:text-sm transition-all duration-200
                            @error('units') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 dark:border-gray-600 focus:border-indigo-600 focus:ring-indigo-600 dark:bg-gray-700 dark:text-white @enderror">
                    </div>
                    @error('units')
                        <p class="text-red-600 text-xs mt-1.5 flex items-center gap-1.5">
                            <x-heroicon-s-exclamation-circle class="w-4 h-4" />
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-8 mt-8">
                {{-- Description --}}
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 top-3 flex items-start pl-3">
                            <x-heroicon-s-document-text class="h-5 w-5 text-gray-400 dark:text-gray-500" />
                        </div>
                        <textarea id="description" wire:model="description"
                            placeholder="Provide a detailed description of the lab test."
                            rows="4"
                            class="block w-full rounded-lg border py-2.5 pl-10 pr-4 shadow-sm sm:text-sm transition-all duration-200
                            @error('description') border-red-500 focus:border-red-500 focus:ring-red-500 @else border-gray-300 dark:border-gray-600 focus:border-indigo-600 focus:ring-indigo-600 dark:bg-gray-700 dark:text-white @enderror"></textarea>
                    </div>
                    @error('description')
                        <p class="text-red-600 text-xs mt-1.5 flex items-center gap-1.5">
                            <x-heroicon-s-exclamation-circle class="w-4 h-4" />
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            {{-- Form Actions / Footer --}}
            <div class="flex items-center justify-end gap-4 mt-10 pt-6 border-t border-gray-200 dark:border-gray-700">
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2.5 rounded-lg bg-indigo-600 px-8 py-3 text-sm font-semibold text-white shadow-lg hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-200 transform hover:-translate-y-0.5"
                    wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-not-allowed">
                    <span wire:loading wire:target="saveTest" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Saving...</span>
                    </span>
                    <span wire:loading.remove wire:target="saveTest" class="flex items-center gap-2">
                        <x-heroicon-s-check-circle class="w-5 h-5" />
                        Save Lab Test
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
