<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4">
                <div class="flex flex-col gap-2">
                    {{-- Mobile Sidebar Toggle (Kept for completeness but usually handled by layout) --}}
                    <button @click="open = true"
                        class="lg:hidden p-2 rounded-xl text-slate-500 bg-slate-50 shadow-sm border border-slate-200 hover:bg-slate-100 mb-2 dark:bg-gray-800 dark:text-slate-400 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors"
                        aria-label="Open menu">
                        <x-heroicon-o-bars-3 class="w-6 h-6" />
                    </button>

                    {{-- Breadcrumbs --}}
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-1" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('nurse.dashboard') }}" wire:navigate
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    Home
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">Supply Usage</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            Supply Usage
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                            Record supply used by you during patient care or procedures.
                        </p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="p-4 sm:p-6 pb-20">

            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 p-6 sm:p-8">

                {{-- Success/Error Alerts --}}
                @if (session()->has('message'))
                    <div x-data="{ open: true }" x-show="open" x-transition
                        class="mb-6 p-4 text-sm text-emerald-800 rounded-xl bg-emerald-50 border border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800 flex justify-between items-start shadow-sm"
                        role="alert">
                        <div class="flex items-center gap-2">
                            <x-heroicon-s-check-circle class="w-5 h-5 flex-shrink-0" />
                            <span class="font-bold">{{ session('message') }}</span>
                        </div>
                        <button @click="open = false" class="text-emerald-800 hover:text-emerald-900 dark:text-emerald-400 dark:hover:text-emerald-300 p-1.5 rounded-lg">
                            <x-heroicon-s-x-mark class="w-5 h-5" />
                        </button>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div x-data="{ open: true }" x-show="open" x-transition
                        class="mb-6 p-4 text-sm text-red-800 rounded-xl bg-red-50 border border-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800 flex justify-between items-start shadow-sm"
                        role="alert">
                        <div class="flex items-center gap-2">
                            <x-heroicon-s-exclamation-triangle class="w-5 h-5 flex-shrink-0" />
                            <span class="font-bold">{{ session('error') }}</span>
                        </div>
                        <button @click="open = false" class="text-red-800 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 p-1.5 rounded-lg">
                            <x-heroicon-s-x-mark class="w-5 h-5" />
                        </button>
                    </div>
                @endif


                <div class="flex flex-col gap-6">
                    @forelse ($supplies as $supply)
                        @php
                            $isLowStock = $supply->current_stock <= $supply->min_stock_level;
                        @endphp
                        <div
                            class="flex flex-col md:flex-row items-start md:items-center gap-4 p-4 border border-slate-200 dark:border-gray-700 rounded-xl bg-slate-50 dark:bg-gray-800/50">

                            <div class="flex-1 w-full space-y-1">
                                <div class="flex items-center justify-between">
                                    <label class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ $supply->name }}
                                    </label>
                                    <span class="text-xs font-medium {{ $isLowStock ? 'text-red-600 dark:text-red-400' : 'text-slate-500 dark:text-slate-400' }}">
                                        Stock: {{ $supply->current_stock }} {{ $supply->unit_of_measure }}
                                    </span>
                                </div>

                                <div class="relative">
                                    <input type="number"
                                        class="block w-full rounded-xl border border-slate-300 dark:border-gray-700 dark:bg-gray-900 py-2.5 px-3 shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:text-white sm:text-sm"
                                        wire:model.live.defer="quantitiesUsed.{{ $supply->id }}"
                                        placeholder="Enter quantity used">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-slate-500 dark:text-slate-400 text-sm">{{ $supply->unit_of_measure }}</span>
                                    </div>
                                </div>
                            </div>

                            <button
                                class="w-full md:w-auto flex-shrink-0 inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold text-blue-600 border border-blue-600 rounded-xl hover:bg-blue-50 dark:text-blue-400 dark:border-blue-400 dark:hover:bg-gray-700 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 shadow-sm"
                                wire:click="recordSupplyUsage({{ $supply->id }})">
                                Record Usage
                            </button>
                        </div>
                        @error('quantitiesUsed.' . $supply->id)
                            <p class="text-red-500 text-xs font-medium mt-1 ml-4">{{ $message }}</p>
                        @enderror
                    @empty
                        <div class="flex flex-col items-center justify-center text-slate-500 dark:text-slate-400 py-12">
                            <x-heroicon-o-archive-box class="w-16 h-16 mb-2 text-slate-300 dark:text-gray-600" />
                            <p class="text-sm font-medium text-slate-700 dark:text-slate-300">No medical supplies found</p>
                            <p class="text-xs mt-1">Please ensure supplies are properly stocked in the inventory.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</main>
