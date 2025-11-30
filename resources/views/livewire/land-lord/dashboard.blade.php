<div class="flex-1 p-4 md:p-6 lg:ml-64 bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js" defer></script>

    <style>
        /* Ensures all major sections look consistent and clean */
        .card {
            @apply p-6 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700/50;
        }
    </style>

    <!-- Z-index changed from z-10 to z-20 to ensure the header's stacking context is prioritized over the content sections (z-10) below it. -->
    <header class="mb-10 flex flex-col md:flex-row justify-between items-start md:items-center relative z-20">
        <div>
            <h1 class="text-4xl leading-tight font-extrabold text-gray-900 dark:text-white">
                {{ __('ui.dashboard') }}
            </h1>
            <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
                {{ __('ui.welcome_message', ['name' => auth()->user()->name]) }}
            </p>
        </div>

        <div class="mt-6 md:mt-0 flex items-center space-x-4">
            <div class="relative">
                <select class="appearance-none bg-white dark:bg-gray-800 rounded-xl shadow-md border border-gray-200 dark:border-gray-700 px-4 pr-10 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out cursor-pointer">
                    <option>{{ __('ui.last_30_days') }}</option>
                    <option>{{ __('ui.last_3_months') }}</option>
                    <option>{{ __('ui.last_6_months') }}</option>
                    <option>{{ __('ui.last_year') }}</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            <x-language-switcher/>
        </div>
    </header>

    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12 relative z-10">
        @foreach ([
            ['title' => 'ui.total_tenants', 'value' => $totalTenants ?? 125, 'color' => 'indigo', 'icon' => 'users', 'change' => '12%', 'change_color' => 'green', 'svg_path_up' => 'M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z'],
            ['title' => 'ui.active_subscriptions', 'value' => $activeSubscriptions ?? 110, 'color' => 'green', 'icon' => 'check-circle', 'change' => '8%', 'change_color' => 'green', 'svg_path_up' => 'M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z'],
            ['title' => 'ui.monthly_revenue', 'value' => '$' . number_format($monthlyRevenue ?? 25400, 0), 'color' => 'yellow', 'icon' => 'currency-dollar', 'change' => '23%', 'change_color' => 'green', 'svg_path_up' => 'M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z'],
            ['title' => 'ui.new_tenants', 'value' => $newTenants ?? 8, 'color' => 'red', 'icon' => 'plus-circle', 'change' => '5%', 'change_color' => 'red', 'svg_path_down' => 'M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z']
        ] as $stat)
            <div class="card group hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __($stat['title']) }}</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">
                            {{ $stat['value'] }}
                        </p>
                        <div class="flex items-center mt-2 text-xs">
                            <span class="text-{{ $stat['change_color'] }}-500 font-medium flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="{{ $stat['svg_path_up'] ?? $stat['svg_path_down'] }}" clip-rule="evenodd"></path>
                                </svg>
                                {{ $stat['change'] }}
                            </span>
                            <span class="text-gray-500 dark:text-gray-400 ml-1">{{ __('ui.from_last_month') }}</span>
                        </div>
                    </div>
                    <div class="bg-{{ $stat['color'] }}-500 p-3 rounded-xl shadow-lg">
                        <x-heroicon-o-{{ $stat['icon'] }} class="h-8 w-8 text-white" />
                    </div>
                </div>
            </div>
        @endforeach
    </section>


    <section class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12 relative z-10">
        <div class="card h-96" x-data="{ revenueData: @js($revenueChart) }" x-init="/* Chart.js initialization logic */">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('ui.revenue_growth') }}</h2>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.last_6_months') }}</span>
                    <button class='p-1.5 rounded-lg bg-blue-50 dark:bg-gray-700 text-blue-600 hover:bg-blue-100 dark:hover:bg-gray-600 transition'>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="h-80">
                <canvas x-ref="revenueCanvas"></canvas>
            </div>
        </div>

        <div class="card h-96" x-data="{ tenantData: @js($tenantGrowthChart) }" x-init="/* Chart.js initialization logic */">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('ui.tenant_growth') }}</h2>
                <div class="flex items-center space-x-2">
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ __('ui.last_6_months') }}</span>
                    <button class='p-1.5 rounded-lg bg-blue-50 dark:bg-gray-700 text-blue-600 hover:bg-blue-100 dark:hover:bg-gray-600 transition'>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="h-80">
                <canvas x-ref="tenantCanvas"></canvas>
            </div>
        </div>
    </section>


    <section class="card relative z-10">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ __('ui.recent_tenants') }}</h2>
            <a href="#" class="inline-flex items-center px-3 py-1 bg-blue-500 text-white text-sm font-medium rounded-lg hover:bg-blue-600 transition duration-150 shadow-md">
                {{ __('ui.view_all') }}
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
        <div class="overflow-x-auto -mx-6 sm:-mx-0">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('ui.tenant_name') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('ui.hospital_name') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('ui.domain') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('ui.subscription') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">{{ __('ui.joined_on') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider"><span class="sr-only">{{ __('ui.actions') }}</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($recentTenants as $tenant)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-600/10 text-blue-600 ring-2 ring-blue-600/50 flex items-center justify-center font-semibold dark:bg-blue-400/10 dark:text-blue-400">
                                        {{ substr($tenant['name'], 0, 1) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $tenant['name'] }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $tenant['id'] ?? 'T' . rand(1000, 9999) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $tenant['hospital_name'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <a href="#" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 transition duration-150">{{ $tenant['domain'] }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full
                                {{ $tenant['subscription'] == 'Premium' ? 'bg-green-50 text-green-700 dark:bg-green-700/30 dark:text-green-300' :
                                   ($tenant['subscription'] == 'Basic' ? 'bg-blue-50 text-blue-700 dark:bg-blue-700/30 dark:text-blue-300' :
                                   'bg-yellow-50 text-yellow-700 dark:bg-yellow-700/30 dark:text-yellow-300') }}">
                                {{ $tenant['subscription'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $tenant['created_at'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                            <a href="#" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition duration-150 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700/50">{{ __('ui.view') }}</a>
                            <a href="#" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300 transition duration-150 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700/50">{{ __('ui.edit') }}</a>
                        </td>
                    </tr>
                    @empty
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
