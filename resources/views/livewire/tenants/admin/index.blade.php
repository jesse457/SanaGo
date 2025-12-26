<div class="flex-1 bg-gray-50 h-screen overflow-y-auto  dark:bg-gray-900 font-sans"
    x-cloak
    x-data="{ mobileOpen: false }">

    {{-- Sticky Header --}}
    <header class="sticky top-0 z-20 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">

                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg hidden sm:block">
                        <x-heroicon-s-chart-pie class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">Admin Dashboard</h1>
                        <p class="text-xs text-gray-500 font-medium">System Overview</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-4">
                {{-- Low Stock Alert --}}
                @if(isset($lowStockCount) && $lowStockCount > 0)
                    <div class="flex items-center text-xs font-bold text-red-600 bg-red-50 px-3 py-1.5 rounded-full border border-red-200">
                        <x-heroicon-s-exclamation-triangle class="w-4 h-4 mr-1" />
                        {{ $lowStockCount }} Low Stock
                    </div>
                @endif
        {{-- Dark Mode Toggle --}}
                <button @click="$store.theme.toggle()" class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <x-heroicon-o-sun x-show="!$store.theme.on" class="w-5 h-5 text-gray-500" />
                    <x-heroicon-o-moon x-show="$store.theme.on" class="w-5 h-5 text-yellow-400" />
                </button>

                {{-- Profile --}}
                <div class="relative" x-data="{ open: false }">

                    <button @click="open = !open" class="flex items-center gap-2 group">
                         <img src="{{ auth()->user()->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) }}" class="w-8 h-8 rounded-full border border-gray-200">
                    </button>
                    {{-- Dropdown omitted --}}
                </div>
            </div>
        </div>
    </header>

    <div class="p-6 space-y-6">

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $stats = [
                    ['label' => 'Daily Revenue', 'val' => number_format($dailyTotalRevenue ?? 0) . ' FCFA', 'icon' => 'banknotes', 'color' => 'emerald'],
                    ['label' => 'Admitted Today', 'val' => $totalPatientsAdmittedToday ?? 0, 'icon' => 'user-plus', 'color' => 'blue'],
                    ['label' => 'Appointments', 'val' => $totalAppointmentsToday ?? 0, 'icon' => 'calendar', 'color' => 'amber'],
                    ['label' => 'Bed Occupancy', 'val' => ($totalBedsOccupied ?? 0) . '/' . ($totalBeds ?? 0), 'icon' => 'home-modern', 'color' => 'rose'],
                ];
            @endphp
            @foreach($stats as $stat)
                <div class="group bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-2 truncate">{{ $stat['val'] }}</h3>
                        </div>
                        <span class="p-2 bg-{{ $stat['color'] }}-50 text-{{ $stat['color'] }}-600 rounded-lg">
                            <x-dynamic-component :component="'heroicon-o-' . $stat['icon']" class="w-6 h-6" />
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Charts Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Chart --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6"
                 wire:ignore
                 x-data="{
                    init() {
                        const ctx = this.$refs.canvas.getContext('2d');
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: @js($patientFlowLabels ?? ['M','T','W','T','F','S','S']),
                                datasets: [{
                                    label: 'Patients',
                                    data: @js($patientFlowData ?? [0,0,0,0,0,0,0]),
                                    borderColor: '#4f46e5',
                                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                    fill: true,
                                    tension: 0.4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                                    x: { grid: { display: false } }
                                }
                            }
                        });
                    }
                 }">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-4">Patient Flow</h3>
                <div class="h-72 w-full">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>

            {{-- Summary Stats --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-6">Weekly Breakdown</h3>
                <div class="space-y-4">
                     @php
                        $sData = $encounterSummaryData ?? [10, 20, 5, 2];
                        $sLabels = $encounterSummaryLabels ?? ['Consultation', 'Emergency', 'Lab', 'Surgery'];
                    @endphp
                    @foreach($sData as $i => $val)
                         <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="font-medium text-gray-600 dark:text-gray-300">{{ $sLabels[$i] }}</span>
                                <span class="font-bold">{{ $val }}</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1.5 dark:bg-gray-700">
                                <div class="bg-indigo-500 h-1.5 rounded-full" style="width: {{ ($val / (max($sData) ?: 1)) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Bottom Tables --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Staff --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-bold text-gray-900 dark:text-white">Staff Distribution</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Role</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Active</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($userRoleSummary ?? [] as $role)
                        <tr>
                            <td class="px-6 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $role['role_name'] }}</td>
                            <td class="px-6 py-3 text-right text-sm text-green-600 font-medium">{{ $role['active_users'] }} Online</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Recent Admissions --}}
             <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
                <h3 class="font-bold text-gray-900 dark:text-white mb-4">Recent Admissions</h3>
                <div class="space-y-3">
                    @forelse($recentAdmissions ?? [] as $adm)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                    {{ substr($adm->patient?->name ?? '?', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $adm->patient?->name }}</p>
                                    <p class="text-xs text-gray-500">Ward: {{ $adm->bed?->ward?->name }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-medium bg-blue-100 text-blue-700 px-2 py-0.5 rounded">{{ $adm->status }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center">No recent admissions.</p>
                    @endforelse
                </div>
             </div>
        </div>
    </div>
</div>
