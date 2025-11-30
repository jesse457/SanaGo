 <main class="flex-1 p-4 md:p-6 lg:ml-64 bg-gray-50 dark:bg-gray-900 min-h-screen">
     <div class="mb-6">
         <nav class="flex" aria-label="Breadcrumb">
             <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                 <li class="inline-flex items-center">
                     <a href="{{ route('admin.dashboard') }}"
                         class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-600 dark:text-gray-300">
                         <x-heroicon-s-home class="w-4 h-4 me-2.5" />
                         Home
                     </a>
                 </li>
                 <li>
                     <div class="flex items-center">
                         <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                         <span class="ms-1 text-sm font-medium text-gray-400 md:ms-2 dark:text-gray-300">Feedback
                             History</span>
                     </div>
                 </li>
             </ol>
         </nav>
     </div>

     <header class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
         <div>
             <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-1 flex items-center gap-3">
                 <x-heroicon-o-document-text class="w-8 h-8 text-indigo-600" />
                 Feedback History
             </h1>
             <p class="text-gray-600 dark:text-gray-400">View, search, and manage user feedback submissions.</p>
         </div>

         <div class="flex items-center gap-3">
             <a href="{{ route('receptionist.receptionist-feedback') }}" wire:navigate
                 class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition duration-150 ease-in-out shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                 <x-heroicon-o-plus class="w-5 h-5" /> Submit Feedback
             </a>
         </div>
     </header>

     <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-10 dark:bg-gray-800 dark:border-gray-700">
        

         @if (!isset($feedbacks) || $feedbacks->total() === 0)
             <div class="text-center py-12 text-gray-500 dark:text-gray-300">
                 <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-4 h-12 w-12 text-gray-300" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M8 10h.01M12 10h.01M16 10h.01M9 16h6M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z" />
                 </svg>
                 <p class="text-lg font-medium">No feedback found</p>
                 <p class="mt-2 text-sm">Submit your first feedback using the button above.</p>
                 <div class="mt-4">
                     <a href="{{ route('receptionist.receptionist-feedback') }}" wire:navigate
                         class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition duration-150 ease-in-out shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                         <x-heroicon-o-plus class="w-5 h-5" /> Submit Feedback
                     </a>
                 </div>
             </div>
         @else
             <div class="overflow-x-auto rounded-md">
                 <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                     <thead class="bg-gray-50 dark:bg-gray-700">
                         <tr>
                             <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                 Subject</th>
                             <th
                                 class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                 Category</th>

                             <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                 Status</th>
                             <th
                                 class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
                                 Submitted</th>
                             <th
                                 class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                 Actions</th>
                         </tr>
                     </thead>

                     <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                         @foreach ($feedbacks as $feedback)
                             @php
                                  $status = strtolower($feedback->status ?? 'unknown');


                                 $statusStyles = [
                                     'pending' => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-800'],
                                     'resolved' => ['bg' => 'bg-green-50', 'text' => 'text-green-800'],
                                     'closed' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-800'],
                                 ];


                                 $ssty = $statusStyles[$status] ?? [
                                     'bg' => 'bg-purple-50',
                                     'text' => 'text-purple-800',
                                 ];
                             @endphp

                             <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                 <td class="px-4 py-4 whitespace-nowrap">
                                     <div class="text-sm font-medium text-gray-900 dark:text-white">
                                         {{ $feedback->subject ?? 'No subject' }}</div>
                                     <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">
                                         {{ Str::limit($feedback->message ?? '', 120) }}</div>
                                 </td>

                                 <td class="px-4 py-4 whitespace-nowrap hidden sm:table-cell">
                                     <div class="text-sm text-gray-700 dark:text-gray-200">
                                         {{ ucfirst($feedback->category ?? 'general') }}</div>
                                 </td>



                                 <td class="px-4 py-4 whitespace-nowrap">
                                     <span
                                         class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $ssty['bg'] }} {{ $ssty['text'] }}">
                                         {{ ucfirst($status !== 'unknown' ? $status : 'Unknown') }}
                                     </span>
                                 </td>

                                 <td class="px-4 py-4 whitespace-nowrap hidden md:table-cell">
                                     <div class="text-sm text-gray-600 dark:text-gray-300">
                                         {{ optional($feedback->created_at)->format('M d, Y H:i') ?? '--' }}
                                     </div>
                                 </td>

                                 <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                     <div class="flex items-center justify-end gap-2">
                                         <button wire:click.prevent="showFeedback({{ $feedback->id }})"
                                             class="inline-flex items-center gap-2 px-3 py-1 rounded-md bg-white border border-gray-200 hover:bg-gray-50 text-sm dark:bg-gray-700 dark:border-gray-600 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                             <x-heroicon-s-eye class="w-4 h-4 text-gray-600 dark:text-gray-200" /> View
                                         </button>

                                     </div>
                                 </td>
                             </tr>
                         @endforeach
                     </tbody>
                 </table>
             </div>

             {{-- Pagination --}}
             <div class="mt-4 flex items-center justify-between gap-4">
                 <div class="text-sm text-gray-600 dark:text-gray-300">
                     Showing <span class="font-semibold">{{ $feedbacks->firstItem() ?? 0 }}</span> to <span
                         class="font-semibold">{{ $feedbacks->lastItem() ?? 0 }}</span> of <span
                         class="font-semibold">{{ $feedbacks->total() ?? 0 }}</span>
                 </div>

                 <div>
                     {{ $feedbacks->links() }}
                 </div>
             </div>
         @endif
     </div>

     {{-- Modal --}}
     <div x-data="{ open: @entangle('showModal') }" x-init="$watch('open', value => {
                 if (value) {
                     document.body.classList.add('overflow-hidden');
                     setTimeout(() => {
                                 const ta = $el.querySelector('textarea[wire\\:model\\.defer=\"replyDraft\"]'); if (ta) ta.focus(); }, 60); } else {
         document.body.classList.remove('overflow-hidden'); } }); ">
            <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-end justify-center sm:items-center">
                <div class="fixed inset-0 bg-black/50 transition-opacity" x-show="open" aria-hidden="true"></div>

                <div role="dialog" aria-modal="true" aria-labelledby="modal-title"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden w-full max-w-3xl mx-4 z-50 transform transition-all"
                    x-show="open"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-6"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-6"
                    @keydown.escape.window="open = false; $wire.closeModal();"
                    @click.away="open = false; $wire.closeModal();">

                    <div class="px-6 py-4 border-b dark:border-gray-700 flex justify-between items-center">
                        <h3 id="modal-title" class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $modalTitle ?? 'Feedback Details' }}</h3>

                        <div class="flex items-center gap-2">
                            <button @click="open = false; $wire.closeModal();"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-400"
                                aria-label="Close dialog">
                                {{-- <x-heroicon-s-x class="w-5 h-5 text-gray-600 dark:text-gray-200" /> --}}
                            </button>
                        </div>
                    </div>

                    <div class="px-6 py-6 max-h-[70vh] overflow-auto">
                           @if ($modalFeedback)
         <div class="space-y-6">

             {{-- Header: subject, meta --}}
             <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                 <div class="min-w-0">


                     <div class="mt-2 text-sm text-gray-500 dark:text-gray-400 flex flex-wrap items-center gap-2">
                         <span
                             class="inline-flex items-center gap-2 px-2 py-1 rounded-full bg-gray-50 dark:bg-gray-700 text-xs text-gray-600 dark:text-gray-200">
                             <x-heroicon-s-tag class="w-4 h-4" /> {{ ucfirst($modalFeedback->category ?? 'General') }}
                         </span>

                         <span class="text-xs text-gray-400">•</span>

                         <span class="text-xs text-gray-400">
                             Submitted {{ optional($modalFeedback->created_at)->diffForHumans() }}
                         </span>

                         <span class="hidden sm:inline-flex text-xs text-gray-400">•</span>


                     </div>
                 </div>

                 <div class="text-right hidden sm:block">
                     <div class="text-xs text-gray-500 dark:text-gray-300">Status</div>
                     <div class="mt-1">
                         <span
                             class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-800">
                             {{ ucfirst($modalFeedback->status ?? 'Open') }}
                         </span>
                     </div>
                 </div>
             </div>

             {{-- Original message --}}
             <div class="bg-gray-50 dark:bg-gray-900 rounded-md p-4 border border-gray-100 dark:border-gray-700">
                 <div class="prose max-w-none dark:prose-invert text-gray-700 dark:text-gray-200">
                     {!! nl2br(e($modalFeedback->message)) !!}
                 </div>
             </div>

             {{-- Published response --}}
             <div>
                 <h5 class="text-sm font-medium text-gray-800 dark:text-gray-100">Response</h5>

                 @if (!empty($modalFeedback->response))
                     <div class="mt-3 p-4 rounded-md border bg-white dark:bg-gray-800 dark:border-gray-700">
                         <div class="flex items-start gap-3">
                             <div class="flex-shrink-0">
                                 <div
                                     class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-semibold">
                                     {{ strtoupper(Str::limit(optional($modalFeedback->responder)->name ?? 'S', 1, '')) }}
                                 </div>
                             </div>

                             <div class="min-w-0">
                                 <div class="text-sm text-gray-700 dark:text-gray-200">
                                     {!! nl2br(e($modalFeedback->response)) !!}
                                 </div>

                                 <div class="text-xs text-gray-400 mt-3">
                                     Responded by {{ optional($modalFeedback->responder)->name ?? 'Staff' }} •
                                     {{ optional($modalFeedback->updated_at)->diffForHumans() }}
                                 </div>
                             </div>
                         </div>
                     </div>
                 @else
                     <div
                         class="mt-3 p-4 rounded-md border border-dashed border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-center text-sm text-gray-500 dark:text-gray-300">
                         <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-2 h-6 w-6 text-gray-300"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                 d="M8 10h.01M12 10h.01M16 10h.01M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z" />
                         </svg>

                         <div class="font-medium">No response yet</div>
                         <div class="text-xs mt-1">Use the reply area below to write a response.</div>
                     </div>
                 @endif
             </div>



             <div class="flex gap-2 mt-4 justify-end">
                 <button @click="open = false; $wire.closeModal();"
                     class="px-3 py-2 bg-gray-100 rounded-md hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-200">
                     Close
                 </button>
             </div>
         </div>
     @else
         <div class="text-center py-6 text-gray-500 dark:text-gray-300">Loading…</div>
         @endif
     </div>
     </div>
     </div>
     </div>

 </main>
