<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 lg:ml-64 p-6 dark:bg-gray-900">
    {{-- Mobile hamburger --}}
    <button @click="open = true"
        class="lg:hidden p-2 rounded-md text-gray-700 bg-white shadow hover:bg-gray-100 mb-4 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
        aria-label="Open menu">
        <x-heroicon-o-bars-3 class="w-6 h-6" />
    </button>

    {{-- Breadcrumbs --}}
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('receptionist.dashboard') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors duration-150">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" />Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <a href="{{ route('receptionist.appointments') }}" wire:navigate
                            class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-blue-400">
                            Appointments
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm font-medium text-gray-400 md:ms-2 dark:text-gray-400">Add to Queue</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6 flex items-center">
        <x-heroicon-s-clipboard-document-check class="w-7 h-7 mr-3 text-emerald-600 dark:text-emerald-400" />
        Add Patient to Queue
    </h1>

    <div class="card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <form wire:submit="bookAppointment" autocomplete="off" novalidate>
            {{-- Patient search --}}
            <div class="mb-5">
                <label for="patientSearchInput" class="form-label text-gray-700 dark:text-gray-300">Patient</label>
                <div class="relative">
                    <input id="patientSearchInput" type="text" wire:model.live.debounce.300ms="patientSearch"
                        placeholder="Search patient by name, UID or phone..."
                        class="h-12 pl-10 pr-10 py-2 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white {{ $selectedPatientId ? 'cursor-not-allowed bg-gray-50 dark:bg-gray-700/50' : '' }}"
                        {{ $selectedPatientId ? 'disabled' : '' }}>
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                    </span>
                    @if ($selectedPatientId)
                        <button type="button" wire:click="clearSelectedPatient"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-500 focus:outline-none"
                            aria-label="Clear selected patient">
                            <x-heroicon-o-x-circle class="w-5 h-5" />
                        </button>
                    @endif
                </div>

                @if (strlen($patientSearch) >= 2 && !$selectedPatientId)
                    <div class="relative">
                        <div class="absolute mt-2 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md shadow-lg max-h-64 overflow-y-auto z-10">
                            <div wire:loading wire:target="patientSearch" class="p-3 text-sm text-gray-500 dark:text-gray-400 flex items-center">
                                <x-heroicon-o-arrow-path class="w-4 h-4 animate-spin mr-2" /> Searching...
                            </div>
                            <div wire:loading.remove wire:target="patientSearch">
                                @forelse ($foundPatients as $patient)
                                    <button type="button"
                                        wire:click="selectPatient({{ $patient->id }}, '{{ addslashes($patient->first_name) }} {{ addslashes($patient->last_name) }} ({{ $patient->patient_uid }})')"
                                        class="w-full text-left p-3 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer transition-colors duration-150 focus:outline-none">
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $patient->first_name }} {{ $patient->last_name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-300">{{ $patient->patient_uid }} • {{ $patient->phone }}</p>
                                    </button>
                                @empty
                                    <p class="p-3 text-sm text-center text-gray-500 dark:text-gray-400">No patients found.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif

                @if ($selectedPatientId)
                    <div class="mt-2 inline-flex items-center rounded-full bg-emerald-50 text-emerald-800 px-3 py-1 text-sm dark:bg-emerald-900/30 dark:text-emerald-300">
                        <x-heroicon-o-user class="w-4 h-4 mr-2" /> {{ $selectedPatientName }}
                    </div>
                @endif
                @error('selectedPatientId') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
            </div>

            {{-- Doctor selection --}}
            <div class="mb-5">
                <label class="form-label text-gray-700 dark:text-gray-300">Doctor & Time</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="appointmentDate" class="text-xs text-gray-500 dark:text-gray-400">Date</label>
                        <input id="appointmentDate" type="date" wire:model.live="appointmentDate" min="{{ now()->toDateString() }}"
                            class="form-input dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div>
                        <label for="appointmentTime" class="text-xs text-gray-500 dark:text-gray-400">Scheduled Arrival Time</label>
                        <input id="appointmentTime" type="time" wire:model.live="appointmentTime"
                            class="form-input dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    {{-- End Time input has been removed --}}
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @forelse ($doctors as $doc)
                        @php $isSelected = $doctorId == $doc['id']; @endphp
                        <button type="button" wire:click="$set('doctorId', {{ $doc['id'] }})"
                            class="text-left p-4 rounded-lg border transition focus:outline-none {{ $isSelected ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/30 ring-2 ring-blue-500' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 hover:border-gray-300 dark:hover:border-gray-600' }}"
                            aria-pressed="{{ $isSelected ? 'true' : 'false' }}">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-200">{{ $doc['name'] }}</p>
                                    @if ($doc['department'])
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $doc['department'] }}</p>
                                    @endif
                                </div>
                                @if($isSelected)
                                    <div class="w-6 h-6 flex items-center justify-center rounded-full bg-blue-600 text-white shrink-0">
                                        <x-heroicon-s-check class="w-4 h-4" />
                                    </div>
                                @else
                                    <div class="w-6 h-6 border rounded-full shrink-0"></div>
                                @endif
                            </div>
                        </button>
                    @empty
                        <p class="text-center col-span-full p-6 border border-dashed rounded-md text-sm text-gray-500 dark:text-gray-400">No doctors found.</p>
                    @endforelse
                </div>
                @error('doctorId') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
            </div>

            {{-- Details (reason, price) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="reasonForVisit" class="form-label text-gray-700 dark:text-gray-300">Reason for Visit (Optional)</label>
                    <input id="reasonForVisit" type="text" wire:model="reasonForVisit" class="form-input dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label for="price" class="form-label text-gray-700 dark:text-gray-300">Price (Optional)</label>
                    <input id="price" type="number" wire:model="price" min="0" step="1" class="form-input dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-4 mt-6 pt-4 border-t dark:border-gray-700">
                <button type="button" wire:click="resetForm" class="btn-secondary">Reset</button>
                <button type="submit" class="btn-primary" wire:loading.attr="disabled" wire:target="bookAppointment">
                    <span wire:loading.remove wire:target="bookAppointment" class="flex items-center">
                        <x-heroicon-o-user-plus class="w-5 h-5 mr-2" />
                        Add to Queue
                    </span>
                    <span wire:loading wire:target="bookAppointment" class="flex items-center">
                        <x-heroicon-o-arrow-path class="w-5 h-5 mr-2 animate-spin" />
                        Adding...
                    </span>
                </button>
            </div>
        </form>
    </div>
</main>
