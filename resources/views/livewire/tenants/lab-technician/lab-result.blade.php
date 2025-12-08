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
                                    <span class="text-gray-900 dark:text-white">Lab Results</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            View Lab Results
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                            Browse completed test records, download reports, and review patient diagnostic history.
                        </p>
                    </div>
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
                        <input type="text" wire:model.live.debounce.400ms="search"
                            placeholder="Search by Patient Name, UID, or Test Type..."
                            class="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out">
                    </div>

                    {{-- Date Filter --}}
                    <div class="relative w-full md:w-auto md:min-w-[220px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-o-calendar-days class="w-4 h-4 text-gray-400" />
                        </div>
                        <input type="date" wire:model.live="dateFilter"
                            class="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out dark:[color-scheme:dark]">
                    </div>
                </div>

                {{-- Clear Filters --}}
                @if ($search || $dateFilter)
                    <div class="flex items-center justify-end mt-3 md:mt-0">
                        <button wire:click="$set('search', ''); $set('dateFilter', '')"
                            class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium hover:underline transition-colors flex items-center gap-1">
                            <x-heroicon-m-trash class="w-3 h-3" /> Clear Filters
                        </button>
                    </div>
                @endif
            </div>
        </header>

        {{-- Content Area --}}
        <div class="relative min-h-[400px] p-4 sm:p-6 pb-20">

            {{-- Table Card --}}
            <div
                class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-gray-800">
                        <thead class="bg-slate-50 dark:bg-gray-950">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Patient</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Test Details</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Completed Date</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                            @forelse ($results as $result)
                                @php
                                    $patientName = $result->labRequest?->patient?->first_name . ' ' . $result->labRequest?->patient?->last_name;
                                    $statusClasses = match($result->status) {
                                        'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800',
                                        'Cancelled' => 'bg-rose-50 text-rose-700 border-rose-100 dark:bg-rose-900/30 dark:text-rose-300 dark:border-rose-800',
                                        'In Progress' => 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800',
                                        default => 'bg-slate-50 text-slate-700 border-slate-100 dark:bg-gray-800 dark:text-slate-300 dark:border-gray-700',
                                    };
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 group">
                                    {{-- Patient Column --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-700 dark:text-blue-300 text-sm font-bold ring-2 ring-white dark:ring-gray-800 shadow-sm">
                                                {{ substr($result->labRequest?->patient?->first_name ?? '?', 0, 1) }}{{ substr($result->labRequest?->patient?->last_name ?? '?', 0, 1) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-slate-900 dark:text-white">
                                                    {{ $patientName }}
                                                </div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                                                    {{ $result->labRequest?->patient?->patient_uid }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Test Name --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center rounded-md bg-slate-100 dark:bg-gray-800/50 px-2.5 py-1 text-sm font-medium text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-gray-700">
                                            {{ $result->labRequest?->testDefinition?->test_name }}
                                        </span>
                                    </td>

                                    {{-- Result Date --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col text-sm text-slate-500 dark:text-slate-400">
                                            <span class="font-bold text-slate-900 dark:text-white">{{ $result->result_date->format('M d, Y') }}</span>
                                            <span class="text-xs">{{ $result->result_date->format('h:i A') }}</span>
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold border shadow-sm {{ $statusClasses }}">
                                            @if($result->status == 'Completed')
                                                <x-heroicon-s-check-circle class="w-3.5 h-3.5 mr-1" />
                                            @endif
                                            {{ $result->status }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('lab-technician.enter-results', $result->lab_request_id) }}"
                                           wire:navigate
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/40 transition-all duration-200 shadow-sm border border-blue-100 dark:border-blue-800">
                                            <x-heroicon-m-eye class="h-4 w-4" />
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-slate-50 dark:bg-gray-800 rounded-full p-4 mb-3 border border-slate-100 dark:border-gray-700">
                                                <x-heroicon-o-clipboard-document-list class="w-10 h-10 text-slate-400 dark:text-gray-500" />
                                            </div>
                                            <h3 class="text-base font-bold text-slate-900 dark:text-white">No Results Found</h3>
                                            <p class="text-sm text-slate-500 dark:text-gray-400 mt-1 max-w-sm">
                                                You haven't submitted any results yet, or no records match your search criteria.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($results->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100 dark:border-gray-800 bg-slate-50 dark:bg-gray-900/50">
                        {{ $results->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</main>
