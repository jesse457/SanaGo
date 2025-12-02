<div class="w-full min-h-full p-4 md:p-6 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800">

    {{-- Header Section --}}
    <header class="max-w-7xl mx-auto mb-8">
        {{-- Title & Filters --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6 gap-6">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-3">
                    <div class="p-2 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg shadow-md">
                        <x-heroicon-s-banknotes class="w-6 h-6 text-white" />
                    </div>
                    {{ __('admin.revenue_title') }}
                </h1>
                <p class="text-slate-600 dark:text-slate-400 mt-2 text-sm md:text-base pl-1">
                    {{ __('admin.revenue_description') }}
                </p>
            </div>

            {{-- Time Period Filters --}}
            <div class="flex flex-wrap items-center gap-2" wire:key="time-period-filters">
                @php
                    $periods = [
                        'today' => __('admin.time_filter_today'),
                        'week' => __('admin.time_filter_week'),
                        'month' => __('admin.time_filter_month'),
                        'year' => __('admin.time_filter_year'),
                    ];
                @endphp
                @foreach ($periods as $key => $label)
                    <button type="button" wire:click="$set('timePeriod', '{{ $key }}')"
                        class="px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 border shadow-sm
                        {{ $timePeriod === $key
                            ? 'bg-slate-900 border-slate-900 text-white shadow-md scale-105 transform'
                            : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700'
                        }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Main Revenue Summary Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-6 md:px-8 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700/50">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">Total Revenue</p>
                        <div class="flex flex-wrap items-baseline gap-3 mt-1">
                            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-slate-100 tracking-tight">
                                FCFA {{ number_format($totalRevenue, 0) }}
                            </h2>

                            {{-- Trend Indicator --}}
                            @if($revenueGrowth > 0)
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold text-emerald-700 bg-emerald-100 dark:bg-emerald-900/50 dark:text-emerald-400 rounded-full">
                                    <x-heroicon-s-arrow-trending-up class="w-3.5 h-3.5 mr-1" />
                                    {{ number_format($revenueGrowth, 1) }}%
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-bold text-red-700 bg-red-100 dark:bg-red-900/50 dark:text-red-400 rounded-full">
                                    <x-heroicon-s-arrow-trending-down class="w-3.5 h-3.5 mr-1" />
                                    {{ number_format($revenueGrowth, 1) }}%
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">
                            Compared to <span class="font-semibold">FCFA {{ number_format($previousTotalRevenue, 0) }}</span> in previous period
                        </p>
                    </div>

                    {{-- Large Icon Decoration --}}
                    <div class="hidden sm:block p-4 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg transform rotate-3">
                        <x-heroicon-s-currency-dollar class="w-8 h-8 text-white" />
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Breakdown Grid --}}
    <div class="max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">

        {{-- Card 1: Medications --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 hover:shadow-md transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg group-hover:bg-emerald-200 dark:group-hover:bg-emerald-800/50 transition-colors">
                    <x-heroicon-s-beaker class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Meds</span>
            </div>
            <p class="text-lg xl:text-xl font-bold text-slate-900 dark:text-slate-100 truncate">
                {{ number_format($medicationRevenue, 0) }}
            </p>
            <div class="mt-3 w-full bg-slate-100 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-1000" style="width: {{ round(($medicationRevenue / max($totalRevenue, 1)) * 100) }}%"></div>
            </div>
        </div>

        {{-- Card 2: Appointments --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 hover:shadow-md transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 transition-colors">
                    <x-heroicon-s-calendar-days class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Appts</span>
            </div>
            <p class="text-lg xl:text-xl font-bold text-slate-900 dark:text-slate-100 truncate">
                {{ number_format($appointmentRevenue, 0) }}
            </p>
            <div class="mt-3 w-full bg-slate-100 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-1000" style="width: {{ round(($appointmentRevenue / max($totalRevenue, 1)) * 100) }}%"></div>
            </div>
        </div>

        {{-- Card 3: Lab Tests --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 hover:shadow-md transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg group-hover:bg-amber-200 dark:group-hover:bg-amber-800/50 transition-colors">
                    <x-heroicon-s-clipboard-document-check class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Labs</span>
            </div>
            <p class="text-lg xl:text-xl font-bold text-slate-900 dark:text-slate-100 truncate">
                {{ number_format($labRevenue, 0) }}
            </p>
            <div class="mt-3 w-full bg-slate-100 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                <div class="bg-amber-500 h-1.5 rounded-full transition-all duration-1000" style="width: {{ round(($labRevenue / max($totalRevenue, 1)) * 100) }}%"></div>
            </div>
        </div>

        {{-- Card 4: Admissions --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 hover:shadow-md transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg group-hover:bg-purple-200 dark:group-hover:bg-purple-800/50 transition-colors">
                    <x-heroicon-s-building-office-2 class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Admit</span>
            </div>
            <p class="text-lg xl:text-xl font-bold text-slate-900 dark:text-slate-100 truncate">
                {{ number_format($admissionRevenue, 0) }}
            </p>
            <div class="mt-3 w-full bg-slate-100 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                <div class="bg-purple-500 h-1.5 rounded-full transition-all duration-1000" style="width: {{ round(($admissionRevenue / max($totalRevenue, 1)) * 100) }}%"></div>
            </div>
        </div>

        {{-- Card 5: Bed Fees --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5 hover:shadow-md transition-all duration-300 group">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-rose-100 dark:bg-rose-900/30 rounded-lg group-hover:bg-rose-200 dark:group-hover:bg-rose-800/50 transition-colors">
                    {{-- Note: Assuming x-heroicon-s-archive-box as fallback for bed icon if hugeicons not installed --}}
                    <x-heroicon-s-archive-box class="w-5 h-5 text-rose-600 dark:text-rose-400" />
                </div>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Beds</span>
            </div>
            <p class="text-lg xl:text-xl font-bold text-slate-900 dark:text-slate-100 truncate">
                {{ number_format($bedFeeRevenue, 0) }}
            </p>
            <div class="mt-3 w-full bg-slate-100 dark:bg-slate-700 h-1.5 rounded-full overflow-hidden">
                <div class="bg-rose-500 h-1.5 rounded-full transition-all duration-1000" style="width: {{ round(($bedFeeRevenue / max($totalRevenue, 1)) * 100) }}%"></div>
            </div>
        </div>

        {{-- Card 6: Quick Stats Summary --}}
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 dark:from-slate-700 dark:to-slate-800 rounded-xl shadow-lg border border-slate-700 p-5 text-white flex flex-col justify-between h-full">
            <div class="flex items-center justify-between">
                <div class="p-2 bg-white/10 rounded-lg">
                    <x-heroicon-s-chart-pie class="w-5 h-5 text-slate-200" />
                </div>
                <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">Active</span>
            </div>
            <div>
                <p class="text-2xl font-bold text-white mt-2">
                    {{ $patientRevenues->total() }}
                </p>
                <div class="mt-1 text-xs text-slate-400">
                    Patients Billed
                </div>
            </div>
        </div>
    </div>

    {{-- Detailed Revenue Table --}}
    <div class="max-w-7xl mx-auto bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/50 dark:bg-slate-800">
            <div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                    <x-heroicon-s-users class="w-5 h-5 text-slate-400" />
                    Revenue by Patient
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Highest revenue generating patients for the selected period
                </p>
            </div>
            <div class="text-right hidden sm:block">
                 <span class="text-xs font-semibold text-slate-500 bg-slate-100 dark:bg-slate-700 dark:text-slate-300 px-3 py-1 rounded-full">
                    {{ $patientRevenues->total() }} Records
                 </span>
            </div>
        </div>

        {{-- Scrollable Table Container --}}
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Appts</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Meds</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Labs</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Beds</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse ($patientRevenues as $index => $revenueSummary)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors duration-150">
                            {{-- Patient Column --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                            {{ strtoupper(substr($revenueSummary->patient->first_name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                            {{ $revenueSummary->patient->first_name }} {{ $revenueSummary->patient->last_name }}
                                        </div>
                                        <div class="text-[10px] font-mono text-slate-500 dark:text-slate-400 mt-0.5">
                                            {{ $revenueSummary->patient->patient_uid }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Data Columns --}}
                            <td class="px-6 py-4 text-right text-sm text-slate-600 dark:text-slate-300 font-medium">
                                {{ number_format($revenueSummary->appointments, 0) }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-slate-600 dark:text-slate-300 font-medium">
                                {{ number_format($revenueSummary->medications, 0) }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-slate-600 dark:text-slate-300 font-medium">
                                {{ number_format($revenueSummary->labs, 0) }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm text-slate-600 dark:text-slate-300 font-medium">
                                {{ number_format($revenueSummary->bed_fees, 0) }}
                            </td>

                            {{-- Total Column --}}
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    FCFA {{ number_format($revenueSummary->total, 0) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="p-3 bg-slate-100 dark:bg-slate-700 rounded-full mb-3">
                                        <x-heroicon-o-inbox class="w-8 h-8 text-slate-400" />
                                    </div>
                                    <p class="text-sm font-medium text-slate-900 dark:text-slate-100">No revenue data found</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Try selecting a different time period or add records.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($patientRevenues->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/30">
                {{ $patientRevenues->links() }}
            </div>
        @endif
    </div>
</div>
