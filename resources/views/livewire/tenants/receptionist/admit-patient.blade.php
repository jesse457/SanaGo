<main class="flex-1 p-4 md:p-6 lg:ml-64 bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">

    {{-- Breadcrumbs --}}
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('receptionist.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-300 transition-colors duration-150">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" />
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <a href="{{ route('receptionist.checkin') }}" wire:navigate
                            class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-blue-300">My
                            Patients for Admission</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm font-medium text-gray-400 md:ms-2 dark:text-gray-200">
                            Admit Patient</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white flex items-center mb-4">
        <x-heroicon-s-clipboard-document-list class="h-8 w-8 mr-3 text-blue-600 dark:text-blue-400" />
        Admit Patient
    </h2>
    {{-- Admission Form Section --}}
    <section id="admission-form-section">
        <div class="card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
            <div
                class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8 border-b border-gray-200 dark:border-gray-700 pb-6 gap-4">
                <div>

                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        For Patient: <span class="font-bold text-blue-600 dark:text-blue-400">{{ $patient->first_name }}
                            {{ $patient->last_name }}</span> (ID: {{ $patient->patient_uid }})
                    </p>
                </div>
                <div class="text-left sm:text-right">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Gender</p>
                    <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ ucfirst($patient->gender) }}
                    </p>
                </div>
            </div>

            <form wire:submit.prevent="saveAdmission" class="space-y-6">
                {{-- Row 1: Bed and Reason for Admission --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="bedId"
                            class="form-label mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Assign
                            Bed</label>
                        <select id="bedId" wire:model="bedId"
                            class="form-select block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500 @error('bedId') border-red-500 @enderror">
                            <option value="">Select an Available Bed</option>
                            @foreach ($availableBeds as $bed)
                                <option value="{{ $bed->id }}">Bed {{ $bed->bed_number }}
                                </option>
                            @endforeach
                        </select>
                        @error('bedId')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="reasonForAdmission"
                            class="form-label mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Reason for
                            Admission</label>
                        <input type="text" id="reasonForAdmission" wire:model="reasonForAdmission"
                            class="form-input block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500 @error('reasonForAdmission') border-red-500 @enderror"
                            placeholder="e.g., Post-operative care">
                        @error('reasonForAdmission')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Row 2: Admission Date --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="admissionDate"
                            class="form-label mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Admission
                            Date</label>
                        <input type="date" id="admissionDate" wire:model="admissionDate"
                            class="form-input block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500 @error('admissionDate') border-red-500 @enderror">
                        @error('admissionDate')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                      <div>
                        <label for="observationFee"
                            class="form-label mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">Observation
                            Fee</label>
                        <input type="number" id="observationFee" wire:model="observationFee"
                            class="form-input block w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500 @error('observationFee') border-red-500 @enderror"
                            placeholder="e.g., 100">
                        @error('observationFee')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                           
                </div>

                {{-- Form Actions --}}
                <div class="flex justify-end pt-8 border-t border-gray-200 dark:border-gray-700 mt-6 space-x-4">

                    <button type="submit"
                        class="inline-flex items-center px-6 py-3 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-150 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        wire:loading.attr="disabled" wire:target="saveAdmission">
                        <span wire:loading.remove wire:target="saveAdmission" class="flex items-center">
                            <x-heroicon-s-check-circle class="h-4 w-4 mr-2" />
                            Confirm Admission
                        </span>
                        <span wire:loading wire:target="saveAdmission" class="flex items-center">
                            <x-heroicon-s-arrow-path class="animate-spin h-4 w-4 mr-2" />
                            Processing...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </section>

</main>
