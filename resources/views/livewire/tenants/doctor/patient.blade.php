<main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50 dark:bg-gray-900">

    {{-- 1. HEADER SECTION --}}
    <header class="flex-shrink-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm z-20">
        <div class="px-6 py-5 flex items-center justify-between">
            <div>
                <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 mt-2">
                    <ol class="flex items-center space-x-2">
                        <li>
                            <a href="{{ route('doctor.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                {{ __('doctor.home') }}
                            </a>
                        </li>
                        <li><x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300" /></li>
                        <li class="text-gray-900 dark:text-white">{{ __('doctor.patients') }}</li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                    {{ __('doctor.my_patients') }}
                </h1>
                         <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-md">
                            {{ __('doctor.my_patients_subtitle') }}
                        </p>
            </div>

            <div class="hidden md:flex items-center gap-3">
                 <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800">
                    <x-heroicon-m-users class="w-3 h-3 mr-1.5"/>
                    {{ $patients->total() }} Total Patients
                 </span>
            </div>
        </div>

        {{-- Toolbar (Search & Filter) --}}
        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex flex-col md:flex-row gap-4">

            {{-- Search Input --}}
            <div class="relative flex-1 max-w-lg group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-heroicon-m-magnifying-glass class="h-4 w-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       class="block w-full pl-9 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-800 placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-shadow shadow-sm"
                       placeholder="{{ __('doctor.search_patient_placeholder_long') }}">

                <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <x-heroicon-o-arrow-path class="h-4 w-4 text-blue-500 animate-spin" />
                </div>
            </div>

            {{-- Filters --}}
            <div class="flex gap-3 w-full md:w-auto overflow-x-auto pb-1 md:pb-0">
                <select wire:model.live="sortBy" class="pl-3 pr-8 py-2 text-sm border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                    @foreach ($sortOpts as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterStatus" class="pl-3 pr-8 py-2 text-sm border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                    <option value="">{{ __('doctor.all_statuses') }}</option>
                    <option value="active">{{ __('doctor.active') }}</option>
                    <option value="new">{{ __('doctor.new') }}</option>
                </select>
            </div>
        </div>
    </header>

    {{-- 2. MAIN CONTENT (Scrollable) --}}
    <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($patients as $patient)
                <a href="{{ route('doctor.patient-info', $patient->id) }}" wire:navigate
                   class="group flex flex-col bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-400 dark:hover:border-blue-500 shadow-sm hover:shadow-md transition-all duration-200 relative overflow-hidden h-full">

                    <div class="p-5 flex-1">
                        {{-- Header: Avatar & Name --}}
                        <div class="flex items-start gap-4 mb-4">
                            <div class="relative flex-shrink-0">
                                @if($patient->profile_picture)
                                    <img src="{{ Storage::disk('s3')->temporaryUrl($patient->profile_picture, now()->addMinutes(10)) }}" class="w-12 h-12 rounded-full object-cover border border-gray-100 dark:border-gray-700">
                                @else
                                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                        {{ substr($patient->first_name, 0, 1) }}
                                    </div>
                                @endif
                                <span class="absolute -bottom-1 -right-1 block h-3.5 w-3.5 rounded-full ring-2 ring-white dark:ring-gray-800 {{ $patient->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                            </div>

                            <div class="min-w-0 flex-1">
                                <h3 class="text-base font-bold text-gray-900 dark:text-white truncate group-hover:text-blue-600 transition-colors">
                                    {{ $patient->first_name }} {{ $patient->last_name }}
                                </h3>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="inline-flex items-center rounded bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 text-[10px] font-mono font-medium text-gray-600 dark:text-gray-300">
                                        {{ $patient->patient_uid }}
                                    </span>
                                    <span class="text-xs text-gray-400">•</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $patient->age }} yrs</span>
                                </div>
                            </div>
                        </div>

                        {{-- Body: Stats --}}
                        <div class="grid grid-cols-2 gap-3 mb-2">
                             @php
                                $nextAppt = $patient->appointments->where('appointment_date', '>', now())->sortBy('appointment_date')->first();
                                $lastAppt = $patient->appointments->where('appointment_date', '<=', now())->sortByDesc('appointment_date')->first();
                            @endphp

                            <div class="bg-gray-50 dark:bg-gray-900/50 p-2.5 rounded-lg border border-gray-100 dark:border-gray-700/50">
                                <span class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Next Visit</span>
                                @if($nextAppt)
                                    <span class="block text-xs font-semibold text-blue-600 dark:text-blue-400 truncate">
                                        {{ $nextAppt->appointment_date->format('M d, H:i') }}
                                    </span>
                                @else
                                    <span class="block text-xs text-gray-400 italic">None Scheduled</span>
                                @endif
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-900/50 p-2.5 rounded-lg border border-gray-100 dark:border-gray-700/50">
                                <span class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Last Visit</span>
                                <span class="block text-xs font-semibold text-gray-700 dark:text-gray-300 truncate">
                                    {{ $lastAppt ? $lastAppt->appointment_date->format('M d, Y') : 'Never' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Footer: Quick Actions --}}
                    <div class="px-5 py-3 bg-gray-50 dark:bg-gray-900/30 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <div class="flex items-center text-xs text-gray-500">
                            <x-heroicon-m-phone class="w-3.5 h-3.5 mr-1.5 opacity-70"/>
                            {{ $patient->phone ?? 'No Phone' }}
                        </div>
                        <div class="flex items-center text-blue-600 dark:text-blue-400 text-xs font-medium group-hover:translate-x-1 transition-transform">
                            View Profile <x-heroicon-m-arrow-right class="w-3.5 h-3.5 ml-1"/>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-16 text-center">
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-full p-4 mb-4">
                        <x-heroicon-o-users class="w-10 h-10 text-gray-400" />
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('doctor.no_patients_found') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm max-w-sm mt-1 mx-auto">
                        {{ __('doctor.try_adjusting_search') }}
                    </p>
                    <button wire:click="$set('search', '')" class="mt-4 text-blue-600 hover:text-blue-800 font-medium text-sm">
                        Clear Search Filters
                    </button>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $patients->links(data: ['scrollTo' => false]) }}
        </div>
    </div>
</main>
