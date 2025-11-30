<main class="flex-1 p-4 md:p-6 lg:ml-64 bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">
    {{-- Breadcrumbs --}}
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route($userRole . '.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors duration-150">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" />
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <a href="{{ route($userRole . '.checkin') }}" wire:navigate
                            class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-blue-400">Patient
                            Admissions</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm font-semibold text-gray-400 md:ms-2 dark:text-gray-200">Admission
                            Details</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white flex items-center mb-4">
        <x-heroicon-s-clipboard-document-list class="h-8 w-8 mr-3 text-blue-600 dark:text-blue-400" />
        Patient Admission Details
    </h2>
    {{-- Admission Details Section --}}
    <section id="admission-details-section">
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

            @if ($admissions->isNotEmpty())
                {{-- Admission Selector --}}
                <div class="mb-6">
                    <label for="admissionSelector" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Select Admission Record:
                    </label>
                    <select wire:model.live="selectedAdmissionId" id="admissionSelector"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @foreach ($admissions as $admissionOption)
                            <option value="{{ $admissionOption->id }}">
                                Admission on {{ $admissionOption->admission_date->format('M d, Y') }} (Status:
                                {{ $admissionOption->status }})
                            </option>
                        @endforeach
                    </select>
                </div>

                @if ($selectedAdmission)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-gray-700 dark:text-gray-300">
                        <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-xl shadow-sm flex items-start space-x-4">
                            <x-heroicon-o-user-circle class="h-6 w-6 text-gray-400 dark:text-gray-500" />
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white mb-1">Assigned Doctor:</p>
                                <p class="text-sm">{{ $selectedAdmission->doctor->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-xl shadow-sm flex items-start space-x-4">
                            <x-hugeicons-bed-single-02 class="h-8 w-6 text-gray-400 dark:text-gray-500" />
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white mb-1">Assigned Bed:</p>
                                <p class="text-sm">Bed {{ $selectedAdmission->bed->bed_number ?? 'N/A' }} (Ward:
                                    {{ $selectedAdmission->bed->ward->name ?? 'N/A' }})</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-xl shadow-sm flex items-start space-x-4">
                            <x-heroicon-o-document-text class="h-6 w-6 text-gray-400 dark:text-gray-500" />
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white mb-1">Reason for Admission:</p>
                                <p class="text-sm">{{ $selectedAdmission->reason_for_admission }}</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-xl shadow-sm flex items-start space-x-4">
                            <x-heroicon-o-calendar-days class="h-6 w-6 text-gray-400 dark:text-gray-500" />
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white mb-1">Admission Date:</p>
                                <p class="text-sm">{{ $selectedAdmission->admission_date->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-xl shadow-sm flex items-start space-x-4">
                            <x-heroicon-o-exclamation-circle class="h-6 w-6 text-gray-400 dark:text-gray-500" />
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white mb-1">Current Status:</p>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium
                                    @if ($selectedAdmission->status === 'Admitted') bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300
                                    @elseif($selectedAdmission->status === 'Discharged') bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-700/40 dark:text-gray-300 @endif">
                                    {{ $selectedAdmission->status }}
                                </span>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-5 rounded-xl shadow-sm flex items-start space-x-4">
                            <x-heroicon-o-arrow-right-on-rectangle class="h-6 w-6 text-gray-400 dark:text-gray-500" />
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white mb-1">Discharge Date:</p>
                                <p class="text-sm">{{ $selectedAdmission->discharge_date?->format('M d, Y') ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-10">
                        <x-heroicon-o-inbox class="h-16 w-16 text-gray-300 dark:text-gray-600 mb-4 mx-auto" />
                        <p class="text-lg font-medium text-gray-600 dark:text-gray-400">No admission record selected.
                        </p>
                    </div>
                @endif

                <div
                    class="flex flex-col sm:flex-row justify-end pt-8 border-t border-gray-200 dark:border-gray-700 mt-8 space-y-4 sm:space-y-0 sm:space-x-4">
                    <button wire:navigate href="{{ route($userRole . '.checkin') }}"
                        class="inline-flex items-center justify-center px-6 py-3 rounded-lg text-sm font-medium text-gray-700 hover:text-blue-600 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-300 transition-colors duration-150 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <x-heroicon-s-arrow-left class="h-4 w-4 mr-2" />
                        Back to Admissions List
                    </button>

                    @if ($selectedAdmission && $selectedAdmission->status === 'Admitted')
                        <button wire:click="updateAdmissionStatus({{ $selectedAdmission->id }}, 'Discharged')"
                            class="inline-flex items-center justify-center px-6 py-3 rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition-colors duration-150 transform hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                            wire:loading.attr="disabled"
                            wire:target="updateAdmissionStatus({{ $selectedAdmission->id }})">
                            <span wire:loading.remove wire:target="updateAdmissionStatus({{ $selectedAdmission->id }})"
                                class="flex items-center">
                                <x-heroicon-s-arrow-right-on-rectangle class="h-4 w-4 mr-2" />
                                Discharge Patient
                            </span>
                            <span wire:loading wire:target="updateAdmissionStatus({{ $selectedAdmission->id }})"
                                class="flex items-center">
                                <x-heroicon-s-arrow-path class="animate-spin h-4 w-4 mr-2" />
                                Processing...
                            </span>
                        </button>
                    @elseif ($selectedAdmission->status == 'Pending')
                        <button disabled
                            class="inline-flex items-center justify-center px-6 py-3 rounded-lg text-sm font-medium text-gray-500 bg-gray-200 dark:bg-gray-700 dark:text-gray-400 cursor-not-allowed">
                            <x-heroicon-s-check-circle class="h-4 w-4 mr-2" />
                            Not Admitted
                        </button>
                    @else
                        <button disabled
                            class="inline-flex items-center justify-center px-6 py-3 rounded-lg text-sm font-medium text-gray-500 bg-gray-200 dark:bg-gray-700 dark:text-gray-400 cursor-not-allowed">
                            <x-heroicon-s-check-circle class="h-4 w-4 mr-2" />
                            Already Discharged
                        </button>
                    @endif
                </div>
            @else
                <div class="text-center py-10">
                    <x-heroicon-o-inbox class="h-16 w-16 text-gray-300 dark:text-gray-600 mb-4 mx-auto" />
                    <p class="text-lg font-medium text-gray-600 dark:text-gray-400">No admission details found for this
                        patient.</p>
                    <button wire:navigate href="{{ route($userRole . '.checkin') }}"
                        class="mt-6 inline-flex items-center px-6 py-3 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-150">
                        <x-heroicon-s-arrow-left class="h-4 w-4 mr-2" />
                        Back to Admissions List
                    </button>
                </div>
            @endif
        </div>
    </section>
</main>
