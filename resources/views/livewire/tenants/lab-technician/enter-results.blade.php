<div>
    <main class="flex-1 p-4 md:p-6 lg:ml-64 bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">
        {{-- Breadcrumbs --}}
        <div class="mb-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <li class="inline-flex items-center">
                        <a href="{{ route('lab-technician.dashboard') }}" wire:navigate
                           class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors">
                            <x-heroicon-s-home class="h-4 w-4 me-2.5"/>
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <x-heroicon-s-chevron-right class="rtl:rotate-180 w-4 h-4 text-gray-400 mx-1"/>
                            <a href="{{ route('lab-technician.test-requests') }}" wire:navigate
                               class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-blue-400">
                                Lab Requests
                            </a>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <x-heroicon-s-chevron-right class="rtl:rotate-180 w-4 h-4 text-gray-400 mx-1"/>
                            <span class="ms-1 text-sm  text-gray-400 md:ms-2 dark:text-gray-200">
                                {{ $labRequest->result ? 'Edit' : 'Enter' }} Results
                            </span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        <section id="enter-results-section">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-1 flex items-center gap-3">
                    <x-heroicon-o-clipboard-document-check class="h-10 w-10 mr-4 text-blue-600 dark:text-blue-400"/>
                    <span>{{ $labRequest->result ? 'Edit' : 'Enter' }} Lab Results</span>
                </h2>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 lg:p-8 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-6 flex items-center">
                        <x-heroicon-s-document-text class="w-7 h-7 mr-3 text-gray-400 dark:text-gray-500"/>
                        Request Details
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-8 gap-y-6">
                        <div class="flex items-start">
                            <x-heroicon-o-user-circle class="h-6 w-6 mr-3 text-gray-400 mt-1 flex-shrink-0"/>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Patient</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ optional($labRequest->patient)->first_name ?? '—' }}
                                    {{ optional($labRequest->patient)->last_name  ?? '' }}
                                    <span class="block text-sm font-normal text-gray-500">{{ optional($labRequest->patient)->patient_uid }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <x-heroicon-o-beaker class="h-6 w-6 mr-3 text-gray-400 mt-1 flex-shrink-0"/>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Test</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ optional($labRequest->testDefinition)->test_name ?? '—' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                             <x-heroicon-o-academic-cap class="h-6 w-6 mr-3 text-gray-400 mt-1 flex-shrink-0"/>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Requested By</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    Dr. {{ optional($labRequest->doctor)->name ?? '—' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <x-heroicon-o-chat-bubble-left-ellipsis class="h-6 w-6 mr-3 text-gray-400 mt-1 flex-shrink-0"/>
                            <div>
                                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Reason</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $labRequest->reason_for_test ?? '—' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <form wire:submit.prevent="saveResults" class="p-6 lg:p-8 space-y-8" wire:loading.class="opacity-50 transition-opacity" wire:target="saveResults, attachments">
                    <div>
                        <label for="results_text" class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            <x-heroicon-o-clipboard-document-list class="w-5 h-5 mr-2"/>
                            Results
                        </label>
                        <textarea id="results_text" wire:model.defer="results_text" rows="6"
                                  class="form-input"></textarea>
                        @error('results_text') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="analysis_comments" class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                            <x-heroicon-o-chat-bubble-bottom-center-text class="w-5 h-5 mr-2"/>
                            Analysis Comments
                        </label>
                        <textarea id="analysis_comments" wire:model.defer="analysis_comments" rows="4"
                                  class="form-input"></textarea>
                        @error('analysis_comments') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                             <x-heroicon-o-paper-clip class="w-5 h-5 mr-2"/>
                            Attachments
                        </label>
                        <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 dark:border-gray-100/25 px-6 py-10">
                            <div class="text-center">
                                <x-heroicon-o-arrow-up-tray class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-500" />
                                <div class="mt-4 flex text-sm leading-6 text-gray-600 dark:text-gray-400">
                                    <label for="file-upload" class="relative cursor-pointer rounded-md bg-white dark:bg-gray-800 font-semibold text-blue-600 dark:text-blue-400 focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-600 focus-within:ring-offset-2 dark:focus-within:ring-offset-gray-800 hover:text-blue-500 dark:hover:text-blue-300">
                                        <span>Upload a file</span>
                                        <input id="file-upload" wire:model="attachments" type="file" class="sr-only" >
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs leading-5 text-gray-600 dark:text-gray-400">PDF, PNG, JPG up to 10MB</p>
                            </div>
                        </div>
                        @error('attachments') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-4">
                        @if ($attachments)
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Files to Upload:</p>
                                <ul class="divide-y divide-gray-200 dark:divide-gray-600 rounded-md border border-gray-200 dark:border-gray-600">
                                    @foreach($attachments as $file)
                                        <li class="flex items-center justify-between py-2 pl-3 pr-4 text-sm">
                                            <div class="flex w-0 flex-1 items-center">
                                                <x-heroicon-s-document class="h-5 w-5 flex-shrink-0 text-gray-400"/>
                                                <span class="ml-2 w-0 flex-1 truncate">{{ $file->getClientOriginalName() }}</span>
                                            </div>
                                            <span class="ml-4 flex-shrink-0 text-gray-500">{{ round($file->getSize() / 1024, 2) }} KB</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if($existingAttachments->count())
                            <div>
                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Files:</p>
                                <ul class="divide-y divide-gray-200 dark:divide-gray-600 rounded-md border border-gray-200 dark:border-gray-600">
                                    @foreach($existingAttachments as $file)
                                        <li class="flex items-center justify-between py-2 pl-3 pr-4 text-sm">
                                            <div class="flex w-0 flex-1 items-center">
                                                <x-heroicon-s-document-check class="h-5 w-5 flex-shrink-0 text-green-500"/>
                                                <span class="ml-2 w-0 flex-1 truncate">{{ basename($file->file_path) }}</span>
                                            </div>
                                            <div class="ml-4 flex-shrink-0">
                                                <a href="{{-- route to download --}}" class="font-medium text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">Download</a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="mt-8 pt-5 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                        <button type="submit"
                                class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled" wire:target="saveResults, attachments">

                            {{-- Loading State --}}
                            <div wire:loading wire:target="saveResults, attachments" class="flex items-center">
                                <x-heroicon-s-arrow-path class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"/>
                                <span>Saving...</span>
                            </div>

                            {{-- Default State --}}
                            <div wire:loading.remove wire:target="saveResults, attachments" class="flex items-center">
                                <x-heroicon-s-check-circle class="-ml-1 mr-2 h-5 w-5"/>
                                <span>Save Results</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </main>
</div>
