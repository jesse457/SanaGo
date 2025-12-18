<div class="w-full min-h-screen bg-slate-50 dark:bg-slate-900 font-sans text-slate-600 dark:text-slate-300">

    {{-- Main Container --}}
    <main class="max-w-[1600px] mx-auto p-4 sm:p-6 lg:p-8">

        {{-- Header Section --}}
        <header class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                    <div class="p-2.5 bg-indigo-600 rounded-xl shadow-lg shadow-indigo-500/20">
                        <x-heroicon-s-banknotes class="w-6 h-6 text-white" />
                    </div>
                    {{ __('admin.revenue_title') }}
                </h1>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400 max-w-2xl">
                    {{ __('admin.revenue_description') }}
                </p>
            </div>

            {{-- Time Filter Tabs --}}
            <div class="bg-white dark:bg-slate-800 p-1 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm inline-flex flex-wrap gap-1">
                @php
                    $periods = [
                        'today' => __('admin.time_filter_today'),
                        'week'  => __('admin.time_filter_week'),
                        'month' => __('admin.time_filter_month'),
                        'year'  => __('admin.time_filter_year'),
                    ];
                @endphp
                @foreach ($periods as $key => $label)
                    <button
                        wire:click="$set('timePeriod', '{{ $key }}')"
                        class="px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-800
                        {{ $timePeriod === $key
                            ? 'bg-slate-900 dark:bg-indigo-600 text-white shadow-md'
                            : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'
                        }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </header>

        {{-- Loading Overlay --}}
        <div wire:loading.flex class="fixed inset-0 z-50 flex items-center justify-center bg-white/50 dark:bg-slate-900/50 backdrop-blur-sm">
            <div class="flex items-center gap-3 px-6 py-3 bg-white dark:bg-slate-800 rounded-full shadow-2xl border border-slate-100 dark:border-slate-700">
                <x-heroicon-o-arrow-path class="w-6 h-6 text-indigo-600 animate-spin" />
                <span class="font-medium text-slate-900 dark:text-white">Updating Dashboard...</span>
            </div>
        </div>

        {{-- Top Summary Card (Hero) --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden mb-8">
            <div class="relative overflow-hidden">
                {{-- Decorative Background --}}
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-indigo-500/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-emerald-500/10 rounded-full blur-3xl"></div>

                <div class="relative p-6 sm:p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div>
                        <p class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-1">Total Revenue ({{ ucfirst($timePeriod) }})</p>
                        <div class="flex items-baseline gap-4">
                            <h2 class="text-4xl sm:text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                                FCFA {{ number_format($totalRevenue, 0) }}
                            </h2>

                            @if($revenueGrowth != 0)
                                <div class="flex items-center px-2.5 py-1 rounded-full text-sm font-bold {{ $revenueGrowth > 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-400' }}">
                                    @if($revenueGrowth > 0)
                                        <x-heroicon-s-arrow-trending-up class="w-4 h-4 mr-1.5" />
                                        +{{ number_format($revenueGrowth, 1) }}%
                                    @else
                                        <x-heroicon-s-arrow-trending-down class="w-4 h-4 mr-1.5" />
                                        {{ number_format($revenueGrowth, 1) }}%
                                    @endif
                                </div>
                            @else
                                <span class="text-sm font-medium text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded-full">0% Change</span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-3 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                            vs. Previous Period: <span class="font-semibold text-slate-700 dark:text-slate-200">FCFA {{ number_format($previousTotalRevenue, 0) }}</span>
                        </p>
                    </div>

                    {{-- Quick Action / Icon --}}
                    <div class="hidden md:flex items-center justify-center w-16 h-16 bg-gradient-to-br from-indigo-500 to-violet-600 rounded-2xl shadow-inner ring-4 ring-indigo-50 dark:ring-slate-700/50">
                        <x-heroicon-o-currency-dollar class="w-8 h-8 text-white" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Metrics Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-8">

            {{-- Medication Card --}}
            <x-revenue-metric-card
                title="Medications"
                amount="{{ $medicationRevenue }}"
                total="{{ $totalRevenue }}"
                icon="beaker"
                color="emerald"
            />

            {{-- Appointments Card --}}
            <x-revenue-metric-card
                title="Appointments"
                amount="{{ $appointmentRevenue }}"
                total="{{ $totalRevenue }}"
                icon="calendar-days"
                color="blue"
            />

            {{-- Lab Tests Card --}}
            <x-revenue-metric-card
                title="Lab Tests"
                amount="{{ $labRevenue }}"
                total="{{ $totalRevenue }}"
                icon="clipboard-document-check"
                color="amber"
            />

            {{-- Admissions Card --}}
            <x-revenue-metric-card
                title="Admissions"
                amount="{{ $admissionRevenue }}"
                total="{{ $totalRevenue }}"
                icon="building-office-2"
                color="purple"
            />

            {{-- Bed Fees Card --}}
            <x-revenue-metric-card
                title="Bed Fees"
                amount="{{ $bedFeeRevenue }}"
                total="{{ $totalRevenue }}"
                icon="archive-box"
                color="rose"
            />
        </div>

        {{-- Detailed Data Section --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50 dark:bg-slate-800/50">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <x-heroicon-s-user-group class="w-5 h-5 text-indigo-500" />
                        Top Revenue Sources (Patients)
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Financial breakdown by patient for selected period.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 text-xs font-medium bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-full shadow-sm text-slate-600 dark:text-slate-300">
                        {{ $patientRevenues->total() }} Records Found
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-900/40 border-b border-slate-200 dark:border-slate-700 text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400 font-semibold">
                            <th class="px-6 py-4">Patient Details</th>
                            <th class="px-6 py-4 text-right hidden sm:table-cell">Meds</th>
                            <th class="px-6 py-4 text-right hidden md:table-cell">Appts</th>
                            <th class="px-6 py-4 text-right hidden md:table-cell">Labs</th>
                            <th class="px-6 py-4 text-right hidden lg:table-cell">Services</th>
                            <th class="px-6 py-4 text-right">Total Contrib.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        @forelse ($patientRevenues as $revenue)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/20 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-md ring-2 ring-white dark:ring-slate-800">
                                            {{ strtoupper(substr($revenue->patient->first_name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                {{ $revenue->patient->first_name }} {{ $revenue->patient->last_name }}
                                            </div>
                                            <div class="text-xs font-mono text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700/50 px-1.5 py-0.5 rounded w-fit mt-0.5">
                                                {{ $revenue->patient->patient_uid }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-right text-sm font-medium text-slate-600 dark:text-slate-300 hidden sm:table-cell">
                                    {{ number_format($revenue->medications, 0) }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium text-slate-600 dark:text-slate-300 hidden md:table-cell">
                                    {{ number_format($revenue->appointments, 0) }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium text-slate-600 dark:text-slate-300 hidden md:table-cell">
                                    {{ number_format($revenue->labs, 0) }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium text-slate-600 dark:text-slate-300 hidden lg:table-cell">
                                    {{ number_format($revenue->admissions + $revenue->bed_fees, 0) }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20">
                                        FCFA {{ number_format($revenue->total, 0) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                                            <x-heroicon-o-document-magnifying-glass class="w-8 h-8 text-slate-400" />
                                        </div>
                                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">No Revenue Records Found</h3>
                                        <p class="text-slate-500 dark:text-slate-400 max-w-sm mt-2">
                                            There are no revenue records available for the selected time period. Try adjusting the filters.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($patientRevenues->hasPages())
                <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                    {{ $patientRevenues->links() }}
                </div>
            @endif
        </div>
    </main>
</div>
