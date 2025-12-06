<main class="flex-1 overflow-x-hidden overflow-y-auto  p-6 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto">
        {{-- Breadcrumbs --}}
         <div class="mb-6 mt-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <li class="inline-flex items-center">
                        <a href="{{ route('receptionist.dashboard') }}" wire:navigate
                            class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-300 transition-colors duration-150">
                            <x-heroicon-s-home class="w-4 h-4 me-2.5" />
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                            <span class="ms-1 text-sm  text-gray-900 md:ms-2 dark:text-gray-400">
                                Feedbacks</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center gap-3">

                    Feedback History
                </h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 ml-14">
                    View submitted feedback, track responses, and manage communication history.
                </p>
            </div>

            <a href="{{ route('receptionist.receptionist-feedback') }}" wire:navigate
                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all duration-200 shadow-lg shadow-indigo-600/20 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <x-heroicon-o-plus class="w-5 h-5" />
                Submit New Feedback
            </a>
        </div>

        {{-- Main Content Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/60 dark:border-gray-700 overflow-hidden">

            @if (!isset($feedbacks) || $feedbacks->total() === 0)
                <div class="text-center py-16 px-6">
                    <div class="h-20 w-20 bg-gray-50 dark:bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <x-heroicon-o-inbox class="w-10 h-10 text-gray-300 dark:text-gray-500" />
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">No feedback submissions found</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                        Your submitted feedback history will appear here. Submit your first feedback to get started.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('receptionist.receptionist-feedback') }}" wire:navigate
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600">
                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                            Write Feedback
                        </a>
                    </div>
                </div>
            @else
                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Subject</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400 hidden sm:table-cell">Category</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Status</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400 hidden md:table-cell">Submitted</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @foreach ($feedbacks as $feedback)
                                @php
                                    $status = strtolower($feedback->status ?? 'unknown');
                                    $config = match($status) {
                                        'pending' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'text' => 'text-amber-700 dark:text-amber-400', 'border' => 'border-amber-100 dark:border-amber-800', 'icon' => 'clock'],
                                        'resolved' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-700 dark:text-emerald-400', 'border' => 'border-emerald-100 dark:border-emerald-800', 'icon' => 'check-circle'],
                                        'closed' => ['bg' => 'bg-gray-50 dark:bg-gray-700/20', 'text' => 'text-gray-600 dark:text-gray-400', 'border' => 'border-gray-100 dark:border-gray-700', 'icon' => 'lock-closed'],
                                        default => ['bg' => 'bg-purple-50 dark:bg-purple-900/20', 'text' => 'text-purple-700 dark:text-purple-400', 'border' => 'border-purple-100 dark:border-purple-800', 'icon' => 'question-mark-circle'],
                                    };
                                @endphp

                                <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors duration-200 group">
                                    {{-- Subject --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white truncate max-w-xs">
                                                {{ $feedback->subject ?? 'No Subject' }}
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs mt-0.5">
                                                {{ Str::limit($feedback->message ?? '', 60) }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Category --}}
                                    <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            {{ ucfirst($feedback->category ?? 'General') }}
                                        </span>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }}">
                                            @if($config['icon'] == 'clock') <x-heroicon-o-clock class="w-3.5 h-3.5" /> @endif
                                            @if($config['icon'] == 'check-circle') <x-heroicon-o-check-circle class="w-3.5 h-3.5" /> @endif
                                            @if($config['icon'] == 'lock-closed') <x-heroicon-o-lock-closed class="w-3.5 h-3.5" /> @endif
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>

                                    {{-- Date --}}
                                    <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                        <span class="text-sm text-gray-600 dark:text-gray-300 font-mono">
                                            {{ optional($feedback->created_at)->format('M d, Y') }}
                                        </span>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click.prevent="showFeedback({{ $feedback->id }})"
                                            class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 p-2 rounded-lg transition-colors"
                                            title="View Details">
                                            <x-heroicon-o-eye class="w-5 h-5" />
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($feedbacks->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                        {{ $feedbacks->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

    {{-- Details Modal --}}
    <div x-data="{ open: @entangle('showModal') }"
         x-show="open"
         x-cloak
         class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <div x-show="open"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="open"
                    @click.away="open = false; $wire.closeModal()"
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100 dark:border-gray-700">

                    {{-- Modal Header --}}
                    <div class="bg-gray-50/50 dark:bg-gray-800/50 px-4 py-4 sm:px-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">
                            Feedback Details
                        </h3>
                        <button type="button" @click="open = false; $wire.closeModal()" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <x-heroicon-o-x-mark class="h-6 w-6" />
                        </button>
                    </div>

                    {{-- Modal Body --}}
                    <div class="px-4 py-5 sm:p-6">
                        @if ($modalFeedback)
                            <div class="space-y-6">
                                {{-- Subject & Meta --}}
                                <div>
                                    <div class="flex items-start justify-between">
                                        <h4 class="text-xl font-bold text-gray-900 dark:text-white">
                                            {{ $modalFeedback->subject }}
                                        </h4>
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                                            {{ $modalFeedback->status === 'resolved' ? 'bg-green-50 text-green-700 ring-green-600/20' : 'bg-yellow-50 text-yellow-800 ring-yellow-600/20' }}">
                                            {{ ucfirst($modalFeedback->status) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                        <span class="flex items-center gap-1">
                                            <x-heroicon-s-tag class="w-3 h-3" />
                                            {{ ucfirst($modalFeedback->category ?? 'General') }}
                                        </span>
                                        <span>•</span>
                                        <span>{{ $modalFeedback->created_at->format('M d, Y \a\t h:i A') }}</span>
                                    </div>
                                </div>

                                {{-- Message Content --}}
                                <div class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                                    <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line leading-relaxed">
                                        {{ $modalFeedback->message }}
                                    </p>
                                </div>

                                {{-- Divider --}}
                                <div class="relative">
                                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                        <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                                    </div>
                                    <div class="relative flex justify-center">
                                        <span class="bg-white dark:bg-gray-800 px-2 text-sm text-gray-500">Admin Response</span>
                                    </div>
                                </div>

                                {{-- Response Section --}}
                                @if (!empty($modalFeedback->response))
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold border-2 border-white dark:border-gray-800 shadow-sm">
                                                {{ strtoupper(substr(optional($modalFeedback->responder)->name ?? 'A', 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="flex-grow">
                                            <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl rounded-tl-none p-4 border border-indigo-100 dark:border-indigo-800/50">
                                                <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line">
                                                    {{ $modalFeedback->response }}
                                                </p>
                                            </div>
                                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 pl-1">
                                                Responded by <span class="font-medium text-gray-900 dark:text-white">{{ optional($modalFeedback->responder)->name ?? 'Administrator' }}</span>
                                                • {{ $modalFeedback->updated_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-6 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                                        <x-heroicon-o-clock class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600" />
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Awaiting response from administration.</p>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="flex justify-center py-8">
                                <svg class="animate-spin h-8 w-8 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    {{-- Modal Footer --}}
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 rounded-b-2xl">
                        <button type="button" @click="open = false; $wire.closeModal()"
                            class="inline-flex w-full justify-center rounded-lg bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 sm:w-auto">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
