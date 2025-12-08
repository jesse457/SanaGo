<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4">
                <div class="flex flex-col gap-2">
                    {{-- Breadcrumbs --}}
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('admin.dashboard') }}" wire:navigate
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    {{ __('admin.home') }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <a href="{{ route('admin.feedback-history') }}" wire:navigate
                                        class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        {{ __('admin.feedbacks_bar') }}
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">{{ __('admin.submit_feedback_breadcrumb') }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            {{ __('admin.submit_feedback_title') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                            {{ __('admin.submit_feedback_description') }}
                        </p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="p-4 sm:p-6 pb-20">
            <div
                class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">

                {{-- Card Header --}}
                <div class="px-6 py-5 border-b border-slate-100 dark:border-gray-800 flex items-center justify-between bg-slate-50/50 dark:bg-gray-800/30">
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ __('admin.form_header_title') }}</h3>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('admin.form_header_description') }}</p>
                    </div>
                    <span
                        class="hidden sm:inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-900/30 px-3 py-1 text-xs font-bold text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                        {{ __('admin.form_header_app_name') }}
                    </span>
                </div>

                {{-- Form --}}
                <form wire:submit="submit" class="p-6 sm:p-8 space-y-8">

                    {{-- Section 1: Details --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        {{-- Subject --}}
                        <div class="space-y-1.5">
                            <label for="subject" class="block text-sm font-bold text-slate-700 dark:text-slate-300">
                                {{ __('admin.label_subject') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input id="subject" type="text" wire:model.defer="subject"
                                    placeholder="{{ __('admin.placeholder_subject') }}"
                                    class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2.5 px-4 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all duration-200" />
                            </div>
                            @error('subject')
                                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div class="space-y-1.5">
                            <label for="category" class="block text-sm font-bold text-slate-700 dark:text-slate-300">
                                {{ __('admin.label_category') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="category" wire:model.defer="category"
                                    class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2.5 px-4 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all duration-200">
                                    <option value="dashboard">{{ __('admin.option_dashboard') }}</option>
                                    <option value="shift-management">{{ __('admin.option_shift_management') }}</option>
                                    <option value="create-shifts">{{ __('admin.option_create_shifts') }}</option>
                                    <option value="revenue-report">{{ __('admin.option_revenue_report') }}</option>
                                    <option value="setings">{{ __('admin.option_settings') }}</option>
                                    <option value="user-activities">{{ __('admin.option_user_activities') }}</option>
                                    <option value="user-management">{{ __('admin.option_user_management') }}</option>
                                    <option value="create-new-user">{{ __('admin.option_create_new_user') }}</option>
                                </select>
                            </div>
                            @error('category')
                                <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Message --}}
                    <div class="space-y-1.5">
                        <label for="message" class="block text-sm font-bold text-slate-700 dark:text-slate-300">
                            {{ __('admin.label_message') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea id="message" rows="6" wire:model.defer="message"
                            placeholder="{{ __('admin.placeholder_message') }}"
                            class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all duration-200 resize-none"></textarea>

                        <div class="flex justify-between items-start mt-1">
                            @error('message')
                                <p class="text-xs text-red-500 font-medium">{{ $message }}</p>
                            @else
                                <span></span> {{-- Spacer --}}
                            @enderror
                            <p class="text-xs text-slate-400 dark:text-slate-500 text-right">
                                {{ __('admin.message_tip') }}
                            </p>
                        </div>
                    </div>

                    {{-- Optional File Upload (Commented out in original, kept commented but styled) --}}
                    {{--
                    <div class="space-y-1.5">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Add a picture or file (optional)</label>
                        <div class="mt-1 border-2 border-dashed border-slate-300 dark:border-gray-700 rounded-xl p-6 text-center hover:bg-slate-50 dark:hover:bg-gray-800/50 transition-colors">
                             <!-- Upload UI Here -->
                        </div>
                    </div>
                    --}}

                    {{-- Form Footer --}}
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 pt-6 border-t border-slate-100 dark:border-gray-800">
                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            {{ __('admin.disclaimer_prefix') }}
                            <a href="#" class="text-blue-600 hover:underline font-medium">{{ __('admin.disclaimer_rules') }}</a>
                            {{ __('admin.disclaimer_and') }}
                            <a href="#" class="text-blue-600 hover:underline font-medium">{{ __('admin.disclaimer_privacy_policy') }}</a>.
                        </div>

                        <div class="flex flex-col-reverse sm:flex-row gap-3 w-full sm:w-auto">
                            <button type="button"
                                wire:click="reset(['subject', 'category', 'department', 'message', 'attachments'])"
                                class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 text-slate-700 dark:text-slate-300 bg-white dark:bg-gray-800 hover:bg-slate-50 dark:hover:bg-gray-700 text-sm font-bold shadow-sm transition-all duration-200">
                                {{ __('admin.button_start_over') }}
                            </button>

                            <button type="submit"
                                class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-2.5 rounded-xl text-white bg-blue-600 hover:bg-blue-700 shadow-md hover:shadow-lg text-sm font-bold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-offset-gray-900 disabled:opacity-50 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled" wire:target="submit">
                                <span wire:loading.remove wire:target="submit" class="flex items-center gap-2">
                                    <x-heroicon-s-paper-airplane class="w-4 h-4 -rotate-45" />
                                    {{ __('admin.button_send') }}
                                </span>
                                <span wire:loading wire:target="submit" class="flex items-center gap-2">
                                    <x-heroicon-o-arrow-path class="w-4 h-4 animate-spin" />
                                    {{ __('admin.button_sending') }}
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
