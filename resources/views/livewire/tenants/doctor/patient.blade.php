<main class="flex-1 p-4 md:p-8 min-w-0 overflow-y-auto">

    {{-- Header Section --}}
    <div class="flex flex-col gap-4 mb-8">
        <div class="flex items-center justify-between">
            {{-- Mobile Hamburger Trigger --}}
            <button @click="open = true" class="lg:hidden p-2 -ml-2 rounded-lg text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800">
                <x-heroicon-o-bars-3 class="w-7 h-7" />
            </button>

            {{-- Breadcrumbs --}}
            <nav class="hidden md:flex text-sm font-medium text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li>
                        <a href="{{ route('doctor.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition flex items-center">
                            <x-heroicon-s-home class="w-4 h-4 mr-1" />
                            {{ __('doctor.home') }}
                        </a>
                    </li>
                    <li><x-heroicon-s-chevron-right class="w-4 h-4 text-gray-300" /></li>
                    <li class="text-gray-900 dark:text-white">{{ __('doctor.patients') }}</li>
                </ol>
            </nav>
        </div>

        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                {{ __('doctor.my_patients') }}
            </h1>
            <p class="mt-1 text-gray-500 dark:text-gray-400">
                {{ __('doctor.my_patients_subtitle') }}
            </p>
        </div>
    </div>

    {{-- Filters & Search Toolbar --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 mb-6 sticky top-0 z-30">
        <div class="flex flex-col lg:flex-row gap-4">

            {{-- Search Input --}}
            <div class="flex-1 relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       class="block w-full pl-10 pr-10 py-2.5 border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-gray-50 dark:bg-gray-900/50 text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition-all"
                       placeholder="{{ __('doctor.search_patient_placeholder_long') }}">

                {{-- Loading Spinner inside input --}}
                <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <x-heroicon-o-arrow-path class="h-4 w-4 text-blue-500 animate-spin" />
                </div>
            </div>

            {{-- Filters Group --}}
            <div class="flex gap-2 overflow-x-auto pb-1 lg:pb-0">
                <select wire:model.live="sortBy" class="pl-3 pr-8 py-2.5 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    @foreach ($sortOpts as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>

                <select wire:model.live="filterStatus" class="pl-3 pr-8 py-2.5 border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">{{ __('doctor.all_statuses') }}</option>
                    <option value="active">{{ __('doctor.active') }}</option>
                    <option value="new">{{ __('doctor.new') }}</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Patients Grid --}}
    <div class="space-y-4">
        @forelse($patients as $patient)
            <a href="{{ route('doctor.patient-info', $patient->id) }}" wire:navigate
               class="group block bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-400 dark:hover:border-blue-500 shadow-sm hover:shadow-md transition-all duration-200 relative overflow-hidden">

                <div class="p-5 flex flex-col sm:flex-row gap-5">

                    {{-- Avatar & Basic Info --}}
                    <div class="flex items-start gap-4 min-w-0 flex-1">
                        <div class="relative flex-shrink-0">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($patient->first_name . ' ' . $patient->last_name) }}&background=e0e7ff&color=3730a3&bold=true"
                                 alt=""
                                 class="w-14 h-14 rounded-full object-cover ring-2 ring-white dark:ring-gray-700">

                            {{-- Status Indicator (Example logic) --}}
                            <span class="absolute bottom-0 right-0 block h-3.5 w-3.5 rounded-full ring-2 ring-white dark:ring-gray-800 {{ $patient->is_active ? 'bg-green-400' : 'bg-gray-400' }}"></span>
                        </div>

                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white truncate group-hover:text-blue-600 transition-colors">
                                    {{ $patient->first_name }} {{ $patient->last_name }}
                                </h3>
                                <span class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-700 px-2 py-1 text-xs font-medium text-gray-600 dark:text-gray-300 ring-1 ring-inset ring-gray-500/10">
                                    UID: {{ $patient->patient_uid ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-3">
                                <span class="flex items-center">
                                    <x-heroicon-m-cake class="w-4 h-4 mr-1 text-gray-400" />
                                    {{ $patient->age }} {{ __('doctor.yrs') }}
                                </span>
                                <span class="flex items-center">
                                    <x-heroicon-m-phone class="w-4 h-4 mr-1 text-gray-400" />
                                    {{ $patient->phone ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Appointment Meta Data --}}
                    <div class="flex flex-col sm:items-end justify-center gap-1 text-sm border-t sm:border-t-0 sm:border-l border-dashed border-gray-200 dark:border-gray-700 pt-3 sm:pt-0 sm:pl-5 min-w-[200px]">

                        {{-- Next Appointment --}}
                        @php
                            $nextAppt = $patient->appointments->where('appointment_date', '>', now())->sortBy('appointment_date')->first();
                            $lastAppt = $patient->appointments->where('appointment_date', '<=', now())->sortByDesc('appointment_date')->first();
                        @endphp

                        <div class="flex items-center justify-between sm:justify-end w-full gap-2">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('doctor.next') }}</span>
                            @if($nextAppt)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ $nextAppt->appointment_date->format('M d, H:i') }}
                                </span>
                            @else
                                <span class="text-gray-400 italic text-xs">{{ __('doctor.none') }}</span>
                            @endif
                        </div>

                        {{-- Last Visit --}}
                        <div class="flex items-center justify-between sm:justify-end w-full gap-2 mt-1">
                            <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('doctor.last') }}</span>
                            <span class="text-gray-700 dark:text-gray-300 font-medium">
                                {{ $lastAppt ? $lastAppt->appointment_date->format('M d, Y') : '—' }}
                            </span>
                        </div>
                    </div>

                    {{-- Action Arrow (Desktop only) --}}
                    <div class="hidden sm:flex items-center justify-center text-gray-300 group-hover:text-blue-500 transition-transform group-hover:translate-x-1">
                        <x-heroicon-m-chevron-right class="w-6 h-6" />
                    </div>
                </div>

                {{-- Quick Action Footer (Optional) --}}
                <div class="bg-gray-50 dark:bg-gray-900/50 px-5 py-2 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <span class="text-xs text-gray-400">
                        {{ __('doctor.registered') }} {{ $patient->created_at->format('M Y') }}
                    </span>
                    <button catch.stop="alert('Chat')" class="text-sm text-blue-600 dark:text-blue-400 hover:underline flex items-center font-medium">
                        <x-heroicon-s-chat-bubble-oval-left class="w-4 h-4 mr-1" />
                        {{ __('doctor.quick_message') }}
                    </button>
                </div>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-full mb-4">
                    <x-heroicon-o-magnifying-glass class="w-10 h-10 text-gray-400" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">{{ __('doctor.no_patients_found') }}</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm max-w-xs text-center mt-1">
                    {{ __('doctor.try_adjusting_search') }}
                </p>
                <button wire:click="$set('search', '')" class="mt-4 text-blue-600 hover:underline text-sm font-medium">
                    {{ __('doctor.clear_search') }}
                </button>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $patients->links(data: ['scrollTo' => false]) }}
    </div>
</main>
