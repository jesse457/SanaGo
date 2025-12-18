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
                                    <span class="text-gray-900 dark:text-white">Lab Requests</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            Manage Lab Requests
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                            View incoming test requests, track status, and process results for patients efficiently.
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
                            placeholder="Search by Patient UID, Name, or Test Name..."
                            class="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out">
                    </div>

                    {{-- Filter Dropdown --}}
                    <div class="relative w-full md:w-auto md:min-w-[220px]">
                        <select wire:model.live="statusFilter"
                            class="block w-full border border-gray-300 dark:border-gray-600 rounded-lg py-2 pl-3 pr-8 leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out">
                            <option value="">All Statuses</option>
                            <option value="Pending">Pending</option>
                            <option value="In_Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                    </div>
                </div>

                {{-- Clear Filters --}}
                @if ($search || $statusFilter)
                    <div class="flex items-center justify-end mt-3">
                        <button wire:click="$set('search', ''); $set('statusFilter', '')"
                            class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium hover:underline transition-colors flex items-center gap-1">
                            <x-heroicon-m-trash class="w-3 h-3" /> Clear Filters
                        </button>
                    </div>
                @endif
            </div>
        </header>

        {{-- Content Area --}}
        <div class="relative min-h-[400px] p-4 sm:p-6 pb-20">

            <section id="lab-requests-section">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">

                    {{-- Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 dark:divide-gray-800">
                            <thead class="bg-slate-50 dark:bg-gray-950">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Patient</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Test Details</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Requested</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                                @forelse ($requests as $request)
                                    @php
                                        $statusClasses = match($request->status) {
                                            'Pending' => 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800',
                                            'In_Progress' => 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800',
                                            'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800',
                                            default => 'bg-slate-50 text-slate-700 border-slate-100 dark:bg-gray-800 dark:text-slate-300 dark:border-gray-700',
                                        };
                                        $displayStatus = str_replace('_', ' ', $request->status);
                                    @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 group">
                                        {{-- Patient Column --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-300 text-sm font-bold ring-2 ring-white dark:ring-gray-800 shadow-sm">
                                                    {{ substr($request->patient?->first_name ?? '?', 0, 1) }}{{ substr($request->patient?->last_name ?? '?', 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-bold text-slate-900 dark:text-white">
                                                        {{ $request->patient?->first_name }} {{ $request->patient?->last_name }}
                                                    </div>
                                                    <div class="text-xs text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                                                        {{ $request->patient?->patient_uid }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Test Details --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center rounded-md bg-slate-100 dark:bg-gray-800/50 px-2.5 py-1 text-sm font-medium text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-gray-700">
                                                {{ $request->testDefinition?->test_name }}
                                            </span>
                                        </td>

                                        {{-- Date --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-col text-sm text-slate-500 dark:text-slate-400">
                                                <span class="font-bold text-slate-900 dark:text-white">{{ $request->request_date?->format('M d, Y') ?? '-' }}</span>
                                                <span class="text-xs">{{ $request->request_date?->format('h:i A') ?? '' }}</span>
                                            </div>
                                        </td>

                                        {{-- Status --}}
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold border shadow-sm {{ $statusClasses }}">
                                                {{ $displayStatus }}
                                            </span>
                                        </td>

                                        {{-- Actions --}}
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end gap-3">
                                                {{-- Start Test Action --}}
                                                @if($request->status === 'Pending')
                                                    <button type="button"
                                                        wire:click="updateStatus({{ $request->id }})"
                                                        title="Mark In Progress"
                                                        class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                                                        <x-heroicon-s-play-circle class="w-4 h-4" />
                                                    </button>
                                                @endif

                                                {{-- Edit/Enter Results Action --}}
                                                <a href="{{ route('lab-technician.enter-results', $request->id) }}" wire:navigate
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-200 shadow-sm
                                                    {{ $request->result
                                                        ? 'bg-white text-slate-700 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 dark:bg-gray-800 dark:text-slate-200 dark:ring-gray-600 dark:hover:bg-gray-700'
                                                        : 'bg-blue-600 text-white hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600' }}">
                                                    @if($request->result)
                                                        <x-heroicon-m-pencil-square class="h-3.5 w-3.5" />
                                                        Edit
                                                    @else
                                                        <x-heroicon-m-beaker class="h-3.5 w-3.5" />
                                                        Process
                                                    @endif
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-16 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="bg-slate-50 dark:bg-gray-800 rounded-full p-4 mb-3 border border-slate-100 dark:border-gray-700">
                                                    <x-heroicon-o-beaker class="w-10 h-10 text-slate-400 dark:text-gray-500" />
                                                </div>
                                                <h3 class="text-base font-bold text-slate-900 dark:text-white">No Lab Requests Found</h3>
                                                <p class="text-sm text-slate-500 dark:text-gray-400 mt-1 max-w-sm">
                                                    There are no requests matching your search or filters at the moment.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($requests->hasPages())
                        <div class="px-6 py-4 border-t border-slate-100 dark:border-gray-800 bg-slate-50 dark:bg-gray-900/50">
                            {{ $requests->links() }}
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</main>
