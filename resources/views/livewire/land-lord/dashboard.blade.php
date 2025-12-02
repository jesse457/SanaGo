<div>
    
    <!--
      CONTENT WRAPPER
      - Reduced spacing (space-y-4)
      - Full width (max-w-full)
      - Minimal outer padding (p-2 md:p-4)
    -->
    <div class="max-w-full mx-auto space-y-4 p-2 md:p-4">

        <!-- Header (Compact) -->
        <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    {{ __('ui.dashboard') }}
                </h1>
                <p class="text-xs md:text-sm text-slate-500 dark:text-slate-400">
                    {{ __('ui.welcome_message', ['name' => auth()->user()->name ?? 'User']) }}
                </p>
            </div>

            <div class="flex items-center gap-2 w-full md:w-auto">
                <div class="relative group w-full md:w-40">
                    <select class="w-full appearance-none bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 pl-3 pr-8 py-2 text-xs font-medium text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all cursor-pointer">
                        <option>{{ __('ui.last_30_days') }}</option>
                        <option>{{ __('ui.last_3_months') }}</option>
                        <option>{{ __('ui.last_6_months') }}</option>
                        <option>{{ __('ui.last_year') }}</option>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400 group-hover:text-indigo-500 transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                <!-- Assuming language switcher is compact -->
                <x-language-switcher class="shrink-0 scale-90 origin-right"/>
            </div>
        </header>

        <!-- Stats Grid (Compact) -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $stats = [
                    ['title' => 'ui.total_tenants', 'value' => $totalTenants ?? 125, 'change' => '12%', 'trend' => 'up', 'icon' => 'users', 'theme' => 'indigo'],
                    ['title' => 'ui.active_subscriptions', 'value' => $activeSubscriptions ?? 110, 'change' => '8%', 'trend' => 'up', 'icon' => 'check-circle', 'theme' => 'emerald'],
                    ['title' => 'ui.monthly_revenue', 'value' => '$' . number_format($monthlyRevenue ?? 25400, 0), 'change' => '23%', 'trend' => 'up', 'icon' => 'currency-dollar', 'theme' => 'amber'],
                    ['title' => 'ui.new_tenants', 'value' => $newTenants ?? 8, 'change' => '5%', 'trend' => 'down', 'icon' => 'plus-circle', 'theme' => 'rose']
                ];
            @endphp

            @foreach ($stats as $stat)
                @php
                    $colors = match($stat['theme']) {
                        'indigo' => ['bg' => 'bg-indigo-50 dark:bg-indigo-900/40', 'text' => 'text-indigo-600 dark:text-indigo-400'],
                        'emerald' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/40', 'text' => 'text-emerald-600 dark:text-emerald-400'],
                        'amber' => ['bg' => 'bg-amber-50 dark:bg-amber-900/40', 'text' => 'text-amber-600 dark:text-amber-400'],
                        'rose' => ['bg' => 'bg-rose-50 dark:bg-rose-900/40', 'text' => 'text-rose-600 dark:text-rose-400'],
                    };
                    $trendColor = $stat['trend'] === 'up' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400';
                    $trendIcon = $stat['trend'] === 'up' ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6';
                @endphp

                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 hover:shadow-md transition-shadow duration-300 group">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __($stat['title']) }}</p>
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white mt-1 tracking-tight">{{ $stat['value'] }}</h3>
                        </div>
                        <div class="p-2 rounded-lg {{ $colors['bg'] }} {{ $colors['text'] }} transition-transform group-hover:scale-110 duration-300">
                             <x-dynamic-component :component="'heroicon-o-'.$stat['icon']" class="h-5 w-5" />
                        </div>
                    </div>
                    <div class="flex items-center mt-3">
                        <span class="flex items-center text-[10px] font-bold {{ $trendColor }} bg-slate-50 dark:bg-gray-700/50 px-1.5 py-0.5 rounded">
                            <svg class="w-2.5 h-2.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $trendIcon }}"></path></svg>
                            {{ $stat['change'] }}
                        </span>
                        <span class="text-[10px] text-slate-400 ml-1.5">{{ __('ui.from_last_month') }}</span>
                    </div>
                </div>
            @endforeach
        </section>

        <!-- Charts Section (Height Reduced to h-64 / 256px) -->
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Revenue Chart -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col h-64"
                 x-data="dashboardChart({
                    type: 'line',
                    color: '#6366f1',
                    label: 'Revenue',
                    data: @js($revenueChart['data'] ?? [12, 19, 15, 25, 22, 30]),
                    labels: @js($revenueChart['labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'])
                 })"
                 x-init="initChart()">

                <div class="flex justify-between items-center mb-2">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('ui.revenue_growth') }}</h2>
                    </div>
                    <button class="text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                        <x-heroicon-o-ellipsis-horizontal class="w-5 h-5" />
                    </button>
                </div>
                <div class="relative flex-1 w-full overflow-hidden">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>

            <!-- Tenant Growth Chart -->
            <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 flex flex-col h-64"
                 x-data="dashboardChart({
                    type: 'bar',
                    color: '#10b981',
                    label: 'New Tenants',
                    data: @js($tenantGrowthChart['data'] ?? [5, 8, 12, 10, 15, 20]),
                    labels: @js($tenantGrowthChart['labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'])
                 })"
                 x-init="initChart()">

                <div class="flex justify-between items-center mb-2">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('ui.tenant_growth') }}</h2>
                    </div>
                    <button class="text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                        <x-heroicon-o-ellipsis-horizontal class="w-5 h-5" />
                    </button>
                </div>
                <div class="relative flex-1 w-full overflow-hidden">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </section>

        <!-- Table Section (Compact Rows) -->
        <section class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">{{ __('ui.recent_tenants') }}</h2>
                <a href="#" class="text-xs font-semibold text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 flex items-center transition">
                    {{ __('ui.view_all') }}
                    <x-heroicon-o-arrow-right class="w-3 h-3 ml-1" />
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900/50">
                        <tr>
                            @foreach(['tenant_name', 'hospital_name', 'domain', 'subscription', 'joined_on'] as $head)
                                <th scope="col" class="px-4 py-2 text-left text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('ui.'.$head) }}</th>
                            @endforeach
                            <th scope="col" class="relative px-4 py-2"><span class="sr-only">{{ __('ui.actions') }}</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse ($recentTenants ?? [] as $tenant)
                        <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xs shadow-sm ring-2 ring-white dark:ring-gray-800">
                                        {{ substr($tenant['name'], 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-xs font-semibold text-slate-900 dark:text-white">{{ $tenant['name'] }}</div>
                                        <div class="text-[10px] text-slate-500 dark:text-slate-400">ID: #{{ $tenant['id'] ?? rand(100,999) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-xs text-slate-600 dark:text-slate-300">{{ $tenant['hospital_name'] }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-xs text-indigo-500 hover:underline cursor-pointer">{{ $tenant['domain'] }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                @php
                                    $sub = $tenant['subscription'];
                                    $badge = match($sub) {
                                        'Premium' => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
                                        'Basic' => 'bg-indigo-50 text-indigo-700 border-indigo-200 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20',
                                        default => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
                                    };
                                    $dot = match($sub) {
                                        'Premium' => 'bg-emerald-500',
                                        'Basic' => 'bg-indigo-500',
                                        default => 'bg-amber-500',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium border {{ $badge }}">
                                    <span class="w-1 h-1 rounded-full mr-1.5 {{ $dot }}"></span>
                                    {{ $sub }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-xs text-slate-500">{{ $tenant['created_at'] }}</td>
                            <td class="px-4 py-2.5 whitespace-nowrap text-right text-xs font-medium">
                                <button class="text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                                <p class="text-sm font-semibold">{{ __('ui.no_records_found') }}</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardChart', (config) => ({
            chart: null,
            initChart() {
                if (this.chart) this.chart.destroy();

                const ctx = this.$refs.canvas.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 200); // Shorter gradient for shorter chart
                gradient.addColorStop(0, this.hexToRgba(config.color, 0.2));
                gradient.addColorStop(1, this.hexToRgba(config.color, 0.0));

                this.chart = new Chart(ctx, {
                    type: config.type === 'bar' ? 'bar' : 'line',
                    data: {
                        labels: config.labels,
                        datasets: [{
                            label: config.label,
                            data: config.data,
                            borderColor: config.color,
                            backgroundColor: config.type === 'bar' ? config.color : gradient,
                            borderWidth: 2,
                            borderRadius: 3,
                            pointRadius: 0, // Cleaner look for small charts
                            pointHoverRadius: 4,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: { legend: { display: false }, tooltip: { enabled: true } },
                        scales: {
                            y: { display: true, border: { display: false }, grid: { display: true, drawTicks: false }, ticks: { display: true, font: {size: 10} } },
                            x: { display: true, grid: { display: false }, ticks: { font: {size: 10} } }
                        }
                    }
                });
            },
            hexToRgba(hex, alpha) {
                const r = parseInt(hex.slice(1, 3), 16);
                const g = parseInt(hex.slice(3, 5), 16);
                const b = parseInt(hex.slice(5, 7), 16);
                return `rgba(${r}, ${g}, ${b}, ${alpha})`;
            }
        }));
    });
</script>
