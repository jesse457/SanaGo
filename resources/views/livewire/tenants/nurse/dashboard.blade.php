<div class="flex-1 bg-gray-50 h-screen overflow-y-auto dark:bg-gray-900 font-sans">

    {{-- Sticky Header --}}
    <header class="sticky top-0 z-20 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-6 py-4">
        <div class="flex items-center justify-between">

            {{-- Left: Mobile Toggle & Title --}}
            <div class="flex items-center gap-4">
                {{-- Mobile Hamburger --}}
                <button @click="open = true"
                    class="lg:hidden p-2 -ml-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">
                    <x-heroicon-o-bars-3 class="w-6 h-6" />
                </button>

                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg hidden sm:block">
                        <x-heroicon-s-user-group class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 dark:text-white leading-tight">
                            Nurse Dashboard
                        </h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                            Overview of Ward A &bull; <span class="text-emerald-600 dark:text-emerald-400">Shift Active</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Right: Actions & Profile --}}
            <div class="flex items-center gap-4">
                {{-- Quick Action (Optional) --}}
                <button class="hidden md:flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700 transition">
                    <x-heroicon-o-plus class="w-4 h-4" />
                    <span>New Vitals</span>
                </button>

                <div class="h-6 w-px bg-gray-200 dark:bg-gray-700"></div>

                {{-- Profile Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 group focus:outline-none">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Nurse') }}&background=6366f1&color=fff&size=64"
                             class="w-8 h-8 rounded-full border border-gray-200 dark:border-gray-700 shadow-sm group-hover:ring-2 ring-indigo-100 transition">

                        <div class="hidden md:block text-left">
                            <p class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ auth()->user()->name ?? 'User' }}</p>
                            <p class="text-[10px] text-gray-500 uppercase tracking-wider">RN - Level 2</p>
                        </div>

                        <x-heroicon-s-chevron-down class="w-3 h-3 text-gray-400 group-hover:text-gray-600 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''"/>
                    </button>

                    {{-- Dropdown Panel --}}
                    <div x-show="open" x-transition.origin.top.right x-cloak @click.outside="open = false"
                        class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-100 dark:border-gray-700 z-50 py-1">
                        <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700 mb-1">
                            <p class="text-xs text-gray-500">Signed in as</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ auth()->user()->email }}</p>
                        </div>

                        <a href="{{ route('nurse.profile') }}" wire:navigate
                            class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <x-heroicon-o-user class="w-4 h-4 mr-3 text-gray-400" /> Profile
                        </a>

                        <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>

                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <x-heroicon-o-arrow-left-on-rectangle class="w-4 h-4 mr-3" /> Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="p-6 space-y-6">

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $stats = [
                    ['label' => 'Admitted Patients', 'count' => $admitted ?? 0, 'icon' => 'users', 'color' => 'text-sky-600', 'bg' => 'bg-sky-50 dark:bg-sky-900/20', 'desc' => 'Currently in ward'],
                    ['label' => 'Vitals Due', 'count' => $vitalsDue ?? 0, 'icon' => 'clock', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'desc' => 'Within next 4 hours'],
                    ['label' => 'Critical Supplies', 'count' => $lowStock ?? 0, 'icon' => 'exclamation-triangle', 'color' => 'text-amber-600', 'bg' => 'bg-amber-50 dark:bg-amber-900/20', 'desc' => 'Requires attention'],
                ];
            @endphp

            @foreach ($stats as $stat)
                <div class="group bg-white dark:bg-gray-800 p-5 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $stat['label'] }}</p>
                            <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $stat['count'] }}</h3>
                            <p class="text-xs text-gray-400 mt-1">{{ $stat['desc'] }}</p>
                        </div>
                        <span class="p-2 {{ $stat['bg'] }} {{ $stat['color'] }} rounded-lg">
                            <x-dynamic-component component="heroicon-o-{{ $stat['icon'] }}" class="w-6 h-6" />
                        </span>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Main Content Area: Admissions Table --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm flex flex-col">

            {{-- Table Header --}}
            <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Recent Admissions</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Patient status and bed allocation</p>
                </div>

                {{-- Filters / Actions --}}
                <div class="flex items-center gap-2">
                    <div class="relative">
                        <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                        <input type="text" placeholder="Search patient..."
                            class="pl-9 pr-4 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition w-full sm:w-64">
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Patient Details
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Location
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Admission Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Status
                            </th>
                            <th scope="col" class="relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($admittedPatients as $admission)
                            <tr class="group hover:bg-gray-50 dark:hover:bg-gray-700/25 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-9 w-9 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 flex items-center justify-center font-bold text-xs border border-indigo-200 dark:border-indigo-800">
                                            {{ substr($admission->patient->first_name ?? 'U', 0, 1) }}{{ substr($admission->patient->last_name ?? '', 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $admission->patient->first_name ?? 'Unknown' }} {{ $admission->patient->last_name ?? '' }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                                ID: #{{ $admission->patient->id ?? '---' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex flex-col">
                                        <span class="text-sm text-gray-900 dark:text-gray-200">{{ $admission->bed->ward->name ?? 'Unassigned' }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Bed {{ $admission->bed->bed_number ?? 'N/A' }}</span>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-600 dark:text-gray-300 font-mono">
                                        {{ $admission->admission_date ? $admission->admission_date->format('M d, Y') : 'N/A' }}
                                    </span>
                                    <div class="text-xs text-gray-400">
                                        {{ $admission->admission_date ? $admission->admission_date->format('H:i A') : '' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800">
                                        Admitted
                                    </span>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="#" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="p-3 bg-gray-100 dark:bg-gray-800 rounded-full mb-3">
                                            <x-heroicon-o-clipboard-document-list class="w-6 h-6 text-gray-400" />
                                        </div>
                                        <p class="font-medium text-gray-900 dark:text-gray-200">No patients currently admitted</p>
                                        <p class="text-xs mt-1">Check pending admissions or refresh the page.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer (Optional Pagination Placeholder) --}}
            @if(isset($admittedPatients) && method_exists($admittedPatients, 'links'))
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-b-xl">
                    {{ $admittedPatients->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
