<div class=" bg-white  mx-auto space-y-8 font-sans p-6">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
           <nav class="flex mb-5 mt-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="{{ route('landlord.dashboard') }}" wire:navigate
                    class="inline-flex items-center text-sm font-medium text-slate-700 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-white">
                    <x-heroicon-s-home class="w-4 h-4 me-2.5" />
                    Home
                </a>
            </li>

            <li>
                <div class="flex items-center">
                    <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-slate-400 mx-1" />
                    <span class="ms-1 text-sm font-medium text-slate-500 md:ms-2 dark:text-slate-400">Create
                        Tenant
                    </span>
                </div>
            </li>
        </ol>
    </nav>
         <h2 class="text-3xl font-bold tracking-tight text-slate-900 dark:text-white">
                Tenant Feedback</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400"> and resolve issues reported by tenants.
            </p>
        </div>

       
    </div>

    {{-- Complaints Table --}}
    <div
        class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
                <thead class="bg-slate-50/80 dark:bg-slate-900/50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Ticket</th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Tenant</th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Subject</th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Priority</th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Status</th>
                        <th scope="col"
                            class="px-6 py-4 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            Submitted</th>
                        <th scope="col" class="relative px-6 py-4"><span class="sr-only">View</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800 divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse ($feedbacks as $feedback)
                        <tr class="group hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors duration-200 cursor-pointer"
                            onclick="window.location='{{ route('landlord.respond-feedback', ['feedback' => $feedback->id]) }}'">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900 dark:text-white">
                                #{{ $feedback->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs shadow-sm mr-3">
                                        {{ substr($feedback->tenant->name ?? 'T', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-slate-900 dark:text-white">
                                            {{ $feedback->tenant->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-slate-500">{{ $feedback->user->name ?? 'User' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="text-sm text-slate-600 dark:text-slate-300">{{ Str::limit($feedback->subject, 30) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $priorityClass = match ($feedback->priority ?? 'medium') {
                                        'high',
                                        'urgent'
                                            => 'text-rose-600 bg-rose-50 dark:bg-rose-500/10 dark:text-rose-400',
                                        'medium'
                                            => 'text-amber-600 bg-amber-50 dark:bg-amber-500/10 dark:text-amber-400',
                                        default => 'text-slate-600 bg-slate-100 dark:bg-slate-700 dark:text-slate-300',
                                    };
                                @endphp
                                <span
                                    class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $priorityClass }}">
                                    {{ ucfirst($feedback->priority ?? 'medium') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClass = match ($feedback->status) {
                                        'open'
                                            => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-900/20 dark:text-rose-400',
                                        'pending'
                                            => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-900/20 dark:text-amber-400',
                                        'resolved'
                                            => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/20 dark:text-emerald-400',
                                        'closed'
                                            => 'bg-slate-50 text-slate-700 ring-slate-600/20 dark:bg-slate-700/50 dark:text-slate-400',
                                        default => 'bg-slate-50 text-slate-700',
                                    };
                                @endphp
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $statusClass }}">
                                    <span
                                        class="w-1.5 h-1.5 rounded-full mr-1.5 {{ str_contains($statusClass, 'emerald') ? 'bg-emerald-500' : (str_contains($statusClass, 'rose') ? 'bg-rose-500' : 'bg-slate-500') }}"></span>
                                    {{ ucfirst($feedback->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                {{ $feedback->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <x-heroicon-o-chevron-right
                                    class="w-5 h-5 text-slate-400 group-hover:text-indigo-500 transition-colors" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-12 h-12 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-3">
                                        <x-heroicon-o-check-circle class="w-6 h-6 text-slate-400" />
                                    </div>
                                    <h3 class="text-sm font-medium text-slate-900 dark:text-white">All caught up!</h3>
                                    <p class="text-sm text-slate-500 mt-1">No pending feedback found matching your
                                        criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
