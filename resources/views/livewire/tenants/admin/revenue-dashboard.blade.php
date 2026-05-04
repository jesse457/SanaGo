<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4 md:flex md:items-center md:justify-between space-y-3 md:space-y-0">

                {{-- Title & Breadcrumbs --}}
                <div class="flex-1 min-w-0">
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-1" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="#" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    {{ __('Financials') }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">Revenue Analytics</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                        Revenue Overview
                    </h2>
                </div>

                {{-- Period Selector --}}
                <div class="flex items-center gap-2">
                    <div class="inline-flex p-1 bg-slate-100 dark:bg-gray-900 rounded-xl border border-slate-200 dark:border-gray-700">
                        @foreach (['today' => 'Today', 'week' => 'Week', 'month' => 'Month', 'year' => 'Year'] as $key => $label)
                            <button wire:click="$set('timePeriod', '{{ $key }}')"
                                class="px-4 py-1.5 text-xs font-bold rounded-lg transition-all {{ $timePeriod === $key ? 'bg-white dark:bg-gray-800 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Filter Bar --}}
            <div class="px-4 sm:px-6 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex flex-col md:flex-row gap-3 items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500">Live Financial Inflow</span>
                </div>

                <button class="inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 rounded-lg text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 transition-colors">
                    <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                    Export Ledger
                </button>
            </div>
        </header>

        <div class="p-4 sm:p-6 space-y-6">
            {{-- Loading Overlay --}}
            <div wire:loading.flex class="fixed inset-0 bg-white/60 dark:bg-gray-900/60 backdrop-blur-[2px] z-50 flex items-center justify-center">
                <div class="flex items-center gap-3 px-6 py-3 bg-white dark:bg-gray-800 rounded-full shadow-xl border border-slate-100 dark:border-gray-700 animate-bounce">
                    <x-heroicon-o-arrow-path class="animate-spin h-5 w-5 text-blue-600" />
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Syncing Financials...</span>
                </div>
            </div>

            {{-- 2. HERO SUMMARY CARD --}}
            <div class="relative overflow-hidden bg-white dark:bg-gray-900 border border-slate-200 dark:border-gray-800 rounded-2xl p-8 shadow-sm">
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-500/5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>

                <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-8 items-end">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Gross Collection ({{ ucfirst($timePeriod) }})</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-xl font-bold text-slate-400">FCFA</span>
                            <h2 class="text-5xl sm:text-6xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                                {{ number_format($totalRevenue, 0) }}
                            </h2>
                        </div>
                        <div class="mt-4 flex items-center gap-2">
                            @if($revenueGrowth >= 0)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 text-xs font-bold border border-emerald-100 dark:border-emerald-800">
                                    <x-heroicon-s-arrow-trending-up class="w-3 h-3" />
                                    +{{ number_format($revenueGrowth, 1) }}%
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400 text-xs font-bold border border-red-100 dark:border-red-800">
                                    <x-heroicon-s-arrow-trending-down class="w-3 h-3" />
                                    {{ number_format($revenueGrowth, 1) }}%
                                </span>
                            @endif
                            <span class="text-xs text-slate-500">vs previous period</span>
                        </div>
                    </div>

                    {{-- Mini Chart Visualization --}}
                    <div class="flex items-end justify-end gap-1.5 h-20">
                        @foreach([30, 45, 35, 60, 50, 80, 55] as $val)
                            <div style="height: {{ $val }}%" class="w-2.5 bg-slate-100 dark:bg-gray-800 rounded-t-sm transition-all hover:bg-blue-400"></div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 3. METRICS GRID --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $metrics = [
                        ['label' => 'Medications', 'val' => $breakdown['medication'] ?? 0, 'icon' => 'beaker', 'color' => 'emerald'],
                        ['label' => 'Consultations', 'val' => $breakdown['appointment'] ?? 0, 'icon' => 'calendar-days', 'color' => 'blue'],
                        ['label' => 'Lab Tests', 'val' => $breakdown['lab'] ?? 0, 'icon' => 'amber', 'color' => 'amber'],
                        ['label' => 'Admissions', 'val' => $breakdown['admission'] ?? 0, 'icon' => 'building-office-2', 'color' => 'purple'],
                    ];
                @endphp

                @foreach($metrics as $m)
                    <div class="bg-white dark:bg-gray-900 border border-slate-200 dark:border-gray-800 p-5 rounded-2xl shadow-sm hover:border-blue-300 dark:hover:border-blue-700 transition-all group">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-gray-800 border border-slate-100 dark:border-gray-700 group-hover:bg-blue-50 dark:group-hover:bg-blue-900/20 transition-colors">
                                @if($m['icon'] === 'beaker') <x-heroicon-o-beaker class="w-5 h-5 text-emerald-500" /> @endif
                                @if($m['icon'] === 'calendar-days') <x-heroicon-o-calendar-days class="w-5 h-5 text-blue-500" /> @endif
                                @if($m['icon'] === 'amber') <x-heroicon-o-clipboard-document-check class="w-5 h-5 text-amber-500" /> @endif
                                @if($m['icon'] === 'building-office-2') <x-heroicon-o-building-office-2 class="w-5 h-5 text-purple-500" /> @endif
                            </div>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $m['label'] }}</p>
                        <div class="text-xl font-bold text-slate-900 dark:text-white mt-0.5">
                            <span class="text-[10px] text-slate-400 font-medium">FCFA</span> {{ number_format($m['val'], 0) }}
                        </div>
                        <div class="mt-3 w-full bg-slate-100 dark:bg-gray-800 h-1 rounded-full overflow-hidden">
                            <div class="bg-slate-900 dark:bg-blue-500 h-full" style="width: {{ $totalRevenue > 0 ? ($m['val'] / $totalRevenue) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- 4. DESKTOP TABLE --}}
            <div class="hidden md:block bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-gray-800">
                    <thead class="bg-slate-50 dark:bg-gray-950">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Patient Name</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Meds</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Consults</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Labs</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Admissions</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                        @forelse ($patientRevenues as $revenue)
                            <tr class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-gray-800 flex items-center justify-center text-xs font-bold text-slate-500 border border-slate-200 dark:border-gray-700">
                                            {{ strtoupper(substr($revenue->patient?->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($revenue->patient?->last_name ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $revenue->patient?->first_name }} {{ $revenue->patient?->last_name }}</div>
                                            <div class="text-[10px] font-medium text-slate-400">UID: #{{ $revenue->patient?->patient_uid ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right text-xs font-medium text-slate-500">{{ number_format($revenue->medications, 0) }}</td>
                                <td class="px-6 py-4 text-right text-xs font-medium text-slate-500">{{ number_format($revenue->appointments, 0) }}</td>
                                <td class="px-6 py-4 text-right text-xs font-medium text-slate-500">{{ number_format($revenue->labs, 0) }}</td>
                                <td class="px-6 py-4 text-right text-xs font-medium text-slate-500">{{ number_format($revenue->admissions, 0) }}</td>
                                <td class="px-6 py-4 text-right">
                                    <span class="inline-flex items-center px-2 py-1 bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400 text-xs font-bold rounded-lg border border-emerald-100 dark:border-emerald-900">
                                        {{ number_format($revenue->total_contribution ?? $revenue->total ?? 0, 0) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button class="p-2 text-slate-400 hover:text-blue-600 transition-colors"><x-heroicon-m-ellipsis-horizontal class="w-5 h-5" /></button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-10 text-center text-slate-400 text-sm italic">No records for this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- 5. MOBILE CARD VIEW --}}
            <div class="md:hidden space-y-4">
                @foreach ($patientRevenues as $revenue)
                    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-slate-200 dark:border-gray-800 p-4">
                        <div class="flex items-center justify-between mb-3 pb-3 border-b border-slate-100 dark:border-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-gray-800 flex items-center justify-center text-[10px] font-bold text-slate-500">
                                    {{ strtoupper(substr($revenue->patient?->first_name ?? 'U', 0, 1)) }}
                                </div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $revenue->patient?->first_name }}</h3>
                            </div>
                            <span class="text-xs font-bold text-emerald-600">{{ number_format($revenue->total_contribution ?? 0, 0) }} FCFA</span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                            <div class="flex justify-between"><span>Meds:</span> <span class="text-slate-600">{{ number_format($revenue->medications, 0) }}</span></div>
                            <div class="flex justify-between"><span>Labs:</span> <span class="text-slate-600">{{ number_format($revenue->labs, 0) }}</span></div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($patientRevenues->hasPages())
                <div class="mt-4">
                    {{ $patientRevenues->links() }}
                </div>
            @endif
        </div>
    </div>
</main>
