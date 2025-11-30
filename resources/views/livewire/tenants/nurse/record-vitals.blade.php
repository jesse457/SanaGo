<div class="min-h-screen bg-gray-50/50 dark:bg-gray-900" x-data="{ mobileMenuOpen: false }">

    {{--
      NOTE: This assumes the Sidebar from the previous fix is present
      and handles the 'lg:ml-64' spacing via layout.
    --}}

   

    {{-- ========================================== --}}
    {{-- SECTION 2: RECORD VITALS FORM              --}}
    {{-- ========================================== --}}
    <section id="record-vitals" class="p-4 md:p-8 lg:ml-64 pb-20">

        {{-- Breadcrumbs --}}
        <nav class="flex mb-6" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li class="inline-flex items-center">
                    <a href="{{ route('nurse.dashboard') }}" wire:navigate class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white transition-colors">
                        <x-heroicon-s-home class="w-4 h-4 mr-2" /> Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="w-4 h-4 text-gray-400 mx-1" />
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Record Vitals</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <div class="p-2 bg-indigo-600 rounded-lg shadow-lg shadow-indigo-600/20">
                        <x-heroicon-o-heart class="w-6 h-6 text-white" />
                    </div>
                    Vitals & Notes
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 ml-12">Record daily health metrics for inpatients.</p>
            </div>
        </div>

        {{-- Patient Selector Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8">
            <label for="patientSelect" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Select Patient</label>
            <div class="relative max-w-2xl">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" />
                </div>
                <select id="patientSelect"
                        wire:model.live="selectedPatientId"
                        onchange="Livewire.dispatch('updatedSelectedPatientId')"
                        class="block w-full pl-10 pr-10 py-3 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent sm:text-sm rounded-xl bg-gray-50 dark:bg-gray-900 dark:text-white transition-shadow shadow-sm">
                    <option value="">-- Search or Select Patient --</option>
                    @foreach ($patients ?? [] as $patient)
                        <option value="{{ $patient->id }}">
                            {{ $patient->first_name }} {{ $patient->last_name }} (ID: {{ $patient->id }})
                        </option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <x-heroicon-s-chevron-up-down class="h-5 w-5 text-gray-400" />
                </div>
            </div>
            @error('selectedPatientId') <p class="text-red-500 text-xs mt-2 font-medium flex items-center"><x-heroicon-s-exclamation-circle class="w-4 h-4 mr-1"/>{{ $message }}</p> @enderror
        </div>

        {{-- Vitals Entry Form --}}
        @if ($selectedPatientId && !($showNewPatientForm ?? false))
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">

                <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-blue-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 dark:text-white">
                        Recording for: <span class="text-indigo-600 dark:text-indigo-400 text-lg ml-1">{{ $selectedPatientName ?? 'Patient' }}</span>
                    </h3>
                    <div class="text-xs text-gray-500 bg-white dark:bg-gray-900 px-3 py-1 rounded-full border border-gray-200 dark:border-gray-600">
                        {{ now()->format('d M Y, H:i') }}
                    </div>
                </div>

                <div class="p-6 md:p-8">
                    {{-- Validation Errors --}}
                    @if ($errors->any())
                        <div class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800">
                            <div class="flex">
                                <x-heroicon-s-x-circle class="h-5 w-5 text-red-400" />
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">There were errors with your submission</h3>
                                    <ul class="mt-2 text-sm text-red-700 dark:text-red-300 list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-8">
                        @php
                            $vitalsFields = [
                                ['model' => 'bloodPressure', 'label' => 'Blood Pressure', 'icon' => 'heart', 'placeholder' => '120/80', 'unit' => 'mmHg', 'type' => 'text'],
                                ['model' => 'temperature', 'label' => 'Temperature', 'icon' => 'fire', 'placeholder' => '37.0', 'unit' => '°C', 'type' => 'number', 'step' => '0.1'],
                                ['model' => 'heartRate', 'label' => 'Heart Rate', 'icon' => 'bolt', 'placeholder' => '72', 'unit' => 'bpm', 'type' => 'number'],
                                ['model' => 'oxygenSaturation', 'label' => 'O2 Saturation', 'icon' => 'cloud', 'placeholder' => '98', 'unit' => '%', 'type' => 'number'],
                                ['model' => 'respiratoryRate', 'label' => 'Respiratory Rate', 'icon' => 'arrow-path', 'placeholder' => '16', 'unit' => 'bpm', 'type' => 'number', 'nullable' => true],
                                ['model' => 'weightKg', 'label' => 'Weight', 'icon' => 'scale', 'placeholder' => '70.5', 'unit' => 'kg', 'type' => 'number', 'step' => '0.1', 'nullable' => true],
                            ];
                        @endphp

                        @foreach($vitalsFields as $field)
                            <div class="group">
                                <label for="{{ $field['model'] }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 ml-1">
                                    {{ $field['label'] }}
                                </label>
                                <div class="relative rounded-xl shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-dynamic-component component="heroicon-o-{{ $field['icon'] }}" class="h-5 w-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors" />
                                    </div>
                                    <input type="{{ $field['type'] }}"
                                           id="{{ $field['model'] }}"
                                           wire:model.defer="{{ $field['model'] }}"
                                           placeholder="{{ $field['placeholder'] }}"
                                           {{ isset($field['step']) ? 'step=' . $field['step'] : '' }}
                                           class="block w-full pl-10 pr-12 py-3 border-gray-200 dark:border-gray-700 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-900 dark:text-white dark:placeholder-gray-500 transition-all sm:text-sm @error($field['model']) border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 @enderror">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-400 sm:text-sm">{{ $field['unit'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Abnormal Toggle --}}
                        <div class="sm:col-span-2 lg:col-span-3 flex items-center p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700/50">
                            <div class="flex-shrink-0">
                                <button type="button"
                                        x-data="{ on: @entangle('flagAbnormal') }"
                                        @click="on = !on"
                                        :class="on ? 'bg-red-500' : 'bg-gray-200 dark:bg-gray-600'"
                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:ring-offset-gray-800">
                                    <span class="sr-only">Flag as Abnormal</span>
                                    <span aria-hidden="true"
                                          :class="on ? 'translate-x-5' : 'translate-x-0'"
                                          class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                </button>
                            </div>
                            <div class="ml-4 cursor-pointer" @click="$wire.set('flagAbnormal', !{{ $flagAbnormal ?? 'false' }})">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">Flag Vitals as Abnormal</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Enable this if the readings are outside the patient's normal range.</p>
                            </div>
                        </div>

                        {{-- Notes Area --}}
                        <div class="sm:col-span-2 lg:col-span-3">
                            <label for="nurseNotes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 ml-1">
                                Clinical Notes / Observations
                            </label>
                            <div class="mt-1">
                                <textarea id="nurseNotes"
                                          wire:model.defer="nurseNotes"
                                          rows="4"
                                          class="shadow-sm block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-4"
                                          placeholder="Patient appears comfortable. Resting in bed..."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                        <button type="button" class="mr-3 px-6 py-3 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            Cancel
                        </button>

                        <button wire:click="saveVitals"
                                wire:loading.attr="disabled"
                                class="relative inline-flex items-center px-8 py-3 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-70 disabled:cursor-not-allowed transition-all">

                            <span wire:loading.remove wire:target="saveVitals" class="flex items-center">
                                <x-heroicon-o-check class="w-5 h-5 mr-2" />
                                Save Vitals Record
                            </span>

                            <span wire:loading wire:target="saveVitals" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-dashed border-gray-300 dark:border-gray-700 p-12 text-center">
                <div class="mx-auto h-24 w-24 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mb-4">
                    <x-heroicon-o-user-plus class="h-10 w-10 text-gray-400" />
                </div>
                <h3 class="mt-2 text-lg font-medium text-gray-900 dark:text-white">No Patient Selected</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-sm mx-auto">
                    Select a patient from the dropdown above or register a new patient to start recording vitals.
                </p>
            </div>
        @endif
    </section>
</div>
