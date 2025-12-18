<main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50 dark:bg-gray-900">

    {{-- 1. HEADER SECTION (Sticky) --}}
    <header class="flex-shrink-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm z-20">
        <div class="px-6 py-5 md:flex md:items-center md:justify-between space-y-3 md:space-y-0">

            {{-- Title & Breadcrumbs --}}
            <div class="flex-1 min-w-0">
                <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 mt-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="{{ route('doctor.dashboard') }}" wire:navigate class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                {{ __('doctor.home') }}
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
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                    {{ __('doctor.lab_requests') }}
                </h2>
                         <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('doctor.lab_requests_subtitle') }}
                        </p>
            </div>

            {{-- Right Actions / Stats --}}
            <div class="flex items-center gap-4">
                <div class="hidden md:flex items-center gap-2 px-3 py-1 bg-gray-100 dark:bg-gray-700 rounded-full border border-gray-200 dark:border-gray-600">
                    <span class="text-xs font-semibold text-gray-500 dark:text-gray-400">Total Requests</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $this->requests->total() }}</span>
                </div>
            </div>
        </div>

        {{-- Filters Bar --}}
        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex flex-col md:flex-row gap-4 items-center justify-between">
            <div class="flex items-center gap-3 w-full md:w-auto flex-1">
                {{-- Search --}}
                <div class="relative w-full md:max-w-xs group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-heroicon-m-magnifying-glass class="h-4 w-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                    </div>
                    <input type="text" wire:model.live.debounce.400ms="search"
                        class="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-800 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
                        placeholder="Search by Patient Name...">
                </div>

                {{-- Status Filter --}}
                <div class="relative w-full md:w-48">
                    <select wire:model.live="statusFilter"
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-lg bg-white dark:bg-gray-800">
                        <option value="">All Statuses</option>
                        <option value="Pending">Pending</option>
                        <option value="In_Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>
            </div>

            {{-- Active Filters Badges --}}
            @if($search || $statusFilter)
                <div class="flex items-center gap-2">
                    <button wire:click="$set('search', ''); $set('statusFilter', '')"
                        class="text-xs text-red-600 hover:text-red-800 font-medium hover:underline transition-colors flex items-center gap-1">
                        <x-heroicon-m-trash class="w-3 h-3" /> Clear Filters
                    </button>
                </div>
            @endif
        </div>
    </header>

    {{-- 2. MAIN SCROLLABLE CONTENT --}}
    <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="min-w-full inline-block align-middle">
                <div class="overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Patient
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Test Details
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Date Requested
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($this->requests as $request)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                    {{-- Patient Column --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-9 w-9 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center text-blue-700 dark:text-blue-300 font-bold text-xs">
                                                {{ substr($request->patient?->first_name ?? '?', 0, 1) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $request->patient?->first_name }} {{ $request->patient?->last_name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    ID: {{ $request->patient?->patient_uid }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Test Name Column --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white font-medium">
                                            {{ $request->testDefinition?->test_name ?? 'Unknown Test' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $request->testDefinition?->code ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- Date Column --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            {{ $request->request_date?->format('M d, Y') }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $request->request_date?->format('h:i A') }}
                                        </div>
                                    </td>

                                    {{-- Status Column --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClasses = match($request->status) {
                                                'Completed' => 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800',
                                                'In_Progress' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800',
                                                default => 'bg-yellow-100 text-yellow-800 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-300 dark:border-yellow-800',
                                            };
                                            $label = str_replace('_', ' ', $request->status);
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusClasses }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5 opacity-75"></span>
                                            {{ $label }}
                                        </span>
                                    </td>

                                    {{-- Actions Column --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        @php
                                            $hasResults = !empty($request->result);
                                            $isCompleted = $request->status === 'Completed';
                                            $canView = $hasResults || $isCompleted;
                                            // Assuming you have a route to view specific lab result details, or linking back to consultation
                                            // Adjust route as needed
                                            $actionRoute = '#';
                                            if($request->consultation_id) {
                                                $actionRoute = route('doctor.medical-records', ['record' => $request->consultation_id]);
                                            }
                                        @endphp

                                        @if($canView)
                                            <a href="{{ $actionRoute }}" wire:navigate class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 font-semibold flex items-center justify-end gap-1">
                                                View Results <x-heroicon-m-arrow-right class="w-4 h-4"/>
                                            </a>
                                        @else
                                            <span class="text-gray-400 dark:text-gray-600 cursor-not-allowed flex items-center justify-end gap-1 italic text-xs">
                                                <x-heroicon-m-clock class="w-3 h-3"/> Pending
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center bg-gray-50 dark:bg-gray-800/50">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="bg-gray-100 dark:bg-gray-700 rounded-full p-4 mb-3">
                                                <x-heroicon-o-beaker class="w-8 h-8 text-gray-400 dark:text-gray-500" />
                                            </div>
                                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">No requests found</h3>
                                            <p class="text-sm text-gray-500 mt-1">Try adjusting your search or filters.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination Footer --}}
            @if ($this->requests->hasPages())
                <div class="bg-gray-50 dark:bg-gray-800 px-6 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $this->requests->links() }}
                </div>
            @endif
        </div>
    </div>
</main>
