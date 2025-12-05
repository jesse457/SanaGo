<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50/50 p-6 dark:bg-gray-900">

    <div class="max-w-4xl mx-auto">
        {{-- Breadcrumbs --}}
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('receptionist.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors">
                        <x-heroicon-o-home class="w-4 h-4 me-2" />
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-400 mx-1" />
                        <a href="{{ route('receptionist.checkin') }}" wire:navigate
                            class="text-sm font-medium text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors">
                            Admissions
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-400 mx-1" />
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Admit Patient</span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center gap-3">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400">
                    <x-heroicon-s-clipboard-document-list class="w-7 h-7" />
                </div>
                Patient Admission
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 ml-14">
                Assign a bed and record admission details for inpatient care.
            </p>
        </div>

        {{-- Main Content --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/60 dark:border-gray-700 overflow-hidden">

            {{-- Patient Summary Header --}}
            <div class="px-6 py-5 bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-lg">
                        {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $patient->first_name }} {{ $patient->last_name }}
                        </h2>
                        <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400 font-mono mt-0.5">
                            <span>UID: {{ $patient->patient_uid }}</span>
                            <span class="w-1 h-1 bg-gray-400 rounded-full"></span>
                            <span class="capitalize">{{ $patient->gender }}</span>
                        </div>
                    </div>
                </div>

                {{-- Status Badge (Optional visual) --}}
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400">
                    Admission Pending
                </span>
            </div>

            <div class="p-6 md:p-8">
                <form wire:submit.prevent="saveAdmission" class="space-y-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Bed Selection --}}
                        <div class="col-span-1">
                            <label for="bedId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Bed</label>
                            <select id="bedId" wire:model="bedId"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white py-2.5">
                                <option value="">-- Choose an Available Bed --</option>
                                @foreach ($availableBeds as $bed)
                                    <option value="{{ $bed->id }}">
                                        Bed {{ $bed->bed_number }} ({{ $bed->type ?? 'General' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('bedId') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Admission Date --}}
                        <div class="col-span-1">
                            <label for="admissionDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Admission Date</label>
                            <input type="date" id="admissionDate" wire:model="admissionDate"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white" />
                            @error('admissionDate') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Reason --}}
                        <div class="col-span-1 md:col-span-2">
                            <label for="reasonForAdmission" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason for Admission</label>
                            <input type="text" id="reasonForAdmission" wire:model="reasonForAdmission" placeholder="e.g. Post-operative observation, Severe dehydration..."
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white" />
                            @error('reasonForAdmission') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        {{-- Observation Fee --}}
                        <div class="col-span-1">
                            <label for="observationFee" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Observation Fee / Deposit</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <span class="text-gray-500 sm:text-sm">$</span>
                                </div>
                                <input type="number" id="observationFee" wire:model="observationFee" placeholder="0.00"
                                    class="block w-full rounded-lg border-gray-300 pl-7 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white" />
                            </div>
                            @error('observationFee') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                        <a href="{{ route('receptionist.checkin') }}" wire:navigate
                           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                            Cancel
                        </a>

                        <button type="submit" wire:loading.attr="disabled"
                            class="inline-flex items-center justify-center px-6 py-2 border border-transparent text-sm font-semibold rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all disabled:opacity-70 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="saveAdmission" class="flex items-center gap-2">
                                <x-heroicon-s-arrow-right-on-rectangle class="w-5 h-5" />
                                Confirm Admission
                            </span>
                            <span wire:loading wire:target="saveAdmission" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Processing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
