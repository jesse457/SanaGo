<div class="flex-1 bg-gray-100 p-8 lg:ml-64 dark:bg-gray-900">
    <section id="sales-reports" class="dashboard-section">
        <div
            class="sticky z-10 top-0 mb-4
               bg-white/80 dark:bg-gray-900/80 backdrop-blur-md
               border-b border-gray-200/50 dark:border-gray-700/50
               px-4 py-3 shadow-sm rounded-b-lg">
            <div class="flex items-center justify-between">

                {{-- Left: title --}}
                <div class="flex items-center space-x-2">
                    <x-heroicon-s-clipboard-document-list class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                    <div>
                        <h1 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white">
                            {{ __('pharmacist.dashboard.dashboard_title') }}
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 hidden md:block">
                            {!! __('pharmacist.dashboard.welcome_message', [
                                'name' => '<span class="font-medium text-gray-700 dark:text-gray-300">' . Auth::user()->name . '</span>',
                            ]) !!}
                        </p>
                    </div>
                </div>

                {{-- Right: icons + dropdown --}}
                <div class="flex items-center space-x-3 md:space-x-4">
                    <x-language-switcher />
                    {{-- Profile dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center space-x-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 p-1 pr-2 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=3b82f6&color=fff"
                                alt="avatar"
                                class="w-8 h-8 rounded-full object-cover border border-gray-200 dark:border-gray-600">
                            {{-- ✅ FIXED LINE --}}
                            <x-heroicon-s-chevron-down
                                class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform"
                                x-bind:class="open ? 'rotate-180' : ''" />
                        </button>

                        {{-- Dropdown panel --}}
                        <div x-show="open" x-transition x-cloak @click.outside="open = false"
                            class="absolute right-0 mt-2 w-48 py-2
                           bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-20">
                            <a href="{{ route('pharmacist.profile') }}" wire:navigate
                                class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                                <x-heroicon-o-user
                                    class="w-4 h-4 mr-2 text-gray-500 dark:text-gray-400" />{{ __('pharmacist.dashboard.profile') }}
                            </a>

                            <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                            <form method="POST" action="{{ route('auth.logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                                    <x-heroicon-o-arrow-left-on-rectangle
                                        class="w-4 h-4 mr-2 text-gray-500 dark:text-gray-400" />{{ __('pharmacist.dashboard.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-4"
                role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-4"
                role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            {{-- Prescriptions Dispensed Today Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('pharmacist.dashboard.prescriptions_dispensed_today') }}
                    </h4>
                    <p class="text-4xl font-extrabold text-blue-600 dark:text-blue-400">
                        {{ $prescriptionsDispensedToday }}</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                    <x-heroicon-o-check-circle class="w-10 h-10 text-blue-500 dark:text-blue-300" />
                </div>
            </div>

            {{-- Prescriptions Pending Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('pharmacist.dashboard.prescriptions_pending') }}</h4>
                    <p class="text-4xl font-extrabold text-orange-600 dark:text-orange-400">{{ $prescriptionsPending }}
                    </p>
                </div>
                <div class="p-3 bg-orange-100 dark:bg-orange-900 rounded-full">
                    <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-orange-500 dark:text-orange-300" />
                </div>
            </div>

            {{-- Drugs Left in Inventory Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 flex items-center justify-between">
                <div>
                    <h4 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('pharmacist.dashboard.drugs_left_in_inventory') }}</h4>
                    <p class="text-4xl font-extrabold text-green-600 dark:text-green-400">{{ $drugsLeftInInventory }}
                    </p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                    <x-heroicon-o-cube class="w-10 h-10 text-green-500 dark:text-green-300" />
                </div>
            </div>
        </div>

        <div
            class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 flex items-center justify-between border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ __('pharmacist.dashboard.top_sold_drugs') }}</h3>
                <a href="#"
                    class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                    {{ __('pharmacist.dashboard.view_all') }}
                </a>
            </div>

            {{-- Medication Table (styled like Lab Requests) --}}
            <div class="overflow-hidden  border border-gray-200 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                    {{ __('pharmacist.dashboard.medication') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                    {{ __('pharmacist.dashboard.current_stock') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                    {{ __('pharmacist.dashboard.min_level') }}
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($this->medications as $medication)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        <div class="flex flex-col">
                                            <span class="truncate">{{ $medication->name }}</span>
                                            @if (!empty($medication->code ?? null))
                                                <small
                                                    class="text-xs text-gray-500 dark:text-gray-400">{{ __('pharmacist.dashboard.code') }}:
                                                    {{ $medication->code }}</small>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                        <span
                                            class="{{ $medication->stock_quantity <= $medication->min_stock_level ? 'text-red-600 font-semibold dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                            {{ $medication->stock_quantity }}
                                        </span>
                                    </td>

                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700 dark:text-gray-200">
                                        {{ $medication->min_stock_level }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3"
                                        class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <x-heroicon-s-clipboard-document-list
                                                class="w-12 h-12 mb-4 text-gray-300 dark:text-gray-600" />
                                            <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-1">
                                                {{ __('pharmacist.dashboard.no_medications_found') }}</p>
                                            <p class="text-sm">{{ __('pharmacist.dashboard.try_adjusting_filters') }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/40">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-600 dark:text-gray-300">
                            {{ __('pharmacist.dashboard.showing_to_of', ['first' => $this->medications->firstItem() ?? 0, 'last' => $this->medications->lastItem() ?? 0, 'total' => $this->medications->total() ?? 0]) }}
                        </div>
                        <div>
                            {{ $this->medications->links() }}
                        </div>
                    </div>
                </div>
            </div>


        </div>

    </section>
</div>
