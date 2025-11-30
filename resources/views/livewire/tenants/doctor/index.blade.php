<main id="doctor-dashboard" class="flex-1 p-4  bg-gray-50 dark:bg-gray-900 min-h-screen">

    {{-- 1. Sticky Top Navigation Bar --}}
    <nav class="sticky top-0 z-30 bg-white/80 dark:bg-gray-900/90 backdrop-blur-xl border-b border-gray-200 dark:border-gray-800 px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between transition-all duration-200">

        {{-- Left: Mobile Toggle & Breadcrumb/Title --}}
        <div class="flex items-center gap-4">
            <button @click="open = true"
                class="lg:hidden p-2 -ml-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                <x-heroicon-o-bars-3 class="w-6 h-6" />
            </button>

            <div class="flex items-center gap-2">
                <div class="bg-blue-100 dark:bg-blue-900/30 p-1.5 rounded-lg text-blue-600 dark:text-blue-400">
                    <x-heroicon-s-clipboard-document-list class="w-5 h-5" />
                </div>
                <span class="font-bold text-gray-900 dark:text-white text-lg tracking-tight">{{ __('doctor.dashboard') }}</span>
            </div>
        </div>

        {{-- Right: Global Actions (Search, Lang, Notifs, Profile) --}}
        <div class="flex items-center gap-2 sm:gap-4">

            {{-- Search (Hidden on small mobile) --}}
            <div class="hidden md:block relative">
                <x-heroicon-o-magnifying-glass class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                <input type="text" placeholder="{{ __('doctor.search_placeholder') }}"
                    class="pl-10 pr-4 py-1.5 text-sm border border-gray-200 dark:border-gray-700 rounded-full bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all w-64">
            </div>

            <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 hidden sm:block"></div>

            <x-language-switcher />



            {{-- Profile Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-2 p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=3b82f6&color=fff&size=48"
                        alt="Avatar" class="w-8 h-8 rounded-full border border-gray-200 dark:border-gray-700 shadow-sm">
                    <span class="hidden md:block text-sm font-medium text-gray-700 dark:text-gray-200 max-w-[100px] truncate">{{ Auth::user()->name }}</span>
                    {{-- Alpine Fix: The syntax is correct for class merging in a modern Blade component. --}}
                    <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 hidden md:block transition-transform duration-200"  x-bind:class:class="open ? 'rotate-180' : ''" />
                </button>

                {{-- Dropdown Menu --}}
                <div x-show="open" x-cloak @click.outside="open = false"
                    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                    class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 border border-gray-100 dark:border-gray-700 z-50 divide-y divide-gray-100 dark:divide-gray-700">

                    <div class="px-4 py-3">
                        <p class="text-sm text-gray-900 dark:text-white font-semibold truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                    </div>

                    <div class="py-1">
                        <a href="{{ route('doctor.profile') }}" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400">
                            <x-heroicon-o-user-circle class="mr-3 h-5 w-5 text-gray-400 group-hover:text-blue-500" />
                            {{ __('doctor.my_profile') }}
                        </a>
                        <a href="#" class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400">
                            <x-heroicon-o-cog-6-tooth class="mr-3 h-5 w-5 text-gray-400 group-hover:text-blue-500" />
                            {{ __('doctor.settings') }}
                        </a>
                    </div>

                    <div class="py-1">
                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <button type="submit" class="group flex w-full items-center px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20">
                                <x-heroicon-o-arrow-right-start-on-rectangle class="mr-3 h-5 w-5 text-red-400 group-hover:text-red-500" />
                                {{ __('doctor.sign_out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- 2. Welcome Header with Gradient --}}
    <div class="relative bg-white dark:bg-gray-900 pt-8 pb-20 px-4 sm:px-6 lg:px-8 border-b border-gray-200 dark:border-gray-800">
        <div class="absolute inset-0 bg-gradient-to-b from-blue-50/50 to-white/0 dark:from-blue-900/10 dark:to-gray-900/0 pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                {{-- Dynamic greeting time logic, formatted using a placeholder for the time --}}
                {{ __('doctor.greeting_time', ['time' => now()->format('H') < 12 ? 'Good morning' : (now()->format('H') < 18 ? 'Good afternoon' : 'Good evening')]) }},
                <span class="text-blue-600 dark:text-blue-400">{{ Auth::user()->name }}</span>
            </h1>
            <p class="mt-2 text-sm md:text-base text-gray-500 dark:text-gray-400 max-w-2xl">
                {{ __('doctor.dashboard_overview_text') }}
            </p>
        </div>
    </div>

    {{-- 3. KPI Cards (Floating Overlay) --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 relative z-10">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            @php
                // Using new, simpler KPI keys
                $kpi = [
                    ['label' => __('doctor.kpi_patients'), 'count' => $patientsUnderCare->count(), 'icon' => 'users', 'color' => 'blue', 'trend' => '+2 this week'],
                    ['label' => __('doctor.kpi_appointments'), 'count' => $upcomingAppointments->count(), 'icon' => 'calendar', 'color' => 'emerald', 'trend' => 'Next: 2:00 PM'],
                    ['label' => __('doctor.kpi_lab_results'), 'count' => $incomingLabResults->count(), 'icon' => 'beaker', 'color' => 'amber', 'trend' => '3 Urgent'],
                ];
            @endphp

            @foreach ($kpi as $item)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-5 flex items-start justify-between hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group cursor-default">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $item['label'] }}</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $item['count'] }}</h3>
                    <p class="text-xs font-medium mt-2 px-2 py-0.5 rounded-md bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300 inline-block">
                        {{ $item['trend'] }}
                    </p>
                </div>
                <div class="p-3 rounded-xl bg-{{ $item['color'] }}-50 dark:bg-{{ $item['color'] }}-900/20 text-{{ $item['color'] }}-600 dark:text-{{ $item['color'] }}-400 group-hover:scale-110 transition-transform duration-300">
                    @if ($item['icon'] === 'users') <x-heroicon-s-users class="w-6 h-6" />
                    @elseif ($item['icon'] === 'calendar') <x-heroicon-s-calendar-days class="w-6 h-6" />
                    @else <x-hugeicons-test-tube-01 class="w-6 h-6" /> @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- 4. Main Content Grid --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">

            {{-- Card 1: Upcoming Appointments --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col h-[500px]">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg text-emerald-600 dark:text-emerald-400">
                            <x-heroicon-s-calendar class="w-5 h-5" />
                        </div>
                        <h2 class="font-bold text-gray-900 dark:text-white">{{ __('doctor.upcoming_schedule') }}</h2>
                    </div>
                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 transition-colors">
                        {{ __('doctor.view_calendar') }} &rarr;
                    </a>
                </div>

                <div class="overflow-auto custom-scrollbar flex-1 p-0">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 sticky top-0 z-10 text-gray-500 dark:text-gray-400 font-medium text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3">{{ __('doctor.patient') }}</th>
                                <th class="px-6 py-3">{{ __('doctor.time') }}</th>
                                <th class="px-6 py-3 hidden sm:table-cell">{{ __('doctor.status') }}</th>
                                <th class="px-6 py-3 text-right">{{ __('doctor.action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            @forelse ($upcomingAppointments as $appt)
                            <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors cursor-pointer">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $appt->patient->first_name }} {{ $appt->patient->last_name }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-[140px]">{{ $appt->reason_for_visit }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5 text-gray-700 dark:text-gray-300">
                                        <x-heroicon-o-clock class="w-4 h-4 text-gray-400" />
                                        <span class="font-mono">{{ $appt->appointment_time->format('h:i A') }}</span>
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">{{ $appt->appointment_date->format('M d') }}</div>
                                </td>
                                <td class="px-6 py-4 hidden sm:table-cell">
                                    @php
                                        $statusClasses = match($appt->status) {
                                            'confirmed' => 'bg-green-100 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800',
                                            'pending' => 'bg-yellow-100 text-yellow-700 border-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400 dark:border-yellow-800',
                                            'cancelled' => 'bg-red-100 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800',
                                            default => 'bg-gray-100 text-gray-700 border-gray-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusClasses }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current mr-1.5 opacity-60"></span>
                                        {{ ucfirst($appt->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors p-1 rounded-full hover:bg-blue-50 dark:hover:bg-blue-900/30">
                                        <x-heroicon-m-ellipsis-vertical class="w-5 h-5" />
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <div class="inline-flex justify-center items-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                                        <x-heroicon-o-calendar class="w-8 h-8 text-gray-400" />
                                    </div>
                                    <p>{{ __('doctor.no_upcoming_appointments') }}</p>
                                    <button class="mt-2 text-sm text-blue-600 hover:underline">{{ __('doctor.add_new_appointment') }}</button>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Card 2: Lab Results --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col h-[500px]">
                <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg text-amber-600 dark:text-amber-400">
                            <x-hugeicons-test-tube-01 class="w-5 h-5" />
                        </div>
                        <h2 class="font-bold text-gray-900 dark:text-white">{{ __('doctor.latest_lab_results') }}</h2>
                    </div>
                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 transition-colors">
                        {{ __('doctor.view_all') }} &rarr;
                    </a>
                </div>

                <div class="overflow-auto custom-scrollbar flex-1 p-0">
                    <table class="min-w-full text-left text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-700/50 sticky top-0 z-10 text-gray-500 dark:text-gray-400 font-medium text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3">{{ __('doctor.test_name') }}</th>
                                <th class="px-6 py-3">{{ __('doctor.patient') }}</th>
                                <th class="px-6 py-3">{{ __('doctor.status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            @forelse ($incomingLabResults as $result)
                            <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors cursor-pointer">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $result->labRequest->testDefinition->test_name }}</div>
                                    <div class="text-xs text-gray-400 font-mono mt-0.5">
                                        {{ $result->result_date->diffForHumans() }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-700 dark:text-gray-300">{{ $result->labRequest->patient->first_name }} {{ $result->labRequest->patient->last_name }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($result->status === 'Urgent')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-800">
                                            <x-heroicon-s-exclamation-circle class="w-3 h-3 mr-1" /> Urgent
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-800">
                                            Normal
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                    <div class="inline-flex justify-center items-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                                        <x-heroicon-o-beaker class="w-8 h-8 text-gray-400" />
                                    </div>
                                    <p>{{ __('doctor.no_new_lab_results') }}</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. Enhanced Floating Action Button (FAB) --}}
    <div x-data="{ hover: false }" class="fixed bottom-6 right-6 z-50">
        <a href="#"
           @mouseenter="hover = true" @mouseleave="hover = false"
           class="flex items-center justify-center w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-blue-500/30">
            {{-- Alpine Fix: The syntax is correct for class merging in a modern Blade component. --}}

        </a>

        {{-- Tooltip --}}
        <div x-show="hover" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2"
             class="absolute bottom-full right-0 mb-3 mr-1 px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-medium rounded-lg shadow-lg whitespace-nowrap pointer-events-none">
            {{ __('doctor.add_new_appointment') }}
            <div class="absolute bottom-[-4px] right-5 w-2 h-2 bg-gray-900 dark:bg-white rotate-45"></div>
        </div>
    </div>

</main>
