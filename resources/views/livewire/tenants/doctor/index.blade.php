<main id="doctor-dashboard" class="flex-1 h-full bg-gray-50 dark:bg-gray-900 overflow-y-auto custom-scrollbar relative">

    {{-- 1. Sticky Top Navigation Bar --}}
    <nav class="sticky top-0 z-30 bg-white/80 dark:bg-gray-900/90 backdrop-blur-xl border-b border-gray-200 dark:border-gray-800 px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between transition-all duration-200">

        {{-- Left: Sidebar Toggle & Title --}}
        <div class="flex items-center gap-4">
            <!-- Mobile Toggle -->
            <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 -ml-2 text-gray-500 hover:text-gray-700 dark:text-gray-400">
                <x-heroicon-o-bars-3 class="w-6 h-6" />
            </button>
            <!-- Desktop Toggle (Collapse/Expand) -->
            <button @click="toggleSidebar()" class="hidden lg:block p-2 -ml-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                 <x-heroicon-o-bars-3-bottom-left class="w-6 h-6" />
            </button>

            <div class="flex items-center gap-2">
                <span class="font-bold text-gray-900 dark:text-white text-lg tracking-tight">{{ __('doctor.dashboard') }}</span>
            </div>
        </div>

        {{-- Right: Actions --}}
        <div class="flex items-center gap-2 sm:gap-4">
            <x-language-switcher />

            {{-- NOTIFICATION DROPDOWN --}}
            <div class="relative" x-data="{ dropdownOpen: false }">
                <button @click="dropdownOpen = !dropdownOpen"
                        class="relative p-2 rounded-full text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <x-heroicon-o-bell class="w-6 h-6" />

                    {{-- Dynamic Badge connected to parent unreadCount --}}
                    <span x-show="unreadCount > 0" x-cloak x-transition.scale
                          class="absolute top-1.5 right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white ring-2 ring-white dark:ring-gray-900">
                        <span x-text="unreadCount"></span>
                    </span>
                </button>

                {{-- Dropdown Panel --}}
                <div x-show="dropdownOpen" x-cloak
                     @click.outside="dropdownOpen = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     class="absolute right-0 mt-3 w-80 sm:w-96 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden">

                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
                        <div class="flex gap-2 text-xs">
                            <button @click="markAllRead()" x-show="unreadCount > 0" class="text-blue-600 hover:text-blue-700 font-medium">Mark all read</button>
                            <button @click="clearNotifications()" x-show="notifications.length > 0" class="text-gray-400 hover:text-red-500">Clear</button>
                        </div>
                    </div>

                    <div class="max-h-[400px] overflow-y-auto custom-scrollbar">
                        <template x-if="notifications.length === 0">
                            <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                <x-heroicon-o-bell-slash class="w-8 h-8 mx-auto mb-2 opacity-50" />
                                <p class="text-sm">No notifications yet</p>
                            </div>
                        </template>

                        <template x-for="notif in notifications" :key="notif.id">
                            <div @click="markAsRead(notif.id)"
                                 class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 cursor-pointer transition-colors group"
                                 :class="notif.read ? 'bg-white dark:bg-gray-800 opacity-75' : 'bg-blue-50/30 dark:bg-blue-900/10 hover:bg-blue-50 dark:hover:bg-blue-900/20'">
                                <div class="flex items-start gap-3">
                                    <div class="flex-shrink-0 mt-1">
                                        <div x-show="!notif.read" class="w-2.5 h-2.5 rounded-full bg-blue-500 ring-4 ring-blue-50 dark:ring-blue-900/30"></div>
                                        <x-heroicon-o-check-circle x-show="notif.read" class="w-5 h-5 text-gray-300 dark:text-gray-600" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="notif.message"></p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            <span x-text="notif.patient_name"></span> &bull; <span x-text="notif.test_name"></span>
                                        </p>
                                        <p class="text-[10px] text-gray-400 mt-1" x-text="new Date(notif.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Profile Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                 <button @click="open = !open" class="flex items-center gap-2 p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=3b82f6&color=fff&size=32" class="w-8 h-8 rounded-full ring-2 ring-white dark:ring-gray-800">
                </button>
            </div>
        </div>
    </nav>

    {{-- 2. Rest of Dashboard Content (Header, KPIs, Tables, FAB) --}}
    <div class="relative bg-white dark:bg-gray-900 pt-8 pb-20 px-4 sm:px-6 lg:px-8 border-b border-gray-200 dark:border-gray-800">
        {{-- ... Existing Header Code ... --}}
        <div class="absolute inset-0 bg-gradient-to-b from-blue-50/50 to-white/0 dark:from-blue-900/10 dark:to-gray-900/0 pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                {{ __('doctor.greeting_time', ['time' => now()->format('H') < 12 ? 'Good morning' : (now()->format('H') < 18 ? 'Good afternoon' : 'Good evening')]) }},
                <span class="text-blue-600 dark:text-blue-400">{{ Auth::user()->name }}</span>
            </h1>
            <p class="mt-2 text-sm md:text-base text-gray-500 dark:text-gray-400 max-w-2xl">
                {{ __('doctor.dashboard_overview_text') }}
            </p>
        </div>
    </div>

    {{-- 3. KPI Cards --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-12 relative z-10">
          @php
                // Using new, simpler KPI keys
                $kpi = [
                    [
                        'label' => __('doctor.kpi_patients'),
                        'count' => $patientsUnderCare->count(),
                        'icon' => 'users',
                        'color' => 'blue',
                        'trend' => '+2 this week',
                    ],
                    [
                        'label' => __('doctor.kpi_appointments'),
                        'count' => $upcomingAppointments->count(),
                        'icon' => 'calendar',
                        'color' => 'emerald',
                        'trend' => 'Next: 2:00 PM',
                    ],
                    [
                        'label' => __('doctor.kpi_lab_results'),
                        'count' => $incomingLabResults->count(),
                        'icon' => 'beaker',
                        'color' => 'amber',
                        'trend' => '3 Urgent',
                    ],
                ];
            @endphp
        {{-- ... Existing KPI Grid ... --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
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
                       {{-- Icons... --}}
                       @if ($item['icon'] === 'users') <x-heroicon-s-users class="w-6 h-6" />
                       @elseif ($item['icon'] === 'calendar') <x-heroicon-s-calendar-days class="w-6 h-6" />
                       @else <x-hugeicons-test-tube-01 class="w-6 h-6" /> @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- 4. Main Content Grid (Tables) --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            {{-- Upcoming Appointments Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col h-[500px]">
                 {{-- ... Table Content ... --}}
                 <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg text-emerald-600 dark:text-emerald-400">
                            <x-heroicon-s-calendar class="w-5 h-5" />
                        </div>
                        <h2 class="font-bold text-gray-900 dark:text-white">{{ __('doctor.upcoming_schedule') }}</h2>
                    </div>
                 </div>
                 <div class="overflow-auto custom-scrollbar flex-1 p-0">
                    <table class="min-w-full text-left text-sm">
                        {{-- ... Table Body ... --}}
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            @forelse ($upcomingAppointments as $appt)
                            <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $appt->patient->first_name }} {{ $appt->patient->last_name }}</div>
                                    <div class="text-xs text-gray-500 truncate max-w-[140px]">{{ $appt->reason_for_visit }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-mono">{{ $appt->appointment_time->format('h:i A') }}</span>
                                </td>
                                {{-- ... other columns ... --}}
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">No appointments</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                 </div>
            </div>

            {{-- Lab Results Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col h-[500px]">
                {{-- ... Table Content ... --}}
                 <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800/50 rounded-t-2xl">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg text-amber-600 dark:text-amber-400">
                            <x-hugeicons-test-tube-01 class="w-5 h-5" />
                        </div>
                        <h2 class="font-bold text-gray-900 dark:text-white">{{ __('doctor.latest_lab_results') }}</h2>
                    </div>
                 </div>
                 <div class="overflow-auto custom-scrollbar flex-1 p-0">
                     <table class="min-w-full text-left text-sm">
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            @forelse ($incomingLabResults as $result)
                            <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900 dark:text-white">{{ $result->labRequest->testDefinition->test_name }}</div>
                                </td>
                                {{-- ... other columns ... --}}
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-6 py-12 text-center text-gray-500">No results</td></tr>
                            @endforelse
                        </tbody>
                     </table>
                 </div>
            </div>
        </div>
    </div>

    {{-- 5. Floating Action Button --}}
    <div x-data="{ hover: false }" class="fixed bottom-6 right-6 z-50">
        <a href="#" @mouseenter="hover = true" @mouseleave="hover = false"
            class="flex items-center justify-center w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-blue-500/30">
            <x-heroicon-o-plus class="w-8 h-8" />
        </a>
        <div x-show="hover" x-cloak
            class="absolute bottom-full right-0 mb-3 mr-1 px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-medium rounded-lg shadow-lg whitespace-nowrap pointer-events-none transition-all">
            {{ __('doctor.add_new_appointment') }}
        </div>
    </div>

</main>
