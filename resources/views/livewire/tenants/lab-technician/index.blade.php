<div class="flex-1 bg-gray-50 h-screen overflow-y-auto  dark:bg-gray-900 font-sans"
    x-cloak
    x-data="{
        mobileOpen: false,
        notifOpen: false,
        isOffline: !navigator.onLine,
        init() {
            window.addEventListener('offline', () => this.isOffline = true);
            window.addEventListener('online', () => this.isOffline = false);
        }
    }">

    {{-- Network Alert --}}
    <div x-show="isOffline" x-transition.origin.top class="bg-rose-600 text-white text-xs font-bold text-center py-2 relative z-50">
        You are offline. Results may not sync immediately.
    </div>

    {{-- Sticky Header --}}
    <header class="sticky top-0 z-20 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-6 py-4">
        <div class="flex items-center justify-between">

            {{-- Left: Mobile Toggle & Title --}}
            <div class="flex items-center gap-4">
                <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 -ml-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">
                    <x-heroicon-o-bars-3 class="w-6 h-6" />
                </button>

                <div class="flex items-center gap-3">
                    <div class="p-2 bg-purple-50 dark:bg-purple-900/30 rounded-lg hidden sm:block">
                        <x-heroicon-s-beaker class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                            Lab Dashboard
                        </h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                            <span class="text-green-600 dark:text-green-400">● Online</span> &bull; {{ Auth::user()->name }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Right: Actions --}}
            <div class="flex items-center gap-4">

                {{-- Notification Bell --}}
                <div class="relative">
                    <button @click="notifOpen = !notifOpen" class="relative p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition focus:outline-none">
                        <x-heroicon-o-bell class="w-6 h-6" />
                        <span x-show="unreadCount > 0" class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-500 rounded-full ring-2 ring-white dark:ring-gray-900"></span>
                    </button>

                    {{-- Notification Dropdown --}}
                    <div x-show="notifOpen" @click.outside="notifOpen = false" x-cloak x-transition.origin.top.right
                         class="absolute right-0 mt-3 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-800">
                            <h3 class="text-xs font-bold text-gray-500 uppercase">Notifications</h3>
                            <button @click="markAllRead()" class="text-xs text-purple-600 hover:underline">Mark read</button>
                        </div>
                        <div class="max-h-64 overflow-y-auto">
                            <template x-for="note in notifications" :key="note.id">
                                <div class="px-4 py-3 border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 flex gap-3 cursor-pointer">
                                    <div class="mt-0.5">
                                        <div class="w-2 h-2 rounded-full" :class="note.read ? 'bg-gray-300' : 'bg-purple-500'"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white" x-text="note.message"></p>
                                        <p class="text-xs text-gray-500 mt-1" x-text="new Date(note.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})"></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="h-6 w-px bg-gray-200 dark:bg-gray-700"></div>

                {{-- Profile Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 group focus:outline-none">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=9333ea&color=fff"
                             class="w-8 h-8 rounded-full border border-gray-200 dark:border-gray-700 shadow-sm group-hover:ring-2 ring-purple-100 transition">
                         <div class="hidden md:block text-left">
                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ Auth::user()->name }}</p>
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider">Technician</p>
                        </div>
                         <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 group-hover:text-gray-600 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''"/>
                    </button>

                    <div x-show="open" @click.outside="open = false" x-cloak x-transition.origin.top.right
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-100 dark:border-gray-700 z-50 py-1">
                        <a href="{{ route('lab-technician.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700">Profile</a>
                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">Sign out</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <div class="p-6 space-y-6">

        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- Completed --}}
            <div class="group bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tests Completed</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $completedTestsToday->count() }}</h3>
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1 font-medium">Today</p>
                    </div>
                    <span class="p-2 bg-green-50 text-green-600 dark:bg-green-900/20 dark:text-green-400 rounded-lg">
                        <x-heroicon-o-clipboard-document-check class="w-6 h-6" />
                    </span>
                </div>
            </div>

            {{-- In Progress --}}
            <div class="group bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Processing</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $inProgessTest->count() }}</h3>
                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-1 font-medium">Currently active</p>
                    </div>
                    <span class="p-2 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-lg">
                        <x-heroicon-o-beaker class="w-6 h-6 animate-pulse" />
                    </span>
                </div>
            </div>

            {{-- Pending --}}
            <div class="group bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Requests</p>
                        <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $pendingLabRequests->count() }}</h3>
                        <p class="text-xs text-amber-600 dark:text-amber-400 mt-1 font-medium">Needs attention</p>
                    </div>
                    <span class="p-2 bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400 rounded-lg">
                        <x-heroicon-o-clock class="w-6 h-6" />
                    </span>
                </div>
            </div>
        </div>

        {{-- Pending Table --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm flex flex-col">
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Pending Lab Requests</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Queue management</p>
                </div>
                <a href="#" class="text-sm font-medium text-purple-600 hover:text-purple-700">View All</a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Test</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Doctor</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Urgency</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse ($pendingLabRequests as $request)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/25 transition group cursor-pointer">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-500 dark:text-gray-300">
                                            {{ substr($request->patient?->first_name ?? '?', 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $request->patient?->first_name }} {{ $request->patient?->last_name }}</div>
                                            <div class="text-xs text-gray-500">ID: {{ $request->patient?->patient_uid }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    {{ $request->testDefinition?->test_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    Dr. {{ $request->doctor?->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $colors = match($request->urgency_level) {
                                            'Urgent' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                            'High' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-400',
                                            default => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $colors }}">
                                        {{ $request->urgency_level }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">No pending requests</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
