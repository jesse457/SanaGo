<main id="doctor-medical-record" class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50 dark:bg-gray-900">
    {{-- Scrollable Content Area --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar relative">
        <div class="p-4 md:p-8 pb-32 max-w-7xl mx-auto">
            {{-- Header & Nav --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-2">
                    <button @click="open = true"
                        class="lg:hidden p-2 -ml-2 rounded-md text-gray-600 hover:bg-gray-100 dark:text-gray-300 transition-colors">
                        <x-heroicon-o-bars-3 class="w-6 h-6" />
                    </button>

                    <nav class="hidden md:flex text-sm font-medium text-gray-500 dark:text-gray-400">
                        <ol class="flex items-center space-x-2">
                            <li><a href="{{ route('doctor.dashboard') }}"
                                    class="hover:text-blue-600 transition">{{ __('doctor.home') }}</a></li>
                            <li><x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300" /></li>
                            <li class="text-gray-300 dark:text-white ">
                                {{ __('doctor.patient_consultation') }}</li>
                        </ol>
                    </nav>
                </div>

                <header
                    class="flex items-start justify-between border-b border-gray-200 dark:border-gray-700 pb-4 mb-6">
                    {{-- Left Block: Title --}}
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-600 rounded-xl shadow-lg shadow-blue-600/40 ring-1 ring-white/20">
                            <x-heroicon-o-clipboard-document-list class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                                {{ __('doctor.clinical_consultation') }}
                            </h1>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ __('doctor.consultation_header_subtitle') }}
                            </p>
                        </div>
                    </div>

                    {{-- Right Block: Date --}}
                    <div class="hidden md:block text-right pt-1">
                        <span
                            class="block text-xs text-gray-400 uppercase tracking-wider font-semibold">{{ __('doctor.today') }}</span>
                        <span
                            class="text-lg font-mono font-medium text-gray-700 dark:text-gray-300">{{ now()->format('F d, Y') }}</span>
                    </div>
                </header>
            </div>

            {{-- Patient Selector (Command Palette Style) --}}
            <div class="mb-10 relative z-40 group" x-data="{ open: false }" @click.outside="open = false">
                <div
                    class="relative shadow-sm transition-all duration-300 group-focus-within:shadow-lg group-focus-within:-translate-y-1">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <x-heroicon-o-magnifying-glass
                            class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                    </div>
                    <input type="search" wire:model.live.debounce.350ms="patientQuery" @focus="open = true"
                        class="block w-full rounded-xl border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 pl-11 pr-12 py-4 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg transition-all"
                        placeholder="{{ __('doctor.search_patient_placeholder') }}..." />

                    <div wire:loading wire:target="patientQuery"
                        class="absolute inset-y-0 right-0 pr-4 flex items-center">
                        <x-heroicon-o-arrow-path class="h-5 w-5 text-blue-500 animate-spin" />
                    </div>
                </div>

                {{-- Search Dropdown --}}
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0" x-cloak
                    class="absolute w-full mt-2 bg-white/95 dark:bg-gray-800/95 backdrop-blur-md rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50 max-h-[28rem] overflow-y-auto custom-scrollbar">

                    @if (strlen($patientQuery) > 1)
                        <ul>
                            <li
                                class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-700/50">
                                {{ __('doctor.search_results') }}
                            </li>
                            @forelse($patientResults as $p)
                                <li wire:click="selectPatient({{ $p->id }})" @click="open = false"
                                    class="cursor-pointer p-4 hover:bg-blue-50 dark:hover:bg-blue-900/30 border-b border-gray-50 dark:border-gray-700 last:border-0 transition-colors flex items-center justify-between group">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-300 flex items-center justify-center font-bold text-sm ring-2 ring-white dark:ring-gray-700">
                                            {{ substr($p->first_name, 0, 1) }}{{ substr($p->last_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p
                                                class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 transition-colors">
                                                {{ $p->last_name }}, {{ $p->first_name }}
                                            </p>
                                            <p
                                                class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2 mt-0.5">
                                                <span
                                                    class="font-mono bg-gray-100 dark:bg-gray-700 px-1.5 rounded text-gray-600 dark:text-gray-300">{{ $p->patient_uid }}</span>
                                                <span>&bull;</span>
                                                <span>{{ $p->age }} {{ __('doctor.yrs') }}</span>
                                                <span>&bull;</span>
                                                <span>{{ $p->gender }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    <x-heroicon-m-chevron-right
                                        class="w-5 h-5 text-gray-300 group-hover:text-blue-500 transition-transform group-hover:translate-x-1" />
                                </li>
                            @empty
                                <li class="p-8 text-center text-gray-500">
                                    <p>{{ __('doctor.no_patients_found') }}</p>
                                </li>
                            @endforelse
                        </ul>
                    @endif
                </div>
            </div>

            {{-- Active Consultation Form --}}
            @if ($selectedPatientId && $patient)
                <form wire:submit.prevent="saveAll" class="space-y-6 animate-fade-in-up">

                    {{-- Patient Context Card --}}
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 relative overflow-hidden group">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-gradient-to-b from-blue-500 to-indigo-600">
                        </div>

                        <div class="flex flex-col md:flex-row gap-6 items-start md:items-center relative z-10">
                            <div
                                class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 text-2xl font-bold shadow-inner border-4 border-white dark:border-gray-800">
                                {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-1">
                                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white truncate">
                                        {{ $patient->full_name }}</h2>
                                    @if ($hasUnsavedChanges)
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-700/50 animate-pulse">
                                            <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5"></span>
                                            {{ __('doctor.unsaved_changes') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center bg-gray-50 dark:bg-gray-700/50 px-2 py-0.5 rounded">
                                        <x-heroicon-m-identification class="w-4 h-4 mr-1.5 text-gray-400" />
                                        <span
                                            class="font-mono text-gray-700 dark:text-gray-300">{{ $patient->patient_uid }}</span>
                                    </span>
                                    <span class="flex items-center">
                                        <x-heroicon-m-cake class="w-4 h-4 mr-1.5 text-gray-400" /> {{ $patient->age }}
                                        {{ __('doctor.yrs') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Main Content Grid --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        {{-- LEFT COLUMN: Clinical Inputs --}}
                        <div class="lg:col-span-2 space-y-6">
                            {{-- 1. Assessment Section --}}
                            <div
                                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                <div
                                    class="flex items-center gap-2 mb-6 pb-4 border-b border-gray-100 dark:border-gray-700">
                                    <div class="p-1.5 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                                        <x-heroicon-o-document-text
                                            class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ __('doctor.assessment') }}</h3>
                                </div>

                                <div class="space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ __('doctor.chief_complaint') }} <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" wire:model.defer="complaint"
                                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5"
                                                placeholder="e.g. Severe headache...">
                                            @error('complaint')
                                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ __('doctor.working_diagnosis') }}
                                            </label>
                                            <input type="text" wire:model.defer="diagnosisText"
                                                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5"
                                                placeholder="e.g. Migraine with aura">
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            {{ __('doctor.clinical_notes_and_plan') }}
                                        </label>
                                        <textarea wire:model.defer="clinicalNotes" rows="6"
                                            class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm leading-relaxed resize-y"
                                            placeholder="Record detailed observations and plan here..."></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- 2. Prescription Section --}}
                            <div
                                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                                <div
                                    class="flex items-center gap-2 mb-6 pb-4 border-b border-gray-100 dark:border-gray-700">
                                    <div class="p-1.5 bg-green-50 dark:bg-green-900/30 rounded-lg">
                                        <x-heroicon-o-beaker class="w-5 h-5 text-green-600 dark:text-green-400" />
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ __('doctor.prescriptions') }}</h3>
                                </div>

                                <div class="space-y-6">
                                    {{-- Add Medication Dropdown --}}
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            {{ __('doctor.select_medication') }}
                                        </label>
                                        <div class="flex gap-2">
                                            <div class="relative flex-1">
                                                <select wire:model="selectedMedicationId"
                                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm py-2.5">
                                                    <option value="">{{ __('doctor.choose_medication') }}...
                                                    </option>
                                                    @foreach ($medicationOptions as $med)
                                                        <option value="{{ $med->id }}">
                                                            {{ $med->name }}
                                                            {{ $med->dosage_form ? '(' . $med->dosage_form . ')' : '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <button type="button" wire:click="addMedication"
                                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors disabled:opacity-50">
                                                <x-heroicon-m-plus class="w-5 h-5" />
                                                <span class="hidden sm:inline ml-1">{{ __('doctor.add') }}</span>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Medication List Table --}}
                                    @if (count($prescriptionItems) > 0)
                                        <div
                                            class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                                    <tr>
                                                        <th
                                                            class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                                                            Drug</th>
                                                        <th
                                                            class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                                                            Dosage</th>
                                                        <th
                                                            class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                                                            Freq</th>
                                                        <th
                                                            class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">
                                                            Duration</th>
                                                        <th class="px-3 py-2"></th>
                                                    </tr>
                                                </thead>
                                                <tbody
                                                    class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                                    @foreach ($prescriptionItems as $index => $item)
                                                        <tr>
                                                            <td
                                                                class="px-3 py-2 text-sm font-medium text-gray-900 dark:text-white min-w-[120px]">
                                                                {{ $item['name'] }}
                                                            </td>
                                                            <td class="px-3 py-2">
                                                                <input type="text"
                                                                    wire:model.defer="prescriptionItems.{{ $index }}.dosage"
                                                                    class="w-full border-0 bg-transparent p-0 text-sm focus:ring-0 placeholder-gray-400 focus:text-green-600"
                                                                    placeholder="500mg">
                                                            </td>
                                                            <td class="px-3 py-2">
                                                                <input type="text"
                                                                    wire:model.defer="prescriptionItems.{{ $index }}.frequency"
                                                                    class="w-full border-0 bg-transparent p-0 text-sm focus:ring-0 placeholder-gray-400 focus:text-green-600"
                                                                    placeholder="1-0-1">
                                                            </td>
                                                            <td class="px-3 py-2">
                                                                <input type="text"
                                                                    wire:model.defer="prescriptionItems.{{ $index }}.duration"
                                                                    class="w-full border-0 bg-transparent p-0 text-sm focus:ring-0 placeholder-gray-400 focus:text-green-600"
                                                                    placeholder="5 days">
                                                            </td>
                                                            <td class="px-3 py-2 text-right">
                                                                <button type="button"
                                                                    wire:click="removeMedication({{ $index }})"
                                                                    class="text-gray-400 hover:text-red-500 transition-colors">
                                                                    <x-heroicon-o-trash class="w-4 h-4" />
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div
                                            class="text-center py-6 bg-gray-50 dark:bg-gray-900/50 rounded-lg border-2 border-dashed border-gray-200 dark:border-gray-700">
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ __('doctor.no_medications_added') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                           
                            {{-- 3. Lab Request Section --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
    <div class="flex items-center gap-2 mb-6 pb-4 border-b border-gray-100 dark:border-gray-700">
        <div class="p-1.5 bg-purple-50 dark:bg-purple-900/30 rounded-lg">
            <x-heroicon-o-beaker class="w-5 h-5 text-purple-600 dark:text-purple-400" />
        </div>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
            {{ __('doctor.lab_requests') }}</h3>
    </div>

    <div class="space-y-6">
        {{-- Add Lab Dropdown --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ __('doctor.select_lab_test') }}
            </label>
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <select wire:model="selectedLabTestId"
                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm py-2.5">
                        <option value="">{{ __('doctor.choose_lab_test') }}...
                        </option>
                        @foreach ($labTestOptions as $lab)
                            <option value="{{ $lab->id }}">{{ $lab->test_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="button" wire:click="addLabTest"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors disabled:opacity-50">
                    <x-heroicon-m-plus class="w-5 h-5" />
                    <span class="hidden sm:inline ml-1">{{ __('doctor.add') }}</span>
                </button>
            </div>
        </div>

        {{-- Lab Items List --}}
        @if (count($labItems) > 0)
            <div class="space-y-3">
                @foreach ($labItems as $index => $item)
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex-1 flex items-center gap-3">
                            <span class="bg-purple-100 dark:bg-purple-900 text-purple-700 dark:text-purple-300 text-xs font-bold px-2 py-1 rounded">
                                {{ $index + 1 }}
                            </span>
                            <span class="font-medium text-gray-900 dark:text-white text-sm">{{ $item['test_name'] }}</span>
                        </div>

                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2 w-full sm:w-auto">
                            <select wire:model.defer="labItems.{{ $index }}.urgency"
                                class="block w-full sm:w-28 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-xs py-1.5 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <option value="normal">Normal</option>
                                <option value="urgent">Urgent</option>
                                <option value="critical">Critical</option>
                            </select>

                            <select wire:model.defer="labItems.{{ $index }}.lab_tech_id"
                                class="block w-full sm:w-36 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-xs py-1.5 shadow-sm focus:border-purple-500 focus:ring-purple-500">
                                <option value="">{{ __('doctor.select_technician') }}</option>
                                @foreach ($labTechnicianOptions as $tech)
                                    <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                @endforeach
                            </select>

                            <input type="text" wire:model.defer="labItems.{{ $index }}.reason"
                                placeholder="Reason..."
                                class="block w-full sm:w-40 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-xs py-1.5 shadow-sm focus:border-purple-500 focus:ring-purple-500">

                            <button type="button" wire:click="removeLabTest({{ $index }})"
                                class="self-end sm:self-auto p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded transition-colors">
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-6 bg-gray-50 dark:bg-gray-900/50 rounded-lg border-2 border-dashed border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ __('doctor.no_labs_added') }}</p>
            </div>
        @endif
    </div>
</div>
                        </div>

                        {{-- RIGHT COLUMN: Attachments --}}
                        <div class="lg:col-span-1 space-y-6">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6"
                                x-data="{ progress: @entangle('uploadProgress') }">

                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2">
                                        <x-heroicon-o-paper-clip class="w-5 h-5 text-gray-400" />
                                        <h3 class="font-bold text-gray-900 dark:text-white">
                                            {{ __('doctor.attachments') }}</h3>
                                    </div>
                                    <span class="text-xs text-gray-400">{{ count($attachmentUrls ?? []) }}
                                        {{ __('doctor.files') }}</span>
                                </div>

                                {{-- Upload Zone --}}
                                <label
                                    class="relative flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 dark:bg-gray-700/30 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-400 transition-all group overflow-hidden">
                                    <div
                                        class="flex flex-col items-center justify-center pt-5 pb-6 relative z-10">
                                        <div
                                            class="p-2 bg-white dark:bg-gray-800 rounded-full shadow-sm mb-2 group-hover:scale-110 transition-transform">
                                            <x-heroicon-o-cloud-arrow-up class="w-6 h-6 text-blue-500" />
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center px-4">
                                            <span
                                                class="font-semibold text-blue-600">{{ __('doctor.click_to_upload') }}</span>
                                            <br><span class="text-[10px]">JPG, PNG, PDF (Max 10MB)</span>
                                        </p>
                                    </div>
                                    <input type="file" wire:model="attachments" class="hidden" />

                                    {{-- Progress Overlay --}}
                                    <div x-show="progress > 0" x-cloak
                                        class="absolute inset-0 bg-white/90 dark:bg-gray-800/90 z-20 flex flex-col items-center justify-center">
                                        <span class="text-sm font-bold text-blue-600"
                                            x-text="progress + '%'"></span>
                                        <div class="w-2/3 bg-gray-200 rounded-full h-1.5 mt-2">
                                            <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-300"
                                                :style="'width: ' + progress + '%'"></div>
                                        </div>
                                    </div>
                                </label>

                                {{-- Files List --}}
                                @if (!empty($attachmentUrls) || $attachments)
                                    <div class="mt-4 space-y-2 max-h-64 overflow-y-auto custom-scrollbar pr-1">
                                        @foreach ($attachmentUrls as $i => $url)
                                            <div
                                                class="flex items-center gap-3 p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-100 dark:border-gray-600 group hover:border-blue-200 transition-colors">
                                                <a href="{{ $url }}" target="_blank"
                                                    class="flex-shrink-0">
                                                    <div
                                                        class="w-10 h-10 rounded bg-white dark:bg-gray-600 flex items-center justify-center text-gray-400">
                                                        <x-heroicon-o-document class="w-6 h-6" />
                                                    </div>
                                                </a>
                                                <div class="flex-1 min-w-0">
                                                    <p
                                                        class="text-xs font-medium truncate text-gray-700 dark:text-gray-200">
                                                        File {{ $i + 1 }}</p>
                                                    <p class="text-[10px] text-gray-400 uppercase">Stored</p>
                                                </div>
                                                <button type="button"
                                                    wire:click="removeStoredAttachment({{ $i }})"
                                                    class="text-gray-400 hover:text-red-500 p-1.5 hover:bg-red-50 rounded-md transition-colors">
                                                    <x-heroicon-o-trash class="w-4 h-4" />
                                                </button>
                                            </div>
                                        @endforeach

                                        @if ($attachments)
                                            <div
                                                class="flex items-center gap-3 p-2 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-100 dark:border-blue-800 animate-pulse-slow">
                                                <div
                                                    class="w-10 h-10 flex items-center justify-center bg-blue-100 dark:bg-blue-800 rounded text-blue-600">
                                                    <x-heroicon-o-document class="w-5 h-5" />
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p
                                                        class="text-xs font-medium truncate text-blue-700 dark:text-blue-300">
                                                        {{ $attachments->getClientOriginalName() }}</p>
                                                    <p class="text-[10px] text-blue-400 uppercase">Ready to
                                                        upload
                                                    </p>
                                                </div>
                                                <button type="button" wire:click="removeAttachment"
                                                    class="text-blue-400 hover:text-red-500 p-1.5 hover:bg-red-50 rounded-md transition-colors">
                                                    <x-heroicon-o-x-mark class="w-4 h-4" />
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            @else
                {{-- Empty State (No Patient Selected) --}}
                <div class="flex flex-col items-center justify-center py-24 text-center opacity-60 animate-fade-in">
                    <div
                        class="w-32 h-32 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-6">
                        <x-heroicon-o-user-group class="w-16 h-16 text-gray-300 dark:text-gray-600" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ __('doctor.ready_for_consultation') }}</h3>
                    <p class="text-gray-500 max-w-sm mx-auto">{{ __('doctor.use_search_above_instruction') }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Sticky Action Footer --}}
    @if ($selectedPatientId && $patient)
        <div
            class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 md:px-8 shadow-[0_-4px_20px_-5px_rgba(0,0,0,0.1)] z-50 flex flex-col md:flex-row justify-between items-center gap-4 animate-slide-up">
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <x-heroicon-m-hashtag class="w-4 h-4" />
                <span>{{ __('doctor.consultation_id') }}:</span>
                <span class="font-mono font-medium text-gray-700 dark:text-gray-300">
                    {{ Str::substr($patient->patient_uid, 0, 6) }}-{{ now()->format('Hi') }}
                </span>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <button type="button" wire:click="saveDraft" wire:loading.attr="disabled"
                    class="flex-1 md:flex-none justify-center items-center px-5 py-2.5 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-xl text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50">
                    {{ __('doctor.save_draft') }}
                </button>

                <button type="button" wire:click="saveAndSign" wire:loading.attr="disabled"
                    class="flex-1 md:flex-none justify-center items-center px-8 py-2.5 border border-transparent text-sm font-bold rounded-xl shadow-md shadow-blue-600/20 text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all hover:-translate-y-0.5 disabled:opacity-50 disabled:translate-y-0">
                    <span wire:loading.remove wire:target="saveAndSign" class="flex items-center gap-2">
                        <x-heroicon-m-check-circle class="w-5 h-5" />
                        {{ __('doctor.finalize_record') }}
                    </span>
                    <span wire:loading wire:target="saveAndSign" class="flex items-center gap-2">
                        <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin" />
                        {{ __('doctor.processing') }}...
                    </span>
                </button>
            </div>
        </div>
    @endif
</main>
