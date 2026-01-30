<div class="w-full min-h-screen bg-zinc-50/50 dark:bg-zinc-950/50 font-sans text-zinc-900 dark:text-zinc-100 p-4 sm:p-6 lg:p-8">

    {{-- Main Container --}}
    <main class="max-w-[1600px] mx-auto space-y-8">

        {{-- 1. HEADER SECTION --}}
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div>
                {{-- Breadcrumbs --}}
                <div class="flex items-center gap-2 text-xs font-medium text-zinc-500 mb-2">
                    <span>Financials</span>
                    <span class="text-zinc-300">/</span>
                    <span class="text-zinc-900 dark:text-zinc-100">Revenue Analytics</span>
                </div>

                <div class="flex items-center gap-4">
                    {{-- Icon Box --}}
                    <div class="w-12 h-12 flex items-center justify-center bg-zinc-900 dark:bg-white rounded-2xl shadow-lg shadow-zinc-900/10">
                        <x-heroicon-o-banknotes class="w-6 h-6 text-white dark:text-zinc-900" stroke-width="2" />
                    </div>

                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white tracking-tight">
                            {{ __('Revenue Overview') }}
                        </h1>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold uppercase tracking-wide border border-emerald-200 dark:border-emerald-500/20">
                                Live Data
                            </span>
                            <p class="text-[11px] font-medium text-zinc-400 uppercase tracking-wider">
                                {{ __('Financial Inflow Analytics') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Time Filter Tabs --}}
            <div class="flex items-center gap-3">
                <div class="flex items-center p-1.5 bg-zinc-100 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-800">
                    @php
                        $periods = [
                            'today' => 'Today',
                            'week'  => 'Week',
                            'month' => 'Month',
                            'year'  => 'Year',
                        ];
                    @endphp
                    @foreach ($periods as $key => $label)
                        <button
                            wire:click="$set('timePeriod', '{{ $key }}')"
                            class="px-4 py-1.5 text-[11px] font-bold uppercase tracking-wider rounded-lg transition-all duration-200
                            {{ $timePeriod === $key
                                ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-white shadow-sm border border-zinc-200 dark:border-zinc-700'
                                : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-300'
                            }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </header>

        {{-- Loading Overlay --}}
        <div wire:loading.flex class="fixed inset-0 z-50 flex items-center justify-center bg-white/50 dark:bg-zinc-900/50 backdrop-blur-sm transition-opacity">
            <div class="flex items-center gap-3 px-6 py-3 bg-white dark:bg-zinc-800 rounded-2xl shadow-xl border border-zinc-100 dark:border-zinc-700">
                <x-heroicon-o-arrow-path class="w-5 h-5 text-indigo-600 animate-spin" />
                <span class="text-sm font-bold text-zinc-900 dark:text-white">Syncing Financials...</span>
            </div>
        </div>

        {{-- 2. MAIN SUMMARY CARD (HERO) --}}
        <div class="relative overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] p-8 sm:p-10 shadow-sm">
            {{-- Decorative Blurs --}}
            <div class="absolute top-0 right-0 w-96 h-96 bg-indigo-500/5 rounded-full blur-[100px] -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row justify-between lg:items-end gap-10">
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-full text-[10px] font-bold uppercase tracking-widest border border-emerald-100 dark:border-emerald-500/20">
                        <x-heroicon-s-arrow-trending-up class="w-3 h-3" />
                        Financial Performance
                    </div>

                    <div>
                        <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest mb-1">Gross Collection ({{ ucfirst($timePeriod) }})</p>
                        <div class="flex items-baseline gap-3">
                            <span class="text-base font-bold text-zinc-400">FCFA</span>
                            {{-- $totalRevenue comes directly from the component property --}}
                            <h2 class="text-6xl sm:text-7xl font-black text-zinc-900 dark:text-white tracking-tighter">
                                {{ number_format($totalRevenue, 0) }}
                            </h2>
                        </div>
                    </div>

                    <p class="text-xs font-medium text-zinc-500 italic">
                        {{-- $revenueGrowth comes directly from component property --}}
                        Growth vs previous period:
                        @if($revenueGrowth != 0)
                            <span class="ml-2 {{ $revenueGrowth > 0 ? 'text-emerald-600' : 'text-rose-600' }} font-bold">
                                {{ $revenueGrowth > 0 ? '+' : '' }}{{ number_format($revenueGrowth, 1) }}%
                            </span>
                        @else
                            <span class="ml-2 text-zinc-400">0%</span>
                        @endif
                    </p>
                </div>

                {{-- Visual Bar Chart (Visual Only) --}}
                <div class="flex gap-2 sm:gap-3 h-32 items-end">
                    @foreach([35, 50, 45, 80, 60, 90, 75] as $index => $height)
                        <div class="group relative flex flex-col items-center gap-2">
                            <div style="height: {{ $height }}%"
                                 class="w-3 sm:w-4 rounded-t-lg transition-all duration-500
                                 {{ $index === 5
                                    ? 'bg-indigo-500 shadow-[0_0_15px_rgba(99,102,241,0.5)]'
                                    : 'bg-zinc-200 dark:bg-zinc-800 group-hover:bg-indigo-300'
                                 }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- 3. METRICS GRID --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            {{--
                Accessing $breakdown array from the component.
                Using null coalescence (?? 0) to prevent errors if keys are missing.
            --}}
            @php
                $metrics = [
                    [
                        'title' => 'Medications',
                        'amount' => $breakdown['medication'] ?? 0,
                        'icon' => 'beaker', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-100 dark:bg-emerald-500/10'
                    ],
                    [
                        'title' => 'Consultations',
                        'amount' => $breakdown['appointment'] ?? 0,
                        'icon' => 'calendar-days', 'color' => 'text-blue-600', 'bg' => 'bg-blue-100 dark:bg-blue-500/10'
                    ],
                    [
                        'title' => 'Lab Tests',
                        'amount' => $breakdown['lab'] ?? 0,
                        'icon' => 'clipboard-document-check', 'color' => 'text-amber-600', 'bg' => 'bg-amber-100 dark:bg-amber-500/10'
                    ],
                    [
                        'title' => 'Admissions',
                        'amount' => $breakdown['admission'] ?? 0,
                        'icon' => 'building-office-2', 'color' => 'text-purple-600', 'bg' => 'bg-purple-100 dark:bg-purple-500/10'
                    ],
                ];
            @endphp

            @foreach($metrics as $metric)
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 p-6 rounded-[2rem] hover:shadow-md transition-all group">
                    <div class="flex justify-between items-start mb-6">
                        <div class="p-3 rounded-2xl {{ $metric['bg'] }} {{ $metric['color'] }} border border-current border-opacity-10">
                            @if($metric['icon'] === 'beaker') <x-heroicon-o-beaker class="w-6 h-6" /> @endif
                            @if($metric['icon'] === 'calendar-days') <x-heroicon-o-calendar-days class="w-6 h-6" /> @endif
                            @if($metric['icon'] === 'clipboard-document-check') <x-heroicon-o-clipboard-document-check class="w-6 h-6" /> @endif
                            @if($metric['icon'] === 'building-office-2') <x-heroicon-o-building-office-2 class="w-6 h-6" /> @endif
                        </div>
                    </div>

                    <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">{{ $metric['title'] }}</p>
                    <div class="flex items-baseline gap-1">
                        <span class="text-[10px] font-bold text-zinc-400">FCFA</span>
                        <span class="text-2xl font-black text-zinc-900 dark:text-zinc-100 tracking-tight">
                            {{ number_format($metric['amount'], 0) }}
                        </span>
                    </div>

                    {{-- Visual Proportion Bar --}}
                    <div class="w-full bg-zinc-100 dark:bg-zinc-800 h-1.5 rounded-full mt-4 overflow-hidden">
                        <div style="width: {{ $totalRevenue > 0 ? ($metric['amount'] / $totalRevenue) * 100 : 0 }}%" class="h-full bg-zinc-900 dark:bg-zinc-100 rounded-full"></div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- 4. DETAILED DATA SECTION --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-[2rem] overflow-hidden flex flex-col shadow-sm">

            {{-- Table Header --}}
            <div class="px-6 py-5 border-b border-zinc-100 dark:border-zinc-800 flex flex-col sm:flex-row justify-between items-center gap-4 bg-zinc-50/50 dark:bg-zinc-900/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-zinc-900 dark:bg-white rounded-lg">
                        <x-heroicon-m-funnel class="w-4 h-4 text-white dark:text-zinc-900" />
                    </div>
                    <h3 class="text-[11px] font-black text-zinc-900 dark:text-white uppercase tracking-[0.2em]">Transaction Ledger</h3>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    {{--
                       NOTE: Search input is visual only because the component
                       does not have a $search property.
                    --}}
                    <div class="relative flex-1 sm:flex-none opacity-50 cursor-not-allowed" title="Search disabled in this view">
                        <x-heroicon-m-magnifying-glass class="absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 w-4 h-4" />
                        <input
                            type="text"
                            disabled
                            placeholder="Search..."
                            class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 h-10 rounded-xl pl-9 pr-4 text-xs font-bold outline-none w-full sm:w-64 cursor-not-allowed"
                        >
                    </div>
                    <button class="p-2.5 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-zinc-500 hover:text-indigo-600 transition-colors">
                        <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-zinc-50/50 dark:bg-zinc-950 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100 dark:border-zinc-800">
                            <th class="px-6 py-4">Patient Descriptor</th>
                            <th class="px-6 py-4 text-right hidden sm:table-cell">Meds</th>
                            <th class="px-6 py-4 text-right hidden md:table-cell">Consults</th>
                            <th class="px-6 py-4 text-right hidden lg:table-cell">Labs</th>
                            <th class="px-6 py-4 text-right hidden xl:table-cell">Adm.</th>
                            <th class="px-6 py-4 text-right">Total</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                        @forelse ($patientRevenues as $revenue)
                            <tr class="group hover:bg-zinc-50/80 dark:hover:bg-zinc-800/40 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-xs font-black text-zinc-500 border border-zinc-200 dark:border-zinc-700">
                                            {{ strtoupper(substr($revenue->patient?->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($revenue->patient?->last_name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-xs font-bold text-zinc-900 dark:text-zinc-100">
                                                {{ $revenue->patient?->first_name ?? 'Unknown' }} {{ $revenue->patient?->last_name ?? 'Patient' }}
                                            </div>
                                            <div class="mt-1 px-1.5 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded inline-block text-[9px] font-mono text-zinc-500 font-bold tracking-tighter">
                                                #{{ $revenue->patient?->patient_uid ?? $revenue->patient_id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right text-xs font-bold text-zinc-500 hidden sm:table-cell">
                                    {{ number_format($revenue->medications, 0) }}
                                </td>
                                <td class="px-6 py-4 text-right text-xs font-bold text-zinc-500 hidden md:table-cell">
                                    {{ number_format($revenue->appointments, 0) }}
                                </td>
                                <td class="px-6 py-4 text-right text-xs font-bold text-zinc-500 hidden lg:table-cell">
                                    {{ number_format($revenue->labs, 0) }}
                                </td>
                                <td class="px-6 py-4 text-right text-xs font-bold text-zinc-500 hidden xl:table-cell">
                                    {{ number_format($revenue->admissions, 0) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-black text-emerald-600 dark:text-emerald-400 text-xs bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded-lg">
                                        FCFA {{ number_format($revenue->total_contribution ?? $revenue->total ?? 0, 0) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center">
                                        <button class="p-2 text-zinc-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-zinc-800 rounded-lg transition-colors">
                                            <x-heroicon-m-ellipsis-horizontal class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <div class="w-12 h-12 bg-zinc-50 dark:bg-zinc-800 rounded-full flex items-center justify-center">
                                            <x-heroicon-o-document-magnifying-glass class="w-6 h-6 text-zinc-400" />
                                        </div>
                                        <p class="text-sm font-medium text-zinc-500">No transactions found for this period.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($patientRevenues->hasPages())
                <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/50">
                    {{ $patientRevenues->links() }}
                </div>
            @endif
        </div>
    </main>
</div>
