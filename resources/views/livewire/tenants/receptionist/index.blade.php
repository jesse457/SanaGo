<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50/50 p-6 dark:bg-gray-900">
    {{-- Mobile Sidebar Toggle --}}
    <button @click="open = true" class="lg:hidden p-2 rounded-lg text-gray-500 bg-white shadow-sm border border-gray-200 hover:bg-gray-50 mb-6 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors">
        <x-heroicon-o-bars-3 class="w-6 h-6" />
    </button>

    {{-- Sticky Header --}}
    <header class="sticky top-0 z-20 mb-8 -mx-6 px-6 py-4 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200/50 dark:border-gray-700/50 flex flex-col md:flex-row md:items-center justify-between gap-4 transition-all">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, {{ Auth::user()->name }}
                <span class="text-2xl">👋</span>
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ now()->format('l, F jS, Y') }} &bull; Receptionist Dashboard
            </p>
        </div>

        <div class="flex items-center gap-4">
            {{-- Notifications --}}
            <button class="relative p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-800 dark:text-gray-400 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                <x-heroicon-o-bell class="w-6 h-6" />
                <span class="absolute top-2 right-2.5 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white dark:ring-gray-900"></span>
            </button>

            <div class="h-8 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

            {{-- User Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-3 p-1.5 pr-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors border border-transparent hover:border-gray-200 dark:hover:border-gray-700">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4f46e5&color=fff&bold=true"
                        alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-lg object-cover shadow-sm">
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 leading-none">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Receptionist</p>
                    </div>
                    <x-heroicon-m-chevron-down class="w-4 h-4 text-gray-400 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''" />
                </button>

                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95" @click.outside="open = false" style="display: none;"
                    class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 py-1 z-30 ring-1 ring-black ring-opacity-5">

                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 md:hidden">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                    </div>

                    <a href="{{ route('receptionist.profile') }}" wire:navigate class="group flex items-center px-4 py-2.5 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <x-heroicon-o-user class="mr-3 h-5 w-5 text-gray-400 group-hover:text-indigo-500 transition-colors" />
                        Profile Settings
                    </a>

                    <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button type="submit" class="w-full group flex items-center px-4 py-2.5 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/10">
                            <x-heroicon-o-arrow-left-on-rectangle class="mr-3 h-5 w-5 text-red-400 group-hover:text-red-500" />
                            Sign Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Flash Messages --}}
    @if (session()->has('message'))
        <div class="flex items-center p-4 mb-6 text-sm text-green-800 rounded-xl bg-green-50 border border-green-100 dark:bg-gray-800 dark:text-green-400 dark:border-green-800" role="alert">
            <x-heroicon-o-check-circle class="flex-shrink-0 inline w-5 h-5 me-3" />
            <span class="font-medium">Success!</span> &nbsp; {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="flex items-center p-4 mb-6 text-sm text-red-800 rounded-xl bg-red-50 border border-red-100 dark:bg-gray-800 dark:text-red-400 dark:border-red-800" role="alert">
            <x-heroicon-o-exclamation-circle class="flex-shrink-0 inline w-5 h-5 me-3" />
            <span class="font-medium">Error!</span> &nbsp; {{ session('error') }}
        </div>
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        {{-- Total Patients --}}
        <div class="group bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200/60 dark:border-gray-700 hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform duration-300">
                    <x-heroicon-o-users class="w-6 h-6" />
                </div>
                <span class="text-xs font-medium text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">All Time</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Registered Patients</p>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalPatientsRegistered }}</h3>
            </div>
        </div>

        {{-- Pending Appointments --}}
        <div class="group bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200/60 dark:border-gray-700 hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform duration-300">
                    <x-heroicon-o-clock class="w-6 h-6" />
                </div>
                <span class="text-xs font-medium text-amber-700 bg-amber-50 dark:bg-amber-900/30 dark:text-amber-400 px-2 py-1 rounded-full">Today</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Appointments</p>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $appointmentsTodayPending }}</h3>
            </div>
        </div>

        {{-- Confirmed Appointments --}}
        <div class="group bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200/60 dark:border-gray-700 hover:shadow-md transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform duration-300">
                    <x-heroicon-o-check-badge class="w-6 h-6" />
                </div>
                <span class="text-xs font-medium text-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 dark:text-emerald-400 px-2 py-1 rounded-full">Today</span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Confirmed Appointments</p>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $appointmentsTodayConfirmed }}</h3>
            </div>
        </div>
    </div>

    {{-- Recent Appointments Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/60 dark:border-gray-700 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    Today's Schedule
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Manage patient flow for {{ now()->format('M d, Y') }}
                </p>
            </div>
            <a href="{{ route('receptionist.book-appointment') }}" wire:navigate class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 dark:hover:bg-indigo-900/50 rounded-lg transition-colors">
                Book New
                <x-heroicon-s-arrow-right class="ml-2 w-4 h-4" />
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                <thead class="bg-gray-50/50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Patient</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Doctor</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Time</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Reason</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($appointmentsToday as $appointment)
                        <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-600 dark:text-gray-300">
                                        {{ substr($appointment->patient->first_name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $appointment->patient->first_name ?? 'N/A' }} {{ $appointment->patient->last_name ?? '' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                Dr. {{ $appointment->doctor->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5 text-sm text-gray-700 dark:text-gray-300">
                                    <x-heroicon-o-clock class="w-4 h-4 text-gray-400" />
                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                {{ $appointment->reason_for_visit ?: 'General Checkup' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = match(strtolower($appointment->status)) {
                                        'waiting' => 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800',
                                        'in consultation' => 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800',
                                        'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-800',
                                        'canceled' => 'bg-red-50 text-red-700 border-red-100 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800',
                                        default => 'bg-gray-50 text-gray-700 border-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusClasses }}">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="h-12 w-12 rounded-full bg-gray-50 dark:bg-gray-700/50 flex items-center justify-center mb-4">
                                        <x-heroicon-o-calendar class="w-6 h-6 text-gray-400" />
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">No appointments today</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">The schedule is currently clear.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>
