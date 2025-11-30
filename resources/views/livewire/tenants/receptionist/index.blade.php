<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 lg:ml-64 p-6 dark:bg-gray-900">
    {{-- Mobile hamburger --}}
    <button @click="open = true" class="lg:hidden p-2 rounded-md text-gray-700 bg-white shadow hover:bg-gray-100 mb-4 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
        <x-heroicon-o-bars-3 class="w-6 h-6" />
    </button>

    <section id="receptionist-dashboard" class="dashboard-section mb-8">
        <div class="sticky z-10 top-0 mb-4
               bg-white/80 dark:bg-gray-900/80 backdrop-blur-md
               border-b border-gray-200/50 dark:border-gray-700/50
               px-4 py-3 shadow-sm rounded-b-lg">
            <div class="flex items-center justify-between">

                {{-- Left: title --}}
                <div class="flex items-center space-x-2">
                    <x-heroicon-s-clipboard-document-list class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                    <div>
                        <h1 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white">
                            Dashboard
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 hidden md:block">
                            Welcome back, <span class="font-medium text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span>!
                        </p>
                    </div>
                </div>

                {{-- Right: icons + dropdown --}}
                <div class="flex items-center space-x-3 md:space-x-4">

                    {{-- Notifications --}}
                    <button class="relative p-2 rounded-full text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-300 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <x-heroicon-o-bell class="w-6 h-6" />
                        <span class="absolute -top-0.5 -right-0.5 w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse"></span>
                    </button>

                    {{-- Profile dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center space-x-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 p-1 pr-2 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=3b82f6&color=fff"
                                alt="avatar" class="w-8 h-8 rounded-full object-cover border border-gray-200 dark:border-gray-600">
                            {{-- ✅ FIXED LINE --}}
                            <x-heroicon-s-chevron-down class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                        </button>

                        {{-- Dropdown panel --}}
                        <div x-show="open" x-transition x-cloak @click.outside="open = false"
                            class="absolute right-0 mt-2 w-48 py-2
                           bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-20">
                            <a href="{{ route('receptionist.profile') }}" wire:navigate class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                                <x-heroicon-o-user class="w-4 h-4 mr-2 text-gray-500 dark:text-gray-400" />Profile
                            </a>
                            <a href="#" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                                <x-heroicon-o-cog-6-tooth class="w-4 h-4 mr-2 text-gray-500 dark:text-gray-400" />Settings
                            </a>
                            <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                            <form method="POST" action="{{ route('auth.logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                                    <x-heroicon-o-arrow-left-on-rectangle class="w-4 h-4 mr-2 text-gray-500 dark:text-gray-400" />Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Session Flash Messages --}}
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-4 dark:bg-green-900/20 dark:border-green-700 dark:text-green-200" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-4 dark:bg-red-900/20 dark:border-red-700 dark:text-red-200" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Dashboard Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            {{-- Total Patients --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 flex flex-col items-center justify-center text-center hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full mb-3">
                    <x-heroicon-o-users class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                </div>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Total Patients Registered</h3>
                <p class="text-4xl font-bold text-blue-600 dark:text-blue-400">{{ $totalPatientsRegistered }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Registered to date</p>
            </div>

            {{-- Appointments Today --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 flex flex-col items-center justify-center text-center hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full mb-3">
                    <x-heroicon-o-calendar-days class="w-8 h-8 text-green-600 dark:text-green-400" />
                </div>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Appointments Pending Today</h3>
                <p class="text-4xl font-bold text-green-600 dark:text-green-400">{{ $appointmentsTodayPending}}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Appointments Pending</p>
            </div>



            {{-- Placeholder Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 flex flex-col items-center justify-center text-center hover:shadow-lg transition-shadow duration-200 cursor-pointer">
                <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-full mb-3">
                    <x-heroicon-o-user-group class="w-8 h-8 text-indigo-600 dark:text-indigo-400" />
                </div>
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2"> Appointments Confirmed Today</h3>
                <p class="text-4xl font-bold text-indigo-600 dark:text-indigo-400">{{ $appointmentsTodayConfirmed}}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Confirmed Appointments</p>
            </div>
        </div>

        {{-- Today's Appointments Table --}}
        <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <h4 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center">
                <x-heroicon-s-calendar-days class="w-6 h-6 mr-2 text-blue-600 dark:text-blue-400" />
                Today's Appointments Overview
            </h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Doctor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($appointmentsToday as $appointment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $appointment->patient->first_name ?? 'N/A' }} {{ $appointment->patient->last_name ?? '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $appointment->doctor->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                    {{ $appointment->reason_for_visit }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="status-badge status-{{ strtolower($appointment->status) }} py-1 px-3 rounded-full text-xs font-medium">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <x-heroicon-o-inbox class="w-12 h-12 text-gray-400 dark:text-gray-600 mb-3" />
                                        <p class="text-lg font-medium">No appointments scheduled for today.</p>
                                        <p class="text-sm text-gray-400">Time to relax, or maybe book some!</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    {{-- This style block can eventually be moved to your main app.css file --}}
    <style>
        .status-badge.status-pending,
        .status-badge.status-scheduled {
            background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a;
        }
        .status-badge.status-confirmed {
            background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;
        }
        .status-badge.status-completed {
            background-color: #e0f2f7; color: #0e7490; border: 1px solid #a5f3fc;
        }
        .status-badge.status-cancelled {
            background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca;
        }
    </style>
</main>
