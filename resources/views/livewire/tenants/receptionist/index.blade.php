<div class="flex-1 bg-gray-50 h-screen overflow-y-auto dark:bg-gray-900 font-sans" x-cloak x-data="{ open: false }">

    {{-- Sticky Header --}}
    <header class="sticky top-0 z-20 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-4 sm:px-6 py-4">
        <div class="flex items-center justify-between gap-4">

            {{-- Left Side: Hamburger + Title --}}
            <div class="flex items-center gap-3 sm:gap-4">
                <!-- Mobile Hamburger Button -->
                <button @click="$dispatch('open-sidebar')" class="lg:hidden p-2 -ml-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg focus:outline-none">
                    <x-heroicon-o-bars-3 class="w-6 h-6" />
                </button>

                <div>
                    <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white leading-tight">{{ __('Reception') }}</h1>
                    <p class="text-xs text-gray-500 font-medium">{{ now()->format('l, F jS') }}</p>
                </div>
            </div>

            {{-- Right Side: User Profile Dropdown --}}
            <div class="flex items-center gap-4">
 {{-- Dark Mode Toggle --}}
                <button @click="$store.theme.toggle()" class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <x-heroicon-o-sun x-show="!$store.theme.on" class="w-5 h-5 text-gray-500" />
                    <x-heroicon-o-moon x-show="$store.theme.on" class="w-5 h-5 text-yellow-400" />
                </button>
                <div class="relative" x-data="{ dd: false }">
                    <!-- Dropdown Trigger -->
                    <button @click="dd = !dd" @keydown.escape.window="dd = false" class="flex items-center gap-2 group focus:outline-none">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4f46e5&color=fff" class="w-9 h-9 rounded-full border border-gray-200 dark:border-gray-700 shadow-sm">
                        <div class="hidden md:flex flex-col text-right">
                            <span class="text-sm font-semibold text-gray-900 dark:text-white leading-none">{{ Auth::user()->name }}</span>
                            <span class="text-xs text-gray-500 leading-none mt-1">{{ __('Receptionist') }}</span>
                        </div>
                        <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 group-hover:text-gray-600 transition-transform duration-200" x-bind:class="dd ? 'rotate-180' : ''"/>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="dd"
                         x-cloak
                         @click.outside="dd = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none border border-gray-100 dark:border-gray-700">

                        <!-- Account Info (Mobile/Tablet specific info that might be hidden in trigger) -->
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Signed in as') }}</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ Auth::user()->email }}</p>
                        </div>

                        <div class="py-1">
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <x-heroicon-s-user-circle class="w-4 h-4 mr-2 text-gray-400" />
                                {{ __('Your Profile') }}
                            </a>
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <x-heroicon-s-cog-6-tooth class="w-4 h-4 mr-2 text-gray-400" />
                                {{ __('Settings') }}
                            </a>
                        </div>

                        <div class="py-1 border-t border-gray-100 dark:border-gray-700">
                            <form method="POST" action="{{ route('auth.logout') }}">
                                @csrf
                                <button type="submit" class="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <x-heroicon-s-arrow-right-on-rectangle class="w-4 h-4 mr-2" />
                                    {{ __('Sign out') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="p-4 sm:p-6 space-y-6">

        {{-- Alerts --}}
        @if (session()->has('message'))
            <div class="p-4 text-sm text-green-700 bg-green-50 dark:bg-green-900/20 dark:text-green-400 rounded-lg border border-green-200 dark:border-green-800 flex items-center">
                <x-heroicon-s-check-circle class="w-5 h-5 mr-2 flex-shrink-0" />
                <span>{{ session('message') }}</span>
            </div>
        @endif

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            {{-- Registered --}}
            <div class="group bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Total Patients') }}</p>
                        <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalPatientsRegistered }}</h3>
                    </div>
                    <span class="p-2 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg">
                        <x-heroicon-o-users class="w-6 h-6" />
                    </span>
                </div>
            </div>

            {{-- Pending --}}
            <div class="group bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Pending Today') }}</p>
                        <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $appointmentsTodayPending }}</h3>
                    </div>
                    <span class="p-2 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg">
                        <x-heroicon-o-clock class="w-6 h-6" />
                    </span>
                </div>
            </div>

            {{-- Confirmed --}}
            <div class="group bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow sm:col-span-2 lg:col-span-1">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">{{ __('Confirmed') }}</p>
                        <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $appointmentsTodayConfirmed }}</h3>
                    </div>
                    <span class="p-2 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg">
                        <x-heroicon-o-check-badge class="w-6 h-6" />
                    </span>
                </div>
            </div>
        </div>

        {{-- Schedule Table --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm flex flex-col">
            <div class="px-4 sm:px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Today\'s Schedule') }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Manage patient flow') }}</p>
                </div>
                <div class="w-full sm:w-auto">
                    <input type="text" placeholder="{{ __('Search patient...') }}" class="w-full sm:w-64 text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">{{ __('Time') }}</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">{{ __('Patient') }}</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase dark:text-gray-400 hidden sm:table-cell">{{ __('Doctor') }}</th>
                            <th class="px-4 sm:px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse ($appointmentsToday as $appointment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/25 transition">
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600 dark:text-gray-300">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}</div>
                                    <div class="sm:hidden text-xs text-gray-500 mt-0.5">Dr. {{ $appointment->doctor->name }}</div>
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                    Dr. {{ $appointment->doctor->name }}
                                </td>
                                <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                    @php
                                        $colors = match(strtolower($appointment->status)) {
                                            'waiting' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                                            'in consultation' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                            'completed' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400',
                                            'canceled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">{{ __('No appointments scheduled') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
