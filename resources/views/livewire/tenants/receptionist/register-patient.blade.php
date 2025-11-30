<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 lg:ml-64 p-6 dark:bg-gray-900">
    {{-- Mobile hamburger --}}
    <button @click="open = true"
        class="lg:hidden p-2 rounded-md text-gray-700 bg-white shadow hover:bg-gray-100 mb-4 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
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
                        <a href="{{ route('receptionist.patients') }}"
                            class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-700 dark:text-gray-400 dark:hover:text-gray-300 transition-colors duration-150 ps-1">
                            Patients
                        </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm font-medium text-gray-400 md:ms-2 dark:text-gray-400">Register
                            Patient</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6 flex items-center">
        <x-heroicon-s-user-plus class="w-7 h-7 mr-3 text-blue-600 dark:text-blue-400" />
        Register New Patient
    </h1>

    <div class="card bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <form wire:submit.prevent="savePatient" class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
            {{-- Adjusted gaps --}}
            <div>
                <label for="first_name" class="form-label text-gray-700 dark:text-gray-300">First Name</label>
                <input type="text" id="first_name"
                    class="form-input dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="John" wire:model.live="first_name">
                @error('first_name')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="last_name" class="form-label text-gray-700 dark:text-gray-300">Last Name</label>
                <input type="text" id="last_name"
                    class="form-input dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Doe" wire:model.live="last_name">
                @error('last_name')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="age" class="form-label text-gray-700 dark:text-gray-300">Age</label>
                <input type="number" id="age"
                    class="form-input dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500"
                    wire:model.live="age">
                @error('age')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="gender" class="form-label text-gray-700 dark:text-gray-300">Gender</label>
                <select id="gender"
                    class="form-input dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500"
                    wire:model.live="gender">
                    <option value="">Select</option>
                    {{-- Ensure these values match your DB check constraint (case-sensitive) --}}
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
                @error('gender')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div class="md:col-span-2">
                <label for="address" class="form-label text-gray-700 dark:text-gray-300">Address</label>
                <input type="text" id="address"
                    class="form-input dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="123 Main St" wire:model.live="address">
                @error('address')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="phone" class="form-label text-gray-700 dark:text-gray-300">Phone Number</label>
                <input type="tel" id="phone"
                    class="form-input dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="+1 (555) 123-4567" wire:model.live="phone">
                @error('phone')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="email" class="form-label text-gray-700 dark:text-gray-300">Email (Optional)</label>
                <input type="email" id="email"
                    class="form-input dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="john.doe@example.com" wire:model.live="email">
                @error('email')
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
           
            <div class="md:col-span-2 flex justify-end pt-4"> {{-- Increased padding-top for spacing --}}
                <button type="submit"
                    class="btn-primary flex items-center justify-center py-2 px-5 rounded-md shadow-sm text-base font-medium
                               text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500
                               dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-offset-gray-800"
                    wire:loading.attr="disabled" wire:target="savePatient">
                    <span wire:loading.remove wire:target="savePatient">
                        <x-heroicon-o-user-plus class="w-5 h-5 inline-block mr-2" />
                        Register Patient
                    </span>
                    <span wire:loading wire:target="savePatient" class="flex items-center">
                        <x-heroicon-o-arrow-path class="w-5 h-5 inline-block mr-2 animate-spin" />
                        {{-- Loading spinner --}}
                        Registering...
                    </span>
                </button>
            </div>
        </form>
    </div>


</main>
