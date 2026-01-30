<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4">
                <div class="flex flex-col gap-2">
                    {{-- Breadcrumbs --}}
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-1" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('lab-technician.dashboard') }}" wire:navigate
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    {{ __('Home') }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <a href="{{ route('lab-technician.test-requests') }}" wire:navigate
                                        class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        {{ __('Lab Requests') }}
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">{{ $labRequest->result ? __('Edit') : __('Enter') }} {{ __('Lab Results') }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            <span>{{ $labRequest->result ? __('Edit') : __('Enter') }} {{ __('Lab Results') }}</span>
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                            {{ __('Record analysis data, comments, and attachments for the requested test.') }}
                        </p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="p-4 sm:p-6 pb-20">

            <section id="enter-results-section" class="max-w-6xl mx-auto">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">

                    {{-- Request Details Header --}}
                    <div class="p-6 lg:p-8 bg-slate-50 dark:bg-gray-800/50 border-b border-slate-100 dark:border-gray-800">
                        <h3 class="text-base font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                            <x-heroicon-m-information-circle class="w-5 h-5" />
                            {{ __('Request Information') }} - {{ __('hello jesse') }}
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-x-8 gap-y-6">
                            {{-- Patient --}}
                            <div>
                                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Patient') }}</p>
                                <div class="flex items-center gap-2">
                                    <div class="p-2 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                                        <x-heroicon-m-user class="w-5 h-5 text-blue-500 dark:text-blue-400" />
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 dark:text-white">
                                            {{ optional($labRequest->patient)->first_name ?? '—' }} {{ optional($labRequest->patient)->last_name ?? '' }}
                                        </p>
                                        <p class="text-xs text-slate-500 font-mono">{{ optional($labRequest->patient)->patient_uid }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Test --}}
                            <div>
                                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Test Name') }}</p>
                                <div class="flex items-center gap-2">
                                    <div class="p-2 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                                        <x-heroicon-m-clipboard-document-check class="w-5 h-5 text-purple-500 dark:text-purple-400" />
                                    </div>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ optional($labRequest->testDefinition)->test_name ?? '—' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Doctor --}}
                            <div>
                                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Requested By') }}</p>
                                <div class="flex items-center gap-2">
                                    <div class="p-2 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                                        <x-heroicon-m-academic-cap class="w-5 h-5 text-emerald-500 dark:text-emerald-400" />
                                    </div>
                                    <p class="text-sm font-bold text-slate-900 dark:text-white">
                                        Dr. {{ optional($labRequest->doctor)->name ?? '—' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Reason --}}
                            <div>
                                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Reason') }}</p>
                                <div class="flex items-center gap-2">
                                    <div class="p-2 bg-white dark:bg-gray-700 rounded-lg shadow-sm">
                                        <x-heroicon-m-chat-bubble-left-ellipsis class="w-5 h-5 text-amber-500 dark:text-amber-400" />
                                    </div>
                                    <p class="text-sm font-medium text-slate-700 dark:text-slate-300 line-clamp-2">
                                        {{ $labRequest->reason_for_test ?? '—' }}
                                    </p>
                                </div>
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
                            <div class="space-y-1.5">
                                <label for="results_text" class="block text-sm font-bold text-slate-700 dark:text-white">
                                    {{ __('Diagnostic Results') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <textarea id="results_text" wire:model.defer="results_text" rows="8"
                                              placeholder="Enter detailed test results here..."
                                              class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-shadow duration-200 dark:text-white placeholder-gray-500 p-4"></textarea>
                                    <div class="absolute bottom-3 right-3 text-slate-400 pointer-events-none">
                                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                                    </div>
                                </div>
                                @error('results_text') <p class="text-red-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                            </div>

                            {{-- Comments Text --}}
                            <div class="space-y-1.5">
                                <label for="analysis_comments" class="block text-sm font-bold text-slate-700 dark:text-white">
                                    {{ __('Technician\'s Comments') }}
                                </label>
                                <div class="relative">
                                    <textarea id="analysis_comments" wire:model.defer="analysis_comments" rows="8"
                                              placeholder="Optional notes regarding the analysis process..."
                                              class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-shadow duration-200 dark:text-white placeholder-gray-500 p-4"></textarea>
                                    <div class="absolute bottom-3 right-3 text-slate-400 pointer-events-none">
                                        <x-heroicon-o-chat-bubble-bottom-center-text class="w-5 h-5" />
                                    </div>
                                </div>
                                @error('analysis_comments') <p class="text-red-600 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- File Upload Section --}}
                        <div class="border-t border-slate-100 dark:border-gray-800 pt-8">
                            <label class="block text-sm font-bold text-slate-700 dark:text-white mb-4">
                                {{ __('Attachments & Reports') }}
                            </label>

                            <div class="flex flex-col md:flex-row gap-6">
                                {{-- Drop Zone --}}
                                <div class="w-full md:w-1/2">
                                    <div class="flex justify-center rounded-xl border-2 border-dashed border-slate-300 dark:border-gray-700 px-6 py-10 hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-all duration-200 group relative">
                                        <div class="text-center space-y-2">
                                            <div class="mx-auto h-12 w-12 text-slate-300 dark:text-slate-600 group-hover:text-blue-500 transition-colors">
                                                <x-heroicon-o-cloud-arrow-up class="h-12 w-12" />
                                            </div>
                                            <div class="flex text-sm text-slate-600 dark:text-slate-400 justify-center">
                                                <label for="file-upload" class="relative cursor-pointer rounded-md font-bold text-blue-600 dark:text-blue-400 focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-600 focus-within:ring-offset-2 hover:text-blue-500">
                                                    <span>{{ __('Upload a file') }}</span>
                                                    <input id="file-upload" wire:model="attachments" type="file"  class="sr-only">
                                                </label>
                                                <p class="pl-1">{{ __('or drag and drop') }}</p>
                                            </div>
                                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('PDF, PNG, JPG up to 10MB') }}</p>
                                        </div>

                                        {{-- Upload Progress Overlay --}}
                                        <div x-show="isUploading" class="absolute inset-0 bg-white/80 dark:bg-gray-800/80 flex flex-col items-center justify-center rounded-xl transition-opacity">
                                            <div class="w-2/3 bg-slate-200 rounded-full h-2.5 dark:bg-gray-700 mb-2">
                                                <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-300" :style="`width: ${progress}%`"></div>
                                            </div>
                                            <span class="text-sm text-blue-600 font-medium">{{ __('Uploading...') }}</span>
                                        </div>
                                    </div>
                                    @error('attachments.*') <p class="text-red-600 text-xs font-medium mt-2">{{ $message }}</p> @enderror
                                </div>

                                {{-- File Lists --}}
                                <div class="w-full md:w-1/2 space-y-6">
                                    @if ($attachments)
                                        <div>
                                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">{{ __('Ready to Upload') }}</h4>
                                            <ul class="space-y-2">
                                                @foreach($attachments as $file)
                                                    <li class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                                                        <div class="flex items-center gap-3 overflow-hidden">
                                                            <x-heroicon-s-document class="h-5 w-5 text-blue-500 flex-shrink-0"/>
                                                            <span class="text-sm font-medium text-blue-900 dark:text-blue-200 truncate">{{ $file->getClientOriginalName() }}</span>
                                                        </div>
                                                        <span class="text-xs text-blue-400 font-mono whitespace-nowrap">{{ round($file->getSize() / 1024, 1) }} KB</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if($existingAttachments->count())
                                        <div>
                                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">{{ __('Existing Files') }}</h4>
                                            <ul class="space-y-2">
                                                @foreach($existingAttachments as $file)
                                                    <li class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 rounded-lg border border-slate-200 dark:border-gray-700 hover:border-emerald-300 transition-colors group">
                                                        <div class="flex items-center gap-3 overflow-hidden">
                                                            <x-heroicon-s-document-check class="h-5 w-5 text-emerald-500 flex-shrink-0"/>
                                                            <span class="text-sm text-slate-700 dark:text-slate-200 truncate">{{ basename($file->file_path) }}</span>
                                                        </div>
                                                        <a href="#" class="text-xs font-bold text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 opacity-0 group-hover:opacity-100 transition-opacity">
                                                            {{ __('Download') }}
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
                        <div class="mt-8 pt-6 border-t border-slate-100 dark:border-gray-800 flex justify-end gap-3">
                            <a href="{{ route('lab-technician.test-requests') }}"
                               class="rounded-xl bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-gray-700 hover:bg-slate-50 dark:hover:bg-gray-700 transition-all">
                                {{ __('Cancel') }}
                            </a>

                            <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                    wire:loading.attr="disabled"
                                    wire:target="saveResults, attachments">

                                <div wire:loading wire:target="saveResults, attachments">
                                    <x-heroicon-o-arrow-path class="animate-spin h-5 w-5 text-white" />
                                </div>

                                <span wire:loading.remove wire:target="saveResults, attachments" class="flex items-center gap-2">
                                    <x-heroicon-m-check class="w-5 h-5" />
                                    {{ __('Save Results') }}
                                </span>
                                <span wire:loading wire:target="saveResults, attachments">{{ __('Processing...') }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</main>
