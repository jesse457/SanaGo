{{--
    * MODERN & CLEAN DOCTOR UI
    *
    * Improvements:
    * - Glassmorphism modals with backdrop blur
    * - unified "soft" shadow design system
    * - Visual vital signs grid with icons
    * - Refined timeline typography
    * - Responsive spacing and transitions
--}}

@php
    // Helper function for time bucketing
    if (!function_exists('timeBucket')) {
        function timeBucket($date, $now)
        {
            if (!$date) return 'doctor.older';
            if ($date->isSameDay($now)) return 'doctor.today';
            if ($date->isSameDay($now->clone()->subDay())) return 'doctor.yesterday';
            if ($date->isAfter($now->clone()->startOfWeek())) return 'doctor.this_week';
            if ($date->isAfter($now->clone()->startOfMonth())) return 'doctor.this_month';
            if ($date->isAfter($now->clone()->startOfYear())) return 'doctor.this_year';
            return 'doctor.older';
        }
    }
    $now = now();
@endphp

<div x-data="{
    tab: 'history',
    open: false,
    labModal: false,
    prescriptionModal: false,
    isFullscreen: false
}"
x-init="window.addEventListener('open-lab-request-modal', () => labModal = true); window.addEventListener('open-prescription-modal', () => prescriptionModal = true); window.addEventListener('close-lab-request-modal', () => labModal = false); window.addEventListener('close-prescription-modal', () => prescriptionModal = false);"
class="w-full h-screen overflow-y-auto bg-slate-50 dark:bg-gray-900 text-slate-600 dark:text-slate-300 font-sans">

    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">
        {{-- Mobile hamburger --}}
        <button @click="open = true"
            class="lg:hidden p-2 mb-4 rounded-lg text-slate-600 bg-white shadow-sm border border-slate-200 hover:bg-slate-50 transition-all">
            <x-heroicon-o-bars-3 class="w-6 h-6" />
        </button>

       {{-- Breadcrumb --}}
        <nav class="mb-6 flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li>
                    <a href="{{ route('doctor.dashboard') }}" wire:navigate class="text-sm text-gray-700 hover:text-blue-600 dark:text-gray-400">
                        <x-heroicon-s-home class="w-4 h-4 inline mr-1" /> {{ __('doctor.home') }}
                    </a>
                </li>
                <li><x-heroicon-s-chevron-right class="w-3 h-3 text-gray-400" /></li>
                <li>
                    <a href="{{ route('doctor.patients') }}" wire:navigate
                        class="text-sm text-gray-700 hover:text-blue-600 dark:text-gray-400">{{ __('doctor.patients') }}</a>
                </li>

                <li><x-heroicon-s-chevron-right class="w-3 h-3 text-gray-400" /></li>
                <li><span class="text-sm text-gray-400 dark:text-gray-400">  {{ optional($patient)->first_name }}
                        {{ optional($patient)->last_name }}</span></li>
            </ol>
        </nav>

        {{-- Patient Header Card --}}
        <div class="relative bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] mb-8 border border-slate-100 dark:border-gray-700 overflow-hidden w-full">
            {{-- Background Decorative Gradient --}}
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-50 dark:bg-blue-900/20 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row items-start justify-between gap-8">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 w-full">
                    {{-- Avatar --}}
                    <div class="relative group">
                        <div class="absolute -inset-0.5 bg-gradient-to-br from-blue-500 to-teal-400 rounded-full opacity-30 group-hover:opacity-50 transition duration-500 blur"></div>
                        <img src="https://placehold.co/120x120/f1f5f9/475569?text={{ substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1) }}"
                            alt="{{ __('doctor.avatar') }}"
                            class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover border-4 border-white dark:border-gray-800 shadow-md">
                    </div>

                    {{-- Info --}}
                    <div class="flex-grow text-center sm:text-left">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-2">
                            <h1 class="text-3xl font-bold text-slate-800 dark:text-white tracking-tight">
                                {{ $patient->first_name }} {{ $patient->last_name }}
                            </h1>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600 dark:bg-gray-700 dark:text-gray-300">
                                #{{ $patient->patient_uid }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-x-8 gap-y-3 mt-4 text-sm">
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                                <x-heroicon-o-cake class="w-4 h-4" />
                                <span>{{ $patient->age }} {{ __('doctor.years_old') }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                                <x-heroicon-o-user class="w-4 h-4" />
                                <span>{{ ucfirst($patient->gender) }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                                <x-heroicon-o-phone class="w-4 h-4" />
                                <span class="select-all">{{ $patient->phone_number ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                                <x-heroicon-o-envelope class="w-4 h-4" />
                                <span class="select-all">{{ $patient->email ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action / Status --}}
                <div class="w-full lg:w-auto flex-shrink-0 flex flex-col items-center lg:items-end justify-center gap-3">
                    @if ($patient->is_admitted_approve && $this->admission)
                        <div @class([
                            'flex items-center gap-3 px-5 py-2.5 rounded-xl border shadow-sm transition-all',
                            'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-900/30 dark:border-amber-800 dark:text-amber-200' => $this->admission->status === 'Pending',
                            'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-900/30 dark:border-emerald-800 dark:text-emerald-200' => $this->admission->status === 'Admitted',
                        ])>
                            <span class="relative flex h-3 w-3">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 @if($this->admission->status === 'Pending') bg-amber-400 @else bg-emerald-400 @endif"></span>
                              <span class="relative inline-flex rounded-full h-3 w-3 @if($this->admission->status === 'Pending') bg-amber-500 @else bg-emerald-500 @endif"></span>
                            </span>
                            <span class="font-semibold text-sm">
                                {{ $this->admission->status === 'Pending' ? __('doctor.admission_pending') : __('doctor.admitted') }}
                            </span>
                        </div>
                    @else
                        <button wire:click="requestPatientAdmit({{ $patient->id }})"
                            wire:loading.attr="disabled"
                            class="group relative w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white text-sm font-semibold rounded-xl shadow-md hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all disabled:opacity-70 disabled:cursor-not-allowed overflow-hidden">

                            <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>

                            <div wire:loading.remove wire:target="requestPatientAdmit" class="flex items-center gap-2">
                                <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                                <span>{{ __('doctor.request_admission') }}</span>
                            </div>
                            <div wire:loading wire:target="requestPatientAdmit" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>{{ __('doctor.processing') }}</span>
                            </div>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tabs Navigation --}}
        <div class="mb-8 border-b border-slate-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="tab = 'history'"
                    class="group relative py-4 px-1 flex items-center gap-2 text-sm font-medium transition-colors duration-200"
                    x-bind:class="tab === 'history' ? 'text-blue-600 dark:text-blue-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'">
                    <x-heroicon-o-clipboard-document-list class="w-5 h-5" x-bind:class="tab === 'history' ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-500'" />
                    {{ __('doctor.consultation_history') }}

                    {{-- Active Underline --}}
                    <span class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-600 rounded-t-full transition-transform duration-300 origin-left"
                          x-bind:class="tab === 'history' ? 'scale-x-100' : 'scale-x-0'"></span>
                </button>

                <button @click="tab = 'vitals'"
                    class="group relative py-4 px-1 flex items-center gap-2 text-sm font-medium transition-colors duration-200"
                    x-bind:class="tab === 'vitals' ? 'text-rose-600 dark:text-rose-400' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'">
                    <x-heroicon-o-heart class="w-5 h-5" x-bind:class="tab === 'vitals' ? 'text-rose-600' : 'text-slate-400 group-hover:text-slate-500'" />
                    {{ __('doctor.vitals') }}

                    {{-- Active Underline --}}
                    <span class="absolute bottom-0 left-0 w-full h-0.5 bg-rose-600 rounded-t-full transition-transform duration-300 origin-left"
                          x-bind:class="tab === 'vitals' ? 'scale-x-100' : 'scale-x-0'"></span>
                </button>
            </nav>
        </div>

        {{-- TABS CONTENT AREA --}}
        <div class="min-h-[400px]">

            {{-- 1. Consultation History --}}
            <div x-show="tab === 'history'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0">

                @php
                    $groupedHistory = $patient->medicalRecords
                        ->sortByDesc('created_at')
                        ->groupBy(fn($record) => timeBucket($record->created_at, $now));
                @endphp

                @forelse($groupedHistory as $bucketKey => $records)
                    <div class="relative pl-8 pb-10 last:pb-0">
                        {{-- Timeline Line --}}
                        <div class="absolute top-0 left-3.5 bottom-0 w-px bg-slate-200 dark:bg-gray-700 last:bottom-auto last:h-full"></div>

                        {{-- Bucket Header --}}
                        <div class="relative flex items-center mb-6 -ml-8">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white dark:bg-gray-900 border-2 border-blue-500 flex items-center justify-center z-10 shadow-sm ml-3.5">
                                <div class="w-2.5 h-2.5 bg-blue-500 rounded-full"></div>
                            </div>
                            <span class="ml-4 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-gray-900 px-2">
                                {{ __($bucketKey) }}
                            </span>
                        </div>

                        {{-- Records List --}}
                        <div class="space-y-6">
                            @foreach ($records as $record)
                                <div class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-md border border-slate-100 dark:border-gray-700 transition-all duration-200 hover:scale-[1.005]">

                                    {{-- Card Header --}}
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 dark:border-gray-700 mb-4">
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-lg font-bold text-slate-800 dark:text-white">
                                                    {{ ucfirst(str_replace('_', ' ', $record->record_type)) }}
                                                </h4>
                                                {{-- Optional status badge here if needed --}}
                                            </div>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                                {{ $record->created_at->format('l, F j, Y') }} at {{ $record->created_at->format('g:i A') }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <div class="text-right">
                                                <p class="text-xs text-slate-400 uppercase tracking-wide font-semibold">{{ __('doctor.doctor') }}</p>
                                                <p class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $record->doctor->name ?? 'Unknown' }}</p>
                                            </div>
                                            <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-gray-700 flex items-center justify-center text-xs font-bold text-slate-600 dark:text-slate-300">
                                                {{ substr($record->doctor->name ?? 'Dr', 0, 1) }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Card Content Grid --}}
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                        {{-- Diagnosis Column --}}
                                        <div class="space-y-4">
                                            @if ($record->diagnosis_text)
                                                <div class="bg-blue-50/50 dark:bg-blue-900/10 rounded-xl p-4 border border-blue-100 dark:border-blue-800/30">
                                                    <div class="flex items-center gap-2 mb-2">
                                                        <x-heroicon-s-clipboard-document-check class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                                        <h5 class="text-sm font-bold text-blue-900 dark:text-blue-100">{{ __('doctor.diagnosis') }}</h5>
                                                    </div>
                                                    <p class="text-slate-700 dark:text-slate-300 text-sm leading-relaxed">
                                                        {{ $record->diagnosis_text }}
                                                    </p>
                                                </div>
                                            @endif

                                            @if ($record->complaint)
                                                <div>
                                                    <h5 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">{{ __('doctor.complaint') }}</h5>
                                                    <p class="text-slate-700 dark:text-slate-300 text-sm">{{ $record->complaint }}</p>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Treatment/Notes Column --}}
                                        <div class="space-y-4">
                                            @if ($record->treatment_plan)
                                                <div>
                                                    <h5 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">{{ __('doctor.treatment_plan') }}</h5>
                                                    <p class="text-slate-700 dark:text-slate-300 text-sm leading-relaxed bg-slate-50 dark:bg-gray-700/50 p-3 rounded-lg border border-slate-100 dark:border-gray-600">
                                                        {{ $record->treatment_plan }}
                                                    </p>
                                                </div>
                                            @endif

                                            <div>
                                                <h5 class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-1">{{ __('doctor.notes') }}</h5>
                                                <p class="text-slate-600 dark:text-slate-400 text-sm">
                                                    {{ Str::limit($record->general_notes ?? ($record->soap_notes ?? __('doctor.no_notes_available')), 150) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Footer Actions --}}
                                    <div class="mt-6 pt-4 border-t border-slate-100 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4">
                                        {{-- Attachments --}}
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($record->attachments as $attachment)
                                                <button type="button" wire:click="previewAttachment({{ $attachment->id }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100 hover:border-slate-300 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 transition-colors">
                                                    <x-heroicon-m-paper-clip class="w-3.5 h-3.5" />
                                                    {{ Str::limit($attachment->file_name, 20) }}
                                                </button>
                                            @endforeach
                                        </div>

                                        <a href="{{ route('doctor.consultation', $record->id) }}" wire:navigate
                                            class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                            {{ __('doctor.view_details') }}
                                            <x-heroicon-m-arrow-long-right class="w-4 h-4" />
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-16 text-center bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-slate-200 dark:border-gray-700">
                        <div class="w-16 h-16 bg-slate-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                            <x-heroicon-o-document-magnifying-glass class="w-8 h-8 text-slate-400" />
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ __('doctor.no_consultations_found_header') }}</h3>
                        <p class="text-slate-500 dark:text-slate-400 max-w-sm mt-1">{{ __('doctor.no_consultations_found_subtext') }}</p>
                    </div>
                @endforelse
            </div>

            {{-- 2. Vitals History --}}
            <div x-show="tab === 'vitals'"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0">

                @php
                    $groupedVitals = $patient
                        ->vitals()
                        ->with('nurse')
                        ->orderBy('recorded_at', 'desc')
                        ->get()
                        ->groupBy(fn($vital) => timeBucket($vital->recorded_at, $now));
                @endphp

                @forelse($groupedVitals as $bucketKey => $vitals)
                    <div class="relative pl-8 pb-10 last:pb-0">
                        <div class="absolute top-0 left-3.5 bottom-0 w-px bg-slate-200 dark:bg-gray-700 last:bottom-auto last:h-full"></div>

                        <div class="relative flex items-center mb-6 -ml-8">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-white dark:bg-gray-900 border-2 border-rose-500 flex items-center justify-center z-10 shadow-sm ml-3.5">
                                <x-heroicon-m-heart class="w-4 h-4 text-rose-500" />
                            </div>
                            <span class="ml-4 text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-gray-900 px-2">
                                {{ __($bucketKey) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach ($vitals as $v)
                                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm border border-slate-100 dark:border-gray-700 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="flex items-center gap-2">
                                            <div class="p-1.5 bg-rose-50 dark:bg-rose-900/20 rounded-lg">
                                                <x-heroicon-m-clock class="w-4 h-4 text-rose-600 dark:text-rose-400" />
                                            </div>
                                            <span class="text-sm font-bold text-slate-700 dark:text-slate-200">
                                                {{ optional($v->recorded_at)->format('h:i A') }}
                                            </span>
                                        </div>
                                        @if ($v->flag_abnormal)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300 ring-1 ring-inset ring-red-600/10">
                                                {{ __('doctor.abnormal') }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="grid grid-cols-3 gap-4">
                                        {{-- Temp --}}
                                        <div class="flex flex-col p-2 rounded-lg bg-slate-50 dark:bg-gray-700/30">
                                            <span class="text-xs text-slate-400 font-medium uppercase">{{ __('doctor.temp') }}</span>
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-lg font-bold text-slate-800 dark:text-white">{{ $v->temperature_celsius ?? '--' }}</span>
                                                <span class="text-xs text-slate-500">°C</span>
                                            </div>
                                        </div>
                                        {{-- BP --}}
                                        <div class="flex flex-col p-2 rounded-lg bg-slate-50 dark:bg-gray-700/30">
                                            <span class="text-xs text-slate-400 font-medium uppercase">{{ __('doctor.bp') }}</span>
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-lg font-bold text-slate-800 dark:text-white">{{ $v->blood_pressure_systolic ?? '--' }}/{{ $v->blood_pressure_diastolic ?? '--' }}</span>
                                            </div>
                                        </div>
                                        {{-- HR --}}
                                        <div class="flex flex-col p-2 rounded-lg bg-slate-50 dark:bg-gray-700/30">
                                            <span class="text-xs text-slate-400 font-medium uppercase">{{ __('doctor.hr') }}</span>
                                            <div class="flex items-baseline gap-1">
                                                <span class="text-lg font-bold text-slate-800 dark:text-white">{{ $v->heart_rate_bpm ?? '--' }}</span>
                                                <span class="text-xs text-slate-500">bpm</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Second Row --}}
                                    <div class="grid grid-cols-3 gap-4 mt-2">
                                        <div class="flex flex-col p-2">
                                            <span class="text-xs text-slate-400">{{ __('doctor.spo2') }}</span>
                                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $v->spo2_percentage ?? '--' }}%</span>
                                        </div>
                                        <div class="flex flex-col p-2">
                                            <span class="text-xs text-slate-400">{{ __('doctor.weight') }}</span>
                                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $v->weight_kg ?? '--' }} kg</span>
                                        </div>
                                        <div class="flex flex-col p-2">
                                            <span class="text-xs text-slate-400">{{ __('doctor.bmi') }}</span>
                                            <span class="font-medium text-slate-700 dark:text-slate-300">{{ $v->bmi ?? '--' }}</span>
                                        </div>
                                    </div>

                                    <div class="mt-3 pt-3 border-t border-slate-100 dark:border-gray-700 flex items-center justify-between text-xs text-slate-500">
                                        <span>{{ __('doctor.nurse') }}: {{ optional($v->nurse)->name ?? 'N/A' }}</span>
                                        @if($v->notes)
                                            <span class="text-blue-600 cursor-help" title="{{ $v->notes }}">
                                                <x-heroicon-s-chat-bubble-oval-left-ellipsis class="w-4 h-4 inline" /> Note
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-16 text-center bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-slate-200 dark:border-gray-700">
                        <div class="w-16 h-16 bg-rose-50 dark:bg-rose-900/20 rounded-full flex items-center justify-center mb-4">
                            <x-heroicon-o-heart class="w-8 h-8 text-rose-400" />
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ __('doctor.no_vitals_found_header') }}</h3>
                        <p class="text-slate-500 dark:text-slate-400 max-w-sm mt-1">{{ __('doctor.no_vitals_found_subtext') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{--
        ENHANCED FILE PREVIEW MODAL
        Features: Backdrop blur, smooth scale animation, better controls
    --}}
    <div x-data="{
        open: @entangle('showAttachmentPreview'),
        isFullscreen: false
    }"
    x-show="open"
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 backdrop-blur-none"
    x-transition:enter-end="opacity-100 backdrop-blur-md"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 backdrop-blur-md"
    x-transition:leave-end="opacity-0 backdrop-blur-none"
    class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 p-4 backdrop-blur-sm">

        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden flex flex-col transform transition-all"
             x-bind:class="isFullscreen ? 'fixed inset-0 rounded-none max-w-none h-full max-h-none' : ''"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             @click.outside="open = false; @this.call('closeAttachmentPreview')">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-900 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <x-heroicon-s-document-text class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white">{{ __('doctor.attachment_preview') }}</h2>
                        <p class="text-xs text-slate-500">{{ __('doctor.viewing_file') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-1 bg-slate-100 dark:bg-gray-800 rounded-lg p-1">
                    <button @click="isFullscreen = !isFullscreen"
                            class="p-1.5 rounded-md hover:bg-white dark:hover:bg-gray-700 text-slate-500 hover:text-blue-600 transition-all hover:shadow-sm"
                            :title="isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'">
                        <x-heroicon-o-arrows-pointing-out x-show="!isFullscreen" class="w-5 h-5" />
                        <x-heroicon-o-arrows-pointing-in x-show="isFullscreen" class="w-5 h-5" />
                    </button>
                    <button @click="open = false; @this.call('closeAttachmentPreview')"
                            class="p-1.5 rounded-md hover:bg-white dark:hover:bg-gray-700 text-slate-500 hover:text-red-600 transition-all hover:shadow-sm"
                            title="{{ __('doctor.close') }}">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>
            </div>

            {{-- Modal Content --}}
            <div class="flex-1 overflow-auto bg-slate-50 dark:bg-gray-950 relative">
                @if ($attachmentPreviewUrl)
                    @if (Str::startsWith($attachmentPreviewMime, 'image/'))
                        <div class="flex items-center justify-center min-h-full p-4">
                            <img src="{{ $attachmentPreviewUrl }}" alt="Preview" class="max-w-full max-h-full object-contain rounded shadow-lg" />
                        </div>
                    @elseif(Str::startsWith($attachmentPreviewMime, 'application/pdf'))
                        <iframe src="{{ $attachmentPreviewUrl }}" class="w-full h-full" frameborder="0"></iframe>
                    @else
                        <div class="flex flex-col items-center justify-center h-full text-center p-12">
                            <div class="w-20 h-20 bg-slate-200 dark:bg-gray-800 rounded-2xl flex items-center justify-center mb-6 shadow-inner">
                                <x-heroicon-o-document-arrow-down class="w-10 h-10 text-slate-400" />
                            </div>
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">{{ __('doctor.preview_not_available') }}</h3>
                            <p class="text-slate-500 dark:text-slate-400 mb-8 max-w-md">{{ __('doctor.unsupported_file_type_desc') }}</p>
                            <a href="{{ $attachmentPreviewUrl }}" target="_blank"
                               class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-600/20 transition-all hover:-translate-y-0.5">
                                <x-heroicon-m-arrow-down-tray class="w-5 h-5" />
                                {{ __('doctor.download_file') }}
                            </a>
                        </div>
                    @endif
                @else
                    <div class="flex items-center justify-center h-full">
                        <x-heroicon-o-arrow-path class="w-8 h-8 animate-spin text-slate-400" />
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{--
        MODAL CONTAINERS
        Using the same enhanced modal style for other dynamic components
    --}}
    <!-- Lab Request Modal -->
    <div x-show="labModal"
         x-cloak
         class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-hidden"
             @click.outside="labModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            <div class="h-full overflow-y-auto custom-scrollbar">
                <livewire:tenants.doctor.components.lab-request-modal />
            </div>
        </div>
    </div>

    <!-- Prescription Modal -->
    <div x-show="prescriptionModal"
         x-cloak
         class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden"
             @click.outside="prescriptionModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0">
            <div class="h-full overflow-y-auto custom-scrollbar">
                <livewire:tenants.doctor.components.prescription-modal />
            </div>
        </div>
    </div>
</div>
