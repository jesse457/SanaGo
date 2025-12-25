

<div class="flex-1 bg-gray-50 dark:bg-gray-900 font-sans"
    x-data="{
        isOffline: !navigator.onLine,
        showOnlineToast: false,
        init() {
            window.addEventListener('offline', () => {
                this.isOffline = true;
                this.showOnlineToast = false;
            });
            window.addEventListener('online', () => {
                this.isOffline = false;
                this.showOnlineToast = true;
                setTimeout(() => this.showOnlineToast = false, 4000);
            });
        }
    }">

    {{-- Network Status Alerts --}}
    <div x-show="isOffline" x-transition
         class="sticky top-0 z-[60] bg-rose-600 text-white text-xs font-bold text-center py-2 shadow-md" x-cloak>
        <div class="flex items-center justify-center gap-2">
            <x-heroicon-s-wifi class="w-4 h-4" />
            <span>YOU ARE OFFLINE. CHANGES MAY NOT SAVE.</span>
        </div>
    </div>

    {{-- Sticky Dashboard Header --}}
    <header class="sticky top-0 lg:top-0 z-20 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 px-4 sm:px-6 py-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">

            {{-- Left: Context --}}
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg hidden xs:block">
                    <x-heroicon-s-building-office-2 class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        {{ __('ui.dashboard') }}
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                        {{ __('ui.welcome_message', ['name' => auth()->user()->name ?? 'Admin']) }}
                    </p>
                </div>
            </div>

            {{-- Right: Filters --}}
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <div class="relative flex-1 sm:w-48">
                    <x-heroicon-m-calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                    <select class="w-full pl-9 pr-8 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-xs sm:text-sm rounded-lg focus:ring-indigo-500 transition cursor-pointer dark:text-gray-200">
                        <option>{{ __('ui.last_30_days') }}</option>
                        <option>Last 3 Months</option>
                    </select>
                </div>
                <div class="shrink-0">
                    <x-language-switcher />
                </div>
            </div>
        </div>
    </header>

      <div class="p-4 sm:p-6 lg:p-8 space-y-6">

        {{-- KPI Cards Grid --}}
        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            @php
                $stats = [
                    ['title' => 'Total Tenants', 'value' => $totalTenants ?? 125, 'icon' => 'users', 'color' => 'indigo'],
                    ['title' => 'Active Subs', 'value' => $activeSubscriptions ?? 110, 'icon' => 'check-badge', 'color' => 'emerald'],
                    ['title' => 'Revenue', 'value' => '$25.4k', 'icon' => 'banknotes', 'color' => 'amber'],
                    ['title' => 'New This Month', 'value' => '12', 'icon' => 'user-plus', 'color' => 'rose'],
                ];
            @endphp

            @foreach ($stats as $stat)
                <div class="bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm transition hover:shadow-md">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $stat['title'] }}</p>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $stat['value'] }}</h3>
                        </div>
                        <div class="p-2 bg-{{ $stat['color'] }}-50 dark:bg-{{ $stat['color'] }}-900/20 text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400 rounded-lg">
                            <x-dynamic-component :component="'heroicon-o-' . $stat['icon']" class="w-6 h-6" />
                        </div>
                    </div>
                </div>
            @endforeach
        </section>

        {{-- Charts Grid --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div wire:ignore class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm h-80 flex flex-col"
                 x-data="adminChart({
                    type: 'line',
                    color: '#6366f1',
                    label: 'Revenue',
                    data: [12, 19, 15, 25, 22, 30],
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
                 })">
                <h2 class="text-sm font-bold text-gray-500 mb-4 uppercase">Revenue Growth</h2>
                <div class="flex-1 min-h-0">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>

            <div wire:ignore class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm h-80 flex flex-col"
                 x-data="adminChart({
                    type: 'bar',
                    color: '#10b981',
                    label: 'New Tenants',
                    data: [5, 8, 12, 10, 15, 20],
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']
                 })">
                <h2 class="text-sm font-bold text-gray-500 mb-4 uppercase">Tenant Growth</h2>
                <div class="flex-1 min-h-0">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </section>

        {{-- Table Section (Responsive) --}}
        <section class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="font-bold text-gray-900 dark:text-white">Recent Tenants</h3>
                <button class="text-sm text-indigo-600 font-semibold hover:underline">View All</button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50 dark:bg-gray-800/50 text-gray-500 text-xs uppercase font-bold">
                        <tr>
                            <th class="px-6 py-3">Tenant</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3 text-right">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach(range(1, 3) as $i)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-xs font-bold text-indigo-700 dark:text-indigo-300">H</div>
                                    <span class="text-sm font-medium dark:text-white">Hospital Center {{ $i }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-full uppercase">Active</span>
                            </td>
                            <td class="px-6 py-4 text-right text-xs text-gray-500 font-mono">2023-10-{{ $i }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    {{-- Online Toast --}}
    <div x-show="showOnlineToast" x-transition class="fixed bottom-6 right-6 z-50 bg-emerald-600 text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-3" x-cloak>
        <x-heroicon-s-check-circle class="w-5 h-5" />
        <span class="text-sm font-bold">Back Online</span>
    </div>
</div>

{{--
  CHART SCRIPT
  Note: Ensure <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  is in your main layout head.
--}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('adminChart', (config) => ({
            chart: null,
            init() {
                // Wait for potential Livewire updates
                this.$nextTick(() => this.render());
            },
            render() {
                const ctx = this.$refs.canvas.getContext('2d');
                if (this.chart) this.chart.destroy();

                this.chart = new Chart(ctx, {
                    type: config.type,
                    data: {
                        labels: config.labels,
                        datasets: [{
                            label: config.label,
                            data: config.data,
                            borderColor: config.color,
                            backgroundColor: config.type === 'bar' ? config.color : 'rgba(99, 102, 241, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { grid: { display: false }, ticks: { display: false } },
                            x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                        }
                    }
                });
            }
        }));
    });
</script>
