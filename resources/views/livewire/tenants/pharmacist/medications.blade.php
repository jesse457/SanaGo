<main class="flex-1 p-4 md:p-6 lg:ml-64 bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">

    {{-- Breadcrumbs --}}
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('pharmacist.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300 transition-colors duration-150">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" />{{ __('pharmacist.dashboard.breadcrumb_home') }}
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <x-heroicon-o-chevron-right class="rtl:rotate-180 w-4 h-4 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm font-semibold text-gray-800 md:ms-2 dark:text-gray-200">{{ __('pharmacist.dispense_medications_page.breadcrumb_dispense_medications') }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <header class="mb-8 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ __('pharmacist.dispense_medications_page.dispense_medications_title') }}</h1>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                {{ __('pharmacist.dispense_medications_page.dispense_medications_description') }}
            </p>
        </div>

        <div class="w-full sm:w-1/3">
            <label for="patient-search" class="sr-only">{{ __('pharmacist.dispense_medications_page.search_patients') }}</label>
            <div class="relative">
                <input id="patient-search" type="text" wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('pharmacist.dispense_medications_page.search_patients_placeholder') }}"
                    class="w-full pr-12 form-input px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <x-heroicon-s-magnifying-glass class="w-5 h-5 text-gray-400" />
                </div>
            </div>
        </div>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Patients list --}}
        <aside
            class="lg:col-span-1 bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800 dark:text-white">{{ __('pharmacist.dispense_medications_page.patients') }}</h2>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $patients->count() ?? 0 }}</span>
            </div>

            <div class="max-h-[60vh] overflow-y-auto -mx-2">
                @forelse ($patients as $patient)
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                        <li wire:click="selectPatient({{ $patient->id }})"
                            class="flex items-center justify-between px-2 py-3 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150 @if ($selectedPatientId == $patient->id) bg-blue-50 dark:bg-blue-900 @endif">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    {{ strtoupper(substr($patient->first_name ?? 'P', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $patient->first_name }} {{ $patient->last_name }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $patient->email ?? 'N/A' }}
                                    </div>
                                </div>
                            </div>

                            <div class="text-right text-xs text-gray-500 dark:text-gray-400">
                                <div class="font-medium">{{ $patient->patient_uid ?? '—' }}</div>
                                <div class="mt-1">
                                    <button wire:click.stop="selectPatient({{ $patient->id }})"
                                        class="text-blue-600 hover:text-blue-900 dark:text-blue-400">
                                        <x-heroicon-o-arrow-right class="w-5 h-5" />
                                    </button>
                                </div>
                            </div>
                        </li>
                    </ul>
                @empty
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        <x-heroicon-o-user-group class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" />
                        <p class="mt-3 font-semibold text-sm">{{ __('pharmacist.dispense_medications_page.no_patients_found') }}</p>
                        <p class="mt-1 text-xs">{{ __('pharmacist.dispense_medications_page.try_different_search') }}</p>
                    </div>
                @endforelse
            </div>
        </aside>

        {{-- Prescriptions / Details --}}
        <section
            class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 border border-gray-100 dark:border-gray-700">
            @if ($selectedPatient)
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <!-- Header Section -->
                    <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ __('pharmacist.dispense_medications_page.prescriptions_for') }}
                                    <span class="text-blue-600 dark:text-blue-400">{{ $selectedPatient->first_name }}
                                        {{ $selectedPatient->last_name }}</span>
                                </h2>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('pharmacist.dispense_medications_page.select_prescription_view_items') }}</p>
                            </div>

                            <!-- Patient Info Badge -->
                            <div class="flex items-center space-x-2">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                    ID: {{ $selectedPatient->patient_uid ?? 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Table Section -->
                    <div class="overflow-hidden">

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                @if (!$patientPrescriptions->isEmpty())
                                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('pharmacist.dispense_medications_page.date') }}
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('pharmacist.dispense_medications_page.prescribed_by') }}
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('pharmacist.dispense_medications_page.status') }}
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('pharmacist.dispense_medications_page.action') }}
                                            </th>
                                        </tr>
                                    </thead>
                                @endif
                                @forelse ($patientPrescriptions as $prescription)
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div
                                                        class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                                                        <svg class="h-4 w-4 text-blue-600 dark:text-blue-400"
                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ optional($prescription->prescription_date)->format('d M Y') ?? '—' }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ optional($prescription->prescription_date)->format('h:i A') ?? '—' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-8 w-8">
                                                        <img class="h-8 w-8 rounded-full"
                                                            src="{{ $prescription->doctor->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($prescription->doctor->name ?? 'Doctor') . '&color=7F9CF5&background=EBF4FF' }}"
                                                            alt="{{ $prescription->doctor->name ?? 'Doctor' }}">
                                                    </div>
                                                    <div class="ml-3">
                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $prescription->doctor->name ?? 'N/A' }}
                                                        </div>
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                                            {{ $prescription->doctor->specialization ?? 'Doctor' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $status = strtolower($prescription->status ?? 'new');
                                                @endphp
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset
                                        @if ($status === 'new') bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-400/10 dark:text-blue-400 dark:ring-blue-400/30
                                        @elseif ($status === 'dispensed') bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-400/10 dark:text-green-400 dark:ring-green-400/30
                                        @elseif ($status === 'partial') bg-yellow-50 text-yellow-800 ring-yellow-600/20 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/30
                                        @else bg-gray-50 text-gray-700 ring-gray-600/20 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/30 @endif">
                                                    {{ ucfirst($status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button wire:click="viewPrescriptionItems({{ $prescription->id }})"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                                    <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    {{ __('pharmacist.dispense_medications_page.view_items') }}
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                @empty
                                    <div class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('pharmacist.dispense_medications_page.no_prescriptions_found') }}</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('pharmacist.dispense_medications_page.no_prescriptions_recorded') }}</p>
                                    </div>
                                @endforelse
                            </table>
                        </div>

                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">{{ __('pharmacist.dispense_medications_page.select_a_patient') }}</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('pharmacist.dispense_medications_page.choose_patient_to_view') }}</p>
                    </div>
                </div>
            @endif
        </section>
    </div>

    {{-- Dispense Modal (Alpine + Livewire entangled) --}}
    <div x-data="{
        show: @entangle('showDispenseModal').live,
        close() {
            this.show = false;
            $dispatch('close-modal')
        }
    }" x-show="show" x-cloak x-on:keydown.escape.window="close()"
        class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:p-6" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">
        {{-- Backdrop --}}
        <div x-show="show" x-transition.opacity class="fixed inset-0 bg-black/40 backdrop-blur-sm"
            aria-hidden="true">
        </div>

        <div x-show="show" x-transition x-on:keydown.escape.window="close()"
            class="relative w-full max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-start justify-between">
                    <div>
                        <h3 id="modal-title" class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ __('pharmacist.dispense_medications_page.dispense_items_prescription', ['id' => $selectedPrescription->id ?? 'N/A']) }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ __('pharmacist.dispense_medications_page.patient') }}:
                            <span class="font-medium text-gray-700 dark:text-gray-200">
                                {{ $selectedPatient->first_name ?? '' }} {{ $selectedPatient->last_name ?? '' }}
                            </span>
                            <span class="mx-2">|</span>
                            {{ __('pharmacist.dispense_medications_page.date') }}:
                            <span class="font-medium text-gray-700 dark:text-gray-200">
                                {{ optional($selectedPrescription?->prescription_date)->format('d M Y') ?? '—' }}
                            </span>
                        </p>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button @click="close()" class="p-2 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                            <span class="sr-only">{{ __('pharmacist.manage_drugs_page.cancel') }}</span>
                            X
                        </button>
                    </div>
                </div>

                <div class="px-6 py-5">

                    <form wire:submit.prevent="updateDispensation" class="space-y-4">
                        <div class="overflow-x-auto">
                            <div class=" shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-600">
                                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('pharmacist.dispense_medications_page.medication') }}
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('pharmacist.dispense_medications_page.prescribed') }}
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('pharmacist.dispense_medications_page.dispensed') }}
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('pharmacist.dispense_medications_page.remaining') }}
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('pharmacist.dispense_medications_page.dispense_now') }}
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('pharmacist.dispense_medications_page.stock') }}
                                            </th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                {{ __('pharmacist.dispense_medications_page.notes') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse ($prescriptionItemsToDispense as $item)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $item->medication->name ?? 'N/A' }}
                                                    </div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        {{ $item->dosage }} · {{ $item->frequency }} ·
                                                        {{ $item->duration }}
                                                    </div>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="text-sm text-gray-900 dark:text-gray-100 font-medium">
                                                        {{ $item->quantity_prescribed ?? 0 }}
                                                    </span>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                                        {{ $item->dispensed_quantity ?? 0 }}
                                                    </span>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if (($availableToDispense[$item->id] ?? 0) > 0) bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                                                        {{ $availableToDispense[$item->id] ?? 0 }}
                                                    </span>
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @if (($availableToDispense[$item->id] ?? 0) > 0)
                                                        <div class="flex items-center">
                                                            <div
                                                                class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 w-full max-w-[120px]">
                                                                <input type="number"
                                                                    wire:model.live="dispensedQuantities.{{ $item->id }}"
                                                                    min="0"
                                                                    max="{{ $availableToDispense[$item->id] ?? 0 }}"
                                                                    class="block flex-1 border-0 bg-transparent py-1.5 pl-3 text-gray-900 dark:text-white placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6"
                                                                    placeholder="0">
                                                            </div>
                                                        </div>
                                                        @error('dispensedQuantities.' . $item->id)
                                                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                                                                {{ $message }}</p>
                                                        @enderror
                                                    @else
                                                        <span
                                                            class="text-sm text-gray-400 dark:text-gray-500">N/A</span>
                                                    @endif
                                                </td>

                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @php $stock = optional($item->medication)->stock_quantity ?? null; @endphp
                                                    @if (is_null($stock))
                                                        <span class="text-sm text-gray-400 dark:text-gray-500">—</span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                                @if ($stock > 20) bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-400/10 dark:text-green-400 dark:ring-green-400/30
                                @elseif($stock > 5) bg-yellow-50 text-yellow-800 ring-yellow-600/20 dark:bg-yellow-400/10 dark:text-yellow-500 dark:ring-yellow-400/30
                                @else bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-400/10 dark:text-red-400 dark:ring-red-400/30 @endif">
                                                            {{ $stock }} {{ __('pharmacist.dispense_medications_page.stock') }}
                                                        </span>
                                                    @endif
                                                </td>

                                                <td class="px-6 py-4">
                                                    <div class="w-full">
                                                        <textarea wire:model.live="pharmacistNotes.{{ $item->id }}" rows="2"
                                                            class="block w-full rounded-md border-0 py-1.5 text-gray-900 dark:text-white shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm sm:leading-6 dark:bg-gray-700/50"
                                                            placeholder="{{ __('pharmacist.dispense_medications_page.notes') }}"></textarea>
                                                        @error('pharmacistNotes.' . $item->id)
                                                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">
                                                                {{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="px-6 py-12 text-center">
                                                    <div class="flex flex-col items-center">
                                                        <svg class="h-12 w-12 text-gray-400" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor"
                                                            aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        <h3
                                                            class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            {{ __('pharmacist.dispense_medications_page.no_items_available') }}</h3>
                                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('pharmacist.dispense_medications_page.no_items_available_description') }}</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                <strong>{{ __('pharmacist.dispense_medications_page.dispensation_tip') }}</strong>
                            </div>

                            <div class="flex items-center space-x-2">
                                <button type="button" @click="close()"
                                    class="inline-flex items-center px-4 py-2 rounded-md border border-gray-300 text-sm text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                    {{ __('pharmacist.manage_drugs_page.cancel') }}
                                </button>

                                <button type="submit" wire:loading.attr="disabled" wire:target="updateDispensation"
                                    class="inline-flex items-center px-4 py-2 rounded-md bg-blue-600 text-white text-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <svg wire:loading.remove wire:target="updateDispensation" class="w-4 h-4 mr-2"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>

                                    <svg wire:loading wire:target="updateDispensation"
                                        class="animate-spin w-4 h-4 mr-2" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4" fill="none"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                    </svg>

                                    {{ __('pharmacist.dispense_medications_page.save_dispensation') }}
                                </button>
                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>


</main>
