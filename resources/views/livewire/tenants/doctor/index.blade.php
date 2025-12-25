<div class="flex-1 bg-gray-50 min-h-screen overflow-y-auto dark:bg-gray-900 font-sans"
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

    {{-- NETWORK STATUS --}}
    <div x-show="isOffline" x-transition.origin.top
         class="bg-rose-600 text-white text-[10px] sm:text-xs font-bold text-center py-2 sticky top-0 z-50 shadow-md">
        <div class="flex items-center justify-center gap-2 px-4">
            <x-heroicon-s-wifi class="w-4 h-4 opacity-80" />
            <span>OFFLINE: RECORDS WILL NOT SYNC</span>
        </div>
    </div>

    {{-- STICKY HEADER --}}
    <header class="sticky top-0 z-20 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-4 sm:px-6 py-3 sm:py-4">
        <div class="flex items-center justify-between">

            {{-- Left: Mobile Toggle & Context --}}
            <div class="flex items-center gap-3">
                <!-- Mobile Menu Button (Visible only on mobile) -->
                <button @click="mobileOpen = !mobileOpen" class="p-2 -ml-2 text-gray-600 dark:text-gray-400 lg:hidden">
                    <x-heroicon-o-bars-3-bottom-left class="w-6 h-6" />
                </button>

                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-50 dark:bg-blue-900/30 rounded-lg hidden xs:block">
                        <x-heroicon-s-heart class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <h1 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white leading-tight">
                            {{ __('doctor.dashboard') }}
                        </h1>
                        <!-- Hidden on very small screens to save space -->
                        <p class="hidden sm:block text-xs text-gray-500 dark:text-gray-400 font-medium">
                            {{ __('doctor.greeting_time', ['time' => now()->format('H') < 12 ? 'Good morning' : (now()->format('H') < 18 ? 'Good afternoon' : 'Good evening')]) }},
                            <span class="text-gray-900 dark:text-white font-semibold">{{ Auth::user()->name }}</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Right: Actions & Profile --}}
            <div class="flex items-center gap-2 sm:gap-4">
                <div class="hidden xs:block">
                    <x-language-switcher />
                </div>

                {{-- Notification Bell --}}
                <div class="relative" x-data="{ dropdownOpen: false }">
                    <button @click="dropdownOpen = !dropdownOpen"
                            class="relative p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">
                        <x-heroicon-o-bell class="w-6 h-6" />
                        <span x-show="unreadCount > 0" x-cloak class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full ring-2 ring-white dark:ring-gray-900"></span>
                    </button>

                    {{-- Notifications Dropdown (Responsive Width) --}}
                    <div x-show="dropdownOpen" x-cloak @click.outside="dropdownOpen = false"
                         x-transition
                         class="fixed inset-x-4 top-16 sm:absolute sm:inset-auto sm:right-0 sm:mt-3 w-auto sm:w-96 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden">

                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Notifications</h3>
                            <button class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Mark all read</button>
                        </div>

                        <div class="max-h-[60vh] sm:max-h-[320px] overflow-y-auto">
                            @forelse($notifications ?? [] as $notif)
                                <!-- Notification Item (Simplified for mobile) -->
                                <div class="px-4 py-3 border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/50 flex gap-3">
                                    <div class="p-1.5 h-fit rounded-full bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                        <x-heroicon-s-beaker class="w-4 h-4" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm text-gray-900 dark:text-white font-medium truncate">{{ $notif->message }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-8 text-center text-gray-500">No new notifications</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

                {{-- Profile --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 group">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563eb&color=fff"
                             class="w-8 h-8 rounded-full border border-gray-200 shadow-sm transition">
                        <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 transition" x-bind:class="open ? 'rotate-180' : ''"/>
                    </button>
                    <!-- Dropdown hidden for brevity -->
                </div>
            </div>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <div class="p-4 sm:p-6 space-y-6">

        {{-- KPI Cards (Grid optimized for mobile) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @php
                $kpi = [
                    ['label' => __('doctor.kpi_patients'), 'count' => 42, 'icon' => 'users', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'desc' => 'Total under care'],
                    ['label' => __('doctor.kpi_appointments'), 'count' => 8, 'icon' => 'calendar-days', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'desc' => 'Remaining today'],
                    ['label' => __('doctor.kpi_lab_results'), 'count' => 3, 'icon' => 'beaker', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'desc' => 'Pending review'],
                ];
            @endphp

            @foreach ($kpi as $item)
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ $item['label'] }}</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $item['count'] }}</h3>
                    </div>
                    <span class="p-3 {{ $item['bg'] }} dark:bg-opacity-10 {{ $item['text'] }} rounded-xl">
                        <x-dynamic-component :component="'heroicon-o-' . $item['icon']" class="w-6 h-6" />
                    </span>
                </div>
            @endforeach
        </div>

        {{-- Tables Grid --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            {{-- Upcoming Appointments --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">{{ __('doctor.upcoming_schedule') }}</h3>
                    <button class="text-xs text-blue-600 font-semibold">VIEW ALL</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">Time</th>
                                <th class="px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">Patient</th>
                                <th class="hidden sm:table-cell px-5 py-3 text-left text-[10px] font-bold text-gray-500 uppercase">Reason</th>
                                <th class="px-5 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse ($upcomingAppointments as $appt)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/25 transition">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $appt->appointment_time->format('H:i') }}</span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-700">
                                                {{ substr($appt->patient->first_name, 0, 1) }}
                                            </div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $appt->patient->first_name }}</div>
                                        </div>
                                    </td>
                                    <td class="hidden sm:table-cell px-5 py-4 whitespace-nowrap">
                                        <span class="text-xs text-gray-500 truncate max-w-[100px] block">{{ $appt->reason_for_visit }}</span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <button class="text-blue-600"><x-heroicon-s-chevron-right class="w-5 h-5"/></button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="p-10 text-center text-gray-400 text-sm">No appointments today</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Lab Results (Simplified Table for Mobile) --}}
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800">
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">{{ __('doctor.latest_lab_results') }}</h3>
                </div>
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($incomingLabResults as $result)
                        <div class="p-4 flex items-center justify-between hover:bg-gray-50 dark:hover:bg-gray-700/25 transition">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-900 dark:text-white truncate">{{ $result->labRequest->testDefinition->test_name }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $result->patient->first_name }} • {{ $result->created_at->format('d M') }}</p>
                            </div>
                            <button class="px-3 py-1.5 text-xs font-bold bg-blue-50 text-blue-600 rounded-lg">REVIEW</button>
                        </div>
                    @empty
                        <div class="p-10 text-center text-gray-400 text-sm">No pending results</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Floating Action Button (Smaller on mobile) --}}
    <div class="fixed bottom-6 right-6 z-40">
        <button class="flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 bg-blue-600 text-white rounded-full shadow-lg active:scale-95 transition-all">
            <x-heroicon-o-plus class="w-6 h-6 sm:w-7 sm:h-7" />
        </button>
    </div>
</div>
