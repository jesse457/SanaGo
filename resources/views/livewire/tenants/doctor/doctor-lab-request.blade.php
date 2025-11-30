<main class="p-6 px-10 flex-1 flex flex-col h-screen overflow-y-auto px-2 bg-gray-50 dark:bg-gray-900">
    {{-- Breadcrumbs --}}
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('doctor.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors duration-150">
                        <x-heroicon-s-home class="h-4 w-4 me-2.5" />{{ __('doctor.home') }}
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-4 h-4 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm text-gray-400 md:ms-2 dark:text-gray-200">{{ __('doctor.lab_requests') }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <section id="lab-requests-section">
        <header class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-1 flex items-center gap-3">
                    <x-heroicon-o-clipboard-document-list class="w-8 h-8 text-indigo-600" /> {{ __('doctor.lab_requests') }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400">{{ __('doctor.lab_requests_subtitle') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <div class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('doctor.showing_requests', [
                        'first' => $this->requests->firstItem() ?? 0,
                        'last' => $this->requests->lastItem() ?? 0,
                        'total' => $this->requests->total(),
                    ]) }}
                </div>
            </div>
        </header>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6 lg:p-8 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="relative w-full md:flex-grow">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-heroicon-s-magnifying-glass class="w-5 h-5 text-gray-400" />
                        </div>
                        <input type="text" wire:model.live.debounce.400ms="search"
                            placeholder="{{ __('doctor.search_lab_placeholder') }}"
                            class="w-full pl-12 pr-4 py-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 shadow-sm">
                    </div>
                    <div class="relative w-full md:w-auto md:min-w-[200px]">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <x-heroicon-s-funnel class="w-5 h-5 text-gray-400" />
                        </div>
                        <select wire:model.live="statusFilter"
                            class="w-full appearance-none pl-12 pr-10 py-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 shadow-sm">
                            <option value="">{{ __('doctor.all_statuses') }}</option>
                            <option value="Pending">{{ __('doctor.status_pending') }}</option>
                            <option value="Completed">{{ __('doctor.status_completed') }}</option>
                            <option value="In_Progress">{{ __('doctor.status_in_progress') }}</option>
                        </select>
                    </div>
                </div>

                @if($search || $statusFilter)
                    <div class="mt-4 flex flex-wrap gap-2">
                        @if($search)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                {{ __('doctor.search') }}: {{ $search }}
                                <button wire:click="$set('search', '')" class="ml-2 text-blue-600 hover:text-blue-800 dark:text-blue-300 dark:hover:text-blue-100">
                                    <x-heroicon-s-x-mark class="w-3 h-3" />
                                </button>
                            </span>
                        @endif
                        @if($statusFilter)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                {{ __('doctor.status') }}: {{ $statusFilter }}
                                <button wire:click="$set('statusFilter', '')" class="ml-2 text-blue-600 hover:text-blue-800 dark:text-blue-300 dark:hover:text-blue-100">
                                    <x-heroicon-s-x-mark class="w-3 h-3" />
                                </button>
                            </span>
                        @endif
                        <button wire:click="$set('search', ''); $set('statusFilter', '')" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            {{ __('doctor.clear_all_filters') }}
                        </button>
                    </div>
                @endif
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                {{ __('doctor.patient') }}</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                {{ __('doctor.test_name') }}</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                {{ __('doctor.request_date') }}</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                {{ __('doctor.status') }}</th>
                            <th scope="col"
                                class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                {{ __('doctor.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 overflow-y-auto  divide-y divide-gray-200 dark:divide-gray-700">
                        {{-- Accessing the computed property $this->requests --}}
                        @forelse ($this->requests as $request)
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
                                        // Status 'In Progress' with a space
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' =>
                                            $request->status == 'In_Progress',
                                    ])>
                                        {{ str_replace('_', ' ', $request->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">

                                    @php
                                        // Determine if the action button should be disabled based on business rules.
                                        $hasConsultation = !empty($request->consultation_id);
                                        $isPending = $request->status == 'Pending';
                                        $isDisabled = !$hasConsultation || $isPending || 'In_Progress' === $request->status;
                                        $route = $isDisabled ? '#' : route('doctor.consultation', $request->consultation_id);
                                    @endphp

                                    <a href="{{ $route }}" @class([
                                        'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm transition-all duration-200 transform',
                                        // Active state styling
                                        'text-blue-600 hover:text-white bg-blue-50 hover:bg-blue-600 dark:bg-blue-900/30 dark:hover:bg-blue-600 dark:text-blue-300 dark:hover:text-white hover:scale-105' => !$isDisabled,
                                        // Disabled state styling: muted colors, no hover effects, and cursor change
                                        'text-gray-400 bg-gray-100 dark:bg-gray-700/50 dark:text-gray-500 cursor-not-allowed' => $isDisabled,
                                    ])
                                        {{-- Prevents navigation when disabled, uses wire:navigate otherwise --}}
                                        {{ $isDisabled ? 'onclick="return false;"' : 'wire:navigate' }}>
                                        <x-heroicon-s-pencil-square class="h-4 w-4" />
                                        {{ $request->result ? __('doctor.view_results') : __('doctor.results_pending') }}
                                    </a>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div
                                        class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                        <x-heroicon-o-document-magnifying-glass class="w-12 h-12 mb-4 text-gray-400" />
                                        <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('doctor.no_lab_requests_found') }}</p>
                                        <p class="text-sm">{{ __('doctor.lab_requests_empty_subtext') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($this->requests->hasPages())
                <div class="p-6 lg:p-8 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                    {{ $this->requests->links() }}
                </div>
            @endif
        </div>
    </section>
</main>
