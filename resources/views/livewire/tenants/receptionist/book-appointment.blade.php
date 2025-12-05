<main class="w-full overflow-x-hidden overflow-y-auto bg-gray-50/50 p-6 dark:bg-gray-900">
    {{-- Mobile hamburger (Only visible on small screens) --}}
    <button @click="open = true"
        class="lg:hidden p-2 rounded-lg text-gray-500 bg-white shadow-sm border border-gray-200 hover:bg-gray-50 mb-6 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors"
        aria-label="Open menu">
        <x-heroicon-o-bars-3 class="w-6 h-6" />
    </button>

    <div class=" mx-auto">
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
                          <a href="{{ route('receptionist.appointments') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-300 transition-colors duration-150">
                          Appointments</a>
                    </div>
                </li>
                 <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm  text-gray-900 md:ms-2 dark:text-gray-400">
                          Book  Appointment</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                Book Appointment
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                Search for a patient and assign them to a doctor's daily queue.
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/60 dark:border-gray-700 overflow-hidden">
            <form wire:submit="bookAppointment" autocomplete="off">
                <div class="p-6 md:p-8 space-y-8">

                    {{-- Section 1: Patient Selection --}}
                    <section class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 text-xs font-bold dark:bg-indigo-900/50 dark:text-indigo-400">1</span>
                                Select Patient
                            </h2>
                            @error('selectedPatientId')
                                <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="relative">
                            {{-- State: No Patient Selected --}}
                            @if (!$selectedPatientId)
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <input type="text"
                                        wire:model.live.debounce.300ms="patientSearch"
                                        class="block w-full pl-11 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl text-gray-900 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-700/50 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 transition-all"
                                        placeholder="Search by name, ID (e.g., P-1234), or phone number..."
                                    />

                                    {{-- Loading Indicator --}}
                                    <div wire:loading wire:target="patientSearch" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                        <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>

                                {{-- Search Results Dropdown --}}
                                @if (strlen($patientSearch) >= 2)
                                    <div class="absolute z-20 mt-2 w-full bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden max-h-80 overflow-y-auto">
                                        @forelse ($foundPatients as $patient)
                                            <button type="button"
                                                wire:click="selectPatient({{ $patient->id }}, '{{ addslashes($patient->first_name) }} {{ addslashes($patient->last_name) }}')"
                                                class="w-full text-left px-4 py-3 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition-colors border-b last:border-0 border-gray-50 dark:border-gray-700/50 group">
                                                <div class="flex justify-between items-center">
                                                    <div>
                                                        <p class="font-medium text-gray-900 dark:text-white group-hover:text-indigo-700 dark:group-hover:text-indigo-400">
                                                            {{ $patient->first_name }} {{ $patient->last_name }}
                                                        </p>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                            ID: {{ $patient->patient_uid ?? 'N/A' }} • Ph: {{ $patient->phone ?? '--' }}
                                                        </p>
                                                    </div>
                                                    <x-heroicon-o-chevron-right class="w-4 h-4 text-gray-300 group-hover:text-indigo-400" />
                                                </div>
                                            </button>
                                        @empty
                                            <div class="px-4 py-8 text-center">
                                                <x-heroicon-o-user-minus class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600" />
                                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No patients found matching "{{ $patientSearch }}"</p>
                                                {{-- Optional: Link to create new patient --}}
                                            </div>
                                        @endforelse
                                    </div>
                                @endif
                            @else
                                {{-- State: Patient Selected (Card View) --}}
                                <div class="relative bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-xl p-4 flex items-center justify-between animate-fade-in-down">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-800 flex items-center justify-center text-indigo-600 dark:text-indigo-300">
                                            <x-heroicon-s-user class="w-6 h-6" />
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-indigo-900 dark:text-indigo-200">Selected Patient</p>
                                            <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $selectedPatientName }}</p>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="clearSelectedPatient"
                                        class="p-2 text-gray-400 hover:text-red-500 hover:bg-white dark:hover:bg-gray-800 rounded-lg transition-all"
                                        title="Remove patient">
                                        <x-heroicon-o-x-mark class="w-5 h-5" />
                                    </button>
                                </div>
                            @endif
                        </div>
                    </section>

                    <hr class="border-gray-100 dark:border-gray-700">

                    {{-- Section 2: Doctor Selection --}}
                    <section class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 text-xs font-bold dark:bg-indigo-900/50 dark:text-indigo-400">2</span>
                                Select Doctor
                            </h2>
                            @error('doctorId')
                                <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @forelse ($doctors as $doc)
                                @php $isSelected = $doctorId == $doc['id']; @endphp
                                <button type="button"
                                    wire:click="$set('doctorId', {{ $doc['id'] }})"
                                    class="relative group p-4 text-left rounded-xl border transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800
                                    {{ $isSelected
                                        ? 'bg-indigo-600 border-indigo-600 shadow-md transform scale-[1.02]'
                                        : 'bg-white border-gray-200 hover:border-indigo-300 hover:shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:hover:border-indigo-500'
                                    }}">

                                    <div class="flex items-start justify-between">
                                        <div class="pr-2">
                                            <p class="font-bold {{ $isSelected ? 'text-white' : 'text-gray-900 dark:text-white' }}">
                                                {{ $doc['name'] }}
                                            </p>
                                            <p class="text-xs mt-1 {{ $isSelected ? 'text-indigo-100' : 'text-gray-500 dark:text-gray-400' }}">
                                                {{ $doc['department'] ?? 'General' }}
                                            </p>
                                        </div>

                                        <div class="h-6 w-6 rounded-full border-2 flex items-center justify-center
                                            {{ $isSelected ? 'border-white bg-white/20 text-white' : 'border-gray-300 text-transparent dark:border-gray-600' }}">
                                            <x-heroicon-s-check class="w-4 h-4 {{ $isSelected ? '' : 'hidden' }}" />
                                        </div>
                                    </div>
                                </button>
                            @empty
                                <div class="col-span-full py-8 text-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-800/50">
                                    <p class="text-gray-500 dark:text-gray-400">No doctors available for booking.</p>
                                </div>
                            @endforelse
                        </div>
                    </section>

                    <hr class="border-gray-100 dark:border-gray-700">

                    {{-- Section 3: Time & Details --}}
                    <section class="space-y-6">
                         <h2 class="text-base font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 text-xs font-bold dark:bg-indigo-900/50 dark:text-indigo-400">3</span>
                            Appointment Details
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Date --}}
                            <div>
                                <label for="appointmentDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                                <input type="date" id="appointmentDate" wire:model.live="appointmentDate" min="{{ now()->toDateString() }}"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700/50 dark:border-gray-600 dark:text-white sm:text-sm p-2.5">
                                @error('appointmentDate') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            {{-- Time --}}
                            <div>
                                <label for="appointmentTime" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Time (Approx)</label>
                                <input type="time" id="appointmentTime" wire:model.live="appointmentTime"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700/50 dark:border-gray-600 dark:text-white sm:text-sm p-2.5">
                                @error('appointmentTime') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            {{-- Reason --}}
                            <div class="md:col-span-2">
                                <label for="reasonForVisit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason for Visit <span class="text-gray-400 font-normal">(Optional)</span></label>
                                <input type="text" id="reasonForVisit" wire:model="reasonForVisit" placeholder="e.g., Fever, Regular Checkup"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700/50 dark:border-gray-600 dark:text-white sm:text-sm p-2.5">
                                @error('reasonForVisit') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>

                            {{-- Price --}}
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Consultation Fee <span class="text-gray-400 font-normal">(Optional)</span></label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" id="price" wire:model="price" min="0" step="1" placeholder="0.00"
                                        class="block w-full rounded-lg border-gray-300 pl-7 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700/50 dark:border-gray-600 dark:text-white sm:text-sm p-2.5">
                                </div>
                                @error('price') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </section>

                </div>

                {{-- Footer / Actions --}}
                <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 flex items-center justify-between border-t border-gray-100 dark:border-gray-700">
                    <button type="button" wire:click="resetForm"
                        class="text-sm font-medium text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-white px-4 py-2 rounded-lg hover:bg-gray-200/50 dark:hover:bg-gray-700 transition-colors">
                        Reset Form
                    </button>

                    <button type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-semibold rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all disabled:opacity-70 disabled:cursor-not-allowed">

                        <span wire:loading.remove wire:target="bookAppointment" class="flex items-center gap-2">
                            <x-heroicon-s-clipboard-document-check class="w-5 h-5" />
                            Confirm Booking
                        </span>

                        <span wire:loading wire:target="bookAppointment" class="flex items-center gap-2">
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
</main>
