<div class="flex-1 bg-gray-50 h-screen overflow-y-auto  dark:bg-gray-900 font-sans"
    x-cloak
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

    {{--
      ========================================
      NETWORK STATUS (System Level)
      ========================================
    --}}
    <div x-show="isOffline" x-transition.origin.top
         class="bg-rose-600 text-white text-xs font-bold text-center py-2 relative z-50 shadow-md">
        <div class="flex items-center justify-center gap-2">
            <x-heroicon-s-wifi class="w-4 h-4 opacity-80" />
            <span>YOU ARE OFFLINE. CHANGES MAY NOT SAVE.</span>
        </div>
    </div>

    <div x-show="showOnlineToast"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-y-10 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-10 opacity-0"
         class="fixed bottom-6 right-6 z-50 bg-emerald-600 text-white px-4 py-3 rounded-lg shadow-lg flex items-center gap-3">
        <x-heroicon-s-check-circle class="w-5 h-5" />
        <div>
            <p class="text-sm font-bold">Back Online</p>
        </div>
    </div>

    {{--
      ========================================
      STICKY HEADER
      ========================================
    --}}
    <header class="sticky top-0 z-20 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-6 py-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">

            {{-- Left: Context --}}
            <div class="flex items-center gap-3">
                <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg hidden sm:block">
                    <x-heroicon-s-building-office-2 class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                        {{ __('ui.dashboard') }}
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                        {{ __('ui.welcome_message', ['name' => auth()->user()->name ?? 'Admin']) }}
                    </p>
                </div>
            </div>

            {{-- Right: Filters & Actions --}}
            <div class="flex items-center gap-3 w-full md:w-auto">

                {{-- Date Range Picker --}}
                <div class="relative group w-full md:w-48">
                    <x-heroicon-m-calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none z-10" />
                    <select class="w-full appearance-none bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block pl-9 p-2.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition cursor-pointer">
                        <option>{{ __('ui.last_30_days') }}</option>
                        <option>{{ __('ui.last_3_months') }}</option>
                        <option>{{ __('ui.last_6_months') }}</option>
                        <option>{{ __('ui.last_year') }}</option>
                    </select>
                    <x-heroicon-m-chevron-down class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                </div>

                <div class="h-8 w-px bg-gray-200 dark:bg-gray-700 hidden md:block"></div>

                {{-- Language Switcher (Compact) --}}
                <div class="shrink-0">
                    <x-language-switcher />
                </div>
            </div>
        </div>
    </header>

    {{--
      ========================================
      MAIN CONTENT
      ========================================
    --}}
    <div class="p-6 space-y-6">

        {{-- KPI Cards --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $stats = [
                    [
                        'title' => 'ui.total_tenants',
                        'value' => $totalTenants ?? 125,
                        'change' => '12%',
                        'trend' => 'up',
                        'icon' => 'users',
                        'bg' => 'bg-indigo-50 dark:bg-indigo-900/20',
                        'text' => 'text-indigo-600 dark:text-indigo-400',
                    ],
                    [
                        'title' => 'ui.active_subscriptions',
                        'value' => $activeSubscriptions ?? 110,
                        'change' => '8%',
                        'trend' => 'up',
                        'icon' => 'check-badge',
                        'bg' => 'bg-emerald-50 dark:bg-emerald-900/20',
                        'text' => 'text-emerald-600 dark:text-emerald-400',
                    ],
                    [
                        'title' => 'ui.monthly_revenue',
                        'value' => '$' . number_format($monthlyRevenue ?? 25400, 0),
                        'change' => '23%',
                        'trend' => 'up',
                        'icon' => 'banknotes',
                        'bg' => 'bg-amber-50 dark:bg-amber-900/20',
                        'text' => 'text-amber-600 dark:text-amber-400',
                    ],
                    [
                        'title' => 'ui.new_tenants',
                        'value' => $newTenants ?? 8,
                        'change' => '5%',
                        'trend' => 'down',
                        'icon' => 'user-plus',
                        'bg' => 'bg-rose-50 dark:bg-rose-900/20',
                        'text' => 'text-rose-600 dark:text-rose-400',
                    ],
                ];
            @endphp

            @foreach ($stats as $stat)
                <div class="group bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __($stat['title']) }}</p>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stat['value'] }}</h3>
                        </div>
                        <span class="p-2 {{ $stat['bg'] }} {{ $stat['text'] }} rounded-lg group-hover:scale-110 transition-transform duration-300">
                            <x-dynamic-component :component="'heroicon-o-' . $stat['icon']" class="w-6 h-6" />
                        </span>
                    </div>

                    <div class="flex items-center mt-3 text-xs font-medium">
                        @if($stat['trend'] === 'up')
                            <span class="text-emerald-600 dark:text-emerald-400 flex items-center bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-full">
                                <x-heroicon-m-arrow-trending-up class="w-3 h-3 mr-1" />
                                {{ $stat['change'] }}
                            </span>
                        @else
                            <span class="text-rose-600 dark:text-rose-400 flex items-center bg-rose-50 dark:bg-rose-900/30 px-2 py-0.5 rounded-full">
                                <x-heroicon-m-arrow-trending-down class="w-3 h-3 mr-1" />
                                {{ $stat['change'] }}
                            </span>
                        @endif
                        <span class="text-gray-400 ml-2">{{ __('ui.from_last_month') }}</span>
                    </div>
                </div>
            @endforeach
        </section>

        {{-- Charts Grid --}}
        <section class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Revenue Chart --}}
            {{--
                NOTE: wire:ignore is crucial here.
                It stops Livewire from destroying the canvas when other parts of the page update.
            --}}
            <div wire:ignore
                 class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col h-80"
                 x-data="adminChart({
                    type: 'line',
                    color: '#6366f1',
                    label: 'Revenue',
                    data: @js($revenueChart['data'] ?? [12, 19, 15, 25, 22, 30]),
                    labels: @js($revenueChart['labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'])
                 })">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">{{ __('ui.revenue_growth') }}</h2>
                    <button class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                        <x-heroicon-m-ellipsis-horizontal class="w-6 h-6" />
                    </button>
                </div>
                <div class="relative flex-1 w-full overflow-hidden">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>

            {{-- Tenant Growth Chart --}}
            <div wire:ignore
                 class="bg-white dark:bg-gray-800 p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col h-80"
                 x-data="adminChart({
                    type: 'bar',
                    color: '#10b981',
                    label: 'New Tenants',
                    data: @js($tenantGrowthChart['data'] ?? [5, 8, 12, 10, 15, 20]),
                    labels: @js($tenantGrowthChart['labels'] ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'])
                 })">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-base font-bold text-gray-900 dark:text-white">{{ __('ui.tenant_growth') }}</h2>
                    <button class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                        <x-heroicon-m-ellipsis-horizontal class="w-6 h-6" />
                    </button>
                </div>
                <div class="relative flex-1 w-full overflow-hidden">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </section>

        {{-- Recent Tenants Table --}}
        <section class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm flex flex-col">
            {{-- Table Header --}}
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('ui.recent_tenants') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Platform registration overview</p>
                </div>
                <a href="#" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition">
                    {{ __('ui.view_all') }}
                </a>
            </div>

            {{-- Table Content --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('ui.tenant_name') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('ui.domain') }}</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('ui.subscription') }}</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">{{ __('ui.joined_on') }}</th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Edit</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse ($recentTenants ?? [] as $tenant)
                            <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/25 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-9 w-9 rounded bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 flex items-center justify-center font-bold text-xs border border-indigo-200 dark:border-indigo-800">
                                            {{ substr($tenant['name'], 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant['name'] }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">ID: #{{ $tenant['id'] ?? rand(100, 999) }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $tenant['hospital_name'] }}</span>
                                        <a href="#" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">{{ $tenant['domain'] }}</a>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $sub = $tenant['subscription'];
                                        $styles = match ($sub) {
                                            'Premium' => 'bg-emerald-100 text-emerald-800 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800',
                                            'Basic'   => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800',
                                            default   => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400 dark:border-amber-800',
                                        };
                                        $dot = match ($sub) {
                                            'Premium' => 'bg-emerald-500',
                                            'Basic'   => 'bg-blue-500',
                                            default   => 'bg-amber-500',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $styles }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dot }} mr-1.5"></span>
                                        {{ $sub }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500 dark:text-gray-400 font-mono">
                                    {{ $tenant['created_at'] }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                        <x-heroicon-o-pencil-square class="w-5 h-5" />
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="p-3 bg-gray-100 dark:bg-gray-800 rounded-full mb-3">
                                            <x-heroicon-o-inbox class="w-6 h-6 text-gray-400" />
                                        </div>
                                        <p class="font-medium text-gray-900 dark:text-gray-200">{{ __('ui.no_records_found') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

{{--
  ========================================
  ALPINE CHART COMPONENT
  ========================================
--}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('adminChart', (config) => ({
            chart: null,
            init() {
                this.render();
            },
            render() {
                if (this.chart) this.chart.destroy();

                const ctx = this.$refs.canvas.getContext('2d');

                // Create gradient for "Area" look
                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, this.hexToRgba(config.color, 0.15));
                gradient.addColorStop(1, this.hexToRgba(config.color, 0.0));

                this.chart = new Chart(ctx, {
                    type: config.type,
                    data: {
                        labels: config.labels,
                        datasets: [{
                            label: config.label,
                            data: config.data,
                            borderColor: config.color,
                            backgroundColor: config.type === 'bar' ? config.color : gradient,
                            borderWidth: 2,
                            borderRadius: 4,
                            pointRadius: 0,
                            pointHoverRadius: 6,
                            pointBackgroundColor: config.color,
                            fill: true,
                            tension: 0.35 // Curve tension
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { intersect: false, mode: 'index' },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(17, 24, 39, 0.95)',
                                padding: 12,
                                cornerRadius: 8,
                                displayColors: false,
                                titleFont: { size: 12 },
                                bodyFont: { size: 14, weight: 'bold' }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                border: { display: false },
                                grid: { color: 'rgba(156, 163, 175, 0.1)' },
                                ticks: { font: { size: 11 }, color: '#9ca3af' }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 11 }, color: '#9ca3af' }
                            }
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
