<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50/50 p-6 dark:bg-gray-900">
    <div class="max-w-5xl mx-auto">
        {{-- Breadcrumbs --}}
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route($userRole . '.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors">
                        <x-heroicon-o-home class="w-4 h-4 me-2" />
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-400 mx-1" />
                        <a href="{{ route($userRole . '.checkin') }}" wire:navigate
                            class="text-sm font-medium text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors">
                            Admissions
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-400 mx-1" />
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Admission Details</span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center gap-3">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400">
                    <x-heroicon-s-clipboard-document-list class="w-7 h-7" />
                </div>
                Admission Overview
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 ml-14">
                Detailed view of patient admission history and current status.
            </p>
        </div>

        {{-- Main Content Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/60 dark:border-gray-700 overflow-hidden">

            {{-- Patient Header Info --}}
            <div class="px-6 py-5 bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-lg border-2 border-white dark:border-gray-700 shadow-sm">
                        {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $patient->first_name }} {{ $patient->last_name }}
                        </h2>
                        <div class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400 font-mono mt-0.5">
                            <span class="bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $patient->patient_uid }}</span>
                            <span class="w-1 h-1 bg-gray-400 rounded-full"></span>
                            <span class="capitalize">{{ $patient->gender }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 md:p-8">
                @if ($admissions->isNotEmpty())

                    {{-- Admission Selector --}}
                    <div class="mb-8 max-w-md">
                        <label for="admissionSelector" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Select Admission Record
                        </label>
                        <div class="relative">
                            <select wire:model.live="selectedAdmissionId" id="admissionSelector"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white py-2.5 pl-3 pr-10">
                                @foreach ($admissions as $admissionOption)
                                    <option value="{{ $admissionOption->id }}">
                                        {{ $admissionOption->admission_date->format('M d, Y') }} — {{ $admissionOption->status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if ($selectedAdmission)
                        {{-- Details Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Card: Doctor --}}
                            <div class="bg-gray-50 dark:bg-gray-700/30 p-5 rounded-xl border border-gray-100 dark:border-gray-700 flex items-start gap-4 transition-colors hover:bg-white hover:border-indigo-100 hover:shadow-sm dark:hover:bg-gray-700/50 dark:hover:border-gray-600">
                                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/50 rounded-lg text-indigo-600 dark:text-indigo-400">
                                    <x-heroicon-o-user-circle class="w-6 h-6" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Assigned Doctor</p>
                                    <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                                        {{ $selectedAdmission->doctor->name ?? 'Not Assigned' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Card: Bed --}}
                            <div class="bg-gray-50 dark:bg-gray-700/30 p-5 rounded-xl border border-gray-100 dark:border-gray-700 flex items-start gap-4 transition-colors hover:bg-white hover:border-indigo-100 hover:shadow-sm dark:hover:bg-gray-700/50 dark:hover:border-gray-600">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg text-blue-600 dark:text-blue-400">
                                    <x-heroicon-o-home-modern class="w-6 h-6" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Bed Allocation</p>
                                    <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                                        Bed {{ $selectedAdmission->bed->bed_number ?? 'N/A' }}
                                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400 ml-1">
                                            ({{ $selectedAdmission->bed->ward->name ?? 'Ward N/A' }})
                                        </span>
                                    </p>
                                </div>
                            </div>

                            {{-- Card: Reason --}}
                            <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700/30 p-5 rounded-xl border border-gray-100 dark:border-gray-700 flex items-start gap-4 transition-colors hover:bg-white hover:border-indigo-100 hover:shadow-sm dark:hover:bg-gray-700/50 dark:hover:border-gray-600">
                                <div class="p-2 bg-amber-100 dark:bg-amber-900/50 rounded-lg text-amber-600 dark:text-amber-400">
                                    <x-heroicon-o-document-text class="w-6 h-6" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Reason for Admission</p>
                                    <p class="text-base text-gray-900 dark:text-white mt-1 leading-relaxed">
                                        {{ $selectedAdmission->reason_for_admission }}
                                    </p>
                                </div>
                            </div>

                            {{-- Card: Dates & Status --}}
                            <div class="bg-gray-50 dark:bg-gray-700/30 p-5 rounded-xl border border-gray-100 dark:border-gray-700 flex items-start gap-4 transition-colors hover:bg-white hover:border-indigo-100 hover:shadow-sm dark:hover:bg-gray-700/50 dark:hover:border-gray-600">
                                <div class="p-2 bg-emerald-100 dark:bg-emerald-900/50 rounded-lg text-emerald-600 dark:text-emerald-400">
                                    <x-heroicon-o-calendar-days class="w-6 h-6" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Admission Date</p>
                                    <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                                        {{ $selectedAdmission->admission_date->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>

                            <div class="bg-gray-50 dark:bg-gray-700/30 p-5 rounded-xl border border-gray-100 dark:border-gray-700 flex items-start gap-4 transition-colors hover:bg-white hover:border-indigo-100 hover:shadow-sm dark:hover:bg-gray-700/50 dark:hover:border-gray-600">
                                <div class="p-2 bg-purple-100 dark:bg-purple-900/50 rounded-lg text-purple-600 dark:text-purple-400">
                                    <x-heroicon-o-arrow-right-on-rectangle class="w-6 h-6" />
                                </div>
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Discharge Date</p>
                                    <p class="text-base font-medium text-gray-900 dark:text-white mt-1">
                                        {{ $selectedAdmission->discharge_date?->format('M d, Y') ?? 'Ongoing' }}
                                    </p>
                                </div>
                            </div>

                            {{-- Status Banner --}}
                            <div class="md:col-span-2 flex items-center justify-between p-4 rounded-xl border
                                @if($selectedAdmission->status === 'Admitted') bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-300
                                @elseif($selectedAdmission->status === 'Discharged') bg-gray-50 border-gray-200 text-gray-600 dark:bg-gray-700/40 dark:border-gray-600 dark:text-gray-400
                                @else bg-yellow-50 border-yellow-200 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-300 @endif">
                                <div class="flex items-center gap-3">
                                    <div class="p-1 rounded-full border-2 border-current">
                                        <x-heroicon-s-information-circle class="w-5 h-5" />
                                    </div>
                                    <span class="font-semibold text-lg">{{ $selectedAdmission->status }}</span>
                                </div>
                                <span class="text-xs font-medium opacity-75 uppercase tracking-wider">Current Status</span>
                            </div>

                        </div>
                    @else
                        {{-- Empty Selection State --}}
                        <div class="flex flex-col items-center justify-center py-12 text-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50/50 dark:bg-gray-800/50">
                            <x-heroicon-o-inbox class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" />
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">No record selected</h3>
                            <p class="text-gray-500 dark:text-gray-400">Please select an admission record from the dropdown above.</p>
                        </div>
                    @endif

                    {{-- Actions Footer --}}
                    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row justify-end gap-3">

                        <button wire:navigate href="{{ route($userRole . '.checkin') }}"
                            class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                            <x-heroicon-s-arrow-left class="w-4 h-4 mr-2" />
                            Back to List
                        </button>

                        @if ($selectedAdmission && $selectedAdmission->status === 'Admitted')
                            <button wire:click="updateAdmissionStatus({{ $selectedAdmission->id }}, 'Discharged')"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center justify-center px-6 py-2 border border-transparent text-sm font-semibold rounded-lg shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all">

                                <span wire:loading.remove wire:target="updateAdmissionStatus" class="flex items-center">
                                    <x-heroicon-s-arrow-right-on-rectangle class="w-4 h-4 mr-2" />
                                    Discharge Patient
                                </span>

                                <span wire:loading wire:target="updateAdmissionStatus" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                        @elseif ($selectedAdmission && $selectedAdmission->status === 'Discharged')
                            <button disabled
                                class="inline-flex items-center justify-center px-6 py-2 border border-transparent text-sm font-medium rounded-lg text-gray-400 bg-gray-100 cursor-not-allowed dark:bg-gray-700 dark:text-gray-500">
                                <x-heroicon-s-check-circle class="w-4 h-4 mr-2" />
                                Patient Discharged
                            </button>
                        @endif
                    </div>
                @else
                    {{-- Empty State (No Admissions) --}}
                    <div class="flex flex-col items-center justify-center py-16 text-center">
                        <div class="h-20 w-20 bg-gray-50 dark:bg-gray-700/50 rounded-full flex items-center justify-center mb-4">
                            <x-heroicon-o-folder-open class="w-10 h-10 text-gray-300 dark:text-gray-500" />
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">No Admission Records Found</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 max-w-sm">
                            This patient has no admission history on file.
                        </p>
                        <a href="{{ route($userRole . '.checkin') }}" wire:navigate
                            class="mt-6 inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400">
                            <x-heroicon-s-arrow-left class="w-4 h-4 mr-1" />
                            Return to Patient List
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</main>
