<div class="flex-1 bg-gray-50 h-screen overflow-y-auto  dark:bg-gray-900 font-sans" x-cloak x-data="{ open: false }">

    {{-- Sticky Header --}}
    <header class="sticky top-0 z-20 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <button @click="open = !open" class="lg:hidden p-2 -ml-2 text-gray-500 hover:bg-gray-100 rounded-lg">
                    <x-heroicon-o-bars-3 class="w-6 h-6" />
                </button>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">Reception</h1>
                    <p class="text-xs text-gray-500 font-medium">{{ now()->format('l, F jS') }}</p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                {{-- Quick Action --}}
                <a href="{{ route('receptionist.book-appointment') }}" class="hidden sm:flex items-center gap-2 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition shadow-sm">
                    <x-heroicon-o-plus class="w-4 h-4" /> Book Appt
                </a>

                <div class="h-6 w-px bg-gray-200 dark:bg-gray-700"></div>

                {{-- User Profile --}}
                <div class="relative" x-data="{ dd: false }">
                    <button @click="dd = !dd" class="flex items-center gap-2 group focus:outline-none">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4f46e5&color=fff" class="w-8 h-8 rounded-full border border-gray-200 dark:border-gray-700">
                        <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 group-hover:text-gray-600 transition-transform" x-bind:class="dd ? 'rotate-180' : ''"/>
                    </button>
                    {{-- Dropdown omitted for brevity --}}
                </div>
            </div>
        </div>
    </header>

    <div class="p-6 space-y-6">

        {{-- Alerts --}}
        @if (session()->has('message'))
            <div class="p-4 text-sm text-green-700 bg-green-50 rounded-lg border border-green-200 flex items-center">
                <x-heroicon-s-check-circle class="w-5 h-5 mr-2" /> {{ session('message') }}
            </div>
        @endif

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Registered --}}
            <div class="group bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Patients</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $totalPatientsRegistered }}</h3>
                    </div>
                    <span class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <x-heroicon-o-users class="w-6 h-6" />
                    </span>
                </div>
            </div>

            {{-- Pending --}}
            <div class="group bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Pending Today</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $appointmentsTodayPending }}</h3>
                    </div>
                    <span class="p-2 bg-amber-50 text-amber-600 rounded-lg">
                        <x-heroicon-o-clock class="w-6 h-6" />
                    </span>
                </div>
            </div>

            {{-- Confirmed --}}
            <div class="group bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Confirmed</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $appointmentsTodayConfirmed }}</h3>
                    </div>
                    <span class="p-2 bg-emerald-50 text-emerald-600 rounded-lg">
                        <x-heroicon-o-check-badge class="w-6 h-6" />
                    </span>
                </div>
            </div>
        </div>

        {{-- Schedule Table --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm flex flex-col">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Today's Schedule</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Manage patient flow</p>
                </div>
                <div class="flex gap-2">
                    <input type="text" placeholder="Search patient..." class="text-sm border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Doctor</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse ($appointmentsToday as $appointment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/25 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600 dark:text-gray-300">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Dr. {{ $appointment->doctor->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $colors = match(strtolower($appointment->status)) {
                                            'waiting' => 'bg-amber-100 text-amber-800',
                                            'in consultation' => 'bg-blue-100 text-blue-800',
                                            'completed' => 'bg-emerald-100 text-emerald-800',
                                            'canceled' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">No appointments scheduled</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
