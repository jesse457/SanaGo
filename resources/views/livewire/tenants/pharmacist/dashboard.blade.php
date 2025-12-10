<div class="flex-1 bg-gray-50 h-screen overflow-y-auto dark:bg-gray-900 font-sans">
    <section id="sales-reports" class="max-w-7xl mx-auto">

        {{-- Sticky Header: Crisper border, less blur, clean white background --}}
        <div class="sticky top-0 z-20 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-6 py-4">
            <div class="flex items-center justify-between">

                {{-- Left: Context --}}
                <div class="flex items-center gap-3">
                    {{-- Icon reduced in size for professional look --}}
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                        <x-heroicon-s-clipboard-document-list class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                            {{ __('pharmacist.dashboard.dashboard_title') }}
                        </h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                           {!! __('pharmacist.dashboard.welcome_message', [
                                'name' => '<span class="text-gray-900 dark:text-gray-200">' . Auth::user()->name . '</span>',
                            ]) !!}
                        </p>
                    </div>
                </div>

                {{-- Right: Actions --}}
                <div class="flex items-center gap-4">
                    <x-language-switcher />

                    {{-- Separator --}}
                    <div class="h-6 w-px bg-gray-200 dark:bg-gray-700"></div>

                    {{-- Profile Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center gap-2 group focus:outline-none">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0f172a&color=fff&size=64"
                                alt="avatar"
                                class="w-8 h-8 rounded-full border border-gray-200 dark:border-gray-700 shadow-sm group-hover:ring-2 ring-gray-100 transition">

                            <div class="hidden md:block text-left">
                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ Auth::user()->name }}</p>
                                <p class="text-[10px] text-gray-500 uppercase tracking-wider">Pharmacist</p>
                            </div>

                            <x-heroicon-s-chevron-down
                                class="w-3 h-3 text-gray-400 group-hover:text-gray-600 transition-transform duration-200"
                                x-bind:class="open ? 'rotate-180' : ''" />
                        </button>

                        {{-- Dropdown Panel --}}
                        <div x-show="open" x-transition.origin.top.right x-cloak @click.outside="open = false"
                            class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-100 dark:border-gray-700 z-50 py-1">
                            <a href="{{ route('pharmacist.profile') }}" wire:navigate
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <x-heroicon-o-user class="w-4 h-4 mr-3 text-gray-400" />
                                {{ __('pharmacist.dashboard.profile') }}
                            </a>
                            <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                            <form method="POST" action="{{ route('auth.logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <x-heroicon-o-arrow-left-on-rectangle class="w-4 h-4 mr-3" />
                                    {{ __('pharmacist.dashboard.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-6">
            {{-- Alerts --}}
            @if (session()->has('message'))
                <div class="flex items-center p-4 mb-4 text-sm text-emerald-800 border border-emerald-200 rounded-lg bg-emerald-50 dark:bg-gray-800 dark:text-emerald-400 dark:border-emerald-800" role="alert">
                    <x-heroicon-s-check-circle class="flex-shrink-0 inline w-5 h-5 mr-3" />
                    <span class="font-medium">{{ session('message') }}</span>
                </div>
            @endif
            @if (session()->has('error'))
                <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-200 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
                    <x-heroicon-s-x-circle class="flex-shrink-0 inline w-5 h-5 mr-3" />
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            {{-- KPI Cards: Modern "Stamps" style --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="group bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('pharmacist.dashboard.prescriptions_dispensed_today') }}</p>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $prescriptionsDispensedToday }}</h3>
                        </div>
                        <span class="p-2 bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400 rounded-lg">
                            <x-heroicon-o-check-circle class="w-6 h-6" />
                        </span>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="group bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('pharmacist.dashboard.prescriptions_pending') }}</p>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $prescriptionsPending }}</h3>
                        </div>
                        <span class="p-2 bg-orange-50 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400 rounded-lg">
                            <x-heroicon-o-clock class="w-6 h-6" />
                        </span>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="group bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('pharmacist.dashboard.drugs_left_in_inventory') }}</p>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $drugsLeftInInventory }}</h3>
                        </div>
                        <span class="p-2 bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400 rounded-lg">
                            <x-heroicon-o-cube class="w-6 h-6" />
                        </span>
                    </div>
                </div>
            </div>

            {{-- Table Section --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm flex flex-col">
                {{-- Table Header --}}
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('pharmacist.dashboard.top_sold_drugs') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Inventory overview and stock alerts</p>
                    </div>
                    <a href="#" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700">
                        {{ __('pharmacist.dashboard.view_all') }}
                    </a>
                </div>

                {{-- Table Content --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    {{ __('pharmacist.dashboard.medication') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    {{ __('pharmacist.dashboard.current_stock') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    {{ __('pharmacist.dashboard.min_level') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                    Status
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @forelse($this->medications as $medication)
                                <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/25 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-blue-600 dark:text-blue-300 font-bold text-xs">
                                                {{ substr($medication->name, 0, 1) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $medication->name }}</div>
                                                @if (!empty($medication->code))
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 font-mono mt-0.5">{{ $medication->code }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 text-right font-mono">
                                        {{ $medication->stock_quantity }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-right font-mono">
                                        {{ $medication->min_stock_level }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($medication->stock_quantity <= $medication->min_stock_level)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800">
                                                Low Stock
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800">
                                                Healthy
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="p-3 bg-gray-100 dark:bg-gray-800 rounded-full mb-3">
                                                <x-heroicon-o-cube class="w-6 h-6 text-gray-400" />
                                            </div>
                                            <p class="font-medium text-gray-900 dark:text-gray-200">{{ __('pharmacist.dashboard.no_medications_found') }}</p>
                                            <p class="text-xs mt-1">{{ __('pharmacist.dashboard.try_adjusting_filters') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Footer --}}
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-b-xl">
                    {{ $this->medications->links() }}
                </div>
            </div>
        </div>
    </section>
</div>
