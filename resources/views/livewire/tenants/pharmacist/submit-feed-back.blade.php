<div class="flex-1 p-4 md:p-6 lg:ml-64 bg-gray-100 dark:bg-gray-900 overflow-y-auto min-h-screen">
    <header class="mb-8 space-y-4 sm:space-y-0">
        <nav class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400">
            <a href="{{ route('lab-technician.dashboard') }}" wire:navigate
                class="flex items-center text-gray-700 hover:text-blue-700 dark:hover:text-gray-200">
                <x-heroicon-s-home class="w-4 h-4 mr-1" />
                {{ __('pharmacist.submit_feedback_page.breadcrumb_home') }}
            </a>
            <x-heroicon-s-chevron-right class="w-4 h-4 mx-1" />
            <a href="{{ route('lab-technician.feedbacks') }}" wire:navigate
                class="flex items-center text-gray-700 hover:text-blue-700 dark:hover:text-blue-200">
                {{ __('pharmacist.submit_feedback_page.breadcrumb_feedbacks') }}
            </a>
            <x-heroicon-s-chevron-right class="w-4 h-4 mx-1" />
            <span class="text-gray-400 ">{{ __('pharmacist.submit_feedback_page.breadcrumb_submit_feedback') }}</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white flex items-center gap-3">
                    <x-heroicon-s-chat-bubble-left-right class="w-8 h-8 text-indigo-500" />
                    {{ __('pharmacist.submit_feedback_page.submit_feedback_title') }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{ __('pharmacist.submit_feedback_page.submit_feedback_description') }}
                </p>
            </div>
        </div>
    </header>

    <div
        class="bg-white dark:bg-gray-800 shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 rounded-2xl overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('pharmacist.submit_feedback_page.help_us_make_it_better') }}</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('pharmacist.submit_feedback_page.help_us_description') }}</p>
            </div>
            <span
                class="inline-flex items-center rounded-full bg-gray-200 dark:bg-gray-700 px-3 py-1 text-xs font-medium text-gray-700 dark:text-gray-300">SanaGo</span>
        </div>



        <form wire:submit="submit" class="p-6 space-y-8" >

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('pharmacist.submit_feedback_page.subject_label') }}</label>
                    <input id="subject" type="text" wire:model.defer="subject"
                        placeholder="{{ __('pharmacist.submit_feedback_page.subject_placeholder') }}" class="form-input" />
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('pharmacist.submit_feedback_page.category_label') }}</label>
                    <select id="category" wire:model.defer="category" class="form-input">
                        <option value="dashboard">{{ __('pharmacist.submit_feedback_page.category_dashboard') }}</option>
                        <option value="dispense-medication">{{ __('pharmacist.submit_feedback_page.category_dispense_medication') }}</option>
                        <option value="manage-drugs">{{ __('pharmacist.submit_feedback_page.category_manage_drugs') }}</option>
                        <option value="create-new-drugs">{{ __('pharmacist.submit_feedback_page.category_create_new_drugs') }}</option>
                         <option value="profile">{{ __('pharmacist.submit_feedback_page.category_profile') }}</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>


            </div>



            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('pharmacist.submit_feedback_page.message_label') }}</label>
                <textarea id="message" rows="6" wire:model.defer="message"
                    placeholder="{{ __('pharmacist.submit_feedback_page.message_placeholder') }}"
                    class="form-input"></textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    <b>{{ __('pharmacist.submit_feedback_page.message_tip_label') }}</b> {{ __('pharmacist.submit_feedback_page.message_tip_text') }}
                </div>
            </div>

            {{-- <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Add a picture or file (optional)</label>
                <div class="mt-1 border-2 border-dashed rounded-lg p-6 text-center text-sm text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-600">
                    <input type="file" multiple wire:model="attachments" id="attachments" class="hidden">
                    <p class="font-medium text-gray-800 dark:text-gray-200">Drag files here</p>
                    <p class="mt-1">or</p>
                    <label for="attachments"
                        class="mt-2 inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 dark:text-indigo-200 dark:bg-indigo-700/50 dark:hover:bg-indigo-700 transition-all duration-200 cursor-pointer">
                        <x-heroicon-s-cloud-arrow-up class="w-4 h-4 mr-1"/>
                        Click to upload
                    </label>
                    <p class="mt-3 text-xs">Accepted: pictures, documents. Up to 5 files.</p>
                </div>

                <div wire:loading wire:target="attachments" class="mt-4 text-center text-sm text-indigo-600 dark:text-indigo-400">
                    Uploading files...
                </div>

                <div class="mt-4 space-y-2">
                    @if (!empty($attachments))
                        @foreach ($attachments as $index => $attachment)
                            <div class="flex items-center justify-between rounded-md border border-gray-200 dark:border-gray-700 px-4 py-3 text-sm transition-all duration-200">
                                <div class="flex items-center truncate">
                                    <x-heroicon-s-paper-clip class="w-5 h-5 text-gray-400 mr-2 flex-shrink-0"/>
                                    <div class="truncate">
                                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $attachment->getClientOriginalName() }}</span>
                                        <span class="ml-2 text-gray-500 text-xs">{{ round($attachment->getSize() / 1024, 2) }} KB</span>
                                    </div>
                                </div>
                                <button type="button" class="text-red-600 hover:text-red-700 ml-4 flex-shrink-0"
                                        wire:click="removeAttachment({{ $index }})">
                                    <x-heroicon-s-trash class="w-5 h-5"/>
                                </button>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div> --}}

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    {!! __('pharmacist.submit_feedback_page.agreement_text', [
                        'rules' => '<a href="#" class="text-indigo-600 hover:underline">' . __('pharmacist.submit_feedback_page.rules') . '</a>',
                        'privacy_policy' => '<a href="#" class="text-indigo-600 hover:underline">' . __('pharmacist.submit_feedback_page.privacy_policy') . '</a>'
                    ]) !!}
                </div>
                <div class="flex justify-end gap-3 w-full sm:w-auto">
                    <button type="button"
                        wire:click="clearData()"
                        class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                        {{ __('pharmacist.submit_feedback_page.start_over_button') }}
                    </button>
                    <button type="submit"
                        class="relative inline-flex items-center justify-center px-4 py-2 rounded-lg text-white bg-blue-600 hover:bg-blue-700 shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled" wire:target="submit">
                        <span wire:loading.remove wire:target="submit">
                            <x-heroicon-s-paper-airplane class="w-5 h-5 inline-block -rotate-45 mr-2" />
                            {{ __('pharmacist.submit_feedback_page.send_button') }}
                        </span>
                        <span wire:loading wire:target="submit" class="flex items-center">
                            <x-heroicon-s-arrow-path class="w-5 h-5 inline-block animate-spin mr-2" />
                            {{ __('pharmacist.submit_feedback_page.sending_text') }}
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
