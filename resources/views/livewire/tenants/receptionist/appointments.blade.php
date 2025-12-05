<div class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50/50 p-6 dark:bg-gray-900">
    {{-- Breadcrumbs --}}
     <div class="mb-6 mt-8">
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
                            Appointments</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    {{-- Header Section --}}
    <header class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8">
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center gap-3">
                 Manage Appointments
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Monitor patient flow, manage schedules, and track consultation status.
            </p>
        </div>
        <a href="{{ route('receptionist.book-appointment') }}" wire:navigate
            class="group inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-all duration-200 shadow-lg shadow-indigo-600/20 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
            <x-heroicon-o-plus class="w-5 h-5 transition-transform group-hover:rotate-90" />
            <span>Book Appointment</span>
        </a>
    </header>

    {{-- Filters Card --}}
    <div x-data="{ open: true }" class="mb-8 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/60 dark:border-gray-700 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700 cursor-pointer" @click="open = !open">
            <div class="flex items-center gap-2 text-gray-700 dark:text-gray-200">
                <div class="p-1.5 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400">
                    <x-heroicon-o-funnel class="w-4 h-4" />
                </div>
                <h3 class="font-semibold text-sm">Filter & Search</h3>
            </div>
            <button class="text-gray-400 hover:text-indigo-600 transition-colors">
                <x-heroicon-s-chevron-up x-show="open" class="w-5 h-5" />
                <x-heroicon-s-chevron-down x-show="!open" class="w-5 h-5" />
            </button>
        </div>

        <div x-show="open" x-collapse class="p-6">
            <form class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" onsubmit="return false;">
                {{-- Date Input --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Date</label>
                    <input type="date" wire:model.live="dateFilter"
                        class="block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700/50 dark:border-gray-600 dark:text-white dark:focus:border-indigo-400 transition-shadow duration-200" />
                </div>

                {{-- Doctor Select --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Doctor</label>
                    <select wire:model.live="doctorFilter"
                        class="block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700/50 dark:border-gray-600 dark:text-white transition-shadow duration-200">
                        <option value="">All Doctors</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->name }} {{ $doctor->department ? '('.$doctor->department->name.')' : '' }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Select --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Status</label>
                    <select wire:model.live="statusFilter"
                        class="block w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700/50 dark:border-gray-600 dark:text-white transition-shadow duration-200">
                        <option value="">All Statuses</option>
                        <option value="Scheduled">Scheduled</option>
                        <option value="Waiting">Waiting</option>
                        <option value="In Consultation">In Consultation</option>
                        <option value="Completed">Completed</option>
                        <option value="Canceled">Canceled</option>
                    </select>
                </div>

                {{-- Search Input --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Search Patient</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <x-heroicon-o-magnifying-glass class="w-5 h-5 text-gray-400 group-focus-within:text-indigo-500 transition-colors" />
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="patientSearch"
                            placeholder="Name, ID, or Phone..."
                            class="block w-full pl-10 rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700/50 dark:border-gray-600 dark:text-white transition-shadow duration-200">
                        <div wire:loading wire:target="patientSearch" class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="animate-spin h-4 w-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Filter Actions --}}
            <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button wire:click="resetFilters"
                    class="text-sm font-medium text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    Clear Filters
                </button>
                <button wire:click="$refresh"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 hover:border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:bg-gray-600 transition-all duration-200 shadow-sm">
                    <x-heroicon-o-arrow-path class="w-4 h-4" />
                    Refresh List
                </button>
            </div>
        </div>
    </div>

    {{-- Appointments Table Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/60 dark:border-gray-700 overflow-hidden relative">
        {{-- Table Header Info --}}
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                Appointment List
                <span class="ml-2 inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300">
                    {{ $appointments->total() }}
                </span>
            </h3>
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 px-3 py-1 rounded-lg">
                Showing {{ $appointments->firstItem() }}-{{ $appointments->lastItem() }}
            </div>
        </div>

        {{-- Loading Overlay --}}
        <div wire:loading.flex wire:target="dateFilter, doctorFilter, statusFilter, patientSearch, resetFilters, $refresh, confirmAppointment, confirmCancelAppointment, reinstateAppointment"
            class="absolute inset-0 z-20 items-center justify-center bg-white/60 backdrop-blur-[2px] dark:bg-gray-800/60 transition-opacity">
            <div class="flex items-center gap-3 bg-white dark:bg-gray-900 rounded-xl px-6 py-4 shadow-xl border border-gray-100 dark:border-gray-700">
                <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
                <span class="text-sm font-medium text-gray-800 dark:text-gray-200">Updating...</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-gray-50/80 dark:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold text-gray-500 dark:text-gray-400">Patient</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-gray-500 dark:text-gray-400">Doctor</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-gray-500 dark:text-gray-400">Date & Time</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-gray-500 dark:text-gray-400">Status</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-gray-500 dark:text-gray-400 text-center">Queue</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-gray-500 dark:text-gray-400 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($appointments as $appointment)
                        @php
                            $patient = $appointment->patient;
                            $doctor = $appointment->doctor;
                            $patientName = $patient ? trim("{$patient->first_name} {$patient->last_name}") : 'Unknown';
                            $dateDisplay = $appointment->appointment_date ? \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') : '--';
                            $timeDisplay = $appointment->appointment_time ? \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') : '--';
                            $status = $appointment->status;

                            // Modern Status Pill Logic with Dot
                            $statusConfig = match (strtolower($status)) {
                                'waiting' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'text' => 'text-amber-700 dark:text-amber-400', 'dot' => 'bg-amber-500'],
                                'in consultation' => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'text' => 'text-blue-700 dark:text-blue-400', 'dot' => 'bg-blue-500 animate-pulse'],
                                'canceled' => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'text' => 'text-red-700 dark:text-red-400', 'dot' => 'bg-red-500'],
                                'completed' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-700 dark:text-emerald-400', 'dot' => 'bg-emerald-500'],
                                default => ['bg' => 'bg-slate-100 dark:bg-slate-700/50', 'text' => 'text-slate-700 dark:text-slate-300', 'dot' => 'bg-slate-500'],
                            };
                        @endphp

                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition-colors duration-150" wire:key="appointment-{{ $appointment->id }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold text-xs ring-2 ring-white dark:ring-gray-800">
                                        {{ substr($patientName, 0, 2) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-900 dark:text-white">{{ $patientName }}</div>
                                        <div class="text-xs text-gray-500 font-mono mt-0.5">{{ $patient->patient_uid ?? '#' }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $doctor ? $doctor->name : '--' }}</div>
                                @if ($doctor && $doctor->department)
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $doctor->department->name }}</div>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $dateDisplay }}</span>
                                    <span class="text-xs text-gray-500 mt-0.5">{{ $timeDisplay }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                    {{ $status }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($appointment->queue_position)
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-sm font-bold text-gray-700 dark:text-gray-300">
                                        {{ $appointment->queue_position }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity duration-200">
                                    {{-- Action: Check In --}}
                                    @if (strtolower($status) === 'scheduled')
                                        <button wire:click="confirmAppointment({{ $appointment->id }})" title="Check In"
                                            class="p-2 rounded-lg text-emerald-600 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:hover:bg-emerald-900/40 transition-colors">
                                            <x-heroicon-o-check class="w-5 h-5" />
                                        </button>
                                    @endif

                                    {{-- Action: Reschedule --}}
                                    @if (in_array(strtolower($status), ['scheduled', 'waiting']))
                                        <button wire:click="openRescheduleModal({{ $appointment->id }})" title="Reschedule"
                                            class="p-2 rounded-lg text-amber-600 bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/20 dark:hover:bg-amber-900/40 transition-colors">
                                            <x-heroicon-o-calendar-days class="w-5 h-5" />
                                        </button>

                                        <button wire:click="confirmCancelAppointment({{ $appointment->id }})" title="Cancel"
                                            class="p-2 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40 transition-colors">
                                            <x-heroicon-o-x-mark class="w-5 h-5" />
                                        </button>
                                    @endif

                                    {{-- Action: Reinstate --}}
                                    @if ($status === 'Canceled')
                                        <button wire:click="reinstateAppointment({{ $appointment->id }})" title="Reinstate"
                                            class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/20 dark:hover:bg-indigo-900/40 transition-colors">
                                            <x-heroicon-o-arrow-uturn-left class="w-4 h-4" />
                                            Restore
                                        </button>
                                    @endif

                                     @if ($status === 'Completed')
                                        <div class="p-2 text-emerald-500">
                                            <x-heroicon-s-check-circle class="w-5 h-5" />
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                        <x-heroicon-o-clipboard-document-list class="w-8 h-8 text-gray-400" />
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">No appointments found</h3>
                                    <p class="text-gray-500 dark:text-gray-400 max-w-sm mt-1">
                                        We couldn't find any appointments matching your criteria. Try adjusting your filters.
                                    </p>
                                    <button wire:click="resetFilters" class="mt-4 text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                                        Clear all filters
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($appointments->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                {{ $appointments->links() }}
            </div>
        @endif
    </div>

    {{-- Reschedule Modal --}}
    <div x-data="{ open: @entangle('showRescheduleModal') }" x-show="open" x-cloak class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <div x-show="open"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="open"
                    x-trap.noscroll="open"
                    @click.away="open = false"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                    <div class="bg-white dark:bg-gray-800 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 dark:bg-indigo-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <x-heroicon-o-clock class="h-6 w-6 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">Reschedule Appointment</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Select a new date and time for the patient's appointment.</p>

                                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="space-y-1">
                                            <label for="rescheduleDate" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">New Date</label>
                                            <input type="date" id="rescheduleDate" wire:model.defer="rescheduleDate"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                                            @error('rescheduleDate')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="space-y-1">
                                            <label for="rescheduleStart" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase">New Time</label>
                                            <input type="time" id="rescheduleStart" wire:model.defer="rescheduleStart"
                                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" />
                                            @error('rescheduleStart')
                                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700/30 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" wire:click="rescheduleAppointmentConfirm" wire:loading.attr="disabled"
                            class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto disabled:opacity-50 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="rescheduleAppointmentConfirm">Confirm Change</span>
                            <span wire:loading wire:target="rescheduleAppointmentConfirm">Saving...</span>
                        </button>
                        <button type="button" @click="open = false"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-200 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
