<div class="flex flex-col min-h-full">
    {{-- STICKY HEADER --}}
    <header class="sticky top-0 z-20 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 px-4 sm:px-8 py-4">
        <div class="flex items-center justify-between gap-4">

            <div class="flex items-center gap-4 min-w-0">
                <div class="hidden sm:flex p-2 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                    <x-heroicon-s-heart class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl font-extrabold text-gray-900 dark:text-white truncate">
                        {{ __('doctor.dashboard') }}
                    </h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium truncate">
                        {{ now()->format('H') < 12 ? 'Good morning' : (now()->format('H') < 18 ? 'Good afternoon' : 'Good evening') }},
                        <span class="text-gray-900 dark:text-gray-200 font-bold">{{ Auth::user()->name }}</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                {{-- Dark Mode Toggle --}}
                <button @click="$store.theme.toggle()" class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <x-heroicon-o-sun x-show="!$store.theme.on" class="w-5 h-5 text-gray-500" />
                    <x-heroicon-o-moon x-show="$store.theme.on" class="w-5 h-5 text-yellow-400" />
                </button>

                <div class="hidden xs:block">
                    <x-language-switcher />
                </div>

                {{-- Notifications --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="relative p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
                        <x-heroicon-o-bell class="w-6 h-6" />
                        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white dark:ring-gray-900"></span>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-cloak x-transition
                         class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden z-50">
                        <div class="p-3 border-b border-gray-100 dark:border-gray-700 font-bold text-xs uppercase tracking-widest text-gray-500">Notifications</div>
                        <div class="max-h-64 overflow-y-auto">
                            @forelse($notifications ?? [] as $notif)
                                <div class="p-3 border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 flex gap-3">
                                    <div class="flex-1 min-w-0 text-xs">
                                        <p class="font-bold text-gray-900 dark:text-white truncate">{{ $notif->message }}</p>
                                        <p class="text-gray-500">{{ $notif->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-gray-400">No notifications</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="h-8 w-px bg-gray-200 dark:bg-gray-800 mx-1"></div>

                {{-- Profile --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563eb&color=fff"
                             class="w-8 h-8 rounded-full ring-2 ring-gray-100 dark:ring-gray-800" x-bind:class="open ? 'rotate-180' : ''" />
                    </button>
                    {{-- Profile dropdown here... --}}
                </div>
            </div>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <div class="p-4 sm:p-8 space-y-8">

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
            @php
                $kpi = [
                    ['label' => __('doctor.kpi_patients'), 'count' => 42, 'icon' => 'users', 'color' => 'blue'],
                    ['label' => __('doctor.kpi_appointments'), 'count' => 8, 'icon' => 'calendar-days', 'color' => 'emerald'],
                    ['label' => __('doctor.kpi_lab_results'), 'count' => 3, 'icon' => 'beaker', 'color' => 'amber'],
                ];
            @endphp

            @foreach ($kpi as $item)
                <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm flex items-center justify-between group hover:border-{{ $item['color'] }}-500 transition-colors">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">{{ $item['label'] }}</p>
                        <h3 class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $item['count'] }}</h3>
                    </div>
                    <div class="p-4 bg-{{ $item['color'] }}-50 dark:bg-{{ $item['color'] }}-900/20 text-{{ $item['color'] }}-600 dark:text-{{ $item['color'] }}-400 rounded-2xl">
                        <x-dynamic-component :component="'heroicon-o-' . $item['icon']" class="w-8 h-8" />
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Tables Section --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            {{-- Upcoming Appointments --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/30 dark:bg-gray-800/30">
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{ __('doctor.upcoming_schedule') }}</h3>
                    <button class="text-xs font-bold text-blue-600 hover:text-blue-700 px-3 py-1 bg-blue-50 dark:bg-blue-900/30 rounded-full transition">VIEW ALL</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">
                                <th class="px-6 py-4">Time</th>
                                <th class="px-6 py-4">Patient</th>
                                <th class="hidden sm:table-cell px-6 py-4">Reason</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @forelse ($upcomingAppointments ?? [] as $appt)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                                    <td class="px-6 py-4 font-bold text-blue-600">{{ $appt->appointment_time->format('H:i') }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center text-xs font-bold text-blue-600">
                                                {{ substr($appt->patient->first_name, 0, 1) }}
                                            </div>
                                            <span class="font-medium dark:text-gray-200">{{ $appt->patient->first_name }}</span>
                                        </div>
                                    </td>
                                    <td class="hidden sm:table-cell px-6 py-4 text-gray-500 text-xs italic">{{ $appt->reason_for_visit }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <button class="p-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition">
                                            <x-heroicon-s-chevron-right class="w-5 h-5 text-gray-400"/>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="p-12 text-center text-gray-400 italic">No appointments for today</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Lab Results --}}
            <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden flex flex-col">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-gray-50/30 dark:bg-gray-800/30">
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{ __('doctor.latest_lab_results') }}</h3>
                </div>
                <div class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse ($incomingLabResults ?? [] as $result)
                        <div class="p-6 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-800/40 transition group">
                            <div class="flex gap-4 items-center">
                                <div class="p-3 bg-amber-50 dark:bg-amber-900/20 text-amber-600 rounded-xl">
                                    <x-heroicon-o-beaker class="w-5 h-5" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $result->labRequest->testDefinition->test_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $result->patient->first_name }} • {{ $result->created_at->format('d M, Y') }}</p>
                                </div>
                            </div>
                            <button class="px-4 py-2 text-xs font-black bg-blue-600 text-white rounded-xl shadow-lg shadow-blue-500/20 hover:scale-105 active:scale-95 transition">REVIEW</button>
                        </div>
                    @empty
                        <div class="p-12 text-center text-gray-400 italic">No pending results for review</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- FAB --}}
    <button class="fixed bottom-6 right-6 w-14 h-14 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl shadow-xl shadow-blue-500/40 flex items-center justify-center transition-all hover:-translate-y-1 active:scale-90 z-40">
        <x-heroicon-o-plus class="w-8 h-8" />
    </button>
</div>
