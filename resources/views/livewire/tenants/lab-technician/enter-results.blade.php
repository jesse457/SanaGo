<div class="flex-1 p-4 sm:p-6 bg-white dark:bg-gray-900 overflow-y-auto min-h-screen">
    {{-- Breadcrumbs & Header --}}
    <div class="mb-8 relative">
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
                        <a href="{{ route('lab-technician.test-requests') }}" wire:navigate
                           class="ms-1 hover:text-indigo-600 md:ms-2 dark:hover:text-indigo-400 transition-colors">
                            Lab Requests
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-4 h-4 text-slate-300 dark:text-slate-600 mx-1" />
                        <span class="ms-1 font-semibold text-slate-900 md:ms-2 dark:text-slate-200">
                            {{ $labRequest->result ? 'Edit' : 'Enter' }} Results
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Title --}}
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white flex items-center gap-3 tracking-tight">

                <span>{{ $labRequest->result ? 'Edit' : 'Enter' }} Lab Results</span>
            </h1>
           <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-2xl">
                Record analysis data, comments, and attachments for the requested test.
            </p>
        </div>
    </div>

    <section id="enter-results-section">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

            {{-- Request Details Header --}}
            <div class="p-6 lg:p-8 bg-slate-50/50 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                    <x-heroicon-m-information-circle class="w-5 h-5" />
                    Request Information
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-8 gap-y-8">
                    {{-- Patient --}}
                    <div class="group">
                        <div class="flex items-center gap-3 mb-1">
                            <div class="p-2 bg-white dark:bg-gray-700 rounded-lg shadow-sm ring-1 ring-gray-900/5">
                                <x-heroicon-m-user class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                            </div>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Patient</span>
                        </div>
                        <p class="text-base font-bold text-gray-900 dark:text-white pl-11">
                            {{ optional($labRequest->patient)->first_name ?? '—' }} {{ optional($labRequest->patient)->last_name ?? '' }}
                        </p>
                        <p class="text-xs text-gray-500 pl-11">{{ optional($labRequest->patient)->patient_uid }}</p>
                    </div>

                    {{-- Test --}}
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <div class="p-2 bg-white dark:bg-gray-700 rounded-lg shadow-sm ring-1 ring-gray-900/5">
                                <x-heroicon-m-clipboard-document-check class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                            </div>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Test Name</span>
                        </div>
                        <p class="text-base font-bold text-gray-900 dark:text-white pl-11">
                            {{ optional($labRequest->testDefinition)->test_name ?? '—' }}
                        </p>
                    </div>

                    {{-- Doctor --}}
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <div class="p-2 bg-white dark:bg-gray-700 rounded-lg shadow-sm ring-1 ring-gray-900/5">
                                <x-heroicon-m-academic-cap class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                            </div>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Requested By</span>
                        </div>
                        <p class="text-base font-bold text-gray-900 dark:text-white pl-11">
                            Dr. {{ optional($labRequest->doctor)->name ?? '—' }}
                        </p>
                    </div>

                    {{-- Reason --}}
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <div class="p-2 bg-white dark:bg-gray-700 rounded-lg shadow-sm ring-1 ring-gray-900/5">
                                <x-heroicon-m-chat-bubble-left-ellipsis class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                            </div>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Reason</span>
                        </div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 pl-11 line-clamp-2">
                            {{ $labRequest->reason_for_test ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Form Section --}}
            <form wire:submit.prevent="saveResults" class="p-6 lg:p-8 space-y-8"
                  x-data="{ isUploading: false, progress: 0 }"
                  x-on:livewire-upload-start="isUploading = true"
                  x-on:livewire-upload-finish="isUploading = false"
                  x-on:livewire-upload-error="isUploading = false"
                  x-on:livewire-upload-progress="progress = $event.detail.progress">

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Results Text --}}
                    <div class="space-y-2">
                        <label for="results_text" class="block text-sm font-semibold text-gray-900 dark:text-white">
                            Diagnostic Results <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <textarea id="results_text" wire:model.defer="results_text" rows="8"
                                      placeholder="Enter detailed test results here..."
                                      class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-shadow duration-200 dark:text-white placeholder-gray-400"></textarea>
                            <div class="absolute bottom-3 right-3 text-gray-400 pointer-events-none">
                                <x-heroicon-o-pencil-square class="w-5 h-5" />
                            </div>
                        </div>
                        @error('results_text') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Comments Text --}}
                    <div class="space-y-2">
                        <label for="analysis_comments" class="block text-sm font-semibold text-gray-900 dark:text-white">
                            Technician's Comments
                        </label>
                        <div class="relative">
                            <textarea id="analysis_comments" wire:model.defer="analysis_comments" rows="8"
                                      placeholder="Optional notes regarding the analysis process..."
                                      class="block w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-shadow duration-200 dark:text-white placeholder-gray-400"></textarea>
                            <div class="absolute bottom-3 right-3 text-gray-400 pointer-events-none">
                                <x-heroicon-o-chat-bubble-bottom-center-text class="w-5 h-5" />
                            </div>
                        </div>
                        @error('analysis_comments') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- File Upload Section --}}
                <div class="border-t border-gray-100 dark:border-gray-700 pt-8">
                    <label class="block text-sm font-semibold text-gray-900 dark:text-white mb-4">
                        Attachments & Reports
                    </label>

                    <div class="flex flex-col md:flex-row gap-6">
                        {{-- Drop Zone --}}
                        <div class="w-full md:w-1/2">
                            <div class="flex justify-center rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 px-6 py-10 hover:border-indigo-500 dark:hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/10 transition-all duration-200 group relative">
                                <div class="text-center space-y-2">
                                    <div class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-500 group-hover:text-indigo-500 transition-colors">
                                        <x-heroicon-o-cloud-arrow-up class="h-12 w-12" />
                                    </div>
                                    <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                                        <label for="file-upload" class="relative cursor-pointer rounded-md font-semibold text-indigo-600 dark:text-indigo-400 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                            <span>Upload a file</span>
                                            <input id="file-upload" wire:model="attachments" type="file" multiple class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">PDF, PNG, JPG up to 10MB</p>
                                </div>

                                {{-- Upload Progress Overlay --}}
                                <div x-show="isUploading" class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 flex flex-col items-center justify-center rounded-xl transition-opacity">
                                    <div class="w-2/3 bg-gray-200 rounded-full h-2.5 dark:bg-gray-700 mb-2">
                                        <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300" :style="`width: ${progress}%`"></div>
                                    </div>
                                    <span class="text-sm text-indigo-600 font-medium">Uploading...</span>
                                </div>
                            </div>
                            @error('attachments.*') <p class="text-red-600 text-xs mt-2">{{ $message }}</p> @enderror
                        </div>

                        {{-- File Lists --}}
                        <div class="w-full md:w-1/2 space-y-6">
                            @if ($attachments)
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Ready to Upload</h4>
                                    <ul class="space-y-2">
                                        @foreach($attachments as $file)
                                            <li class="flex items-center justify-between p-3 bg-indigo-50 dark:bg-indigo-900/20 rounded-lg border border-indigo-100 dark:border-indigo-800">
                                                <div class="flex items-center gap-3 overflow-hidden">
                                                    <x-heroicon-s-document class="h-5 w-5 text-indigo-500 flex-shrink-0"/>
                                                    <span class="text-sm font-medium text-indigo-900 dark:text-indigo-200 truncate">{{ $file->getClientOriginalName() }}</span>
                                                </div>
                                                <span class="text-xs text-indigo-400 font-mono whitespace-nowrap">{{ round($file->getSize() / 1024, 1) }} KB</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if($existingAttachments->count())
                                <div>
                                    <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Existing Files</h4>
                                    <ul class="space-y-2">
                                        @foreach($existingAttachments as $file)
                                            <li class="flex items-center justify-between p-3 bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-indigo-300 transition-colors group">
                                                <div class="flex items-center gap-3 overflow-hidden">
                                                    <x-heroicon-s-document-check class="h-5 w-5 text-green-500 flex-shrink-0"/>
                                                    <span class="text-sm text-gray-700 dark:text-gray-200 truncate">{{ basename($file->file_path) }}</span>
                                                </div>
                                                <a href="#" class="text-xs font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    Download
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                    <a href="{{ route('lab-technician.test-requests') }}"
                       class="rounded-lg bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-semibold text-gray-900 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all">
                        Cancel
                    </a>

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled"
                            wire:target="saveResults, attachments">

                        <div wire:loading wire:target="saveResults, attachments">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>

                        <span wire:loading.remove wire:target="saveResults, attachments" class="flex items-center gap-2">
                            <x-heroicon-m-check class="w-5 h-5" />
                            Save Results
                        </span>
                        <span wire:loading wire:target="saveResults, attachments">Processing...</span>
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
