<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 lg:ml-64 p-6 dark:bg-gray-900">
    <!-- Sidebar toggle (small screens) -->
    <button @click="open = true"
        class="lg:hidden p-2 rounded-md text-gray-700 bg-white shadow hover:bg-gray-100 mb-4 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
        aria-label="Open menu">
        <x-heroicon-o-bars-3 class="w-6 h-6" />
    </button>

    <!-- Breadcrumbs -->
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('receptionist.dashboard') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300 transition-colors duration-150">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" aria-hidden="true" />Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true" />
                        <span class="ms-1 text-sm font-medium text-gray-400 md:ms-2 dark:text-gray-400">Patients</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white flex items-center gap-3">
                <x-heroicon-s-users class="w-8 h-8 text-indigo-600" aria-hidden="true" />
                Patient Management
            </h1>
            <p class="text-gray-600 dark:text-gray-400 pt-2">View, search, and manage all patients.</p>
        </div>

        <div class="flex items-center gap-3 w-full sm:w-auto">
            <a href="{{ route('receptionist.register-patient') }}" wire:navigate
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition duration-150 ease-in-out shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                <x-heroicon-o-user-plus class="w-5 h-5" aria-hidden="true" /> New Patient
            </a>
        </div>
    </header>

    <!-- Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
        <!-- Search -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div class="relative flex-1 w-full md:w-auto">
                <label for="patient-search" class="sr-only">Search patients</label>
                <input id="patient-search" type="text"
                   class="h-12 pl-12 pr-4 w-full border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="Search patients by name, ID, or phone..." wire:model.live.debounce.300ms="search"
                    aria-label="Search patients">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5" aria-hidden="true" />
                </span>
            </div>
        </div>

        <!-- Table container -->
        <div class="overflow-x-auto shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Patient ID</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Name</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                           Age</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Phone</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Last Visit</th>
                        <th scope="col"
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                            Actions</th>
                    </tr>
                </thead>

                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($patients as $patient)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $patient->patient_uid }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 dark:text-gray-200">
                                {{ $patient->first_name }} {{ $patient->last_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $patient->age }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $patient->phone ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                @php
                                    $lastVisit = $patient
                                        ->appointments()
                                        ->orderByDesc('appointment_date')
                                        ->orderByDesc('appointment_time')
                                        ->first();
                                @endphp
                                @if ($lastVisit)
                                    <span class="inline-flex items-center text-blue-600 dark:text-blue-400">
                                        <x-heroicon-o-calendar class="w-4 h-4 mr-1" aria-hidden="true" />
                                        {{ \Illuminate\Support\Carbon::parse($lastVisit->appointment_date)->format('Y-m-d') }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">No recent visit</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="inline-flex items-center gap-2 justify-end">
                                    <button type="button" wire:click="openEditModal({{ $patient->id }})"
                                        class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 dark:text-indigo-400 transition-colors duration-150">
                                        <x-heroicon-o-pencil-square class="w-4 h-4 mr-1" aria-hidden="true" /> Edit
                                    </button>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"
                                class="px-6 py-12 whitespace-nowrap text-center text-sm text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <x-heroicon-o-magnifying-glass-circle
                                        class="w-16 h-16 text-gray-300 dark:text-gray-600 mb-4" aria-hidden="true" />
                                    <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-2">No patients
                                        found matching your criteria.</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or
                                        registering a new patient.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-sm text-gray-600 dark:text-gray-400">Page {{ $patients->currentPage() }} of
                {{ $patients->lastPage() }}</div>
            <div>
                {{ $patients->links('pagination::tailwind') }}
            </div>
        </div>
    </div>

    <!-- Edit Patient Modal (Alpine local state to avoid colliding with outer `open`) -->
    <div x-data="{ editModalOpen: @entangle('showEditModal') }" x-cloak x-trap.noscroll="editModalOpen"
        x-on:keydown.escape.window="editModalOpen = false; $wire.cancelEdit()">
        <div x-show="editModalOpen" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50" @click="editModalOpen = false; $wire.cancelEdit()" aria-hidden="true"></div>

            <div x-show="editModalOpen" x-transition:enter="transition ease-out duration-250"
                x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
                x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
                class="relative bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-3xl w-full z-10 overflow-hidden">
                <div class="p-6">
                    <div
                        class="flex items-start justify-between gap-4 border-b pb-4 border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Edit Patient</h3>
                        <button type="button" @click="editModalOpen = false; $wire.cancelEdit()"
                            class="p-2 rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-400 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-300"
                            aria-label="Close modal">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    </div>

                    <form wire:submit.prevent="savePatient" class="mt-4 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Patient
                                    UID</label>
                                <input type="text" wire:model.defer="patient_uid"
                                    class="form-input" />
                                @error('patient_uid')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">First
                                    name</label>
                                <input type="text" wire:model.defer="first_name"
                                    class="form-input" />
                                @error('first_name')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last
                                    name</label>
                                <input type="text" wire:model.defer="last_name"
                                    class="form-input" />
                                @error('last_name')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">AGE</label>
                                <input type="number" wire:model.defer="age"
                                    class="form-input" />
                                @error('age')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gender</label>
                                <select wire:model.defer="gender"
                                    class="form-input">
                                    <option value="">Select</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                                @error('gender')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>



                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                                <input type="text" wire:model.defer="phone"
                                    class="form-input" />
                                @error('phone')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input type="email" wire:model.defer="email"
                                    class="form-input" />
                                @error('email')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>


                            <div class="md:col-span-2">
                                <label
                                    class="block  text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                                <textarea wire:model.defer="address" rows="3"
                                    class="form-textarea"></textarea>
                                @error('address')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>


                        </div>

                        <div class="flex items-center justify-end gap-3 mt-6">
                            <button type="button" @click="editModalOpen = false; $wire.cancelEdit()"
                                class="px-4 py-2 rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 transition-colors duration-150">
                                Cancel
                            </button>
                             <button type="submit"
                        class="mt-6 px-8 py-3 bg-blue-700 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center gap-2.5 shadow-md hover:shadow-lg"
                        wire:loading.attr="disabled" wire:target="savePatient">
                        <x-heroicon-s-plus wire:loading.remove wire:target="savePatient" class="w-5 h-5 mr-3" />
                        <p wire:loading.remove wire:target="savePatient">Save Changes</p>
                        <x-heroicon-o-arrow-path wire:loading wire:target="savePatient"
                            class="animate-spin w-5 h-5 mr-3" />
                        <p wire:loading wire:target="savePatient">Saving...</p>
                    </button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
