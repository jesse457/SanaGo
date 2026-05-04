<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300 pb-20">

    {{-- STICKY HEADER --}}
  <header class="sticky top-0 lg:top-0 z-20 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 px-4 sm:px-6 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex-1 min-w-0 flex items-center gap-2">
                {{-- Mobile Back Button (Optional) --}}
                <a href="{{ route('landlord.dashboard') }}" class="md:hidden p-2 -ml-2 text-slate-400 hover:text-indigo-600">
                    <x-heroicon-m-chevron-left class="w-5 h-5" />
                </a>
                <h2 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight truncate">
                    Feedback
                </h2>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- 1. STATS OVERVIEW (Responsive Grid) --}}
        {{-- Stacks to 2 columns on mobile, 4 on desktop --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
            <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-slate-200 dark:border-gray-800 shadow-sm">
                <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-wider">Open</p>
                <p class="text-xl md:text-2xl font-bold text-slate-900 dark:text-white mt-1">{{ $feedbacks->where('status', 'open')->count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-slate-200 dark:border-gray-800 shadow-sm">
                <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-wider">Pending</p>
                <p class="text-xl md:text-2xl font-bold text-amber-600 dark:text-amber-500 mt-1">{{ $feedbacks->where('status', 'pending')->count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-slate-200 dark:border-gray-800 shadow-sm">
                <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-wider">Resolved</p>
                <p class="text-xl md:text-2xl font-bold text-emerald-600 dark:text-emerald-500 mt-1">{{ $feedbacks->where('status', 'resolved')->count() }}</p>
            </div>
            <div class="bg-white dark:bg-gray-900 p-4 rounded-xl border border-slate-200 dark:border-gray-800 shadow-sm">
                <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-wider">Urgent</p>
                <p class="text-xl md:text-2xl font-bold text-rose-600 dark:text-rose-500 mt-1">{{ $feedbacks->where('priority', 'urgent')->count() }}</p>
            </div>
        </div>

        {{-- 2. SEARCH & FILTERS (Stacked on Mobile) --}}
        <div class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400" />
                <input type="text" placeholder="Search tickets..."
                    class="w-full pl-10 pr-4 py-3 md:py-2.5 rounded-xl border border-slate-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm">
            </div>
            {{-- Horizontal scroll for filters on very small screens --}}
            <div class="flex gap-2 overflow-x-auto pb-1 md:pb-0 no-scrollbar">
                <select class="flex-none w-1/2 md:w-auto rounded-xl border border-slate-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-sm py-2.5 pl-3 pr-8 focus:ring-indigo-500 shadow-sm">
                    <option>Status: All</option>
                    <option>Open</option>
                    <option>Pending</option>
                    <option>Resolved</option>
                </select>
                <select class="flex-none w-1/2 md:w-auto rounded-xl border border-slate-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-sm py-2.5 pl-3 pr-8 focus:ring-indigo-500 shadow-sm">
                    <option>Priority: All</option>
                    <option>High</option>
                    <option>Medium</option>
                    <option>Low</option>
                </select>
            </div>
        </div>

        {{-- 3. CONTENT AREA --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">

            {{-- DESKTOP VIEW (Table) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-gray-800">
                    <thead class="bg-slate-50 dark:bg-gray-950/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Ticket</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Tenant</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Subject</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                            <th class="relative px-6 py-4"><span class="sr-only">View</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-gray-800">
                        @forelse ($feedbacks as $feedback)
                            <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer"
                                onclick="window.location='{{ route('landlord.respond-feedback', ['feedback' => $feedback->id]) }}'">
                                <td class="px-6 py-4 whitespace-nowrap"><span class="text-sm font-bold font-mono">#{{ $feedback->id }}</span></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs mr-3">
                                            {{ substr($feedback->tenant->name ?? 'T', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $feedback->tenant->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap"><span class="text-sm font-medium">{{ Str::limit($feedback->subject, 30) }}</span></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-bold capitalize
                                    {{ $feedback->priority == 'urgent' ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $feedback->priority }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $feedback->status == 'open' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        {{ ucfirst($feedback->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $feedback->created_at->format('M d') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <x-heroicon-s-chevron-right class="w-4 h-4 text-slate-400 group-hover:text-indigo-600" />
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-12 text-center text-slate-500">No tickets found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE VIEW (Cards) --}}
            {{-- Optimized for touch with larger padding --}}
            <div class="md:hidden divide-y divide-slate-100 dark:divide-gray-800">
                @forelse ($feedbacks as $feedback)
                    <div class="p-5 active:bg-slate-50 dark:active:bg-gray-800/50 transition-colors cursor-pointer"
                         onclick="window.location='{{ route('landlord.respond-feedback', ['feedback' => $feedback->id]) }}'">

                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold font-mono text-slate-400">#{{ $feedback->id }}</span>
                                @if($feedback->priority === 'urgent' || $feedback->priority === 'high')
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400">
                                        {{ $feedback->priority }}
                                    </span>
                                @endif
                            </div>
                            <span class="text-xs text-slate-400">{{ $feedback->created_at->format('M d') }}</span>
                        </div>

                        <h3 class="font-bold text-slate-900 dark:text-white mb-1.5 text-base leading-snug">{{ $feedback->subject }}</h3>

                        <div class="flex justify-between items-end mt-4">
                            <div class="flex items-center gap-2">
                                <div class="h-6 w-6 rounded bg-slate-100 dark:bg-gray-700 flex items-center justify-center text-[10px] font-bold text-slate-600 dark:text-slate-300">
                                    {{ substr($feedback->tenant->name ?? 'T', 0, 1) }}
                                </div>
                                <span class="text-xs font-medium text-slate-600 dark:text-slate-300">{{ $feedback->tenant->name }}</span>
                            </div>

                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-50 border border-slate-200 dark:border-gray-700 dark:bg-gray-800">
                                <span class="w-1.5 h-1.5 rounded-full {{ match($feedback->status) { 'open' => 'bg-emerald-500', 'pending' => 'bg-amber-500', default => 'bg-slate-400' } }}"></span>
                                {{ ucfirst($feedback->status) }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center flex flex-col items-center">
                        <x-heroicon-o-inbox class="w-10 h-10 text-slate-300 mb-2" />
                        <span class="text-slate-500 text-sm">No tickets found.</span>
                    </div>
                @endforelse
            </div>

        </div>

        <div class="mt-6 px-4">
            {{ $feedbacks->links() }}
        </div>
    </div>
</main>
