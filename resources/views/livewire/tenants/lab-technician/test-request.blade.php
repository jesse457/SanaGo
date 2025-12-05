<main class="flex-1 p-4 sm:p-6 bg-white dark:bg-gray-900 overflow-y-auto min-h-screen">
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
                        <span class="ms-1 font-meduim text-slate-900 md:ms-2 dark:text-slate-200">
                            Lab Requests
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Title & Description --}}
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3 tracking-tight">

                Manage Lab Requests
            </h1>
             <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-2xl">
                 View incoming test requests, track status, and process results for patients efficiently.
            </p>
        </div>
    </div>

    <section id="lab-requests-section">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

            <!-- Filters and Search -->
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    {{-- Search Input --}}
                    <div class="relative w-full md:flex-grow">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400" />
                        </div>
                        <input type="text" wire:model.live.debounce.400ms="search"
                            placeholder="Search by Patient UID, Name, or Test Name..."
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-10 py-2.5 transition-all duration-200 dark:text-white">
                    </div>

                    {{-- Filter Dropdown --}}
                    <div class="relative w-full md:w-auto md:min-w-[220px]">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-o-funnel class="w-5 h-5 text-gray-400" />
                        </div>
                        <select wire:model.live="statusFilter"
                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-10 pr-10 py-2.5 appearance-none transition-all duration-200 dark:text-white cursor-pointer">
                            <option value="">All Statuses</option>
                            <option value="Pending">Pending</option>
                            <option value="In_Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <x-heroicon-s-chevron-down class="w-4 h-4 text-gray-400" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Patient</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Test Details</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Requested</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse ($requests as $request)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 group">
                                {{-- Patient Column --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-300 text-sm font-bold">
                                                {{ substr($request->patient?->first_name ?? '?', 0, 1) }}{{ substr($request->patient?->last_name ?? '?', 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $request->patient?->first_name }} {{ $request->patient?->last_name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 font-mono mt-0.5">
                                                {{ $request->patient?->patient_uid }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Test Details --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center rounded-md bg-slate-50 dark:bg-gray-700 px-2 py-1 text-sm font-medium text-slate-700 dark:text-slate-300 ring-1 ring-inset ring-slate-600/20">
                                        {{ $request->testDefinition?->test_name }}
                                    </span>
                                </td>

                                {{-- Date --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col text-sm text-gray-500 dark:text-gray-400">
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $request->request_date?->format('M d, Y') ?? '-' }}</span>
                                        <span class="text-xs">{{ $request->request_date?->format('h:i A') ?? '' }}</span>
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClasses = match($request->status) {
                                            'Pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 ring-amber-600/20',
                                            'In_Progress' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 ring-blue-600/20',
                                            'Completed' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 ring-green-600/20',
                                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 ring-gray-600/20',
                                        };
                                        $displayStatus = str_replace('_', ' ', $request->status);
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusClasses }}">
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
                                                class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                                <x-heroicon-o-play-circle class="w-5 h-5" />
                                            </button>
                                        @endif

                                        {{-- Edit/Enter Results Action --}}
                                        <a href="{{ route('lab-technician.enter-results', $request->id) }}" wire:navigate
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition-all duration-200 shadow-sm
                                            {{ $request->result
                                                ? 'bg-white text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-200 dark:ring-gray-600 dark:hover:bg-gray-700'
                                                : 'bg-indigo-600 text-white hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600' }}">
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
                                        <div class="bg-gray-100 dark:bg-gray-700/50 p-4 rounded-full mb-3">
                                            <x-heroicon-o-beaker class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                                        </div>
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">No Lab Requests Found</h3>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm">
                                            There are no requests matching your search or filters at the moment.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($requests->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </section>
</main>
