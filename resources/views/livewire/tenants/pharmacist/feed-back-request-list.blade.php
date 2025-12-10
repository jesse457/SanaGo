<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4 md:flex md:items-center md:justify-between space-y-3 md:space-y-0">

                {{-- Title & Breadcrumbs --}}
                <div class="flex-1 min-w-0">
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 mt-2" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('pharmacist.dashboard') }}" wire:navigate
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    Home
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">Feedback History</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            Feedback History
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            View, search, and manage your user feedback submissions.
                        </p>
                    </div>
                </div>

                {{-- Action Toolbar --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('pharmacist.submit-feedback') }}" wire:navigate
                        class="group relative inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl shadow-md hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all overflow-hidden dark:focus:ring-offset-gray-900">
                        <div
                            class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                        </div>
                        <x-heroicon-o-plus class="w-5 h-5" />
                        <span>Submit Feedback</span>
                    </a>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="relative min-h-[400px] p-4 sm:p-6 pb-20">

            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">

                @if (!isset($feedbacks) || $feedbacks->total() === 0)
                    <div class="text-center py-16 px-6">
                        <div class="w-16 h-16 bg-slate-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6 border border-slate-100 dark:border-gray-700">
                            <x-heroicon-o-chat-bubble-left-right class="h-8 w-8 text-slate-400" />
                        </div>
                        <p class="text-lg font-bold text-slate-900 dark:text-white mb-2">No feedback found</p>
                        <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-8">
                            Submit your first feedback using the button above.
                        </p>

                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 dark:divide-gray-800">
                            <thead class="bg-slate-50 dark:bg-gray-950">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider hidden sm:table-cell">Category</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider hidden md:table-cell">Submitted</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                                @foreach ($feedbacks as $feedback)
                                    @php
                                        $status = strtolower($feedback->status ?? 'unknown');
                                        $statusStyles = match ($status) {
                                            'pending' => 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800',
                                            'resolved' => 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800',
                                            'closed' => 'bg-slate-50 text-slate-600 border-slate-100 dark:bg-gray-800 dark:text-slate-400 dark:border-gray-700',
                                            default => 'bg-purple-50 text-purple-700 border-purple-100 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800',
                                        };
                                    @endphp

                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150 group">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-slate-900 dark:text-white">
                                                {{ $feedback->subject ?? 'No subject' }}
                                            </div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-1 max-w-xs">
                                                {{ Str::limit($feedback->message ?? '', 80) }}
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200 dark:bg-gray-800/50 dark:text-slate-300 dark:border-gray-700 capitalize">
                                                {{ ucfirst($feedback->category ?? 'general') }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border shadow-sm capitalize {{ $statusStyles }}">
                                                {{ ucfirst($status !== 'unknown' ? $status : 'Unknown') }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell text-sm text-slate-500 dark:text-slate-400">
                                            {{ optional($feedback->created_at)->format('M d, Y H:i') ?? '--' }}
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end gap-2">
                                                <button wire:click.prevent="showFeedback({{ $feedback->id }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white text-slate-600 text-xs font-bold rounded-lg border border-slate-200 hover:bg-slate-50 hover:text-blue-600 dark:bg-gray-800 dark:text-slate-300 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:text-blue-400 transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                    <x-heroicon-s-eye class="w-4 h-4" /> View
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($feedbacks->hasPages())
                        <div class="px-6 py-4 border-t border-slate-100 dark:border-gray-800 bg-slate-50 dark:bg-gray-900/50">
                            {{ $feedbacks->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Modal (Reused from Admin Feedback UI) --}}
        <div x-data="{ open: @entangle('showModal') }"
            x-init="$watch('open', value => { if (value) { document.body.style.overflow = 'hidden'; } else { document.body.style.overflow = ''; } })"
            x-show="open" x-cloak class="relative z-50">
            <template x-teleport="body">
                <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        {{-- Backdrop --}}
                        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity" @click="open = false; $wire.closeModal();"></div>

                        <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                            class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all w-full max-w-3xl border border-slate-100 dark:border-gray-800 my-8">

                            {{-- Modal Header --}}
                            <div class="bg-white dark:bg-gray-900 px-6 py-5 border-b border-slate-100 dark:border-gray-800 flex items-center justify-between sticky top-0 z-10">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                                    {{ $modalTitle ?? 'Feedback Details' }}
                                </h3>
                                <button @click="open = false; $wire.closeModal();"
                                    class="rounded-xl bg-slate-50 dark:bg-gray-800 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400">
                                    <x-heroicon-o-x-mark class="h-6 w-6" />
                                </button>
                            </div>

                            <div class="px-6 py-6 max-h-[70vh] overflow-y-auto custom-scrollbar">
                                @if ($modalFeedback)
                                    <div class="space-y-6">
                                        {{-- Header: meta --}}
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50 dark:bg-gray-800/50 p-4 rounded-xl border border-slate-100 dark:border-gray-800">
                                            <div class="flex flex-wrap items-center gap-3">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white dark:bg-gray-700 border border-slate-200 dark:border-gray-600 text-xs font-bold text-slate-700 dark:text-slate-200 shadow-sm capitalize">
                                                    <x-heroicon-s-tag class="w-3.5 h-3.5 text-slate-400" />
                                                    {{ ucfirst($modalFeedback->category ?? 'General') }}
                                                </span>
                                                <span class="text-xs font-medium text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                                    <x-heroicon-o-clock class="w-3.5 h-3.5" />
                                                    Submitted {{ optional($modalFeedback->created_at)->diffForHumans() }}
                                                </span>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status:</span>
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold capitalize
                                                    {{ match(strtolower($modalFeedback->status ?? 'open')) {
                                                        'pending' => 'bg-amber-50 text-amber-700 border border-amber-100',
                                                        'resolved' => 'bg-emerald-50 text-emerald-700 border border-emerald-100',
                                                        default => 'bg-slate-100 text-slate-700 border border-slate-200',
                                                    } }}">
                                                    {{ ucfirst($modalFeedback->status ?? 'Open') }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- Original message --}}
                                        <div>
                                            <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                                                <x-heroicon-s-chat-bubble-left class="w-4 h-4 text-blue-500" />
                                                Feedback Message
                                            </h4>
                                            <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl border border-slate-200 dark:border-gray-700 shadow-sm">
                                                <div class="prose prose-sm max-w-none dark:prose-invert text-slate-600 dark:text-slate-300">
                                                    {!! nl2br(e($modalFeedback->message)) !!}
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Published response --}}
                                        <div class="relative">
                                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                                <div class="w-full border-t border-slate-200 dark:border-gray-800"></div>
                                            </div>
                                            <div class="relative flex justify-center">
                                                <span class="bg-white dark:bg-gray-900 px-3 text-xs font-bold text-slate-400 uppercase tracking-wider">
                                                    Response
                                                </span>
                                            </div>
                                        </div>

                                        @if (!empty($modalFeedback->response))
                                            <div class="flex gap-4">
                                                <div class="flex-shrink-0">
                                                    <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center border-2 border-white dark:border-gray-800 shadow-sm">
                                                        <span class="text-sm font-bold text-blue-700 dark:text-blue-300">
                                                            {{ strtoupper(Str::limit(optional($modalFeedback->responder)->name ?? 'S', 1, '')) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="flex-grow">
                                                    <div class="bg-blue-50/50 dark:bg-blue-900/10 rounded-2xl rounded-tl-none p-5 border border-blue-100 dark:border-blue-900/30">
                                                        <div class="prose prose-sm max-w-none dark:prose-invert text-slate-700 dark:text-slate-200 mb-3">
                                                            {!! nl2br(e($modalFeedback->response)) !!}
                                                        </div>
                                                        <div class="flex items-center gap-2 text-xs text-slate-400 font-medium">
                                                            <span>Responded by <span class="text-slate-600 dark:text-slate-300 font-bold">{{ optional($modalFeedback->responder)->name ?? 'Staff' }}</span></span>
                                                            <span>•</span>
                                                            <span>{{ optional($modalFeedback->updated_at)->diffForHumans() }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center py-8 bg-slate-50 dark:bg-gray-800/50 rounded-2xl border border-dashed border-slate-300 dark:border-gray-700">
                                                <x-heroicon-o-inbox class="mx-auto h-8 w-8 text-slate-300 mb-2" />
                                                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">No response yet</p>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="flex items-center justify-center py-12">
                                        <x-heroicon-o-arrow-path class="animate-spin h-8 w-8 text-blue-500" />
                                    </div>
                                @endif
                            </div>

                            {{-- Footer --}}
                            <div class="bg-slate-50 dark:bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 rounded-b-2xl border-t border-slate-100 dark:border-gray-800">
                                <button @click="open = false; $wire.closeModal();"
                                    class="inline-flex justify-center w-full sm:w-auto rounded-xl bg-white dark:bg-gray-800 px-6 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-gray-700 hover:bg-slate-50 dark:hover:bg-gray-700 transition-all focus:outline-none focus:ring-2 focus:ring-slate-400">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</main>
