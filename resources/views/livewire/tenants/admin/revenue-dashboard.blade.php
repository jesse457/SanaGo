<div class="flex-1 p-4  bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 overflow-y-auto min-h-screen">
    <header class="max-w-7xl mx-auto px-4 sm:px-6 md:p-8">
        {{-- Page Title --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-slate-900 dark:text-slate-100 flex items-center gap-3">
                    <div class="p-2 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg">
                        <x-heroicon-s-banknotes class="w-6 h-6 text-white" />
                    </div>
                    {{ __('admin.revenue_title') }}
                </h1>
                <p class="text-slate-600 dark:text-slate-400 mt-2">
                    {{ __('admin.revenue_description') }}
                </p>
            </div>
            <div class="flex items-center gap-2 mt-4 sm:mt-0" wire:key="time-period-filters">
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
                        class="px-4 py-2 text-sm font-medium rounded-xl transition-all duration-200 {{ $timePeriod === $key ? 'bg-slate-900 text-white shadow-lg scale-105' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-700' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Main Revenue Summary Card --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-8 py-6 bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 dark:text-slate-400">Total Revenue</p>
                        <div class="flex items-baseline gap-3 mt-2">
                            <h2 class="text-4xl font-bold text-slate-900 dark:text-slate-100">
                                FCFA {{ number_format($totalRevenue, 0) }}
                            </h2>
                            @if($revenueGrowth > 0)
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-emerald-700 bg-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-full">
                                    <x-heroicon-s-arrow-trending-up class="w-3 h-3 mr-1" />
                                    {{ number_format($revenueGrowth, 1) }}%
                                </span>

                            @else
                             <span class="inline-flex items-center px-2 py-1 text-xs font-medium text-red-700 bg-red-100 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                                    <x-heroicon-s-arrow-trending-down class="w-3 h-3 mr-1" />
                                    {{ number_format($revenueGrowth, 1) }}%
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                            Compared to FCFA {{ number_format($previousTotalRevenue, 0) }} in previous period
                        </p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg">
                        <x-heroicon-s-currency-dollar class="w-8 h-8 text-white" />
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Revenue Breakdown Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        {{-- Medications --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                    <x-heroicon-s-beaker class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Medications</span>
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">
                FCFA {{ number_format($medicationRevenue, 0) }}
            </p>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-slate-500 dark:text-slate-400">{{ round(($medicationRevenue / max($totalRevenue, 1)) * 100) }}% of total</span>
            </div>
        </div>

        {{-- Appointments --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                    <x-heroicon-s-calendar-days class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Appointments</span>
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">
                FCFA {{ number_format($appointmentRevenue, 0) }}
            </p>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-slate-500 dark:text-slate-400">{{ round(($appointmentRevenue / max($totalRevenue, 1)) * 100) }}% of total</span>
            </div>
        </div>

        {{-- Lab Tests --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
                    <x-heroicon-s-clipboard-document-check class="w-5 h-5 text-amber-600 dark:text-amber-400" />
                </div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Lab Tests</span>
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">
                FCFA {{ number_format($labRevenue, 0) }}
            </p>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-slate-500 dark:text-slate-400">{{ round(($labRevenue / max($totalRevenue, 1)) * 100) }}% of total</span>
            </div>
        </div>

        {{-- Admissions --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                    <x-heroicon-s-building-office-2 class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                </div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Admissions</span>
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">
                FCFA {{ number_format($admissionRevenue, 0) }}
            </p>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-slate-500 dark:text-slate-400">{{ round(($admissionRevenue / max($totalRevenue, 1)) * 100) }}% of total</span>
            </div>
        </div>

        {{-- Bed Fees --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-rose-100 dark:bg-rose-900/30 rounded-lg">
                    <x-hugeicons-bed-single-02 class="w-5 h-5 text-rose-600 dark:text-rose-400" />
                </div>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Bed Fees</span>
            </div>
            <p class="text-2xl font-bold text-slate-900 dark:text-slate-100">
                FCFA {{ number_format($bedFeeRevenue, 0) }}
            </p>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-slate-500 dark:text-slate-400">{{ round(($bedFeeRevenue / max($totalRevenue, 1)) * 100) }}% of total</span>
            </div>
        </div>

        {{-- Quick Stats --}}
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 dark:from-slate-700 dark:to-slate-600 rounded-xl shadow-lg border border-slate-700 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="p-2 bg-white/10 rounded-lg">
                    <x-heroicon-s-chart-pie class="w-5 h-5 text-white" />
                </div>
                <span class="text-xs font-medium text-slate-300">Summary</span>
            </div>
            <p class="text-2xl font-bold text-white">
                {{ $patientRevenues->total() }}
            </p>
            <div class="mt-2 flex items-center text-xs">
                <span class="text-slate-300">Active Patients</span>
            </div>
        </div>
    </div>

    {{-- Patient Revenue Table --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <x-heroicon-s-users class="w-5 h-5 text-slate-400" />
                        Revenue by Patient
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                        Top revenue-generating patients for selected period
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-slate-500 dark:text-slate-400">Total Records</p>
                    <p class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $patientRevenues->total() }}</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                    <tr>
                        <th class="px-8 py-4 text-left text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Patient</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Appointments</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Medications</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Lab Tests</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Admissions</th>
                        <th class="px-6 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Bed Fees</th>
                        <th class="px-8 py-4 text-right text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Revenue</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse ($patientRevenues as $index => $revenueSummary)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30 transition-colors duration-150">
                            <td class="px-8 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-white font-semibold">
                                            {{ strtoupper(substr($revenueSummary->patient->first_name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-slate-900 dark:text-slate-100">
                                            {{ $revenueSummary->patient->first_name }} {{ $revenueSummary->patient->last_name }}
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">
                                            ID: {{ $revenueSummary->patient->patient_uid }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-slate-900 dark:text-slate-100">
                                FCFA {{ number_format($revenueSummary->appointments, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-slate-900 dark:text-slate-100">
                                FCFA {{ number_format($revenueSummary->medications, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-slate-900 dark:text-slate-100">
                                FCFA {{ number_format($revenueSummary->labs, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-slate-900 dark:text-slate-100">
                                FCFA {{ number_format($revenueSummary->admissions, 0) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-slate-900 dark:text-slate-100">
                                FCFA {{ number_format($revenueSummary->bed_fees, 0) }}
                            </td>
                            <td class="px-8 py-4 whitespace-nowrap text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-200">
                                    FCFA {{ number_format($revenueSummary->total, 0) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <x-heroicon-o-inbox class="w-12 h-12 text-slate-300 dark:text-slate-600 mb-3" />
                                    <p class="text-sm font-medium text-slate-900 dark:text-slate-100">No revenue data found</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Try selecting a different time period</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($patientRevenues->hasPages())
            <div class="px-8 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                {{ $patientRevenues->links() }}
            </div>
        @endif
    </div>
</div>
