<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4 md:flex md:items-center md:justify-between space-y-3 md:space-y-0">

                {{-- Title & Breadcrumbs --}}
                <div class="flex-1 min-w-0">
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-2 mt-2" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('pharmacist.dashboard') }}" wire:navigate
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    {{ __('Home') }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">{{ __('Dispense Medications') }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            {{ __('Dispense Medications') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Select a patient, review prescriptions, and dispense medications safely.') }}
                        </p>
                    </div>
                </div>

                {{-- Search --}}
                <div class="w-full md:w-1/3 relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-heroicon-s-magnifying-glass class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                    </div>
                    <input id="patient-search" type="text" wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('Search patients by name or ID...') }}"
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-700 dark:bg-gray-800 focus:ring-blue-500 focus:border-blue-500 transition duration-150 text-gray-900 dark:text-gray-200" />
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="relative min-h-[400px] p-4 sm:p-6 pb-20">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Patients list --}}
                <aside class="lg:col-span-1 bg-white dark:bg-gray-900 shadow-sm rounded-2xl p-6 border border-slate-200 dark:border-gray-800">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100 dark:border-gray-800">
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('Patients') }}</h2>
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400 px-2 py-0.5 bg-slate-100 dark:bg-gray-800 rounded-full">{{ $patients->count() ?? 0 }}</span>
                    </div>

                    <div class="max-h-[60vh] overflow-y-auto -mx-2 custom-scrollbar">
                        <ul class="divide-y divide-slate-100 dark:divide-gray-800">
                            @forelse ($patients as $patient)
                                <li wire:click="selectPatient({{ $patient->id }})"
                                    class="flex items-center justify-between px-2 py-3 rounded-lg cursor-pointer hover:bg-slate-50 dark:hover:bg-gray-700 transition-colors duration-150
                                    @if ($selectedPatientId == $patient->id) bg-blue-50 dark:bg-blue-900/30 font-bold @endif">
                                    <div class="flex items-center space-x-3">
                                        <div class="h-10 w-10 rounded-full bg-slate-200 dark:bg-gray-700 flex items-center justify-center text-sm font-bold text-slate-700 dark:text-slate-200">
                                            {{ strtoupper(substr($patient->first_name ?? 'P', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                {{ $patient->first_name }} {{ $patient->last_name }}
                                            </div>
                                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ $patient->email ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right text-xs text-slate-500 dark:text-slate-400">
                                        <div class="font-bold text-xs bg-slate-100 dark:bg-gray-800 px-1.5 py-0.5 rounded">{{ $patient->patient_uid ?? '—' }}</div>
                                    </div>
                                </li>
                            @empty
                                <div class="text-center py-12 text-slate-500 dark:text-slate-400">
                                    <x-heroicon-o-user-group class="mx-auto h-12 w-12 text-slate-400 dark:text-slate-600 mb-3" />
                                    <p class="mt-3 font-semibold text-sm">{{ __('No Patients Found') }}</p>
                                </div>
                            @endforelse
                        </ul>
                    </div>
                </aside>

                {{-- Prescriptions / Details --}}
                <section class="lg:col-span-2 bg-white dark:bg-gray-900 shadow-sm rounded-2xl p-6 border border-slate-200 dark:border-gray-800">
                    @if ($selectedPatient)
                        <div>
                            <!-- Header Section -->
                            <div class="px-0 py-0 border-b border-slate-100 dark:border-gray-800 mb-6">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h2 class="text-xl font-bold text-slate-900 dark:text-white">
                                            {{ __('Prescriptions for') }}
                                            <span class="text-blue-600 dark:text-blue-400">{{ $selectedPatient->first_name }}
                                                {{ $selectedPatient->last_name }}</span>
                                        </h2>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Select a prescription to view items and dispense.') }}</p>
                                    </div>

                                    <!-- Patient Info Badge -->
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-slate-100 dark:bg-gray-800 text-slate-700 dark:text-slate-200">
                                            {{ __('Age') }}: {{ $selectedPatient->age ?? __('N/A') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Table Section -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-100 dark:divide-gray-800">
                                    @if (!$patientPrescriptions->isEmpty())
                                        <thead class="bg-slate-50 dark:bg-gray-800/50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                                    {{ __('Date') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                                    {{ __('Prescribed By') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                                    {{ __('Status') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                                    {{ __('Action') }}
                                                </th>
                                            </tr>
                                        </thead>
                                    @endif
                                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-slate-100 dark:divide-gray-800">
                                        @forelse ($patientPrescriptions as $prescription)
                                            <tr class="hover:bg-slate-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-8 w-8 flex items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30">
                                                            <x-heroicon-s-calendar class="h-4 w-4 text-blue-600 dark:text-blue-400" />
                                                        </div>
                                                        <div class="ml-3">
                                                            <div class="text-sm font-bold text-slate-900 dark:text-white">
                                                                {{ optional($prescription->prescription_date)->format('d M Y') ?? '—' }}
                                                            </div>
                                                            <div class="text-xs text-slate-500 dark:text-slate-400">
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
                                                            <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                                {{ $prescription->doctor->name ?? 'N/A' }}
                                                            </div>
                                                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                                                {{ $prescription->doctor->specialization ?? 'Doctor' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @php
                                                        $status = strtolower($prescription->status ?? 'new');
                                                        $statusClasses = match ($status) {
                                                            'new' => 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:border-blue-800',
                                                            'dispensed' => 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-800',
                                                            'partial' => 'bg-amber-50 text-amber-800 border-amber-100 dark:bg-amber-900/30 dark:text-amber-500 dark:border-amber-800',
                                                            default => 'bg-slate-50 text-slate-700 border-slate-100 dark:bg-gray-800 dark:text-slate-400 dark:border-gray-700',
                                                        };
                                                    @endphp
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border shadow-sm {{ $statusClasses }}">
                                                        {{ ucfirst(__($status)) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    <button wire:click="viewPrescriptionItems({{ $prescription->id }})"
                                                        class="inline-flex items-center px-4 py-2 border border-blue-200 text-sm leading-4 font-bold rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors dark:border-blue-800">
                                                        <x-heroicon-s-eye class="w-5 h-5 mr-2" />
                                                        {{ __('View Items') }}
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <div class="px-6 py-12 text-center">
                                                <x-heroicon-o-folder-open class="mx-auto h-12 w-12 text-slate-400 mb-3" />
                                                <h3 class="mt-2 text-sm font-medium text-slate-900 dark:text-white">{{ __('No Prescriptions Found') }}</h3>
                                            </div>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="bg-white dark:bg-gray-900 shadow-sm rounded-lg overflow-hidden">
                            <div class="px-6 py-16 text-center">
                                <x-heroicon-o-user-circle class="mx-auto h-12 w-12 text-slate-400 mb-3" />
                                <h3 class="mt-2 text-sm font-medium text-slate-900 dark:text-white">{{ __('Select a Patient') }}</h3>
                                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Choose a patient from the list to see their prescriptions.') }}</p>
                            </div>
                        </div>
                    @endif
                </section>
            </div>
        </div>

        {{-- Dispense Modal (Alpine + Livewire entangled) --}}
        <div x-data="{
            show: @entangle('showDispenseModal').live,
            close() {
                this.show = false;
                $wire.set('showDispenseModal', false);
            }
        }" x-show="show" x-cloak x-on:keydown.escape.window="close()"
            class="fixed inset-0 z-50 flex items-end justify-center p-4 sm:p-6" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="show" x-transition.opacity class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm"
                aria-hidden="true">
            </div>

            <div x-show="show" x-transition x-on:keydown.escape.window="close()"
                class="relative w-full max-w-4xl mx-auto rounded-2xl">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-xl overflow-hidden border border-slate-100 dark:border-gray-800">
                    <div class="px-6 py-4 border-b border-slate-100 dark:border-gray-800 flex items-start justify-between bg-white dark:bg-gray-900">
                        <div>
                            <h3 id="modal-title" class="text-lg font-bold text-slate-900 dark:text-white">
                                {{ __('Dispense Items — Prescription #:id', ['id' => $selectedPrescription->id ?? 'N/A']) }}
                            </h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                {{ __('Patient') }}:
                                <span class="font-bold text-blue-600 dark:text-blue-400">
                                    {{ $selectedPatient->first_name ?? '' }} {{ $selectedPatient->last_name ?? '' }}
                                </span>
                            </p>
                        </div>

                        <button @click="close()" class="p-2 rounded-xl hover:bg-slate-100 dark:hover:bg-gray-800">
                            <x-heroicon-s-x-mark class="w-5 h-5 text-slate-400" />
                        </button>
                    </div>

                    <div class="p-6">
                        <form wire:submit.prevent="updateDispensation" class="space-y-4">
                            <div class="overflow-x-auto">
                                <div class="ring-1 ring-slate-200 dark:ring-gray-700 rounded-xl overflow-hidden">
                                    <table class="min-w-full divide-y divide-slate-200 dark:divide-gray-700">
                                        <thead class="bg-slate-50 dark:bg-gray-800/50">
                                            <tr>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                                    {{ __('Medication') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                                    {{ __('Prescribed') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                                    {{ __('Dispensed') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                                    {{ __('Remaining') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                                    {{ __('Dispense Now') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                                    {{ __('Stock') }}
                                                </th>
                                                <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                                    {{ __('Notes') }}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-slate-200 dark:divide-gray-700">
                                            @forelse ($prescriptionItemsToDispense as $item)
                                                <tr class="hover:bg-slate-50 dark:hover:bg-gray-700/30 transition-colors">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="text-sm font-medium text-slate-900 dark:text-white">
                                                            {{ $item->medication->name ?? 'N/A' }}
                                                        </div>
                                                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                                            {{ $item->dosage }} · {{ $item->frequency }} ·
                                                            {{ $item->duration }}
                                                        </div>
                                                    </td>

                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="text-sm text-slate-900 dark:text-white font-bold">
                                                            {{ $item->quantity_prescribed ?? 0 }}
                                                        </span>
                                                    </td>

                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="text-sm text-slate-700 dark:text-slate-300 font-medium">
                                                            {{ $item->dispensed_quantity ?? 0 }}
                                                        </span>
                                                    </td>

                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold
                                                            @if (($availableToDispense[$item->id] ?? 0) > 0) bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300
                                                            @else bg-slate-100 text-slate-800 dark:bg-gray-700 dark:text-slate-300 @endif">
                                                            {{ $availableToDispense[$item->id] ?? 0 }}
                                                        </span>
                                                    </td>

                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @if (($availableToDispense[$item->id] ?? 0) > 0)
                                                            <div class="flex items-center">
                                                                <input type="number"
                                                                    wire:model.live="dispensedQuantities.{{ $item->id }}"
                                                                    min="0"
                                                                    max="{{ $availableToDispense[$item->id] ?? 0 }}"
                                                                    class="block w-full rounded-lg border-slate-300 dark:border-gray-600 dark:bg-gray-800 py-1.5 pl-3 text-slate-900 dark:text-white focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                                    placeholder="0">
                                                            </div>
                                                            @error('dispensedQuantities.' . $item->id)
                                                                <p class="mt-1 text-xs text-red-600 font-medium dark:text-red-400">
                                                                    {{ $message }}</p>
                                                            @enderror
                                                        @else
                                                            <span class="text-sm text-slate-400 dark:text-slate-500">N/A</span>
                                                        @endif
                                                    </td>

                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @php $stock = optional($item->medication)->stock_quantity ?? null; @endphp
                                                        @if (is_null($stock))
                                                            <span class="text-sm text-slate-400 dark:text-slate-500">—</span>
                                                        @else
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold ring-1 ring-inset
                                    @if ($stock > 20) bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-900/20 dark:text-emerald-400
                                    @elseif($stock > 5) bg-amber-50 text-amber-800 ring-amber-600/20 dark:bg-amber-900/20 dark:text-amber-500
                                    @else bg-red-50 text-red-700 ring-red-600/20 dark:bg-red-900/20 dark:text-red-400 @endif">
                                                                {{ $stock }}
                                                            </span>
                                                        @endif
                                                    </td>

                                                    <td class="px-6 py-4">
                                                        <textarea wire:model.live="pharmacistNotes.{{ $item->id }}" rows="2"
                                                            class="block w-full rounded-md border-slate-300 dark:border-gray-600 dark:bg-gray-800 py-1.5 text-slate-900 dark:text-white shadow-sm placeholder:text-slate-400 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                                            placeholder="{{ __('Notes') }}"></textarea>
                                                        @error('pharmacistNotes.' . $item->id)
                                                            <p class="mt-1 text-xs text-red-600 font-medium dark:text-red-400">
                                                                {{ $message }}</p>
                                                        @enderror
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="px-6 py-12 text-center">
                                                        <x-heroicon-o-folder-open class="h-12 w-12 text-slate-400 mx-auto mb-3" />
                                                        <h3 class="mt-2 text-sm font-bold text-slate-900 dark:text-white">
                                                            {{ __('No Items Available') }}</h3>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 border-t border-slate-100 dark:border-gray-800">
                                <div class="text-sm text-slate-600 dark:text-slate-400 font-medium">
                                    <span class="font-bold">{{ __('Enter the quantity to dispense now for each medication.') }}</span>
                                </div>

                                <div class="flex items-center space-x-3">
                                    <button type="button" @click="close()"
                                        class="inline-flex items-center px-5 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 text-sm font-bold text-slate-700 bg-white hover:bg-slate-50 dark:bg-gray-800 dark:text-slate-200 dark:hover:bg-gray-700">
                                        {{ __('Cancel') }}
                                    </button>

                                    <button type="submit" wire:loading.attr="disabled" wire:target="updateDispensation"
                                        class="inline-flex items-center px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold hover:bg-blue-700 shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 transition-colors">
                                        <x-heroicon-s-check class="w-5 h-5 mr-2" />
                                        {{ __('Save Dispensation') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
