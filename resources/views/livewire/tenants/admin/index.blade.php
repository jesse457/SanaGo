<div class="min-h-full relative"
     x-data="{
        chart: null,
        init() {
            this.$nextTick(() => this.initChart());
        },
        initChart() {
            const ctx = document.getElementById('patientFlowChart');
            if (!ctx) return;

            // Data passed from Livewire PHP component
            const labels = @js($patientFlowLabels ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);
            const data = @js($patientFlowData ?? [0,0,0,0,0,0,0]);

            // Prevent re-initialization
            if (this.chart) this.chart.destroy();

            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 350);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.25)');
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Appointments',
                        data: data,
                        backgroundColor: gradient,
                        borderColor: '#4f46e5',
                        borderWidth: 2.5,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#9ca3af' } },
                        y: { beginAtZero: true, grid: { color: 'rgba(156, 163, 175, 0.1)' }, ticks: { color: '#9ca3af' } }
                    }
                }
            });
        }
     }">

    {{-- Background Styling (Confined to main area) --}}
    <div class="absolute inset-0 z-0 pointer-events-none h-[500px]">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/40 via-white/20 to-blue-50/40 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800"></div>
    </div>

    {{-- TOP NAVBAR --}}
    <nav class="sticky top-0 left-0 right-0 z-30 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200/70 dark:border-gray-700/70 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-4">
                {{-- Mobile Menu Trigger (Linked to Layout Alpine Data) --}}
                <button @click="mobileOpen = !mobileOpen"
                    class="p-2 rounded-md text-gray-500 lg:hidden hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                {{-- Dashboard Title --}}
                <div class="flex items-center">
                    <div class="bg-indigo-600/10 p-2 rounded-lg mr-3 hidden md:block">
                        <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">Dashboard</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ now()->format('l, F j, Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center space-x-2 sm:space-x-4">
                {{-- Quick Search --}}
                <div class="hidden md:block">
                    <div class="relative">
                        <input type="text" placeholder="Quick search..."
                            class="w-64 pl-10 pr-4 py-2 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                        <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                {{-- Notifications --}}
                <button class="relative p-2 rounded-lg text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    @if (isset($lowStockCount) && $lowStockCount > 0)
                        <span class="absolute top-1.5 right-1.5 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white dark:ring-gray-900"></span>
                    @endif
                </button>

                {{-- User Profile --}}
                <div class="flex items-center pl-3 sm:border-l border-gray-200 dark:border-gray-700">
                    <div class="text-right mr-3 hidden sm:block">
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Admin</p>
                    </div>
                    <button class="h-9 w-9 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 p-[2px] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <div class="h-full w-full rounded-full bg-white dark:bg-gray-900 flex items-center justify-center">
                            @if(auth()->user()->profile_picture)
                                <img src="{{ auth()->user()->profile_picture }}" class="h-full w-full rounded-full object-cover">
                            @else
                                <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                                </span>
                            @endif
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <div class="relative z-10 p-6 mx-auto max-w-7xl">

        {{-- Header Section --}}
        <header class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-gray-900 dark:text-white">
                    {{ $greeting ?? 'Hello' }}, {{ explode(' ', auth()->user()->name ?? 'User')[0] }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium flex items-center">
                    <span class="relative flex h-2 w-2 mr-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    System Operational
                </p>
            </div>

            {{-- Low Stock Alert --}}
            @if (isset($lowStockCount) && $lowStockCount > 0)
                <div class="mt-4 md:mt-0 bg-red-50 border border-red-100 text-red-700 px-4 py-3 rounded-xl flex items-center shadow-sm">
                    <svg class="w-5 h-5 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <span class="block font-bold text-sm">{{ $lowStockCount }} Items Low Stock</span>
                        <span class="block text-xs opacity-75">Check inventory immediately</span>
                    </div>
                </div>
            @endif
        </header>

        {{-- 1. METRIC CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Card 1: Revenue --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg text-emerald-600 dark:text-emerald-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-emerald-600 bg-emerald-50 text-xs font-semibold px-2 py-1 rounded-full">+12%</span>
                </div>
                <p class="text-sm font-medium text-gray-500">Daily Revenue</p>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">
                    {{ number_format($dailyTotalRevenue ?? 0) }} <span class="text-sm font-normal text-gray-400">FCFA</span>
                </h3>
            </div>

            {{-- Card 2: Admitted --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-blue-600 dark:text-blue-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500">Admitted Today</p>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $totalPatientsAdmittedToday ?? 0 }}</h3>
            </div>

            {{-- Card 3: Appointments --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-amber-50 dark:bg-amber-900/20 rounded-lg text-amber-600 dark:text-amber-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500">Appointments</p>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $totalAppointmentsToday ?? 0 }}</h3>
            </div>

            {{-- Card 4: Bed Occupancy --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2.5 bg-rose-50 dark:bg-rose-900/20 rounded-lg text-rose-600 dark:text-rose-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    @php
                        $occ = $totalBedsOccupied ?? 0;
                        $total = $totalBeds ?? 1;
                        $percent = $total > 0 ? round(($occ / $total) * 100) : 0;
                    @endphp
                    <span class="text-xs font-bold text-gray-500">{{ $occ }}/{{ $total }}</span>
                </div>
                <p class="text-sm font-medium text-gray-500">Bed Occupancy</p>
                <div class="flex items-end gap-2">
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white mt-1">{{ $percent }}%</h3>
                </div>
                <div class="mt-3 w-full bg-gray-100 rounded-full h-1.5 dark:bg-gray-700 overflow-hidden">
                    <div class="bg-gradient-to-r from-rose-500 to-pink-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                </div>
            </div>
        </div>

        {{-- 2. CHARTS AREA --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            {{-- Main Chart --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Patient Flow</h2>
                    <select class="text-xs border-none bg-gray-100 dark:bg-gray-700 rounded-md py-1 px-2 focus:ring-0 text-gray-600">
                        <option>Last 6 Months</option>
                        <option>Last 30 Days</option>
                    </select>
                </div>
                <div class="relative h-80 w-full">
                    <canvas id="patientFlowChart"></canvas>
                </div>
            </div>

            {{-- Weekly Breakdown --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Weekly Encounters</h2>
                <div class="space-y-5">
                    @php
                        $summaryData = $encounterSummaryData ?? [10, 20, 15, 30];
                        $summaryLabels = $encounterSummaryLabels ?? ['Consultation', 'Emergency', 'Lab', 'Surgery'];
                        $maxVal = max($summaryData) > 0 ? max($summaryData) : 1;
                    @endphp
                    @foreach ($summaryData as $index => $count)
                        @php $pct = ($count / $maxVal) * 100; @endphp
                        <div>
                            <div class="flex justify-between text-xs mb-1.5">
                                <span class="font-medium text-gray-600 dark:text-gray-300">{{ $summaryLabels[$index] ?? 'Other' }}</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ $count }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2 dark:bg-gray-700">
                                <div class="bg-indigo-500 h-2 rounded-full transition-all duration-1000" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Mini Stats --}}
                <div class="mt-8 grid grid-cols-3 gap-2 text-center border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div>
                        <span class="block text-xl font-black text-indigo-600">{{ $totalDoctors ?? 0 }}</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-gray-400">Doctors</span>
                    </div>
                    <div>
                        <span class="block text-xl font-black text-indigo-600">{{ $totalSystemUsers ?? 0 }}</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-gray-400">Users</span>
                    </div>
                    <div>
                        <span class="block text-xl font-black text-indigo-600">{{ $totalDepartments ?? 0 }}</span>
                        <span class="text-[10px] uppercase font-bold tracking-wider text-gray-400">Depts</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. BOTTOM TABLES --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-8">
            {{-- Staff Distribution --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Staff Distribution</h2>
                </div>
                <div class="p-0 overflow-x-auto">
                    <table class="min-w-full align-middle">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="text-left text-xs font-semibold text-gray-500 uppercase px-6 py-3">Role</th>
                                <th class="text-center text-xs font-semibold text-gray-500 uppercase px-6 py-3">Total</th>
                                <th class="text-right text-xs font-semibold text-gray-500 uppercase px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($userRoleSummary ?? [] as $summary)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $summary['role_name'] }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{ $summary['total_users'] }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $summary['active_users'] > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $summary['active_users'] }} Active
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-6 text-gray-500 text-sm">No staff data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent Admissions --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Recent Admissions</h2>
                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View All</a>
                </div>
                <div class="space-y-4">
                    @forelse($recentAdmissions ?? [] as $admission)
                        <div class="group flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/30 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/20 border border-transparent hover:border-indigo-100 transition-all cursor-pointer">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center text-indigo-600 font-black text-sm mr-3 border border-gray-100 dark:border-gray-600 group-hover:border-indigo-200">
                                    {{ substr($admission->patient?->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-indigo-700">
                                        {{ $admission->patient?->name ?? 'Unknown Patient' }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <span class="font-medium text-gray-600">Ward:</span> {{ $admission->bed?->ward?->name ?? 'N/A' }}
                                        &bull; <span class="font-medium text-gray-600">Bed:</span> {{ $admission->bed?->bed_number ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-400 mb-1">{{ $admission->created_at->format('M d, H:i') }}</p>
                                <span class="inline-block text-[10px] px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-semibold">
                                    {{ $admission->status ?? 'Admitted' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            </div>
                            <p class="text-gray-500 text-sm">No recent admissions found.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
