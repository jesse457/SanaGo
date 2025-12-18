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
class="w-full h-full bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-300 font-sans overflow-y-auto">

    <div class="max-w-7xl mx-auto p-6">
       {{-- Breadcrumb --}}
        <nav class="mb-6 flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2">
                <li>
                    <a href="{{ route('doctor.dashboard') }}" wire:navigate class="text-xs font-medium text-gray-500 hover:text-blue-600 dark:text-gray-400">
                        <x-heroicon-s-home class="w-3 h-3 inline mr-1" /> {{ __('doctor.home') }}
                    </a>
                </li>
                <li><x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300" /></li>
                <li>
                    <a href="{{ route('doctor.patients') }}" wire:navigate
                        class="text-xs font-medium text-gray-500 hover:text-blue-600 dark:text-gray-400">{{ __('doctor.patients') }}</a>
                </li>
                <li><x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300" /></li>
                <li><span class="text-xs font-medium text-gray-900 dark:text-white">  {{ optional($patient)->first_name }} {{ optional($patient)->last_name }}</span></li>
            </ol>
        </nav>

        {{-- Patient Header Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 mb-6">
            <div class="flex flex-col lg:flex-row items-start justify-between gap-6">
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 w-full">
                    {{-- Avatar --}}
                    <div class="relative">
                        <img src="https://placehold.co/120x120/f3f4f6/4b5563?text={{ substr($patient->first_name, 0, 1) . substr($patient->last_name, 0, 1) }}"
                            alt="{{ __('doctor.avatar') }}"
                            class="w-20 h-20 rounded-full object-cover border border-gray-100 dark:border-gray-700">
                    </div>

                    {{-- Info --}}
                    <div class="text-center sm:text-left flex-1">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-1">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $patient->first_name }} {{ $patient->last_name }}
                            </h1>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                {{ $patient->patient_uid }}
                            </span>
                        </div>

                        <div class="flex flex-wrap justify-center sm:justify-start gap-x-6 gap-y-2 mt-2 text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex items-center gap-1.5">
                                <x-heroicon-m-cake class="w-4 h-4 text-gray-400" />
                                <span>{{ $patient->age }} {{ __('doctor.years_old') }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <x-heroicon-m-user class="w-4 h-4 text-gray-400" />
                                <span>{{ ucfirst($patient->gender) }}</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <x-heroicon-m-phone class="w-4 h-4 text-gray-400" />
                                <span>{{ $patient->phone_number ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action --}}
                <div class="flex-shrink-0">
                    @if ($patient->is_admitted_approve && $this->admission)
                         <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $this->admission->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                            {{ $this->admission->status === 'Pending' ? __('doctor.admission_pending') : __('doctor.admitted') }}
                        </span>
                    @else
                        <button wire:click="requestPatientAdmit({{ $patient->id }})"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <x-heroicon-m-arrow-right-on-rectangle class="w-4 h-4 mr-2" />
                            {{ __('doctor.request_admission') }}
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Tabs Navigation --}}
        <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="tab = 'history'"
                    class="group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                    x-bind:class="tab === 'history' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'">
                    <x-heroicon-m-clipboard-document-list class="w-5 h-5 mr-2"  x-bind:class:class="tab === 'history' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'" />
                    {{ __('doctor.consultation_history') }}
                </button>

                <button @click="tab = 'vitals'"
                    class="group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                     x-bind:class="tab === 'vitals' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'">
                    <x-heroicon-m-heart class="w-5 h-5 mr-2" x-bind:class="tab === 'vitals' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'" />
                    {{ __('doctor.vitals') }}
                </button>
            </nav>
        </div>

        {{-- TABS CONTENT AREA --}}
        <div class="min-h-[400px]">

            {{-- 1. Consultation History --}}
            <div x-show="tab === 'history'">
                @php
                    $groupedHistory = $patient->medicalRecords
                        ->sortByDesc('created_at')
                        ->groupBy(fn($record) => timeBucket($record->created_at, $now));
                @endphp

                @forelse($groupedHistory as $bucketKey => $records)
                    <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __($bucketKey) }}
                            </span>
                            <div class="ml-4 h-px bg-gray-200 dark:bg-gray-700 flex-1"></div>
                        </div>

                        <div class="space-y-4">
                            @foreach ($records as $record)
                                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-5 hover:border-blue-400 transition-colors">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <h4 class="text-base font-bold text-gray-900 dark:text-white">
                                                {{ ucfirst(str_replace('_', ' ', $record->record_type)) }}
                                            </h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ $record->created_at->format('M j, Y • g:i A') }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                                Dr. {{ $record->doctor->name ?? 'Unknown' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        {{-- Left Col --}}
                                        <div>
                                            @if ($record->diagnosis_text)
                                                <div class="mb-3">
                                                    <h5 class="text-xs font-semibold text-gray-500 uppercase mb-1">{{ __('doctor.diagnosis') }}</h5>
                                                    <p class="text-sm text-gray-800 dark:text-gray-200 bg-blue-50 dark:bg-blue-900/20 p-2 rounded border border-blue-100 dark:border-blue-800">
                                                        {{ $record->diagnosis_text }}
                                                    </p>
                                                </div>
                                            @endif
                                            @if ($record->complaint)
                                                <div>
                                                    <h5 class="text-xs font-semibold text-gray-500 uppercase mb-1">{{ __('doctor.complaint') }}</h5>
                                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $record->complaint }}</p>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Right Col --}}
                                        <div>
                                            @if ($record->treatment_plan)
                                                <div class="mb-3">
                                                    <h5 class="text-xs font-semibold text-gray-500 uppercase mb-1">{{ __('doctor.treatment_plan') }}</h5>
                                                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $record->treatment_plan }}</p>
                                                </div>
                                            @endif
                                            <div>
                                                <h5 class="text-xs font-semibold text-gray-500 uppercase mb-1">{{ __('doctor.notes') }}</h5>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ Str::limit($record->general_notes ?? $record->soap_notes, 150) }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                        <div class="flex gap-2">
                                            @foreach ($record->attachments as $attachment)
                                                <button wire:click="previewAttachment({{ $attachment->id }})" class="text-xs flex items-center text-gray-500 hover:text-blue-600">
                                                    <x-heroicon-m-paper-clip class="w-3 h-3 mr-1" /> {{ Str::limit($attachment->file_name, 15) }}
                                                </button>
                                            @endforeach
                                        </div>
                                        <a href="{{ route('doctor.consultation', $record->id) }}" wire:navigate class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center">
                                            {{ __('doctor.view_details') }} <x-heroicon-m-arrow-right class="w-3 h-3 ml-1" />
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-10 text-center border border-gray-200 dark:border-gray-700">
                        <x-heroicon-o-document-text class="w-10 h-10 text-gray-400 mx-auto mb-3" />
                        <h3 class="text-gray-900 dark:text-white font-medium">{{ __('doctor.no_consultations_found_header') }}</h3>
                    </div>
                @endforelse
            </div>

            {{-- 2. Vitals History --}}
            <div x-show="tab === 'vitals'">
                @php
                    $groupedVitals = $patient->vitals()->with('nurse')->orderBy('recorded_at', 'desc')->get()->groupBy(fn($vital) => timeBucket($vital->recorded_at, $now));
                @endphp

                @forelse($groupedVitals as $bucketKey => $vitals)
                     <div class="mb-8">
                        <div class="flex items-center mb-4">
                            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                {{ __($bucketKey) }}
                            </span>
                            <div class="ml-4 h-px bg-gray-200 dark:bg-gray-700 flex-1"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach ($vitals as $v)
                                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm p-4">
                                    <div class="flex justify-between items-center mb-3">
                                        <div class="flex items-center text-sm font-medium text-gray-900 dark:text-white">
                                            <x-heroicon-m-clock class="w-4 h-4 text-gray-400 mr-1.5" />
                                            {{ optional($v->recorded_at)->format('h:i A') }}
                                        </div>
                                        @if ($v->flag_abnormal)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200">ABNORMAL</span>
                                        @endif
                                    </div>

                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">Temp</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $v->temperature_celsius ?? '--' }} °C</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">BP</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $v->blood_pressure_systolic }}/{{ $v->blood_pressure_diastolic }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">Heart Rate</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $v->heart_rate_bpm ?? '--' }} bpm</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">SpO2</span>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ $v->spo2_percentage ?? '--' }}%</span>
                                        </div>
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 text-xs text-gray-500">
                                        Nurse: {{ optional($v->nurse)->name ?? 'N/A' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-10 text-center border border-gray-200 dark:border-gray-700">
                        <x-heroicon-o-heart class="w-10 h-10 text-gray-400 mx-auto mb-3" />
                        <h3 class="text-gray-900 dark:text-white font-medium">{{ __('doctor.no_vitals_found_header') }}</h3>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- File Preview & Modals (Standardized) --}}
    {{-- ... Kept functional logic but removed blur effects ... --}}
    <div x-show="labModal" x-cloak class="fixed inset-0 bg-gray-900/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl w-full max-w-3xl max-h-[90vh] overflow-hidden" @click.outside="labModal = false">
            <div class="h-full overflow-y-auto">
                <livewire:tenants.doctor.components.lab-request-modal />
            </div>
        </div>
    </div>
     <div x-show="prescriptionModal" x-cloak class="fixed inset-0 bg-gray-900/50 flex items-center justify-center p-4 z-50">
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-hidden" @click.outside="prescriptionModal = false">
            <div class="h-full overflow-y-auto">
                <livewire:tenants.doctor.components.prescription-modal />
            </div>
        </div>
    </div>
</div>
