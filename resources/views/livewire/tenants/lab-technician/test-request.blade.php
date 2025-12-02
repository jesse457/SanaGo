<main class="flex-1 p-4 mt-8  bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">
    {{-- Breadcrumbs --}}
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('lab-technician.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors duration-150">
                        <x-heroicon-s-home class="h-4 w-4 me-2.5" />
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-4 h-4 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm  text-gray-400 md:ms-2 dark:text-gray-200">Lab Requests</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <section id="lab-requests-section">
        <header class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-1 flex items-center gap-3">
                    <x-heroicon-o-clipboard-document-list class="w-8 h-8 text-indigo-600" />
                    Manage Lab Requests
                </h1>
                <p class="text-gray-600 dark:text-gray-400">View, search, and manage all lab requests.</p>
            </div>
        </header>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl">
            <!-- Filters and Search -->
            <div class="p-6 lg:p-8 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="relative w-full md:flex-grow">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-heroicon-s-magnifying-glass class="w-5 h-5 text-gray-400" />
                        </div>
                        <input type="text" wire:model.live.debounce.400ms="search"
                            placeholder="Search by Patient UID, Name, or Test Name..."
                            class="w-full pl-12 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                    </div>

                    <div class="relative w-full md:w-auto md:min-w-[200px]">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-heroicon-s-funnel class="w-5 h-5 text-gray-400" />
                        </div>
                        <select wire:model.live="statusFilter"
                            class="w-full appearance-none pl-12 pr-10 py-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                            <option value="">All Statuses</option>
                            <option value="Pending">Pending</option>
                            <option value="Completed">Completed</option>
                            <option value="In_Progress">In Progress</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto custom-scrollbar">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                Patient</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                Test Name</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                Request Date</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($requests as $request)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 group">
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-200">
                                    {{ $request->patient?->first_name }} {{ $request->patient?->last_name }}
                                    <span
                                        class="block text-xs text-gray-500 font-normal">{{ $request->patient?->patient_uid }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ $request->testDefinition?->test_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ $request->request_date?->format('M d, Y H:i A') ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span @class([
                                        'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium',
                                        'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300' =>
                                            $request->status == 'Pending',
                                        'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' =>
                                            $request->status == 'Completed',
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' =>
                                            $request->status == 'In_Progress',
                                    ])>
                                        {{ $request->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ route('lab-technician.enter-results', $request->id) }}" wire:navigate
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm text-blue-600 hover:text-white bg-blue-50 hover:bg-blue-600 dark:bg-blue-900/30 dark:hover:bg-blue-600 dark:text-blue-300 dark:hover:text-white transition-all duration-200 transform hover:scale-105">
                                        <x-heroicon-s-pencil-square class="h-4 w-4" />
                                        {{ $request->result ? 'Edit Results' : 'Enter Results' }}
                                    </a>

                                    <button type="button"
                                        wire:click="updateStatus({{ $request->id }})"
                                        @if($request->status !== 'Pending') disabled @endif
                                        class="ms-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm text-green-600 bg-green-100 hover:text-white hover:bg-green-600 dark:bg-green-900/30 dark:hover:bg-green-600 dark:text-green-300 dark:hover:text-white transition-all duration-200 transform hover:scale-105 @if($request->status !== 'Pending') opacity-50 cursor-not-allowed hover:scale-100 @endif"
                                    >
                                        <x-heroicon-o-clock class="w-4 h-4" />
                                        <span>Start test</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div
                                        class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                        <x-heroicon-o-document-magnifying-glass class="w-12 h-12 mb-4 text-gray-400" />
                                        <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-1">No Lab
                                            Requests Found</p>
                                        <p class="text-sm">Try adjusting your search or filter criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($requests->hasPages())
                <div class="p-6 lg:p-8 border-t border-gray-200 dark:border-gray-700">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    </section>
</main>
