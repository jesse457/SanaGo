<main class="flex-1 p-4 md:p-6 lg:ml-64 bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen" data-turbo="false" data-boost="false">
    {{-- This button might be part of a larger layout component,
         but kept here for completeness if it's specific to this page. --}}
    <button
        class="lg:hidden p-3 rounded-xl text-gray-700 bg-white shadow-md hover:bg-gray-100 mb-6 transition-colors duration-200
               dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        <x-heroicon-o-bars-3 class="h-6 w-6" />
    </button>

    {{-- Breadcrumbs --}}
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('nurse.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-300 transition-colors duration-150">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" />
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm font-medium text-gray-400 md:ms-2 dark:text-gray-400">
                            Patients</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    {{-- Session Flash Message for actions --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-6 dark:bg-green-900/30 dark:border-green-700 dark:text-green-300"
            role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer"
                onclick="this.parentElement.style.display='none'">
                <x-heroicon-s-x-mark class="fill-current h-6 w-6 text-green-500" />
            </span>
        </div>
    @endif

    <section id="patient-admission-section" class="dashboard-section">
        <div class="mb-3">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                <x-heroicon-o-user-plus class="h-8 w-8 mr-4 text-blue-600 dark:text-blue-400" />
                Patient Admissions
            </h2>
            <p class="text-gray-600 dark:text-gray-400">View, search, and manage all patient admissions.</p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8 mb-8">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-6">
                <div class="relative w-full md:w-auto flex-grow">
                    <input type="text" wire:model.live.debounce.400ms="search"
                        placeholder="Search patient UID, name or bed..."
                        class="w-full pl-12 pr-4 py-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <x-heroicon-s-magnifying-glass
                        class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                </div>
                {{-- This toggle button seems out of place and likely should be part of a different component or removed. --}}
                <button x-data="{ isOpen: false }" x-bind:class="{ 'bg-blue-500': isOpen, 'bg-gray-200': !isOpen }"
                    @click="isOpen = !isOpen"
                    class="flex w-12 h-7 rounded-full cursor-pointer transition duration-300 relative focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <div class="bg-white mt-1 w-5 h-5 rounded-full shadow-md absolute transition duration-300"
                        :class="{ 'translate-x-6': isOpen, 'translate-x-1': !isOpen }"></div>
                </button>
            </div>

            <div
                class="overflow-x-auto shadow-lg rounded-2xl border border-gray-200 dark:border-gray-700 custom-scrollbar">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300 rounded-tl-xl">
                                Patient ID</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                Name</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                Doctor</th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                Admission Status</th>
                            {{-- Changed 'Last Seen' to 'Admission Date' for clarity --}}
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                Admission Date</th>
                            <th scope="col"
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300 rounded-tr-xl">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($patients as $patient)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                <td class="px-6 py-4 text-sm font-extrabold text-blue-700 dark:text-blue-400">
                                    {{ $patient->patient_uid }}</td>
                                <td class="px-6 py-4 text-sm text-gray-800 dark:text-gray-200 font-medium">
                                    {{ $patient->first_name }} {{ $patient->last_name }}</td>
                                <td
                                    class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 max-w-xs overflow-hidden text-ellipsis">
                                    {{ $patient->admissions->first()?->doctor->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @php
                                        // Use first() to get the latest admission from the collection
                                        $latestAdmission = $patient->admissions->first();
                                        $admissionStatus = $latestAdmission?->status ?? 'No Admission';
                                        $statusClass = '';
                                        $statusText = '';

                                        // Refined switch case for better clarity
                                        switch ($admissionStatus) {
                                            case 'awaiting_bed':
                                                $statusClass =
                                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300';
                                                $statusText = 'Awaiting Bed';
                                                break;
                                            case 'Discharged':
                                                $statusClass =
                                                    'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300';
                                                $statusText = 'Discharged';
                                                break;
                                            case 'Admitted':
                                                $statusClass =
                                                    'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300';
                                                $statusText =
                                                    'Admitted' .
                                                    ($latestAdmission?->bed_id
                                                        ? ' (Bed ' . $latestAdmission->bed_id . ')'
                                                        : '');
                                                break;
                                            default:
                                                $statusClass =
                                                    'bg-gray-100 text-gray-800 dark:bg-gray-700/40 dark:text-gray-300';
                                                $statusText = 'Not Admitted';
                                                break;
                                        }
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                        @if ($admissionStatus == 'awaiting_bed')
                                            <x-heroicon-o-clock class="w-4 h-4 me-1.5" />
                                        @elseif ($admissionStatus == 'Discharged')
                                            <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4 me-1.5" />
                                        @elseif ($admissionStatus == 'Admitted')
                                            <x-heroicon-o-check-circle class="w-4 h-4 me-1.5" />
                                        @else
                                            <x-heroicon-o-x-circle class="w-4 h-4 me-1.5" />
                                        @endif
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                        <x-heroicon-o-calendar-days class="w-4 h-4 me-1.5" />
                                        {{ $latestAdmission?->admission_date?->format('M d, Y') ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                    {{-- Corrected logic to check if a patient is CURRENTLY admitted. This allows re-admission. --}}
                                    @if (($latestAdmission?->status ?? '') === 'Admitted')
                                        <button
                                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-gray-600 bg-gray-50 dark:bg-gray-900/30 dark:text-gray-400 cursor-not-allowed"
                                            disabled>
                                            <x-heroicon-s-check-circle class="h-4 w-4 mr-1.5" />
                                            Admitted
                                        </button>
                                    @else
                                        <button wire:click="admitPatient({{ $patient->id }})"
                                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 dark:text-blue-400 transition-colors duration-150 transform hover:scale-105">
                                            <x-heroicon-s-arrow-right-on-rectangle class="h-4 w-4 mr-1.5" />
                                            Admit
                                        </button>
                                    @endif
                                    <button wire:click="viewPatientDetails({{ $patient->id }})"
                                        class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 dark:text-indigo-400 transition-colors duration-150 transform hover:scale-105">
                                        <x-heroicon-s-eye class="h-4 w-4 mr-1.5" />
                                        Details
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"
                                    class="px-6 py-12 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <x-heroicon-o-inbox class="h-16 w-16 text-gray-300 dark:text-gray-600 mb-4" />
                                        <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">No
                                            patients found matching your search.</p>
                                        <p class="text-base text-gray-500 dark:text-gray-400">Try adjusting your search
                                            filters or registering a new patient.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Livewire Pagination Links --}}
            <div class="mt-8">
                {{ $patients->links() }}
            </div>
        </div>
    </section>

    {{-- Admission Selection Modal (Livewire-controlled) --}}
<div x-data
     x-show="$wire.showAdmissionModal"
     x-on:keydown.escape.window="$wire.set('showAdmissionModal', false)"
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
     style="display: none;">
        <div class="absolute inset-0 bg-black/50"></div>
        <div class="relative z-10 w-full max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Select Admission Request</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Choose the doctor's admission request to process for this patient.</p>
                    </div>
                    <button wire:click="$set('showAdmissionModal', false)" class="text-gray-500 hover:text-gray-700 dark:text-gray-300">
                        <x-heroicon-s-x-mark class="h-5 w-5" />
                    </button>
                </div>

                @if ($recentAdmissions->isEmpty())
                    <div class="py-8 text-center">
                        <x-heroicon-o-inbox class="h-12 w-12 text-gray-300 mx-auto" />
                        <p class="mt-4 text-gray-600 dark:text-gray-300">No recent admission requests found for this patient.</p>
                        <div class="mt-6">
                            <button wire:click="confirmAdmission(null)" class="px-4 py-2 rounded bg-green-600 text-white">Create New Admission</button>
                            <button wire:click="$set('showAdmissionModal', false)" class="ml-2 px-4 py-2 rounded bg-gray-200">Cancel</button>
                        </div>
                    </div>
                @else
                    <div class="max-h-80 overflow-y-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-gray-500">
                                    <th class="p-2">Select</th>
                                    <th class="p-2">Requested</th>
                                    <th class="p-2">Requested By</th>
                                    <th class="p-2">Reason</th>
                                    <th class="p-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentAdmissions as $ad)
                                    <tr class="border-t border-gray-100 dark:border-gray-700">
                                        <td class="p-2 align-top">
                                            <input type="radio" name="selectedAdmission" value="{{ $ad->id }}"
                                                   wire:click="selectAdmission({{ $ad->id }})"
                                                   @if ($selectedAdmissionId === $ad->id) checked @endif
                                            />
                                        </td>
                                        <td class="p-2 align-top">
                                            <div class="text-gray-800 dark:text-gray-200">{{ $ad->created_at->format('M d, Y H:i') }}</div>
                                            <div class="text-xs text-gray-400">#{{ $ad->id }}</div>
                                        </td>
                                        <td class="p-2 align-top">
                                            <div class="text-gray-700 dark:text-gray-300">
                                                {{ optional($ad->doctor)->name ?? 'Doctor' }}
                                            </div>
                                        </td>
                                        <td class="p-2 align-top text-gray-600 dark:text-gray-400">
                                            {{ $ad->reason_for_admission ?? 'N/A' }}
                                        </td>
                                        <td class="p-2 align-top">
                                            <span class="px-2 py-1 rounded text-xs font-medium
                                                {{ $ad->status === 'Admitted' ? 'bg-green-100 text-green-800' : ($ad->status === 'Discharged' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $ad->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6 flex justify-end gap-2">
                        {{-- Open Admission Form button to process a new form based on the selected admission. --}}
                        <button wire:click="confirmAdmission(true)" class="px-4 py-2 rounded bg-indigo-600 text-white
                            {{ $selectedAdmissionId ? '' : 'opacity-50 cursor-not-allowed' }}"
                            {{ $selectedAdmissionId ? '' : 'disabled' }}>
                            Open Admission Form
                        </button>

                        <button wire:click="$set('showAdmissionModal', false)" class="px-4 py-2 rounded bg-gray-200">Cancel</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</main>
