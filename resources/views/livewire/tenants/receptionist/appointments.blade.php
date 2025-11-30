<div class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 lg:ml-64 p-6 dark:bg-gray-900">
    {{-- Breadcrumbs --}}
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('receptionist.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300 transition-colors duration-150">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" />Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm font-medium text-gray-400 md:ms-2 dark:text-gray-400">
                            Appointments</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-1 flex items-center gap-3">
                <x-heroicon-s-calendar-days class="w-8 h-8 text-indigo-600" />
                Manage Appointments
            </h1>
            <p class="text-gray-600 dark:text-gray-400">View, search, and manage all doctor appointments.</p>
        </div>
        <a href="{{ route('receptionist.book-appointment') }}" wire:navigate
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition duration-150 ease-in-out shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <x-heroicon-o-plus class="w-5 h-5" /> Book Appointment
        </a>
    </header>

    <div x-data="{ open: true }" class="mt-4 card mb-4">
        <div class="flex items-center justify-between gap-3 mb-2 p-4 border-b dark:border-gray-700">
            <div class="flex items-center gap-3">
                <x-heroicon-o-funnel class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Filter Appointments</h3>
            </div>
            <button @click="open = !open" class="text-gray-500 hover:text-gray-700">
                <x-heroicon-s-chevron-up x-show="open" class="w-5 h-5 transition-transform" />
                <x-heroicon-s-chevron-down x-show="!open" class="w-5 h-5 transition-transform" />
            </button>
        </div>

        <div x-show="open" x-transition class="p-4 will-change-transform">
            <form class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" onsubmit="return false;"
                aria-label="Appointment filters">
                <div>
                    <label for="dateFilter"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                    <input type="date" id="dateFilter" wire:model.live="dateFilter"
                        class="mt-1 form-input " />
                </div>

                <div>
                    <label for="doctorFilter"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Doctor</label>
                    <select id="doctorFilter" wire:model.live="doctorFilter"
                        class="mt-1 form-input">
                        <option value="">All Doctors</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->name }}@if ($doctor->department)
                                    ({{ $doctor->department->name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="statusFilter"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                    <select id="statusFilter" wire:model.live="statusFilter"
                        class="mt-1 form-select w-full rounded-md border-gray-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500">
                        <option value="">All Statuses</option>
                        {{-- New/Updated Statuses based on model changes --}}
                        <option value="Scheduled">Scheduled (Pre-Check-in)</option>
                        <option value="Waiting">Waiting (Checked In)</option>
                        <option value="In Consultation">In Consultation</option>
                        <option value="Completed">Completed</option>
                        <option value="Canceled">Canceled</option>
                        {{-- Removed 'Pending' and 'Confirmed' --}}
                    </select>
                </div>

                <div class="relative">
                    <label for="patientSearch" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search
                        Patient</label>
                    <div class="relative mt-1">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400" />
                        </span>
                        <input type="text" id="patientSearch" wire:model.live.debounce.300ms="patientSearch"
                            placeholder="Name, ID, or Phone..."
                            class="pl-10 pr-4 py-2 block w-full border border-gray-200 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <div wire:loading wire:target="patientSearch"
                            class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>
            </form>

            <div class="mt-4 flex items-center justify-end gap-2">
                <button wire:click.prevent="$refresh"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400"
                    wire:loading.attr="disabled" wire:target="resetFilters, $refresh">
                    <x-heroicon-o-arrow-path class="w-5 h-5" /> Refresh
                </button>

                <button wire:click="resetFilters"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-md border border-gray-200 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-300"
                    wire:loading.attr="disabled" wire:target="resetFilters, $refresh">
                    <x-heroicon-o-x-mark class="w-5 h-5" /> Clear Filters
                </button>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-100 dark:border-gray-700 relative">
        <div class="p-4 flex items-center justify-between border-b dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                <x-heroicon-o-clipboard-document-list class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                Appointment List
            </h3>

            <div class="text-sm text-gray-500 dark:text-gray-400">Showing
                {{ $appointments->firstItem() }}-{{ $appointments->lastItem() }} of {{ $appointments->total() }} results
            </div>
        </div>

        <div class="overflow-x-auto rounded-b-lg relative min-h-[200px]">
            <div wire:loading.flex
                class="absolute inset-0 z-10 items-center justify-center bg-white/50 dark:bg-gray-900/50 transition-opacity">
                <div class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-md px-4 py-2 shadow">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    <span class="text-sm text-gray-700 dark:text-gray-200">Loading Appointments…</span>
                </div>
            </div>

            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Patient</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Doctor</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date & Time</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Number</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($appointments as $appointment)
                        @php
                            $patient = $appointment->patient;
                            $doctor = $appointment->doctor;
                            $patientName = $patient ? trim("{$patient->first_name} {$patient->last_name}") : 'Unknown';
                            $doctorName = $doctor ? $doctor->name : '--';

                            $dateDisplay = $appointment->appointment_date
                                ? \Carbon\Carbon::parse($appointment->appointment_date)->toFormattedDateString()
                                : '--';
                            $timeDisplay = $appointment->appointment_time
                                ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A')
                                : '--';

                            $status = $appointment->status;
                            // Updated status color mapping
                            $statusClasses = match (strtolower($status)) {
                                'waiting' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400',
                                'in consultation' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                'canceled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                'completed' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-400',
                                // Default for 'Scheduled' or other unknown statuses
                                default => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400',
                            };
                        @endphp

                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150"
                            wire:key="appointment-{{ $appointment->id }}">
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500">
                                        <x-heroicon-o-user class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $patientName }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">ID:
                                            {{ $patient->patient_uid ?? '--' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-700 dark:text-gray-200">{{ $doctorName }}</div>
                                @if ($doctor && $doctor->department)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $doctor->department->name }}</div>
                                @endif
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                <div class="flex items-center gap-2">
                                    <x-heroicon-o-calendar class="w-4 h-4 text-gray-400" />
                                    <span>{{ $dateDisplay }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <x-heroicon-o-clock class="w-4 h-4 text-gray-400" />
                                    {{-- End time removed as per updated Appointment model --}}
                                    <span>{{ $timeDisplay }}</span>
                                </div>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                <span
                                    class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses }}"
                                    role="status" aria-label="Status: {{ $status }}">
                                    {{ $status }}
                                </span>
                            </td>
 <td class="px-4 py-4 whitespace-nowrap">

              <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                        {{ $appointment->queue_position }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-center">
                                <div class="flex items-center justify-center gap-2">

                                    {{-- Reschedule and Cancel - Available for initial/waiting states --}}
                                    @if (in_array(strtolower($status), ['scheduled', 'waiting']))
                                        <button wire:click="openRescheduleModal({{ $appointment->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium text-yellow-700 bg-yellow-100 hover:bg-yellow-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-yellow-400 dark:bg-yellow-900/40 dark:text-yellow-300 dark:hover:bg-yellow-900/60"
                                            aria-label="Reschedule appointment {{ $appointment->id }}">
                                            <x-heroicon-o-arrow-path-rounded-square class="w-4 h-4" />
                                            Reschedule
                                        </button>

                                        <button wire:click="confirmCancelAppointment({{ $appointment->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 dark:bg-red-900/40 dark:text-red-300 dark:hover:bg-red-900/60"
                                            aria-label="Cancel appointment {{ $appointment->id }}">
                                            <x-heroicon-o-x-mark class="w-4 h-4" />
                                            Cancel
                                        </button>
                                    @endif

                                    {{-- Check In: Moves 'Scheduled' to 'Waiting' --}}
                                    @if (strtolower($status) === 'scheduled')
                                        <button wire:click="confirmAppointment({{ $appointment->id }})"
                                            wire:loading.attr="disabled"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-400 dark:bg-green-900/40 dark:text-green-300 dark:hover:bg-green-900/60"
                                            aria-label="Check-in appointment {{ $appointment->id }}">
                                            <x-heroicon-o-check class="w-4 h-4" />
                                            Check In
                                        </button>
                                    @endif

 {{-- Reinstate: Moves 'Canceled'/'Completed' back to 'Waiting' --}}
                                    @if ($status === 'Completed')
                                        <button
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium text-gray-600 bg-gray-100 dark:bg-gray-900/30 dark:text-gray-400 cursor-not-allowed"
                                            disabled>
                                            <x-heroicon-s-check-circle class="w-4 h-4" />
                                            Completed
                                        </button>
                                    @endif

                                    {{-- Reinstate: Moves 'Canceled'/'Completed' back to 'Waiting' --}}
                                    @if ($status === 'Canceled')
                                        <button wire:click="reinstateAppointment({{ $appointment->id }})"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 dark:bg-indigo-900/40 dark:text-indigo-300 dark:hover:bg-indigo-900/60"
                                            aria-label="Reinstate appointment {{ $appointment->id }}">
                                            <x-heroicon-o-arrow-uturn-left class="w-4 h-4" />
                                            Reinstate
                                        </button>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"
                                class="px-4 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <x-heroicon-o-calendar class="w-16 h-16 text-gray-300 dark:text-gray-600" />
                                    <p class="text-lg font-medium text-gray-700 dark:text-gray-300">No appointments
                                        found</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Try adjusting your filters or
                                        book a new appointment.</p>
                                    <div class="mt-3">
                                        <a href="{{ route('receptionist.book-appointment') }}" wire:navigate
                                            class="inline-flex items-center gap-2 px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400">
                                            <x-heroicon-o-plus class="w-4 h-4" /> Book Appointment
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($appointments->hasPages())
            <div class="p-4 border-t dark:border-gray-700">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>

    {{-- Reschedule modal --}}
    <div x-data="{ open: @entangle('showRescheduleModal') }" x-show="open" x-cloak x-trap.noscroll="open"
        x-on:keydown.escape.window="open = false" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/50" @click="open = false"
            aria-hidden="true"></div>

        <div x-show="open" x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
            x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
            class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-xl w-full z-10 overflow-hidden">
            <div class="p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Reschedule Appointment</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Choose a new date and time for the
                            appointment.</p>
                    </div>

                    <div>
                        <button @click="open = false"
                            class="p-2 rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300"
                            aria-label="Close reschedule modal">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>
                </div>

                {{-- Updated grid to col-2 and removed rescheduleEnd field --}}
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="rescheduleDate"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date</label>
                        <input type="date" id="rescheduleDate" wire:model.defer="rescheduleDate"
                            class="mt-1 form-input w-full rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-200" />
                        @error('rescheduleDate')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="rescheduleStart"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Time</label>
                        <input type="time" id="rescheduleStart" wire:model.defer="rescheduleStart"
                            class="mt-1 form-input w-full rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-200" />
                        @error('rescheduleStart')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- The 'End Time' input was removed from here --}}

                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="open = false"
                        class="px-4 py-2 rounded-md border bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-gray-300">Cancel</button>
                    <button type="button" wire:click="rescheduleAppointmentConfirm" wire:loading.attr="disabled"
                        class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-400 flex items-center">
                        <span wire:loading.remove wire:target="rescheduleAppointmentConfirm">Reschedule</span>
                        <span wire:loading wire:target="rescheduleAppointmentConfirm">Saving...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
