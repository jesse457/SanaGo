<main class="flex-1 p-4 sm:p-6 bg-white dark:bg-gray-900 overflow-y-auto min-h-screen">
    {{-- Hero/Header Section --}}
    <div class="mb-8 relative ">
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
                        <span class="ms-1 font-semibold text-slate-900 md:ms-2 dark:text-slate-200">
                            Lab Tests
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Title & Action --}}
        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3 tracking-tight">

                    Manage Lab Tests
                </h1>
              <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-2xl">
                 View, search, and manage all defined lab test definitions including pricing and units.
                </p>
            </div>

            <a href="{{ route('lab-technician.create-lab-definitions') }}" wire:navigate
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 dark:focus:ring-offset-gray-900">
                <x-heroicon-m-plus class="w-5 h-5" aria-hidden="true" />
                <span class="hidden sm:inline">Add New Test</span>
            </a>
        </header>
    </div>

    {{-- Main Content Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

        <!-- Filters and Search -->
        <div class="p-4 md:p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="relative w-full md:max-w-lg">
                    <label for="search" class="sr-only">Search lab tests</label>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400" />
                    </div>
                    <input id="search" type="search" wire:model.live.debounce.400ms="search"
                           placeholder="Search by test name or code..."
                           class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-10 py-2.5 transition-all duration-200 dark:text-white"
                           aria-label="Search lab tests">
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" wire:click="$set('search','')"
                            class="inline-flex items-center gap-2 px-3 py-2 bg-white dark:bg-gray-700 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500">

                        <span class="hidden sm:inline">Clear Filter</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Test Name
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Price
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Units
                        </th>
                        <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    @forelse ($labTests as $test)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                            {{-- Test Name --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $test->test_name }}</span>
                                    @if($test->code ?? false)
                                        <small class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Code: <span class="font-mono text-gray-600 dark:text-gray-300">{{ $test->code }}</span></small>
                                    @endif
                                </div>
                            </td>

                            {{-- Price --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white text-center">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-50 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                    ${{ number_format($test->price, 2) }}
                                </span>
                            </td>

                            {{-- Units --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $test->units }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                <div class="flex items-center justify-end gap-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                    <button wire:click="viewEditTest({{ $test->id }})" title="Edit test"
                                            class="p-2 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 dark:hover:text-indigo-400 rounded-lg transition-colors">
                                        <x-heroicon-m-pencil-square class="w-5 h-5" />
                                    </button>

                                    <button wire:click="viewDeleteTest({{ $test->id }})" title="Delete test"
                                          class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 dark:hover:text-red-400 rounded-lg transition-colors">
                                        <x-heroicon-m-trash class="w-5 h-5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 dark:bg-gray-700/50 p-4 rounded-full mb-3">
                                        <x-heroicon-o-beaker class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                                    </div>
                                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-1">No Lab Tests Found</p>
                                    @if ($search)
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No results for "<span class="font-medium text-gray-700 dark:text-gray-200">{{ $search }}</span>". Try adjusting your search.</p>
                                    @else
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Get started by creating a new lab test definition.</p>
                                        <a href="{{ route('lab-technician.create-lab-definitions') }}" wire:navigate
                                           class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                          <x-heroicon-m-plus class="w-4 h-4 mr-2" /> Create Test
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- You would typically include pagination here if $labTests is a paginator --}}
    </div>

    {{-- Edit Modal (Test) --}}
    <div x-data="{ show: @entangle('showTestEditModal') }"
        x-show="show" x-cloak
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
    >
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showTestEditModal', false)"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <form wire:submit.prevent="updateTest"
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 dark:border-gray-700"
            >
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Edit Lab Test</h3>
                    <button type="button" wire:click="$set('showTestEditModal', false)"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 bg-transparent hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full p-1 transition">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-6 space-y-5">
                    {{-- Test Name --}}
                    <div>
                        <label for="editing-test-name" class="form-label">Test Name</label>
                        <input id="editing-test-name" type="text" wire:model.defer="test_name" class="form-input" />
                        @error('test_name') <span class="form-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Units --}}
                    <div>
                        <label for="editing-units-name" class="form-label">Units</label>
                        <input id="editing-units-name" type="text" wire:model.defer="units" class="form-input" />
                        @error('units') <span class="form-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Price --}}
                    <div>
                        <label for="editing-price" class="form-label">Price (USD)</label>
                        <input id="editing-price" type="number" step="0.01" min="0" wire:model.defer="price" class="form-input" />
                        @error('price') <span class="form-error">{{ $message }}</span> @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label for="editing-description" class="form-label">Description</label>
                        <textarea id="editing-description" rows="2" wire:model.defer="description" class="form-input"></textarea>
                        @error('description') <span class="form-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 flex items-center justify-end gap-3 rounded-b-2xl">
                    <button type="button" wire:click="$set('showTestEditModal', false)"
                            class="btn-secondary">Cancel</button>

                    <button type="submit"
                            class="btn-primary"
                            wire:loading.attr="disabled"
                            wire:target="updateTest">
                        <span wire:loading.remove wire:target="updateTest">Save Changes</span>
                        <span wire:loading wire:target="updateTest">Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Modal (Test) --}}
    <div x-data="{ show: @entangle('showTestDeleteModal') }"
        x-show="show" x-cloak
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
    >
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="$set('showTestDeleteModal', false)"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div
                x-show="show"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-sm border border-gray-100 dark:border-gray-700"
            >
                <div class="p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20 sm:mx-0 sm:h-10 sm:w-10">
                            <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-red-600 dark:text-red-400" />
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delete Test</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Are you sure you want to delete this lab test definition? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 flex justify-end gap-3 rounded-b-2xl">
                    <button type="button" wire:click="$set('showTestDeleteModal', false)"
                            class="btn-secondary">Cancel</button>

                    <button type="button" wire:click="deleteTest"
                            class="btn-danger"
                            wire:loading.attr="disabled"
                            wire:target="deleteTest">
                        <span wire:loading.remove wire:target="deleteTest">Delete</span>
                        <span wire:loading wire:target="deleteTest">Deleting...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>
