<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4 md:flex md:items-center md:justify-between space-y-3 md:space-y-0">

                {{-- Title & Breadcrumbs --}}
                <div class="flex-1 min-w-0">
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
                                    <span class="text-gray-900 dark:text-white">Lab Tests</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            Manage Lab Tests
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                            View, search, and manage all defined lab test definitions including pricing and units.
                        </p>
                    </div>
                </div>

                {{-- Action Toolbar --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('lab-technician.create-lab-definitions') }}" wire:navigate
                        class="group relative inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl shadow-md hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all overflow-hidden dark:focus:ring-offset-gray-900">
                        <div
                            class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                        </div>
                        <x-heroicon-m-plus class="w-5 h-5" />
                        <span>Add New Test</span>
                    </a>
                </div>
            </div>

            {{-- Filters Bar --}}
            <div class="px-4 sm:px-6 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row gap-3 items-center justify-between">
                    {{-- Search Input --}}
                    <div class="relative w-full md:max-w-lg group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-m-magnifying-glass class="h-4 w-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                        </div>
                        <input type="search" wire:model.live.debounce.400ms="search"
                            placeholder="Search by test name or code..."
                            class="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out">
                    </div>

                    {{-- Clear Filter --}}
                    @if ($search)
                        <div class="flex items-center justify-end w-full md:w-auto">
                            <button type="button" wire:click="$set('search','')"
                                class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium hover:underline transition-colors flex items-center gap-1">
                                <x-heroicon-m-trash class="w-3 h-3" /> Clear Filter
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="relative min-h-[400px] p-4 sm:p-6 pb-20">

            {{-- Table Card --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-gray-800">
                        <thead class="bg-slate-50 dark:bg-gray-950">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Test Name</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Price</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Units</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                            @forelse ($labTests as $test)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 group">
                                    {{-- Test Name --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $test->test_name }}</span>
                                            @if($test->code ?? false)
                                                <small class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Code: <span class="font-mono text-slate-700 dark:text-slate-300">{{ $test->code }}</span></small>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Price --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-center">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300 border border-emerald-100 dark:border-emerald-800">
                                            ${{ number_format($test->price, 2) }}
                                        </span>
                                    </td>

                                    {{-- Units --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-600 dark:text-slate-300">
                                        {{ $test->units }}
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button wire:click="viewEditTest({{ $test->id }})" title="Edit test"
                                                    class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                                                <x-heroicon-m-pencil-square class="w-4 h-4" />
                                            </button>

                                            <button wire:click="viewDeleteTest({{ $test->id }})" title="Delete test"
                                                  class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                                <x-heroicon-m-trash class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-slate-50 dark:bg-gray-800 rounded-full p-4 mb-3 border border-slate-100 dark:border-gray-700">
                                                <x-heroicon-o-beaker class="w-10 h-10 text-slate-400 dark:text-gray-500" />
                                            </div>
                                            <p class="text-base font-bold text-slate-900 dark:text-white mb-1">No Lab Tests Found</p>
                                            @if ($search)
                                                <p class="text-sm text-slate-500 dark:text-slate-400">No results for "<span class="font-bold">{{ $search }}</span>". Try adjusting your search.</p>
                                            @else
                                                <p class="text-sm text-slate-500 dark:text-slate-400">Get started by creating a new lab test definition.</p>
                                                <a href="{{ route('lab-technician.create-lab-definitions') }}" wire:navigate
                                                   class="mt-4 inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                                                  <x-heroicon-m-plus class="w-4 h-4" /> Create Test
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Placeholder --}}
                @if (method_exists($labTests, 'links') && $labTests->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 dark:border-gray-800 bg-slate-50 dark:bg-gray-900/50">
                        {{ $labTests->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Edit Modal (Test) --}}
        <div x-data="{ show: @entangle('showTestEditModal') }"
            x-init="$watch('show', value => { if (value) { document.body.style.overflow = 'hidden'; } else { document.body.style.overflow = ''; } })"
            x-show="show" x-cloak class="relative z-50">
            <template x-teleport="body">
                <div x-show="show" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center">
                        {{-- Backdrop --}}
                        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                             class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" wire:click="$set('showTestEditModal', false)"></div>

                        <form wire:submit.prevent="updateTest"
                            x-show="show"
                            x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100 dark:border-gray-800">

                            {{-- Modal Header --}}
                            <div class="px-6 py-5 border-b border-slate-100 dark:border-gray-800 flex items-center justify-between bg-white dark:bg-gray-900">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">Edit Lab Test</h3>
                                <button type="button" wire:click="$set('showTestEditModal', false)"
                                        class="rounded-xl bg-slate-50 dark:bg-gray-800 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors">
                                    <x-heroicon-o-x-mark class="w-5 h-5" />
                                </button>
                            </div>

                            <div class="p-6 space-y-5">
                                {{-- Test Name --}}
                                <div class="space-y-1.5">
                                    <label for="editing-test-name" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Test Name</label>
                                    <input id="editing-test-name" type="text" wire:model.defer="test_name"
                                        class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5" />
                                    @error('test_name') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                </div>

                                {{-- Units --}}
                                <div class="space-y-1.5">
                                    <label for="editing-units-name" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Units</label>
                                    <input id="editing-units-name" type="text" wire:model.defer="units"
                                        class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5" />
                                    @error('units') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                </div>

                                {{-- Price --}}
                                <div class="space-y-1.5">
                                    <label for="editing-price" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Price (USD)</label>
                                    <input id="editing-price" type="number" step="0.01" min="0" wire:model.defer="price"
                                        class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5" />
                                    @error('price') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                </div>

                                {{-- Description --}}
                                <div class="space-y-1.5">
                                    <label for="editing-description" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Description</label>
                                    <textarea id="editing-description" rows="2" wire:model.defer="description"
                                        class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5"></textarea>
                                    @error('description') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="px-6 py-4 bg-slate-50 dark:bg-gray-900/50 flex items-center justify-end gap-3 rounded-b-2xl border-t border-slate-100 dark:border-gray-800">
                                <button type="button" wire:click="$set('showTestEditModal', false)"
                                        class="rounded-xl bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-gray-700 hover:bg-slate-50 dark:hover:bg-gray-700 transition-all">
                                    Cancel
                                </button>

                                <button type="submit"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all"
                                        wire:loading.attr="disabled"
                                        wire:target="updateTest">
                                    <span wire:loading.remove wire:target="updateTest">Save Changes</span>
                                    <span wire:loading wire:target="updateTest" class="flex items-center gap-2">
                                        <x-heroicon-o-arrow-path class="animate-spin h-4 w-4" /> Saving...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>
        </div>

        {{-- Delete Modal (Test) --}}
        <div x-data="{ show: @entangle('showTestDeleteModal') }"
            x-init="$watch('show', value => { if (value) { document.body.style.overflow = 'hidden'; } else { document.body.style.overflow = ''; } })"
            x-show="show" x-cloak class="relative z-50">
            <template x-teleport="body">
                <div x-show="show" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center">
                        {{-- Backdrop --}}
                        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                             class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" wire:click="$set('showTestDeleteModal', false)"></div>

                        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-sm border border-slate-100 dark:border-gray-800">

                            <div class="p-6">
                                <div class="sm:flex sm:items-start">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/20 sm:mx-0 sm:h-10 sm:w-10">
                                        <x-heroicon-o-exclamation-triangle class="h-6 w-6 text-red-600 dark:text-red-400" />
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">Delete Test</h3>
                                        <div class="mt-2">
                                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                                Are you sure you want to delete this lab test definition? This action cannot be undone.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="px-6 py-4 bg-slate-50 dark:bg-gray-900/50 flex justify-end gap-3 rounded-b-2xl border-t border-slate-100 dark:border-gray-800">
                                <button type="button" wire:click="$set('showTestDeleteModal', false)"
                                        class="rounded-xl bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-gray-700 hover:bg-slate-50 dark:hover:bg-gray-700 transition-all">
                                    Cancel
                                </button>

                                <button type="button" wire:click="deleteTest"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-red-700 transition-all"
                                        wire:loading.attr="disabled"
                                        wire:target="deleteTest">
                                    <span wire:loading.remove wire:target="deleteTest">Delete</span>
                                    <span wire:loading wire:target="deleteTest">Deleting...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</main>
