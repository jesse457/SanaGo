<div class="flex-1 bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen" x-data="{
    sidebarOpen: false,
    chart: null,
    init() {
        this.$nextTick(() => this.initChart());
    },
    initChart() {
        const ctx = document.getElementById('patientFlowChart');
        if (!ctx) return;

        // Data passed from Livewire
        const labels = @js($patientFlowLabels);
        const data = @js($patientFlowData);

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

    {{-- Background Styling --}}
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
        <div
            class="absolute inset-0 bg-gradient-to-br from-indigo-50/40 via-white/20 to-blue-50/40 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
        </div>
    </div>



    
    {{-- 3. MAIN CONTENT --}}
    <main class="relative z-10 p-6     mx-auto pt-15">
        {{-- 2. NAVBAR (Moved outside of Main) --}}
        <nav class=" w-auto fixed top-0 right-0 left-0   bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200/70 dark:border-gray-700/70">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        {{-- Mobile Menu Button --}}
                        <button @click="sidebarOpen = !sidebarOpen"
                            class="mr-4 p-2 rounded-md text-gray-500 lg:hidden hover:bg-gray-100 dark:hover:bg-gray-800">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>

                        {{-- Dashboard Title --}}
                        <div class="flex items-center">
                            <svg class="h-8 w-8 text-indigo-600 dark:text-indigo-400 hidden md:block" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            <div class="ml-3">
                                <h1 class="text-lg font-semibold text-gray-900 dark:text-white">Dashboard</h1>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ now()->format('l, F j, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        {{-- Quick Search --}}
                        <div class="hidden md:block">
                            <div class="relative">
                                <input type="text" placeholder="Quick search..."
                                    class="w-64 pl-10 pr-4 py-2 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        {{-- Notifications --}}
                        <button
                            class="relative p-2 rounded-lg text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            @if (isset($lowStockCount) && $lowStockCount > 0)
                                <span class="absolute top-1 right-1 h-2 w-2 rounded-full bg-red-500"></span>
                            @endif
                        </button>

                        {{-- Dark mode toggle --}}
                        <button
                            class="p-2 rounded-lg text-gray-500 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <svg class="h-5 w-5 hidden dark:block" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg class="h-5 w-5 block dark:hidden" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        {{-- User Profile --}}
                        <div class="flex items-center pl-3 border-l border-gray-200 dark:border-gray-700">
                            <div class="text-right mr-3 hidden sm:block">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $userName ?? 'User' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Administrator</p>
                            </div>
                            <button
                                class="h-9 w-9 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-sm hover:ring-2 hover:ring-indigo-500 transition-all">
                                {{ substr($userName ?? 'U', 0, 1) }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
        {{-- Header --}}
        <header class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10">
            <div>
                <h1 class="text-4xl font-black tracking-tight text-gray-900 dark:text-white">
                    {{ $greeting ?? 'Welcome' }}, {{ explode(' ', $userName ?? 'User')[0] }}
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">
                    <span class="inline-block w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span>
                    System Overview
                </p>
            </div>

            {{-- Low Stock Alert --}}
            @if (isset($lowStockCount) && $lowStockCount > 0)
                <div
                    class="mt-4 md:mt-0 bg-red-100 border border-red-200 text-red-800 px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <span class="font-bold">{{ $lowStockCount }} Supplies Low Stock</span>
                </div>
            @endif
        </header>

        {{-- 1. METRIC CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            {{-- Daily Revenue --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl">
                        <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500">Daily Revenue</p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                    {{ number_format($dailyTotalRevenue) }} <span class="text-xs text-gray-400">FCFA</span></h3>
            </div>

            {{-- Admitted Today --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500">Admitted Today</p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalPatientsAdmittedToday }}
                </h3>
            </div>

            {{-- Appointments --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl">
                        <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500">Appointments</p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalAppointmentsToday }}</h3>
            </div>

            {{-- Bed Occupancy --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-rose-50 dark:bg-rose-900/20 rounded-xl">
                        <svg class="h-6 w-6 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-gray-500">
                        {{ $totalBedsOccupied }}/{{ $totalBeds }}
                    </span>
                </div>
                <p class="text-sm font-medium text-gray-500">Bed Occupancy</p>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                    {{ $totalBeds > 0 ? round(($totalBedsOccupied / $totalBeds) * 100) : 0 }}%
                </h3>
                <div class="mt-3 w-full bg-gray-100 rounded-full h-1.5 dark:bg-gray-700 overflow-hidden">
                    <div class="bg-rose-500 h-1.5 rounded-full"
                        style="width: {{ $totalBeds > 0 ? ($totalBedsOccupied / $totalBeds) * 100 : 0 }}%"></div>
                </div>
            </div>
        </div>

        {{-- 2. CHARTS AREA --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            {{-- Main Chart --}}
            <div
                class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Patient Appointments (6 Months)</h2>
                <div class="relative h-80 w-full">
                    <canvas id="patientFlowChart"></canvas>
                </div>
            </div>

            {{-- Weekly Breakdown --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">This Week</h2>
                <div class="space-y-4">
                    @foreach ($encounterSummaryData as $index => $count)
                        @php
                            $max = max($encounterSummaryData) > 0 ? max($encounterSummaryData) : 1;
                            $pct = ($count / $max) * 100;
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span
                                    class="text-gray-600 dark:text-gray-300">{{ $encounterSummaryLabels[$index] }}</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ $count }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 dark:bg-gray-700">
                                <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ $pct }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Mini Stats --}}
                <div
                    class="mt-8 grid grid-cols-3 gap-2 text-center border-t border-gray-200 dark:border-gray-700 pt-4">
                    <div>
                        <span class="block text-xl font-bold text-indigo-600">{{ $totalDoctors }}</span>
                        <span class="text-[10px] uppercase text-gray-500">Doctors</span>
                    </div>
                    <div>
                        <span class="block text-xl font-bold text-indigo-600">{{ $totalSystemUsers }}</span>
                        <span class="text-[10px] uppercase text-gray-500">Users</span>
                    </div>
                    <div>
                        <span class="block text-xl font-bold text-indigo-600">{{ $totalDepartments }}</span>
                        <span class="text-[10px] uppercase text-gray-500">Depts</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. BOTTOM TABLES --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-8">

            {{-- Staff Distribution --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Staff Distribution</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-700">
                                <th class="text-left text-xs font-semibold text-gray-500 uppercase pb-2">Role</th>
                                <th class="text-center text-xs font-semibold text-gray-500 uppercase pb-2">Total</th>
                                <th class="text-right text-xs font-semibold text-gray-500 uppercase pb-2">Active</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($userRoleSummary as $summary)
                                <tr>
                                    <td class="py-3 text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $summary['role_name'] }}</td>
                                    <td class="py-3 text-center text-sm text-gray-500">{{ $summary['total_users'] }}
                                    </td>
                                    <td class="py-3 text-right">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $summary['active_users'] > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $summary['active_users'] }} Active
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-gray-500 text-sm">No staff data
                                        found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Recent Admissions --}}
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Recent Admissions</h2>
                <div class="space-y-4">
                    @forelse($recentAdmissions as $admission)
                        <div
                            class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex items-center">
                                <div
                                    class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-600 font-bold text-sm mr-3">
                                    {{ substr($admission->patient?->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $admission->patient?->name ?? 'Unknown Patient' }}</p>
                                    <p class="text-xs text-gray-500">
                                        Ward: {{ $admission->bed?->ward?->name ?? 'N/A' }} - Bed
                                        {{ $admission->bed?->bed_number ?? 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-400">{{ $admission->created_at->format('M d, H:i') }}</p>
                                <span
                                    class="inline-block text-[10px] px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 border border-blue-100">
                                    {{ $admission->status ?? 'Admitted' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400 text-sm">No recent admissions.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</div>
