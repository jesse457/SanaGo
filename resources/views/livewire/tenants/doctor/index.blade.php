<div class="flex-1 bg-gray-50 h-screen overflow-y-auto dark:bg-gray-900 font-sans"
    x-cloak
    x-data="{
        isOffline: !navigator.onLine,
        showOnlineToast: false,
        mobileOpen: false,
        init() {
            window.addEventListener('offline', () => {
                this.isOffline = true;
                this.showOnlineToast = false;
            });
            window.addEventListener('online', () => {
                this.isOffline = false;
                this.showOnlineToast = true;
                setTimeout(() => this.showOnlineToast = false, 4000);
            });
        }
    }">

    {{--
      ========================================
      NETWORK STATUS
      ========================================
    --}}
    <div x-show="isOffline" x-transition.origin.top
         class="bg-rose-600 text-white text-xs font-bold text-center py-2 relative z-50 shadow-md">
        <div class="flex items-center justify-center gap-2">
            <x-heroicon-s-wifi class="w-4 h-4 opacity-80" />
            <span>YOU ARE OFFLINE. RECORDS MAY NOT SYNC.</span>
        </div>
    </div>

    {{--
      ========================================
      STICKY HEADER
      ========================================
    --}}
    <header class="sticky top-0 z-20 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-6 py-4">
        <div class="flex items-center justify-between">

            {{-- Left: Mobile Toggle & Context --}}
            <div class="flex items-center gap-4">
                <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 -ml-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">
                    <x-heroicon-o-bars-3 class="w-6 h-6" />
                </button>

                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg hidden sm:block">
                        <x-heroicon-s-heart class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                            {{ __('doctor.dashboard') }}
                        </h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                            {{ __('doctor.greeting_time', ['time' => now()->format('H') < 12 ? 'Good morning' : (now()->format('H') < 18 ? 'Good afternoon' : 'Good evening')]) }},
                            <span class="text-gray-900 dark:text-white font-semibold">{{ Auth::user()->name }}</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Right: Actions & Profile --}}
            <div class="flex items-center gap-4">
                <x-language-switcher />

                {{-- Notification Bell --}}
                <div class="relative" x-data="{ dropdownOpen: false }">
                    <button @click="dropdownOpen = !dropdownOpen"
                            class="relative p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition focus:outline-none">
                        <x-heroicon-o-bell class="w-6 h-6" />
                        <span x-show="unreadCount > 0" x-cloak class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full ring-2 ring-white dark:ring-gray-900"></span>
                    </button>

                    {{-- Notifications Dropdown --}}
                    <div x-show="dropdownOpen" x-cloak @click.outside="dropdownOpen = false"
                         x-transition.origin.top.right
                         class="absolute right-0 mt-3 w-80 sm:w-96 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 z-50 overflow-hidden">

                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Notifications</h3>
                            <button class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Mark all read</button>
                        </div>

                        <div class="max-h-[320px] overflow-y-auto">
                            @forelse($notifications ?? [] as $notif)
                                <div class="px-4 py-3 border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer flex gap-3 group relative">
                                    @if(!$notif->read) <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500"></div> @endif

                                    <div class="mt-1">
                                        <div class="p-1.5 rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                            <x-heroicon-s-beaker class="w-4 h-4" />
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-900 dark:text-white font-medium line-clamp-1">{{ $notif->message }}</p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $notif->patient_name }}</span>
                                            <span class="text-xs text-gray-300 dark:text-gray-600">&bull;</span>
                                            <span class="text-xs text-gray-400 font-mono">{{ $notif->created_at->diffForHumans(null, true) }}</span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-8 text-center">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No new notifications</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="h-6 w-px bg-gray-200 dark:bg-gray-700"></div>

                {{-- Profile --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 group focus:outline-none">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563eb&color=fff"
                             class="w-8 h-8 rounded-full border border-gray-200 dark:border-gray-700 shadow-sm group-hover:ring-2 ring-blue-100 transition">
                        <div class="hidden md:block text-left">
                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider">Doctor</p>
                        </div>
                        <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 group-hover:text-gray-600 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''"/>
                    </button>

                    {{-- Dropdown (Simplified) --}}
                    <div x-show="open" x-cloak @click.outside="open = false"
                         x-transition.origin.top.right
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-100 dark:border-gray-700 z-50 py-1">
                         <!-- Profile Links -->
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{--
      ========================================
      MAIN CONTENT
      ========================================
    --}}
    <div class="p-6 space-y-6">

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $kpi = [
                    [
                        'label' => __('doctor.kpi_patients'),
                        'count' => $patientsUnderCare->count() ?? 42,
                        'icon' => 'users',
                        'bg' => 'bg-blue-50 dark:bg-blue-900/20',
                        'text' => 'text-blue-600 dark:text-blue-400',
                        'desc' => 'Total under care'
                    ],
                    [
                        'label' => __('doctor.kpi_appointments'),
                        'count' => $upcomingAppointments->count() ?? 8,
                        'icon' => 'calendar-days',
                        'bg' => 'bg-emerald-50 dark:bg-emerald-900/20',
                        'text' => 'text-emerald-600 dark:text-emerald-400',
                        'desc' => 'Remaining today'
                    ],
                    [
                        'label' => __('doctor.kpi_lab_results'),
                        'count' => $incomingLabResults->count() ?? 3,
                        'icon' => 'beaker',
                        'bg' => 'bg-amber-50 dark:bg-amber-900/20',
                        'text' => 'text-amber-600 dark:text-amber-400',
                        'desc' => 'Pending review'
                    ],
                ];
            @endphp

            @foreach ($kpi as $item)
                <div class="group bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $item['label'] }}</p>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $item['count'] }}</h3>
                            <p class="text-xs text-gray-400 mt-1">{{ $item['desc'] }}</p>
                        </div>
                        <span class="p-2 {{ $item['bg'] }} {{ $item['text'] }} rounded-lg">
                            <x-dynamic-component :component="'heroicon-o-' . $item['icon']" class="w-6 h-6" />
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Tables Grid --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            {{-- Upcoming Appointments --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm flex flex-col h-[500px]">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800 rounded-t-xl">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('doctor.upcoming_schedule') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Today's timeline</p>
                    </div>
                    <button class="text-sm text-blue-600 hover:text-blue-700 font-medium">View Calendar</button>
                </div>

                <div class="overflow-y-auto flex-1 custom-scrollbar">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Patient</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Reason</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @forelse ($upcomingAppointments as $appt)
                                <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/25 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900 dark:text-white font-mono">
                                                {{ $appt->appointment_time->format('H:i') }}
                                            </span>
                                            <span class="text-xs text-gray-500">{{ $appt->appointment_time->format('A') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 flex items-center justify-center font-bold text-xs mr-3">
                                                {{ substr($appt->patient->first_name, 0, 1) }}{{ substr($appt->patient->last_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $appt->patient->first_name }} {{ $appt->patient->last_name }}</div>
                                                <div class="text-xs text-gray-500">#{{ $appt->patient->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                            {{ Str::limit($appt->reason_for_visit, 15) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <button class="p-1 text-gray-400 hover:text-blue-600 transition">
                                            <x-heroicon-o-ellipsis-vertical class="w-5 h-5" />
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <x-heroicon-o-calendar class="w-8 h-8 text-gray-300 mb-2" />
                                            No more appointments today
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Lab Results --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm flex flex-col h-[500px]">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800 rounded-t-xl">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('doctor.latest_lab_results') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Incoming test reports</p>
                    </div>
                </div>

                <div class="overflow-y-auto flex-1 custom-scrollbar">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Test</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Patient</th>
                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Status</th>
                                <th class="px-6 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @forelse ($incomingLabResults as $result)
                                <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/25 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $result->labRequest->testDefinition->test_name }}</div>
                                        <div class="text-xs text-gray-500 font-mono mt-0.5">{{ $result->created_at->format('M d, H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                        {{ optional($result->patient)->first_name }} {{ optional($result->patient)->last_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-pulse"></span>
                                            Unread
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <button class="text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:underline">
                                            Review
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <x-heroicon-o-beaker class="w-8 h-8 text-gray-300 mb-2" />
                                            No pending results
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Floating Action Button (Clean) --}}
    <div class="fixed bottom-8 right-8 z-40">
        <button class="flex items-center justify-center w-14 h-14 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 hover:shadow-xl hover:scale-105 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-500/30">
            <x-heroicon-o-plus class="w-7 h-7" />
        </button>
    </div>
</div>
