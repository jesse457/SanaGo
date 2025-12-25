<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300 pb-20">

    {{-- 1. ENHANCED STICKY HEADER --}}
    {{-- Removed lg:top-10 to prevent weird gaps on desktop. Sticky at top-0. --}}
    <header class="sticky top-0 z-20 bg-white/90 dark:bg-gray-900/90 backdrop-blur-md border-b border-slate-200 dark:border-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 min-w-0">
                <a href="{{ route('landlord.feedbacks') }}"
                   class="p-2 -ml-2 rounded-lg hover:bg-slate-100 dark:hover:bg-gray-800 transition-colors text-slate-500"
                   title="Back to List">
                    <x-heroicon-m-arrow-left class="w-5 h-5" />
                </a>
                <div class="min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Ticket #{{ $feedback->id }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $feedback->status === 'open' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10' : 'bg-slate-100 text-slate-600' }}">
                            {{ $feedback->status }}
                        </span>
                    </div>
                    <h2 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white truncate">
                        {{ $feedback->subject }}
                    </h2>
                </div>
            </div>

            {{-- Quick Action for Mobile (Status) --}}
            <div class="lg:hidden">
                <button @click="$dispatch('open-sidebar')" class="p-2 bg-slate-100 dark:bg-gray-800 rounded-lg">
                    <x-heroicon-o-adjustments-horizontal class="w-5 h-5" />
                </button>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- 2. LEFT COLUMN: Conversation (8 Cols) --}}
            <div class="lg:col-span-8 space-y-8">

                {{-- REPLY EDITOR --}}
                <div x-data="{ tab: 'reply' }" class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                    <div class="flex items-center justify-between p-2 border-b border-slate-100 dark:border-gray-800 bg-slate-50/50 dark:bg-gray-950/50">
                        <div class="flex gap-1">
                            <button @click="tab = 'reply'"
                                :class="tab === 'reply' ? 'bg-white dark:bg-gray-800 shadow-sm text-indigo-600 dark:text-indigo-400' : 'text-slate-500'"
                                class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                                <x-heroicon-s-arrow-turn-down-left class="w-3.5 h-3.5"/> Reply
                            </button>
                            <button @click="tab = 'note'"
                                :class="tab === 'note' ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 ring-1 ring-amber-200 dark:ring-amber-800' : 'text-slate-500'"
                                class="px-4 py-2 rounded-lg text-xs font-bold transition-all flex items-center gap-2">
                                <x-heroicon-s-pencil-square class="w-3.5 h-3.5"/> Private Note
                            </button>
                        </div>
                    </div>

                    <form wire:submit="sendResponse" class="p-4">
                        <div x-show="tab === 'reply'" x-transition>
                            <textarea wire:model="response" rows="4"
                                class="block w-full rounded-xl border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm p-4 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all"
                                placeholder="Write your message..."></textarea>

                            <div class="mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-500 cursor-pointer">
                                    <input type="checkbox" wire:model="sendEmail" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    Notify user via email
                                </label>
                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-indigo-500/20 transition-all active:scale-95">
                                    <span>Send Response</span>
                                    <x-heroicon-s-paper-airplane class="w-4 h-4"/>
                                </button>
                            </div>
                        </div>

                        <div x-show="tab === 'note'" x-cloak x-transition>
                            <textarea wire:model="internalNote" rows="3"
                                class="block w-full rounded-xl border-amber-200 dark:border-amber-900/50 bg-amber-50/30 dark:bg-amber-900/10 text-sm p-4 text-amber-900 dark:text-amber-200 focus:ring-0"
                                placeholder="Visible only to administrators..."></textarea>
                            <div class="mt-4 flex justify-end">
                                <button type="button" class="bg-amber-100 hover:bg-amber-200 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 px-5 py-2 rounded-xl text-xs font-bold transition-all">
                                    Save Internal Note
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- TIMELINE --}}
                <div class="relative space-y-0">
                    {{-- Vertical Line --}}
                    <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-slate-200 dark:bg-gray-800"></div>

                    {{-- Original Message --}}
                    <div class="relative pl-12 pb-10">
                        <div class="absolute left-0 top-0 h-10 w-10 rounded-full bg-slate-100 dark:bg-gray-800 border-4 border-slate-50 dark:border-gray-950 flex items-center justify-center z-10">
                            <span class="text-xs font-bold text-slate-600 dark:text-slate-400">{{ substr($feedback->user->name, 0, 1) }}</span>
                        </div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-bold text-slate-900 dark:text-white text-sm">{{ $feedback->user->name }}</span>
                            <span class="text-[11px] text-slate-400">{{ $feedback->created_at->format('M d, Y • h:i A') }}</span>
                        </div>
                        <div class="bg-white dark:bg-gray-900 rounded-2xl rounded-tl-none border border-slate-200 dark:border-gray-800 p-5 shadow-sm">
                            <div class="prose prose-sm prose-slate dark:prose-invert max-w-none">
                                {!! nl2br(e($feedback->message)) !!}
                            </div>
                        </div>
                    </div>

                    {{-- Response Example --}}
                    <div class="relative pl-12">
                        <div class="absolute left-0 top-0 h-10 w-10 rounded-full bg-indigo-600 border-4 border-slate-50 dark:border-gray-950 flex items-center justify-center z-10">
                            <x-heroicon-s-user class="w-5 h-5 text-white" />
                        </div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-bold text-slate-900 dark:text-white text-sm">You (Support)</span>
                            <span class="text-[11px] text-slate-400">2 hours ago</span>
                        </div>
                        <div class="bg-indigo-50/50 dark:bg-indigo-900/10 rounded-2xl rounded-tl-none border border-indigo-100 dark:border-indigo-800/50 p-5 shadow-sm">
                            <p class="text-sm text-slate-700 dark:text-slate-300">Thank you for reporting this. We have assigned a technician to check the issue.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. RIGHT COLUMN: Meta Info (4 Cols) --}}
            {{-- Added lg:sticky so it stays visible while scrolling conversation --}}
            <aside class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">

                {{-- Ticket Details Card --}}
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-gray-800 bg-slate-50/50 dark:bg-gray-950/50 flex items-center justify-between">
                        <h3 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-widest">Properties</h3>
                        <x-heroicon-o-information-circle class="w-4 h-4 text-slate-400" />
                    </div>

                    <div class="p-5 space-y-5">
                        {{-- Status Select --}}
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Status</label>
                            <select wire:model.live="status"
                                class="w-full rounded-xl border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-800 text-sm font-bold focus:ring-indigo-500 py-2.5">
                                <option value="open">Open</option>
                                <option value="pending">Pending</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>

                        {{-- Priority --}}
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Priority</label>
                            <div class="grid grid-cols-3 gap-2">
                                <button class="py-2 rounded-lg text-xs font-bold border border-slate-200 bg-slate-50 text-slate-600 hover:bg-white transition-colors">Low</button>
                                <button class="py-2 rounded-lg text-xs font-bold border border-amber-200 bg-amber-50 text-amber-700">Mid</button>
                                <button class="py-2 rounded-lg text-xs font-bold border border-rose-200 bg-rose-50 text-rose-700 ring-2 ring-rose-500/10">High</button>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 dark:border-gray-800">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-3">Requester</label>
                            <div class="flex items-center gap-3 bg-slate-50 dark:bg-gray-800/50 p-3 rounded-xl">
                                <div class="h-10 w-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold">
                                    {{ substr($feedback->tenant->name, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $feedback->tenant->name }}</p>
                                    <p class="text-[11px] text-slate-500 truncate">{{ $feedback->user->email }}</p>
                                </div>
                            </div>
                            <a href="#" class="mt-3 inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                                <span>View History</span>
                                <x-heroicon-m-chevron-right class="w-3 h-3"/>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Helpful Tips or Stats --}}
                <div class="bg-indigo-600 rounded-2xl p-5 text-white shadow-lg shadow-indigo-500/20">
                    <p class="text-xs font-bold opacity-80 uppercase tracking-widest mb-2">Average Response</p>
                    <p class="text-2xl font-bold">2.4 Hours</p>
                    <p class="text-[11px] opacity-70 mt-1">Keep it up! Fast responses increase tenant satisfaction.</p>
                </div>
            </aside>
        </div>
    </div>
</main>
