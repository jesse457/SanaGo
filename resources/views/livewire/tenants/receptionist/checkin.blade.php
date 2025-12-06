<main class="flex-1 overflow-x-hidden overflow-y-auto  p-6 dark:bg-gray-900" data-turbo="false"
    data-boost="false">

    <div class="max-w-7xl mx-auto">
        {{-- Breadcrumbs --}}
        <div class="mb-6 mt-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <li class="inline-flex items-center">
                        <a href="{{ route('receptionist.dashboard') }}" wire:navigate
                            class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-300 transition-colors duration-150">
                            <x-heroicon-s-home class="w-4 h-4 me-2.5" />
                            Home
                        </a>
                    </li>

                    <li>
                        <div class="flex items-center">
                            <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                            <span class="ms-1 text-sm  text-gray-900 md:ms-2 dark:text-gray-400">
                                Admissions</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        {{-- Flash Message --}}
        @if (session()->has('message'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                class="flex items-center p-4 mb-6 text-sm text-green-800 rounded-xl bg-green-50 border border-green-100 dark:bg-gray-800 dark:text-green-400 dark:border-green-800 shadow-sm"
                role="alert">
                <x-heroicon-o-check-circle class="flex-shrink-0 inline w-5 h-5 me-3" />
                <span class="font-medium">Success!</span> &nbsp; {{ session('message') }}
                <button @click="show = false"
                    class="ml-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-gray-700">
                    <span class="sr-only">Close</span>
                    <x-heroicon-s-x-mark class="w-5 h-5" />
                </button>
            </div>
        @endif

        {{-- Page Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center gap-3">

                    Patient Admissions
                </h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Manage patient admission requests, bed assignments, and discharge status.
                </p>
            </div>
        </div>

        {{-- Main Content Card --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/60 dark:border-gray-700 overflow-hidden">

            {{-- Search & Filters --}}
            <div class="p-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    <div class="relative w-full md:max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" />
                        </div>
                        <input type="text" wire:model.live.debounce.400ms="search"
                            class="block w-full pl-10 pr-4 py-2.5 bg-white border-gray-200 rounded-xl text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition-shadow"
                            placeholder="Search by UID, name, or bed..." />
                        <div wire:loading wire:target="search"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <svg class="animate-spin h-4 w-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                    </div>

                    {{-- Optional Filter Toggle --}}
                    {{-- Kept your toggle but styled it better --}}
                    <div x-data="{ isOpen: false }" class="flex items-center gap-3">
                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Show Admitted Only</span>
                        <button @click="isOpen = !isOpen"
                            :class="isOpen ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700'"
                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <span :class="isOpen ? 'translate-x-6' : 'translate-x-1'"
                                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"></span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Patient</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Doctor</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Status</th>
                            <th scope="col"
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Admission Date</th>
                            <th scope="col"
                                class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse ($patients as $patient)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition-colors duration-200">
                                {{-- Patient --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-9 w-9 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-xs border border-white dark:border-gray-600 shadow-sm">
                                            {{ substr($patient->first_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                {{ $patient->first_name }} {{ $patient->last_name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 font-mono">
                                                {{ $patient->patient_uid }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Doctor --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ $patient->admissions->first()?->doctor->name ?? 'Not Assigned' }}
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $latestAdmission = $patient->admissions->first();
                                        $admissionStatus = $latestAdmission?->status ?? 'No Admission';

                                        $config = match ($admissionStatus) {
                                            'awaiting_bed' => [
                                                'bg' => 'bg-amber-50 dark:bg-amber-900/20',
                                                'text' => 'text-amber-700 dark:text-amber-400',
                                                'border' => 'border-amber-100 dark:border-amber-800',
                                                'icon' => 'clock',
                                            ],
                                            'Discharged' => [
                                                'bg' => 'bg-gray-50 dark:bg-gray-700/20',
                                                'text' => 'text-gray-600 dark:text-gray-400',
                                                'border' => 'border-gray-100 dark:border-gray-700',
                                                'icon' => 'arrow-right-on-rectangle',
                                            ],
                                            'Admitted' => [
                                                'bg' => 'bg-emerald-50 dark:bg-emerald-900/20',
                                                'text' => 'text-emerald-700 dark:text-emerald-400',
                                                'border' => 'border-emerald-100 dark:border-emerald-800',
                                                'icon' => 'check-circle',
                                            ],
                                            default => [
                                                'bg' => 'bg-gray-50 dark:bg-gray-700/20',
                                                'text' => 'text-gray-500 dark:text-gray-400',
                                                'border' => 'border-gray-100 dark:border-gray-700',
                                                'icon' => 'minus-circle',
                                            ],
                                        };

                                        $statusLabel = match ($admissionStatus) {
                                            'awaiting_bed' => 'Awaiting Bed',
                                            'Admitted' => 'Admitted' .
                                                ($latestAdmission?->bed_id ? " (Bed {$latestAdmission->bed_id})" : ''),
                                            default => $admissionStatus,
                                        };
                                    @endphp

                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }}">
                                        @if ($config['icon'] == 'clock')
                                            <x-heroicon-o-clock class="w-3.5 h-3.5" />
                                        @endif
                                        @if ($config['icon'] == 'check-circle')
                                            <x-heroicon-o-check-circle class="w-3.5 h-3.5" />
                                        @endif
                                        @if ($config['icon'] == 'arrow-right-on-rectangle')
                                            <x-heroicon-o-arrow-right-on-rectangle class="w-3.5 h-3.5" />
                                        @endif
                                        @if ($config['icon'] == 'minus-circle')
                                            <x-heroicon-o-minus-circle class="w-3.5 h-3.5" />
                                        @endif
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                {{-- Date --}}
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($latestAdmission?->admission_date)
                                        <div class="flex items-center gap-1.5 text-sm text-gray-600 dark:text-gray-300">
                                            <x-heroicon-o-calendar-days class="w-4 h-4 text-gray-400" />
                                            {{ $latestAdmission->admission_date->format('M d, Y') }}
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 italic">N/A</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if (($latestAdmission?->status ?? '') === 'Admitted')
                                            <span
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700/50 dark:text-gray-400 cursor-not-allowed border border-transparent">
                                                <x-heroicon-s-check class="w-3.5 h-3.5 mr-1" />
                                                Active
                                            </span>
                                        @else
                                            <button wire:click="admitPatient({{ $patient->id }})"
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                <x-heroicon-s-plus class="w-3.5 h-3.5 mr-1" />
                                                Admit
                                            </button>
                                        @endif

                                        <button wire:click="viewPatientDetails({{ $patient->id }})"
                                            class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors dark:text-gray-400 dark:hover:bg-gray-700"
                                            title="View Details">
                                            <x-heroicon-o-eye class="w-5 h-5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div
                                            class="h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center mb-4">
                                            <x-heroicon-o-magnifying-glass class="w-6 h-6 text-gray-400" />
                                        </div>
                                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">No patients
                                            found</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                            Try adjusting your search criteria.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($patients->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                    {{ $patients->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Admission Selection Modal --}}
    <div x-data="{ show: @entangle('showAdmissionModal') }" x-show="show" x-cloak class="relative z-50" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">

        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="show" x-trap.noscroll="show" @click.away="$wire.set('showAdmissionModal', false)"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-gray-100 dark:border-gray-700">

                    {{-- Modal Header --}}
                    <div
                        class="bg-white dark:bg-gray-800 px-4 py-5 sm:px-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white"
                                id="modal-title">
                                Select Admission Request
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Choose a request to process for admission.
                            </p>
                        </div>
                        <button type="button" wire:click="$set('showAdmissionModal', false)"
                            class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <x-heroicon-o-x-mark class="h-6 w-6" />
                        </button>
                    </div>

                    <div class="px-4 py-5 sm:p-6 bg-gray-50/50 dark:bg-gray-800/50">
                        @if ($recentAdmissions->isEmpty())
                            <div class="text-center py-8">
                                <div
                                    class="h-12 w-12 rounded-full bg-gray-100 dark:bg-gray-700 mx-auto flex items-center justify-center mb-3">
                                    <x-heroicon-o-clipboard-document class="w-6 h-6 text-gray-400" />
                                </div>
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white">No pending requests</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">There are no recent admission
                                    requests for this patient.</p>
                                <div class="mt-6 flex justify-center gap-3">
                                    <button wire:click="confirmAdmission(null)"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Create New Admission
                                    </button>
                                    <button wire:click="$set('showAdmissionModal', false)"
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600">
                                        Close
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="space-y-3 max-h-96 overflow-y-auto">
                                @foreach ($recentAdmissions as $ad)
                                    <div wire:click="selectAdmission({{ $ad->id }})"
                                        class="cursor-pointer group relative flex items-start gap-4 p-4 rounded-xl border transition-all duration-200
                                        {{ $selectedAdmissionId === $ad->id
                                            ? 'bg-indigo-50 border-indigo-200 shadow-sm dark:bg-indigo-900/20 dark:border-indigo-800'
                                            : 'bg-white border-gray-200 hover:border-indigo-300 hover:shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:hover:border-gray-600' }}">

                                        <div class="flex h-5 items-center mt-1">
                                            <input type="radio" name="selectedAdmission"
                                                value="{{ $ad->id }}"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-600 dark:bg-gray-700 dark:border-gray-600"
                                                @checked($selectedAdmissionId === $ad->id)>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start mb-1">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $ad->created_at->format('M d, Y') }} <span
                                                        class="text-gray-400 font-normal mx-1">•</span>
                                                    {{ $ad->created_at->format('H:i') }}
                                                </p>
                                                <span
                                                    class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset
                                                    {{ $ad->status === 'Admitted' ? 'bg-green-50 text-green-700 ring-green-600/20' : 'bg-yellow-50 text-yellow-800 ring-yellow-600/20' }}">
                                                    {{ $ad->status }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-1">
                                                <span class="font-medium text-gray-700 dark:text-gray-200">Requested
                                                    by:</span> {{ optional($ad->doctor)->name ?? 'Unknown' }}
                                            </p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                                {{ $ad->reason_for_admission ?? 'No reason provided.' }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div
                                class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                                <button type="button" wire:click="$set('showAdmissionModal', false)"
                                    class="text-sm font-medium text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white px-4 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    Cancel
                                </button>
                                <button type="button" wire:click="confirmAdmission(true)"
                                    class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-semibold rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                    {{ !$selectedAdmissionId ? 'disabled' : '' }}>
                                    Process Selected Request
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
