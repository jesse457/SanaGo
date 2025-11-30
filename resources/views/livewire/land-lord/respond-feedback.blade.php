<div class="flex-1 p-4 md:p-6 lg:ml-64 bg-gray-100 dark:bg-gray-900 overflow-y-auto min-h-screen">
    {{-- Breadcrumbs & Header --}}
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('landlord.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" />
                        {{ __('ui.home') }}
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <a href="{{ route('dashboard') }}" wire:navigate
                            class="ms-1 text-sm font-medium text-gray-700 md:ms-2 dark:text-gray-300 dark:hover:text-gray-200">
                            {{ __('ui.feedbacks') }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm font-medium text-gray-400 md:ms-2 dark:text-gray-500">
                            {{ __('ui.respond_feedback') }}
                        </span>
                    </div>
                </li>
            </ol>
        </nav>
        <h2 class="text-3xl font-bold text-slate-800 dark:text-white mt-4">{{ __('ui.complaints_header') }}</h2>
        <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
            {{ __('ui.complaints_header_subtext') }}
        </p>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Panel: Ticket Details --}}
        <section
            class="lg:col-span-1 bg-white dark:bg-gray-800 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ __('ui.tenant_details') }}</h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('ui.key_info_context') }}</p>
            </div>

            <div class="p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ __('ui.admin_name') }}</span>
                    <span
                        class="text-sm font-medium text-gray-900 dark:text-gray-100">#{{ $feedback->user->name }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ __('ui.created_at') }}</span>
                    <span
                        class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $feedback->created_at->format('M d, Y') }}</span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ __('ui.category') }}</span>
                    <span
                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        {{ [
                            'complaint' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-200',
                            'suggestion' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-200',
                            'issue' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-200',
                            'general' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
                            'other' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
                        ][$feedback->category] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200' }}">
                        {{ ucfirst($feedback->category) }}
                    </span>
                </div>

                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">{{ __('ui.priority') }}</span>
                    <span
                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                        {{ [
                            'low' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
                            'normal' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-200',
                            'high' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/20 dark:text-amber-200',
                            'urgent' => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-200',
                        ][$feedback->priority] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200' }}">
                        {{ ucfirst($feedback->priority) }}
                    </span>
                </div>

                <div>
                    <span class="block text-sm text-gray-500">{{ __('ui.subject') }}</span>
                    <p class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $feedback->subject }}</p>
                </div>

                <div class="grid grid-cols-2 gap-3">

                    <div>
                        <span class="block text-xs text-gray-500">{{ __('ui.tenant') }}</span>
                        <p class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $feedback->tenant->name ?? 'N/A' }}
                        </p>
                    </div>
                </div>

                <div>
                    <span class="block text-sm text-gray-500">{{ __('ui.original_message') }}</span>
                    <pre
                        class="mt-1 bg-gray-50 dark:bg-gray-700/60 rounded-lg p-3 text-sm text-gray-800 dark:text-gray-100 whitespace-pre-wrap">{{ $feedback->message }}</pre>
                </div>
            </div>

            {{-- Status and Assignee Panel --}}
            <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-700 space-y-4" x-data="{ status: '{{ $status }}' }">
                <div x-data="{ status: @entangle('status') }">
                    <span class="block text-sm text-gray-500">{{ __('ui.status') }}</span>

                    <div class="mt-2 grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach (['open', 'pending', 'resolved', 'closed'] as $s)
                            <button type="button" @click="status = '{{ $s }}'"
                                class="px-3 py-2 rounded-lg text-xs border transition duration-200"
                                :class="{
                                    'bg-indigo-600 text-white border-indigo-600 shadow-md': status === '{{ $s }}',
                                    'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700': status !== '{{ $s }}'
                                }">
                                {{ ucfirst(__('ui.' . $s)) }}
                            </button>
                        @endforeach
                    </div>

                    {{-- hidden input kept for progressive enhancement / forms; not necessary with @entangle --}}
                    <input type="hidden" wire:model="status">
                </div>


            </div>
        </section>

        {{-- Right Panel: Response --}}
        <section
            class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ __('ui.respond') }}</h3>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('ui.respond_subtext') }}
                    </p>
                </div>
                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                        <input type="checkbox"
                            class="rounded border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-indigo-600 focus:ring-indigo-500"
                            wire:model.live="sendEmail">
                        {{ __('ui.email') }}
                    </label>
                    <label class="inline-flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300">
                        <input type="checkbox"
                            class="rounded border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-indigo-600 focus:ring-indigo-500"
                            wire:model.live="sendInApp">
                        {{ __('ui.in_app') }}
                    </label>
                </div>
            </div>

            <form wire:submit="sendResponse">
                <div class="p-5 space-y-4">
                    <div
                        class="rounded-lg border border-gray-200 dark:border-gray-700 p-3 bg-gray-50 dark:bg-gray-700/60">
                        <p class="text-xs text-gray-600 dark:text-gray-300">{{ __('ui.to_label') }}: {{ $feedback->tenant->name ?? 'N/A' }}
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-300">{{ __('ui.subject') }}: {{ $feedback->subject }}</p>
                    </div>

                    <div>
                        <label for="response" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('ui.your_response') }}
                            </label>
                        <textarea id="response" rows="10" wire:model="response" placeholder="{{ __('ui.write_response_placeholder') }}"
                            class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        @error('response')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <button type="button" wire:click="saveDraft"
                            class="px-4 py-2 rounded-lg text-sm text-gray-700 dark:text-gray-200 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 transition duration-200"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveDraft">{{ __('ui.save_draft') }}</span>
                            <span wire:loading wire:target="saveDraft">{{ __('ui.saving') }}</span>
                        </button>
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-md transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="sendResponse">{{ __('ui.send_response') }}</span>
                            <span wire:loading wire:target="sendResponse" class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-5 h-5 inline-block animate-spin mr-2">
                                    <path fill-rule="evenodd"
                                        d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm4.876 4.31a.75.75 0 01-.75 0C15.021 6.362 13.513 6 12 6c-1.42 0-2.822.36-4.062 1.059a.75.75 0 11-.75-1.299C8.36 6.096 10.198 5.625 12 5.625s3.64.471 5.293 1.355a.75.75 0 010 1.299z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ __('ui.sending') }}
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </section>
    </div>
</div>
