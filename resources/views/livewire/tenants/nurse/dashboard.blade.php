{{-- Mobile-first, fully-responsive Nurse Dashboard --}}
<main id="nurse-dashboard" class="flex-1 p-4  bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">

    {{-- Mobile hamburger (unchanged functionality, slightly enhanced style) --}}
    <button @click="open = true"
        class="lg:hidden p-2.5 rounded-lg text-gray-700 bg-white shadow-md hover:bg-gray-100 transition duration-200
               dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 mb-6">
        <x-heroicon-o-bars-3 class="w-6 h-6" />
    </button>

    {{-- ========================================== --}}
    {{-- SECTION 1: DASHBOARD OVERVIEW              --}}
    {{-- ========================================== --}}
    <main id="nurse-dashboard" class="p-4 md:p-8 pt-20 transition-all duration-300 ease-in-out">

        {{-- Header & Profile --}}
        <header class="card flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-black tracking-tight text-gray-900 dark:text-white">
                    Dashboard
                </h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1 font-medium flex items-center">
                    <span class="inline-block w-2 h-2 rounded-full bg-green-500 mr-2 animate-pulse"></span>
                    Welcome back, {{ auth()->user()->name ?? 'Nurse' }}
                </p>
            </div>

            {{-- Profile Dropdown --}}
            <div class="relative z-30" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-full p-1 pr-4 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-all">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Nurse') }}&background=0ea5e9&color=fff&size=48"
                         class="w-9 h-9 rounded-full object-cover ring-2 ring-white dark:ring-gray-900">
                    <div class="text-left hidden sm:block">
                        <p class="text-xs font-bold text-gray-700 dark:text-gray-200">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-[10px] text-gray-400 uppercase tracking-wide">Registered Nurse</p>
                    </div>
                    <x-heroicon-s-chevron-down class="w-4 h-4 text-gray-400 transition-transform duration-200" x-bind:class="open ? 'rotate-180' : ''"/>
                </button>

                {{-- Dropdown Menu --}}
                <div x-show="open"
                     @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                     class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden origin-top-right">

                    <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Account</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? 'email@example.com' }}</p>
                    </div>

                    <div class="p-1">
                        <a href="{{ route('nurse.profile') }}" wire:navigate class="flex items-center px-3 py-2 text-sm text-gray-600 dark:text-gray-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg transition-colors">
                            <x-heroicon-o-user class="w-4 h-4 mr-2"/> Profile
                        </a>
                    </div>

                    <div class="p-1 border-t border-gray-100 dark:border-gray-700">
                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <button type="submit" class="flex w-full items-center px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                <x-heroicon-o-arrow-right-start-on-rectangle class="w-4 h-4 mr-2"/> Sign Out
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- KPI Cards --}}
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @php
                $stats = [
                    ['label' => 'Admitted Patients', 'count' => $admitted ?? 0, 'icon' => 'users', 'color' => 'text-sky-600', 'bg' => 'bg-sky-100 dark:bg-sky-900/30', 'desc' => 'Currently in ward'],
                    ['label' => 'Vitals Due', 'count' => $vitalsDue ?? 0, 'icon' => 'clipboard-document-list', 'color' => 'text-emerald-600', 'bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'desc' => 'Next 4 hours'],
                    ['label' => 'Critical Supplies', 'count' => $lowStock ?? 0, 'icon' => 'cube', 'color' => 'text-amber-600', 'bg' => 'bg-amber-100 dark:bg-amber-900/30', 'desc' => 'Low stock alert'],
                ];
            @endphp

            @foreach ($stats as $stat)
                <div class="group bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md hover:border-indigo-200 dark:hover:border-indigo-800 transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="p-3 rounded-xl {{ $stat['bg'] }} group-hover:scale-110 transition-transform duration-300">
                            <x-dynamic-component component="heroicon-s-{{ $stat['icon'] }}" class="w-6 h-6 {{ $stat['color'] }}" />
                        </div>
                        <span class="text-xs font-medium text-gray-400">{{ $stat['desc'] }}</span>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $stat['count'] }}</h3>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </section>

        {{-- Admitted Patients Table --}}
        <section class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <div class="w-1.5 h-6 bg-indigo-500 rounded-full"></div>
                    Recent Admissions
                </h3>
                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs font-mono rounded-full">
                    {{ isset($admittedPatients) ? $admittedPatients->count() : 0 }} Total
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-4">Patient</th>
                            <th class="px-6 py-4">Ward Info</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Admission Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($admittedPatients as $admission)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 flex-shrink-0 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 flex items-center justify-center font-bold text-sm mr-3 group-hover:ring-2 ring-indigo-500 ring-offset-2 dark:ring-offset-gray-800 transition-all">
                                            {{ substr($admission->patient->first_name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white">
                                                {{ $admission->patient->first_name ?? 'Unknown' }} {{ $admission->patient->last_name ?? '' }}
                                            </div>
                                            <div class="text-xs text-gray-500">ID: #{{ $admission->patient->id ?? '---' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $admission->bed->ward->name ?? 'Unassigned' }}</span>
                                        <span class="text-xs text-blue-600 dark:text-blue-400">Bed {{ $admission->bed->bed_number ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-400 border border-green-200 dark:border-green-900">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                        Admitted
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-gray-500 dark:text-gray-400 font-mono text-xs">
                                    {{ $admission->admission_date ? $admission->admission_date->format('M d, H:i') : 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="p-4 rounded-full bg-gray-100 dark:bg-gray-800 mb-3">
                                            <x-heroicon-o-clipboard-document class="w-8 h-8 text-gray-400" />
                                        </div>
                                        <p class="text-gray-500 dark:text-gray-400 font-medium">No admitted patients found.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</main>
