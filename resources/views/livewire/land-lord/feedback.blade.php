<div class=" p-4  lg:ml-64 max-w-full mx-auto space-y-4 p-2 md:p-4  min-h-screen font-sans">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 text-sm text-slate-500 dark:text-slate-400">
                    <li class="hover:text-indigo-600 transition">
                        <a href="{{ route('landlord.dashboard') }}" wire:navigate>Dashboard</a>
                    </li>
                    <li><x-heroicon-s-chevron-right class="w-3 h-3 text-slate-400" /></li>
                    <li class="font-medium text-slate-900 dark:text-white">Complaints</li>
                </ol>
            </nav>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Tenant Feedback</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Monitor and resolve issues reported by tenants.</p>
        </div>

        {{-- Actions Toolbar --}}
        <div class="flex items-center gap-3">
            <div class="relative group">
                <input type="text" placeholder="Search tickets..." class="w-full md:w-64 pl-10 pr-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <x-heroicon-o-magnifying-glass class="w-4 h-4" />
                </div>
            </div>
            <div class="relative">
                <select class="appearance-none bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 py-2 pl-4 pr-10 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 cursor-pointer shadow-sm">
                    <option value="all">All Status</option>
                    <option value="open">Open</option>
                    <option value="pending">Pending</option>
                    <option value="resolved">Resolved</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-500">
                    <x-heroicon-s-funnel class="w-4 h-4" />
                </div>
            </div>
        </div>
    </div>

    {{-- Complaints Table --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50/80 dark:bg-slate-900/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ticket</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tenant</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Subject</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Priority</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Submitted</th>
                        <th scope="col" class="relative px-6 py-4"><span class="sr-only">View</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse ($feedbacks as $feedback)
                    <tr class="group hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors duration-200 cursor-pointer" onclick="window.location='{{ route('landlord.respond-feedback', ['feedback' => $feedback->id]) }}'">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                            #{{ $feedback->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs shadow-sm mr-3">
                                    {{ substr($feedback->tenant->name ?? 'T', 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-slate-900 dark:text-white">{{ $feedback->tenant->name ?? 'Unknown' }}</div>
                                    <div class="text-xs text-slate-500">{{ $feedback->user->name ?? 'User' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-slate-600 dark:text-slate-300">{{ Str::limit($feedback->subject, 30) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                             @php
                                $priorityClass = match($feedback->priority ?? 'medium') {
                                    'high', 'urgent' => 'text-rose-600 bg-rose-50 dark:bg-rose-500/10 dark:text-rose-400',
                                    'medium' => 'text-amber-600 bg-amber-50 dark:bg-amber-500/10 dark:text-amber-400',
                                    default => 'text-slate-600 bg-slate-100 dark:bg-slate-700 dark:text-slate-300',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $priorityClass }}">
                                {{ ucfirst($feedback->priority ?? 'medium') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusClass = match($feedback->status) {
                                    'open' => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-900/20 dark:text-rose-400',
                                    'pending' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/20 dark:text-amber-400',
                                    'resolved' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/20 dark:text-emerald-400',
                                    'closed' => 'bg-slate-50 text-slate-700 ring-slate-600/20 dark:bg-slate-700/50 dark:text-slate-400',
                                    default => 'bg-slate-50 text-slate-700'
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusClass }}">
                                <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ str_contains($statusClass, 'emerald') ? 'bg-emerald-500' : (str_contains($statusClass, 'rose') ? 'bg-rose-500' : 'bg-slate-500') }}"></span>
                                {{ ucfirst($feedback->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                            {{ $feedback->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <x-heroicon-o-chevron-right class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-3">
                                    <x-heroicon-o-check-circle class="w-6 h-6 text-slate-400" />
                                </div>
                                <h3 class="text-sm font-medium text-slate-900 dark:text-white">All caught up!</h3>
                                <p class="text-sm text-slate-500 mt-1">No pending feedback found matching your criteria.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
