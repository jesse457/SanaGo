<div class=" p-4 md:p-8 max-w-full mx-auto space-y-4 p-2 md:p-4  min-h-screen font-sans">

    {{-- Breadcrumb --}}
    <nav class="flex mb-6" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 text-sm text-slate-500 dark:text-slate-400">
            <li class="hover:text-indigo-600 transition"><a href="{{ route('landlord.dashboard') }}" wire:navigate>Dashboard</a></li>
            <li><x-heroicon-s-chevron-right class="w-3 h-3 text-slate-400" /></li>
            <li class="hover:text-indigo-600 transition"><a href="{{ route('landlord.feedbacks') }}" wire:navigate>Complaints</a></li>
            <li><x-heroicon-s-chevron-right class="w-3 h-3 text-slate-400" /></li>
            <li class="font-medium text-slate-900 dark:text-white truncate">Ticket #{{ $feedback->id }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT COLUMN: Context Card --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden">
                <div class="p-5 border-b border-slate-100 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-800">
                    <h3 class="font-bold text-slate-900 dark:text-white">Ticket Details</h3>
                </div>

                <div class="p-5 space-y-5">
                    {{-- User Info --}}
                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300 font-bold">
                            {{ substr($feedback->user->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $feedback->user->name }}</p>
                            <p class="text-xs text-slate-500 truncate">{{ $feedback->tenant->name ?? 'Unknown Tenant' }}</p>
                        </div>
                    </div>

                    <hr class="border-slate-100 dark:border-slate-700/60">

                    {{-- Metadata --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Status</span>
                             <div x-data="{ status: @entangle('status') }" class="relative">
                                <select wire:model.live="status" class="appearance-none w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-semibold rounded-lg py-2 pl-3 pr-8 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 cursor-pointer">
                                    <option value="open">Open</option>
                                    <option value="pending">Pending</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                                    <x-heroicon-s-chevron-down class="w-3 h-3" />
                                </div>
                            </div>
                        </div>
                        <div>
                            <span class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Priority</span>
                            <span class="inline-flex items-center px-2.5 py-1.5 rounded-lg text-xs font-bold
                                {{ match($feedback->priority) {
                                    'urgent', 'high' => 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400',
                                    'medium' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                    default => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300'
                                } }}">
                                {{ ucfirst($feedback->priority) }}
                            </span>
                        </div>
                        <div class="col-span-2">
                             <span class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Category</span>
                             <div class="text-sm font-medium text-slate-800 dark:text-slate-200 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                                {{ ucfirst($feedback->category) }}
                             </div>
                        </div>
                        <div class="col-span-2">
                             <span class="block text-xs font-medium text-slate-500 uppercase tracking-wider mb-1">Submitted</span>
                             <div class="text-sm text-slate-800 dark:text-slate-200">
                                {{ $feedback->created_at->format('M d, Y \a\t h:i A') }}
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Conversation --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Original Message Bubble --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 p-6 relative">
                <div class="absolute top-0 left-0 w-1 h-full bg-slate-200 dark:bg-slate-700 rounded-l-2xl"></div>

                <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">{{ $feedback->subject }}</h2>

                <div class="prose prose-sm prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-300">
                    {!! nl2br(e($feedback->message)) !!}
                </div>
            </div>

            {{-- Response Editor --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden ring-1 ring-indigo-500/10 dark:ring-indigo-500/20">
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-800 flex justify-between items-center">
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">Post a Reply</h3>

                    <div class="flex items-center gap-4">
                        <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-600 dark:text-slate-400 cursor-pointer">
                            <input type="checkbox" wire:model="sendEmail" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            Email
                        </label>
                        <label class="inline-flex items-center gap-2 text-xs font-medium text-slate-600 dark:text-slate-400 cursor-pointer">
                            <input type="checkbox" wire:model="sendInApp" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            In-App
                        </label>
                    </div>
                </div>

                <form wire:submit="sendResponse" class="p-6 space-y-4">
                    <div class="relative">
                        <textarea wire:model="response" rows="6"
                            class="block w-full rounded-xl border-0 bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-white shadow-inner ring-1 ring-inset ring-slate-200 dark:ring-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 resize-y p-4"
                            placeholder="Write your response here..."></textarea>
                        @error('response') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="text-xs text-slate-400">
                            Responding to <span class="font-semibold">{{ $feedback->user->name }}</span>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" wire:click="saveDraft" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700 transition">
                                Save Draft
                            </button>
                            <button type="submit" wire:loading.attr="disabled" class="px-6 py-2 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 shadow-md shadow-indigo-500/20 disabled:opacity-70 disabled:cursor-not-allowed transition flex items-center gap-2">
                                <span wire:loading.remove>Send Response</span>
                                <span wire:loading class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Sending...
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
