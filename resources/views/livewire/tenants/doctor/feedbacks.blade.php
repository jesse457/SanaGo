<main class="flex-1 p-4 sm:p-6 overflow-y-auto h-screen bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-white transition-colors">
                        <x-heroicon-s-home class="w-4 h-4 me-2" />
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="w-4 h-4 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Feedback History</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg">
                        <x-heroicon-s-user-group class="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    Feedback History
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Manage and track user feedback submissions.</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                
                <a href="{{ route('doctor.send-feedback') }}" wire:navigate
                    class="inline-flex justify-center items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-all duration-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <x-heroicon-o-plus class="w-4 h-4" />
                    Submit Feedback
                </a>
            </div>
        </div>

        <!-- Content Area -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden dark:bg-gray-800 dark:border-gray-700">

            @if (!isset($feedbacks) || $feedbacks->total() === 0)
                <div class="flex flex-col items-center justify-center py-16 px-4 text-center">
                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-full p-4 mb-4">
                        <x-heroicon-o-inbox class="w-12 h-12 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">No feedback found</h3>
                    <p class="mt-1 text-gray-500 dark:text-gray-400 max-w-sm">Get started by submitting your first feedback using the button above.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Category</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Submitted</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach ($feedbacks as $feedback)
                                @php
                                    $status = strtolower($feedback->status ?? 'unknown');
                                    $statusStyles = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 ring-yellow-600/20',
                                        'resolved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 ring-green-600/20',
                                        'closed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 ring-gray-500/10',
                                        'reviewing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 ring-blue-600/20',
                                    ];
                                    $ssty = $statusStyles[$status] ?? 'bg-gray-100 text-gray-800 ring-gray-500/10';
                                @endphp

                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 group">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                {{ $feedback->subject ?? 'No subject' }}
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">
                                                {{ Str::limit($feedback->message ?? '', 80) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                                            {{ ucfirst($feedback->category ?? 'General') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $ssty }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5"></span>
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ optional($feedback->created_at)->format('M d, Y') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click.prevent="showFeedback({{ $feedback->id }})"
                                            class="text-gray-400 hover:text-indigo-600 dark:text-gray-500 dark:hover:text-indigo-400 transition-colors">
                                            <span class="sr-only">View</span>
                                            <x-heroicon-s-eye class="w-5 h-5" />
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-gray-50 dark:bg-gray-800/50 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex items-center justify-between">
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing <span class="font-medium">{{ $feedbacks->firstItem() ?? 0 }}</span> to <span class="font-medium">{{ $feedbacks->lastItem() ?? 0 }}</span> of <span class="font-medium">{{ $feedbacks->total() ?? 0 }}</span> results
                            </p>
                        </div>
                        <div>
                            {{ $feedbacks->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Detail Modal -->
    <div x-data="{ open: @entangle('showModal') }"
         x-init="$watch('open', value => { document.body.classList.toggle('overflow-hidden', value) })"
         @keydown.escape.window="open = false; $wire.closeModal();"
         class="relative z-50"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true">

        <!-- Backdrop -->
        <div x-show="open" x-cloak
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/75 backdrop-blur-sm transition-opacity"></div>

        <!-- Modal Panel -->
        <div x-show="open" x-cloak
             class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">

                <div x-show="open"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     @click.away="open = false; $wire.closeModal();"
                     class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">

                    @if ($modalFeedback)
                        <!-- Modal Header -->
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b dark:border-gray-700 flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">
                                    {{ $modalFeedback->subject ?? 'Feedback Details' }}
                                </h3>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-700 dark:text-gray-300">
                                        {{ ucfirst($modalFeedback->category ?? 'General') }}
                                    </span>
                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ optional($modalFeedback->created_at)->format('M d, Y • H:i') }}
                                    </span>
                                </div>
                            </div>
                            <button @click="open = false; $wire.closeModal();" type="button" class="rounded-md bg-white dark:bg-gray-800 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                <span class="sr-only">Close</span>
                                <x-heroicon-s-x-mark class="h-6 w-6" aria-hidden="true" />
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="px-4 py-6 sm:p-6 bg-gray-50 dark:bg-gray-900/50">

                            <!-- User Message Bubble -->
                            <div class="flex gap-3 mb-6">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        <x-heroicon-s-user class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="bg-white dark:bg-gray-800 rounded-2xl rounded-tl-none p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                                        <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed">
                                            {{ $modalFeedback->message }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Admin Response Bubble -->
                            <div class="flex gap-3 justify-end">
                                @if (!empty($modalFeedback->response))
                                    <div class="flex-1">
                                        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl rounded-tr-none p-4 shadow-sm border border-indigo-100 dark:border-indigo-800 ml-auto">
                                            <h5 class="text-xs font-bold text-indigo-800 dark:text-indigo-300 mb-2 uppercase tracking-wide">Response</h5>
                                            <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed">
                                                {!! nl2br(e($modalFeedback->response)) !!}
                                            </p>
                                            <div class="mt-3 flex items-center justify-end gap-1 text-xs text-indigo-400 dark:text-indigo-500">
                                                <x-heroicon-s-check-circle class="w-3 h-3" />
                                                Replied by {{ optional($modalFeedback->responder)->name ?? 'Support' }}
                                                • {{ optional($modalFeedback->updated_at)->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                            <span class="text-sm font-bold text-indigo-600 dark:text-indigo-300">
                                                {{ strtoupper(Str::limit(optional($modalFeedback->responder)->name ?? 'S', 1, '')) }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <div class="w-full text-center py-4">
                                        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gray-100 dark:bg-gray-700 text-xs text-gray-500 dark:text-gray-400">
                                            <x-heroicon-s-clock class="w-4 h-4" />
                                            Awaiting administrative response...
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="bg-white dark:bg-gray-800 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t dark:border-gray-700">
                            <button type="button"
                                @click="open = false; $wire.closeModal();"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:hover:bg-gray-600 transition-colors">
                                Close
                            </button>
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <x-heroicon-s-arrow-path class="w-8 h-8 text-gray-400 animate-spin mx-auto"/>
                            <p class="mt-2 text-sm text-gray-500">Loading details...</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>
