<div class="flex-1 p-4 sm:p-6 bg-slate-50 dark:bg-gray-900 overflow-y-auto min-h-screen">

    {{-- Top Navigation Bar --}}
    <div class="sticky top-0 z-20 mb-8 -mx-4 sm:-mx-6 px-4 sm:px-6 py-4 bg-white/80 dark:bg-gray-900/90 backdrop-blur-xl border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">

        {{-- Welcome / Context --}}
        <div class="flex items-center gap-3">
            <div class="p-2 bg-indigo-600 rounded-lg shadow-lg shadow-indigo-600/20">
                <x-heroicon-s-beaker class="w-6 h-6 text-white" />
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-900 dark:text-white leading-tight">
                    Dashboard
                </h1>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">
                    Welcome back, {{ Auth::user()->name }}
                </p>
            </div>
        </div>

        {{-- Right Actions --}}
        <div class="flex items-center gap-4">
            {{-- Notification Bell (Optional Placeholder) --}}
            <button class="relative p-2 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                <span class="absolute top-2 right-2 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white dark:ring-gray-900"></span>
                <x-heroicon-o-bell class="w-6 h-6" />
            </button>

            <div class="h-8 w-px bg-gray-200 dark:bg-gray-700 mx-1"></div>

            {{-- Profile Dropdown --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="flex items-center gap-3 p-1 pr-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4f46e5&color=fff"
                        alt="avatar"
                        class="w-9 h-9 rounded-full object-cover ring-2 ring-white dark:ring-gray-800 shadow-sm">
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wider">Technician</p>
                    </div>
                    <x-heroicon-m-chevron-down class="w-4 h-4 text-gray-400 transition-transform duration-200"
                        x-bind:class="open ? 'rotate-180' : ''" />
                </button>

                {{-- Dropdown Menu --}}
                <div x-show="open"
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="transform opacity-0 scale-95"
                    x-transition:enter-end="transform opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="transform opacity-100 scale-100"
                    x-transition:leave-end="transform opacity-0 scale-95"
                    x-cloak
                    @click.outside="open = false"
                    class="absolute right-0 mt-2 w-56 origin-top-right rounded-xl bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none divide-y divide-gray-100 dark:divide-gray-700 z-50">

                    <div class="px-4 py-3">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Signed in as</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ Auth::user()->email }}</p>
                    </div>

                    <div class="py-1">
                        <a href="{{ route('lab-technician.profile') }}" wire:navigate
                            class="group flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/50 hover:text-indigo-600 dark:hover:text-indigo-300">
                            <x-heroicon-s-user class="mr-3 h-5 w-5 text-gray-400 group-hover:text-indigo-500" />
                            Profile Settings
                        </a>
                    </div>

                    <div class="py-1">
                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <button type="submit"
                                class="group flex w-full items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400">
                                <x-heroicon-s-arrow-left-on-rectangle class="mr-3 h-5 w-5 text-gray-400 group-hover:text-red-500" />
                                Sign out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

        {{-- Completed Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:border-green-200 dark:hover:border-green-800 transition-colors duration-200">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Tests Completed</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $completedTestsToday->count() }}</h3>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-1 flex items-center font-medium">
                        <x-heroicon-s-arrow-trending-up class="w-3 h-3 mr-1" /> Today
                    </p>
                </div>
                <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-xl text-green-600 dark:text-green-400 group-hover:bg-green-100 dark:group-hover:bg-green-900/40 transition-colors">
                    <x-heroicon-o-clipboard-document-check class="w-6 h-6" />
                </div>
            </div>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-green-500 to-emerald-400"></div>
        </div>

        {{-- In Progress Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:border-blue-200 dark:hover:border-blue-800 transition-colors duration-200">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">In Progress</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $inProgessTest->count() }}</h3>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1 flex items-center font-medium">
                        Processing now
                    </p>
                </div>
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl text-blue-600 dark:text-blue-400 group-hover:bg-blue-100 dark:group-hover:bg-blue-900/40 transition-colors">
                    <x-heroicon-o-beaker class="w-6 h-6" />
                </div>
            </div>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-400"></div>
        </div>

        {{-- Pending Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-hidden group hover:border-amber-200 dark:hover:border-amber-800 transition-colors duration-200">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Requests</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $pendingLabRequests->count() }}</h3>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1 flex items-center font-medium">
                         Needs attention
                    </p>
                </div>
                <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-xl text-amber-600 dark:text-amber-400 group-hover:bg-amber-100 dark:group-hover:bg-amber-900/40 transition-colors">
                    <x-heroicon-o-clock class="w-6 h-6" />
                </div>
            </div>
            <div class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-amber-500 to-orange-400"></div>
        </div>
    </div>

    {{-- Pending Table Section --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <div class="h-8 w-1 bg-indigo-500 rounded-full"></div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pending Lab Requests</h3>
            </div>
            <a href="#" class="inline-flex items-center gap-1 text-sm font-semibold text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                View All Requests
                <x-heroicon-s-arrow-long-right class="w-4 h-4" />
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Patient
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Test Type
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Requested By
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Date
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Urgency
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    @forelse ($pendingLabRequests as $request)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 group cursor-pointer">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-9 w-9 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600">
                                        {{ substr($request->patient?->first_name ?? '?', 0, 1) }}{{ substr($request->patient?->last_name ?? '?', 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $request->patient?->first_name }} {{ $request->patient?->last_name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            UID: {{ $request->patient?->patient_uid ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                <span class="inline-flex items-center px-2 py-1 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-medium">
                                    {{ $request->testDefinition?->test_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                <div class="flex items-center gap-1.5">
                                    <x-heroicon-m-user-circle class="w-4 h-4 text-gray-400" />
                                    Dr. {{ $request->doctor?->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($request->request_date)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $urgencyColors = match($request->urgency_level) {
                                        'Urgent' => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800',
                                        'High' => 'bg-orange-50 text-orange-700 border-orange-200 dark:bg-orange-900/30 dark:text-orange-400 dark:border-orange-800',
                                        'Normal' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800',
                                        default => 'bg-gray-50 text-gray-600 border-gray-200 dark:bg-gray-700/50 dark:text-gray-400 dark:border-gray-600',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $urgencyColors }}">
                                    {{ $request->urgency_level }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-full mb-3">
                                        <x-heroicon-o-check-badge class="w-10 h-10 text-green-500 dark:text-green-400" />
                                    </div>
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">All Caught Up!</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">There are no pending lab requests at the moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
