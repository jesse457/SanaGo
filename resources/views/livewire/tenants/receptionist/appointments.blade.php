<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4 md:flex md:items-center md:justify-between space-y-3 md:space-y-0">

                {{-- Title & Breadcrumbs --}}
                <div class="flex-1 min-w-0">
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-1" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('receptionist.dashboard') }}" wire:navigate
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                   Home
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">Appointments</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                        Manage Appointments
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Monitor patient flow, manage schedules, and track consultation status.
                    </p>
                </div>

                {{-- Action Toolbar --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('receptionist.book-appointment') }}" wire:navigate
                        class="group relative inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-tr from-pink-500 to-rose-500 text-white text-sm font-semibold rounded-xl shadow-md hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all overflow-hidden dark:focus:ring-offset-gray-900">
                        <div
                            class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                        </div>
                        <x-heroicon-o-plus class="w-5 h-5" />
                        <span>Book Appointment</span>
                    </a>
                </div>
            </div>

            {{-- Filters Bar (Collapsible/Integrated) --}}
            <div x-data="{ showFilters: false }"
                class="px-4 sm:px-6 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row gap-3 items-center justify-between">
                    {{-- Search --}}
                    <div class="relative w-full md:max-w-xs group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-m-magnifying-glass
                                class="h-4 w-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                        </div>
                        <input type="text" wire:model.live.debounce.400ms="patientSearch"
                            class="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
                            placeholder="Search Patient (Name, ID)...">
                    </div>

                    {{-- Toggle Filters Button (Mobile/Desktop) --}}
                    <button @click="showFilters = !showFilters"
                        class="flex md:hidden w-full items-center justify-center gap-2 px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200">
                        <x-heroicon-o-funnel class="w-4 h-4" /> Filters
                    </button>

                    {{-- Filter Inputs (Desktop: Always Visible / Mobile: Collapsible) --}}
                    <div :class="{'hidden': !showFilters, 'flex': showFilters}" class="hidden md:flex flex-col md:flex-row gap-3 w-full md:w-auto mt-3 md:mt-0">
                        {{-- Date --}}
                        <input type="date" wire:model.live="dateFilter"
                             class="block w-full md:w-auto border border-gray-300 dark:border-gray-600 rounded-lg py-2 px-3 leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">

                        {{-- Doctor --}}
                        <select wire:model.live="doctorFilter"
                            class="block w-full md:w-40 border border-gray-300 dark:border-gray-600 rounded-lg py-2 pl-3 pr-8 leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Doctors</option>
                            @foreach ($doctors as $doctor)
                                <option value="{{ $doctor->id }}">{{ $doctor->name }}</option>
                            @endforeach
                        </select>

                        {{-- Status --}}
                        <select wire:model.live="statusFilter"
                            class="block w-full md:w-40 border border-gray-300 dark:border-gray-600 rounded-lg py-2 pl-3 pr-8 leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">All Statuses</option>
                            <option value="Scheduled">Scheduled</option>
                            <option value="Waiting">Waiting</option>
                            <option value="In Consultation">In Consultation</option>
                            <option value="Completed">Completed</option>
                            <option value="Canceled">Canceled</option>
                        </select>
                    </div>
                </div>

                {{-- Active Filters Badges --}}
                @if ($dateFilter || $doctorFilter || $statusFilter || $patientSearch)
                    <div class="flex items-center justify-end mt-3">
                        <button wire:click="resetFilters"
                            class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium hover:underline transition-colors flex items-center gap-1">
                            <x-heroicon-m-trash class="w-3 h-3" /> Clear Filters
                        </button>
                    </div>
                @endif
            </div>
        </header>

        {{-- Content Area --}}
        <div class="relative min-h-[400px] p-4 sm:p-6 pb-20">

            {{-- Loading Overlay --}}
            <div wire:loading.flex wire:target="dateFilter, doctorFilter, statusFilter, patientSearch, resetFilters, confirmAppointment, confirmCancelAppointment, reinstateAppointment"
                class="absolute inset-0 bg-white/60 dark:bg-gray-900/60 backdrop-blur-[2px] z-20 flex items-start justify-center rounded-2xl pt-20 transition-all">
                <div
                    class="flex items-center gap-3 px-6 py-3 bg-white dark:bg-gray-800 rounded-full shadow-xl border border-slate-100 dark:border-gray-700 animate-bounce">
                    <x-heroicon-o-arrow-path class="animate-spin h-5 w-5 text-blue-600" />
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Updating...</span>
                </div>
            </div>

            {{-- DESKTOP: Table View --}}
            <div
                class="hidden md:block bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-gray-800">
                        <thead class="bg-slate-50 dark:bg-gray-950">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Patient</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Doctor</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date & Time</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">Queue</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                            @forelse ($appointments as $appointment)
                                @php
                                    $patient = $appointment->patient;
                                    $doctor = $appointment->doctor;
                                    $status = strtolower($appointment->status);

                                    // Status Styles (Consistent with reference)
                                    $statusStyles = match ($status) {
                                        'waiting' => 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800',
                                        'in consultation' => 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800 animate-pulse',
                                        'canceled' => 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-gray-800 dark:text-slate-400 dark:border-gray-700',
                                        'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800',
                                        default => 'bg-slate-50 text-slate-700 border-slate-100 dark:bg-gray-800 dark:text-slate-300 dark:border-gray-700',
                                    };
                                @endphp

                                <tr wire:key="row-{{ $appointment->id }}"
                                    class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-sm font-bold text-slate-500 dark:text-slate-300 ring-2 ring-white dark:ring-gray-800 shadow-sm">
                                                    {{ substr($patient->first_name, 0, 1) }}
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-pink-600 dark:group-hover:pink-blue-400 transition-colors">
                                                    {{ $patient->first_name }} {{ $patient->last_name }}
                                                </div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400 font-mono">
                                                    {{ $patient->patient_uid ?? '#' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $doctor ? $doctor->name : '--' }}</div>
                                        @if($doctor && $doctor->department)
                                            <div class="text-xs text-slate-400">{{ $doctor->department->name }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-900 dark:text-white">
                                                {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d, Y') }}
                                            </span>
                                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold capitalize border shadow-sm {{ $statusStyles }}">
                                            {{ $appointment->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-nowrap">
                                         @if($appointment->queue_position)
                                            <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 dark:bg-gray-800 text-sm font-bold text-slate-700 dark:text-slate-300 ring-1 ring-slate-200 dark:ring-gray-700">
                                                {{ $appointment->queue_position }}
                                            </div>
                                        @else
                                            <span class="text-slate-300 dark:text-slate-600">•</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            @if ($status === 'scheduled')
                                                <button wire:click="confirmAppointment({{ $appointment->id }})"
                                                    class="p-2 text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 rounded-lg transition-colors" title="Check In">
                                                    <x-heroicon-s-check class="w-4 h-4" />
                                                </button>
                                            @endif

                                            @if (in_array($status, ['scheduled', 'waiting']))
                                                <button wire:click="openRescheduleModal({{ $appointment->id }})"
                                                    class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors" title="Reschedule">
                                                    <x-heroicon-s-calendar-days class="w-4 h-4" />
                                                </button>
                                                <button wire:click="confirmCancelAppointment({{ $appointment->id }})"
                                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Cancel">
                                                    <x-heroicon-s-x-mark class="w-4 h-4" />
                                                </button>
                                            @endif

                                            @if ($status === 'canceled')
                                                <button wire:click="reinstateAppointment({{ $appointment->id }})"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg border border-indigo-200 hover:bg-indigo-100 dark:bg-indigo-900/20 dark:text-indigo-400 dark:border-indigo-800 transition-colors shadow-sm">
                                                    <x-heroicon-s-arrow-uturn-left class="w-3 h-3" /> Restore
                                                </button>
                                            @endif

                                            @if ($status === 'completed')
                                                <span class="text-emerald-500"><x-heroicon-s-check-circle class="w-5 h-5"/></span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="w-16 h-16 bg-slate-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-gray-700">
                                            <x-heroicon-o-calendar class="h-8 w-8 text-slate-400" />
                                        </div>
                                        <h3 class="text-base font-bold text-slate-900 dark:text-white">No appointments found</h3>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                            Try adjusting your filters or search terms.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MOBILE: Card View (Hidden on Desktop) --}}
            <div class="md:hidden space-y-4">
                @forelse ($appointments as $appointment)
                    <div wire:key="mobile-card-{{ $appointment->id }}"
                        class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-slate-200 dark:border-gray-800 p-4 active:scale-[0.99] transition-transform">

                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-slate-100 dark:bg-gray-800 flex items-center justify-center text-sm font-bold text-slate-600 dark:text-slate-300">
                                    {{ substr($appointment->patient->first_name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ $appointment->patient->first_name }} {{ $appointment->patient->last_name }}
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $appointment->patient->patient_uid }}</p>
                                </div>
                            </div>
                            @php
                                $status = strtolower($appointment->status);
                                $statusStyles = match ($status) {
                                    'waiting' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'in consultation' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    'canceled' => 'bg-slate-100 text-slate-600 border-slate-200',
                                    'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    default => 'bg-slate-50 text-slate-700 border-slate-100',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold border capitalize {{ $statusStyles }}">
                                {{ $appointment->status }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                            <div class="bg-slate-50 dark:bg-gray-800/50 p-2 rounded-lg">
                                <span class="block text-slate-400 uppercase text-[10px] font-bold">Doctor</span>
                                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $appointment->doctor->name ?? '--' }}</span>
                            </div>
                            <div class="bg-slate-50 dark:bg-gray-800/50 p-2 rounded-lg">
                                <span class="block text-slate-400 uppercase text-[10px] font-bold">Time</span>
                                <span class="font-medium text-slate-700 dark:text-slate-300">
                                    {{ \Carbon\Carbon::parse($appointment->appointment_date)->format('M d') }},
                                    {{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-t border-slate-100 dark:border-gray-800 pt-3">
                            <div class="text-xs font-bold text-slate-500">
                                Queue: {{ $appointment->queue_position ?? '-' }}
                            </div>
                            <div class="flex gap-1">
                                @if (strtolower($status) === 'scheduled')
                                    <button wire:click="confirmAppointment({{ $appointment->id }})" class="p-2 text-emerald-600 bg-emerald-50 rounded-lg">
                                        <x-heroicon-o-check class="w-4 h-4" />
                                    </button>
                                @endif
                                @if (in_array(strtolower($status), ['scheduled', 'waiting']))
                                    <button wire:click="openRescheduleModal({{ $appointment->id }})" class="p-2 text-blue-600 bg-blue-50 rounded-lg">
                                        <x-heroicon-o-calendar-days class="w-4 h-4" />
                                    </button>
                                    <button wire:click="confirmCancelAppointment({{ $appointment->id }})" class="p-2 text-red-600 bg-red-50 rounded-lg">
                                        <x-heroicon-o-x-mark class="w-4 h-4" />
                                    </button>
                                @endif
                                @if (strtolower($status) === 'canceled')
                                    <button wire:click="reinstateAppointment({{ $appointment->id }})" class="p-2 text-indigo-600 bg-indigo-50 rounded-lg">
                                        <x-heroicon-o-arrow-uturn-left class="w-4 h-4" />
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-slate-300 dark:border-gray-700">
                        <x-heroicon-o-calendar class="mx-auto h-12 w-12 text-slate-300" />
                        <h3 class="mt-2 text-sm font-bold text-slate-900 dark:text-white">No appointments found</h3>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($appointments->hasPages())
                <div class="mt-8">
                    {{ $appointments->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL --}}
    <div x-data="{ open: @entangle('showRescheduleModal') }"
        x-init="$watch('open', value => { if (value) { document.body.style.overflow = 'hidden'; } else { document.body.style.overflow = ''; } })"
        style="display: none;"
        x-show="open">

        <template x-teleport="body">
            <div x-show="open" class="relative z-50" role="dialog" aria-modal="true">
                {{-- Backdrop --}}
                <div x-show="open"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity"
                     @click="open = false"></div>

                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div x-show="open"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                             class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all sm:w-full sm:max-w-lg border border-slate-100 dark:border-gray-800">

                            {{-- Modal Header --}}
                            <div class="bg-white dark:bg-gray-900 px-6 py-4 border-b border-slate-100 dark:border-gray-800 flex items-center justify-between sticky top-0 z-10">
                                <h3 class="text-lg font-bold leading-6 text-slate-900 dark:text-white">
                                    Reschedule Appointment
                                </h3>
                                <button @click="open = false"
                                    class="rounded-lg bg-slate-50 dark:bg-gray-800 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400">
                                    <x-heroicon-o-x-mark class="h-5 w-5" />
                                </button>
                            </div>

                            <div class="p-6 sm:p-8 space-y-6">
                                <p class="text-sm text-slate-500 dark:text-slate-400">
                                    Select a new date and time for the patient's appointment.
                                </p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div class="space-y-1.5">
                                        <label for="rescheduleDate" class="block text-sm font-bold text-slate-700 dark:text-slate-300">New Date</label>
                                        <div class="relative">
                                            <input type="date" id="rescheduleDate" wire:model.defer="rescheduleDate"
                                                class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:text-white py-2.5" />
                                        </div>
                                        @error('rescheduleDate') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="space-y-1.5">
                                        <label for="rescheduleStart" class="block text-sm font-bold text-slate-700 dark:text-slate-300">New Time</label>
                                        <div class="relative">
                                            <input type="time" id="rescheduleStart" wire:model.defer="rescheduleStart"
                                                class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:text-white py-2.5" />
                                        </div>
                                        @error('rescheduleStart') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="px-6 py-4 bg-slate-50 dark:bg-gray-900/50 flex flex-row-reverse gap-3 rounded-b-2xl border-t border-slate-100 dark:border-gray-800">
                                <button type="button" wire:click="rescheduleAppointmentConfirm" wire:loading.attr="disabled"
                                    class="inline-flex w-full sm:w-auto justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-700 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                                    <span wire:loading.remove wire:target="rescheduleAppointmentConfirm">Confirm Change</span>
                                    <span wire:loading wire:target="rescheduleAppointmentConfirm" class="flex items-center gap-2">
                                        <x-heroicon-o-arrow-path class="animate-spin h-4 w-4" /> Saving...
                                    </span>
                                </button>
                                <button type="button" @click="open = false"
                                    class="inline-flex w-full sm:w-auto justify-center rounded-xl bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-gray-700 hover:bg-slate-50 dark:hover:bg-gray-700 transition-all">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</main>
