<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4">
                <div class="flex flex-col gap-2">
                    {{-- Breadcrumbs --}}
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('receptionist.dashboard') }}" wire:navigate
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    {{ __('receptionist.home') }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <a href="{{ route('receptionist.appointments') }}" wire:navigate
                                        class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        Appointments
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">Book Appointment</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            Book Appointment
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                            Search for a patient and assign them to a doctor's daily queue.
                        </p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="p-4 sm:p-6 pb-20">
            <div
                class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                <form wire:submit="bookAppointment" autocomplete="off">
                    <div class="p-6 md:p-8 space-y-10">

                        {{-- Section 1: Patient Selection --}}
                        <section class="space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-800 pb-3">
                                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-3">
                                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-blue-600 text-sm font-bold dark:bg-blue-900/30 dark:text-blue-400">1</div>
                                    Select Patient
                                </h2>
                                @error('selectedPatientId')
                                    <span class="text-xs text-red-500 font-bold bg-red-50 dark:bg-red-900/20 px-2 py-1 rounded-md">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="relative">
                                {{-- State: No Patient Selected --}}
                                @if (!$selectedPatientId)
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <x-heroicon-o-magnifying-glass class="h-5 w-5 text-slate-400" />
                                        </div>
                                        <input type="text"
                                            wire:model.live.debounce.300ms="patientSearch"
                                            class="block w-full pl-11 pr-4 py-3 bg-white border border-slate-300 rounded-xl text-slate-900 text-sm shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white dark:placeholder-gray-500 transition-all"
                                            placeholder="Search by name, ID (e.g., P-1234), or phone number..."
                                        />

                                        {{-- Loading Indicator --}}
                                        <div wire:loading wire:target="patientSearch" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                            <x-heroicon-o-arrow-path class="animate-spin h-5 w-5 text-blue-500" />
                                        </div>
                                    </div>

                                    {{-- Search Results Dropdown --}}
                                    @if (strlen($patientSearch) >= 2)
                                        <div class="absolute z-20 mt-2 w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-slate-100 dark:border-gray-700 overflow-hidden max-h-80 overflow-y-auto ring-1 ring-black ring-opacity-5">
                                            @forelse ($foundPatients as $patient)
                                                <button type="button"
                                                    wire:click="selectPatient({{ $patient->id }}, '{{ addslashes($patient->first_name) }} {{ addslashes($patient->last_name) }}')"
                                                    class="w-full text-left px-4 py-3.5 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors border-b last:border-0 border-slate-50 dark:border-gray-700/50 group flex items-center justify-between">
                                                    <div>
                                                        <p class="font-bold text-slate-900 dark:text-white group-hover:text-blue-700 dark:group-hover:text-blue-400">
                                                            {{ $patient->first_name }} {{ $patient->last_name }}
                                                        </p>
                                                        <p class="text-xs text-slate-500 dark:text-gray-400 mt-0.5 flex items-center gap-2">
                                                            <span class="bg-slate-100 dark:bg-gray-700 px-1.5 py-0.5 rounded text-xs font-mono">{{ $patient->patient_uid ?? 'N/A' }}</span>
                                                            <span>•</span>
                                                            <span>Ph: {{ $patient->phone ?? '--' }}</span>
                                                        </p>
                                                    </div>
                                                    <x-heroicon-s-plus-circle class="w-5 h-5 text-slate-300 group-hover:text-blue-500 transition-colors" />
                                                </button>
                                            @empty
                                                <div class="px-4 py-8 text-center">
                                                    <div class="w-12 h-12 bg-slate-50 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                                        <x-heroicon-o-user-minus class="h-6 w-6 text-slate-400 dark:text-gray-500" />
                                                    </div>
                                                    <p class="text-sm font-medium text-slate-900 dark:text-white">No patients found</p>
                                                    <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">No matches for "{{ $patientSearch }}"</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    @endif
                                @else
                                    {{-- State: Patient Selected (Card View) --}}
                                    <div class="relative bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/30 rounded-xl p-4 flex items-center justify-between animate-fade-in-down">
                                        <div class="flex items-center gap-4">
                                            <div class="h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-blue-600 dark:text-blue-300 ring-4 ring-white dark:ring-gray-800">
                                                <x-heroicon-s-user class="w-6 h-6" />
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider mb-0.5">Selected Patient</p>
                                                <p class="text-lg font-bold text-slate-900 dark:text-white">{{ $selectedPatientName }}</p>
                                            </div>
                                        </div>
                                        <button type="button" wire:click="clearSelectedPatient"
                                            class="p-2 text-slate-400 hover:text-red-500 bg-white dark:bg-gray-800 hover:bg-red-50 dark:hover:bg-red-900/20 border border-slate-200 dark:border-gray-700 rounded-lg transition-all shadow-sm"
                                            title="Remove patient">
                                            <x-heroicon-o-x-mark class="w-5 h-5" />
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </section>

                        {{-- Section 2: Doctor Selection --}}
                        <section class="space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-800 pb-3">
                                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-3">
                                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-blue-600 text-sm font-bold dark:bg-blue-900/30 dark:text-blue-400">2</div>
                                    Select Doctor
                                </h2>
                                @error('doctorId')
                                    <span class="text-xs text-red-500 font-bold bg-red-50 dark:bg-red-900/20 px-2 py-1 rounded-md">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @forelse ($doctors as $doc)
                                    @php $isSelected = $doctorId == $doc['id']; @endphp
                                    <button type="button"
                                        wire:click="$set('doctorId', {{ $doc['id'] }})"
                                        class="relative group p-4 text-left rounded-xl border transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900
                                        {{ $isSelected
                                            ? 'bg-blue-600 border-blue-600 shadow-lg shadow-blue-500/30 transform scale-[1.02]'
                                            : 'bg-white border-slate-200 hover:border-blue-300 hover:shadow-md dark:bg-gray-800 dark:border-gray-700 dark:hover:border-blue-500'
                                        }}">

                                        <div class="flex items-start justify-between">
                                            <div class="pr-2">
                                                <p class="font-bold text-sm {{ $isSelected ? 'text-white' : 'text-slate-900 dark:text-white' }}">
                                                    {{ $doc['name'] }}
                                                </p>
                                                <p class="text-xs mt-1 font-medium {{ $isSelected ? 'text-blue-100' : 'text-slate-500 dark:text-slate-400' }}">
                                                    {{ $doc['department'] ?? 'General' }}
                                                </p>
                                            </div>

                                            <div class="h-6 w-6 rounded-full border-2 flex items-center justify-center transition-colors
                                                {{ $isSelected ? 'border-white bg-white/20 text-white' : 'border-slate-300 text-transparent dark:border-gray-600 group-hover:border-blue-400' }}">
                                                <x-heroicon-s-check class="w-3.5 h-3.5 {{ $isSelected ? '' : 'hidden' }}" />
                                            </div>
                                        </div>
                                    </button>
                                @empty
                                    <div class="col-span-full py-12 text-center border-2 border-dashed border-slate-200 dark:border-gray-700 rounded-xl bg-slate-50 dark:bg-gray-800/50">
                                        <x-heroicon-o-users class="mx-auto h-10 w-10 text-slate-300 dark:text-gray-600 mb-2" />
                                        <p class="text-sm font-medium text-slate-500 dark:text-gray-400">No doctors available for booking.</p>
                                    </div>
                                @endforelse
                            </div>
                        </section>

                        {{-- Section 3: Time & Details --}}
                        <section class="space-y-6">
                            <div class="flex items-center justify-between border-b border-slate-100 dark:border-gray-800 pb-3">
                                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-3">
                                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-50 text-blue-600 text-sm font-bold dark:bg-blue-900/30 dark:text-blue-400">3</div>
                                    Appointment Details
                                </h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Date --}}
                                <div class="space-y-1.5">
                                    <label for="appointmentDate" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Date</label>
                                    <input type="date" id="appointmentDate" wire:model.live="appointmentDate" min="{{ now()->toDateString() }}"
                                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm py-2.5 px-3">
                                    @error('appointmentDate') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                </div>

                                {{-- Time --}}
                                <div class="space-y-1.5">
                                    <label for="appointmentTime" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Time (Approx)</label>
                                    <input type="time" id="appointmentTime" wire:model.live="appointmentTime"
                                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm py-2.5 px-3">
                                    @error('appointmentTime') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                </div>

                                {{-- Reason --}}
                                <div class="md:col-span-2 space-y-1.5">
                                    <label for="reasonForVisit" class="block text-sm font-bold text-slate-700 dark:text-slate-300">
                                        Reason for Visit <span class="text-slate-400 font-normal ml-1 text-xs">(Optional)</span>
                                    </label>
                                    <input type="text" id="reasonForVisit" wire:model="reasonForVisit" placeholder="e.g., Fever, Regular Checkup"
                                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm py-2.5 px-3">
                                    @error('reasonForVisit') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                </div>

                                {{-- Price --}}
                                <div class="space-y-1.5">
                                    <label for="price" class="block text-sm font-bold text-slate-700 dark:text-slate-300">
                                        Consultation Fee <span class="text-slate-400 font-normal ml-1 text-xs">(Optional)</span>
                                    </label>
                                    <div class="relative rounded-xl shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-slate-500 sm:text-sm font-bold">$</span>
                                        </div>
                                        <input type="number" id="price" wire:model="price" min="0" step="1" placeholder="0.00"
                                            class="block w-full rounded-xl border-slate-300 pl-7 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-white sm:text-sm py-2.5 px-3">
                                    </div>
                                    @error('price') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </section>

                    </div>

                    {{-- Footer / Actions --}}
                    <div class="bg-slate-50 dark:bg-gray-900/50 px-6 py-5 flex items-center justify-between border-t border-slate-100 dark:border-gray-800">
                        <button type="button" wire:click="resetForm"
                            class="text-sm font-bold text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white px-4 py-2 rounded-lg hover:bg-slate-200/50 dark:hover:bg-gray-700 transition-colors">
                            Reset Form
                        </button>

                        <button type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl shadow-md text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all disabled:opacity-70 disabled:cursor-not-allowed">

                            <span wire:loading.remove wire:target="bookAppointment" class="flex items-center gap-2">
                                <x-heroicon-s-clipboard-document-check class="w-5 h-5" />
                                Confirm Booking
                            </span>

                            <span wire:loading wire:target="bookAppointment" class="flex items-center gap-2">
                                <x-heroicon-o-arrow-path class="animate-spin h-5 w-5" />
                                Processing...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
