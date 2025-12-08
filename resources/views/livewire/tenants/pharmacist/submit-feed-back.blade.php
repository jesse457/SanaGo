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
                                    {{ __('pharmacist.submit_feedback_page.breadcrumb_home') }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <a href="{{ route('lab-technician.feedbacks') }}" wire:navigate
                                        class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        {{ __('pharmacist.submit_feedback_page.breadcrumb_feedbacks') }}
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">{{ __('pharmacist.submit_feedback_page.breadcrumb_submit_feedback') }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            {{ __('pharmacist.submit_feedback_page.submit_feedback_title') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                            {{ __('pharmacist.submit_feedback_page.submit_feedback_description') }}
                        </p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="p-4 sm:p-6 pb-20">
            <div class="max-w-4xl mx-auto">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">

                    {{-- Card Header --}}
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-gray-800 flex items-center justify-between bg-slate-50/50 dark:bg-gray-800/30">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ __('pharmacist.submit_feedback_page.help_us_make_it_better') }}</h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('pharmacist.submit_feedback_page.help_us_description') }}</p>
                        </div>
                        <span class="hidden sm:inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-900/30 px-3 py-1 text-xs font-bold text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800">SanaGo</span>
                    </div>

                    {{-- Form --}}
                    <form wire:submit="submit" class="p-6 sm:p-8 space-y-8" >

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Subject --}}
                            <div class="space-y-1.5">
                                <label for="subject" class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('pharmacist.submit_feedback_page.subject_label') }} <span class="text-red-500">*</span></label>
                                <input id="subject" type="text" wire:model.defer="subject"
                                    placeholder="{{ __('pharmacist.submit_feedback_page.subject_placeholder') }}"
                                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white py-2.5 px-3" />
                                @error('subject')
                                    <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Category --}}
                            <div class="space-y-1.5">
                                <label for="category" class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('pharmacist.submit_feedback_page.category_label') }} <span class="text-red-500">*</span></label>
                                <select id="category" wire:model.defer="category"
                                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white py-2.5 px-3">
                                    <option value="dashboard">{{ __('pharmacist.submit_feedback_page.category_dashboard') }}</option>
                                    <option value="dispense-medication">{{ __('pharmacist.submit_feedback_page.category_dispense_medication') }}</option>
                                    <option value="manage-drugs">{{ __('pharmacist.submit_feedback_page.category_manage_drugs') }}</option>
                                    <option value="create-new-drugs">{{ __('pharmacist.submit_feedback_page.category_create_new_drugs') }}</option>
                                    <option value="profile">{{ __('pharmacist.submit_feedback_page.category_profile') }}</option>
                                </select>
                                @error('category')
                                    <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Message --}}
                        <div class="space-y-1.5">
                            <label for="message" class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('pharmacist.submit_feedback_page.message_label') }} <span class="text-red-500">*</span></label>
                            <textarea id="message" rows="6" wire:model.defer="message"
                                placeholder="{{ __('pharmacist.submit_feedback_page.message_placeholder') }}"
                                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white p-3"></textarea>

                            <div class="flex justify-between items-start mt-1">
                                @error('message')
                                    <p class="text-xs text-red-500 font-medium">{{ $message }}</p>
                                @else
                                    <span></span> {{-- Spacer --}}
                                @enderror
                                <p class="text-xs text-slate-500 dark:text-slate-400 text-right">
                                    <b>{{ __('pharmacist.submit_feedback_page.message_tip_label') }}</b> {{ __('pharmacist.submit_feedback_page.message_tip_text') }}
                                </p>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-slate-100 dark:border-gray-800">
                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                {!! __('pharmacist.submit_feedback_page.agreement_text', [
                                    'rules' => '<a href="#" class="text-blue-600 hover:underline font-medium">' . __('pharmacist.submit_feedback_page.rules') . '</a>',
                                    'privacy_policy' => '<a href="#" class="text-blue-600 hover:underline font-medium">' . __('pharmacist.submit_feedback_page.privacy_policy') . '</a>'
                                ]) !!}
                            </div>
                            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full sm:w-auto">
                                <button type="button"
                                    wire:click="clearData()"
                                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 text-slate-700 dark:text-slate-200 bg-white dark:bg-gray-800 hover:bg-slate-50 dark:hover:bg-gray-700 text-sm font-bold transition-all duration-200">
                                    {{ __('pharmacist.submit_feedback_page.start_over_button') }}
                                </button>
                                <button type="submit"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-white bg-blue-600 hover:bg-blue-700 shadow-md hover:shadow-lg text-sm font-bold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                    wire:loading.attr="disabled" wire:target="submit">
                                    <span wire:loading.remove wire:target="submit" class="flex items-center gap-2">
                                        <x-heroicon-s-paper-airplane class="w-4 h-4 -rotate-45" />
                                        {{ __('pharmacist.submit_feedback_page.send_button') }}
                                    </span>
                                    <span wire:loading wire:target="submit" class="flex items-center gap-2">
                                        <x-heroicon-s-arrow-path class="w-4 h-4 animate-spin" />
                                        {{ __('pharmacist.submit_feedback_page.sending_text') }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
