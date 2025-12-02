<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50/50 p-6 dark:bg-gray-900">
    {{-- Mobile Sidebar Toggle --}}
    <button @click="open = true" class="lg:hidden p-2 rounded-lg text-gray-500 bg-white shadow-sm border border-gray-200 hover:bg-gray-50 mb-6 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700 dark:hover:bg-gray-700 transition-colors">
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
                        <a href="{{ route('receptionist.patients') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-300 transition-colors duration-150">
                       
                        Patients
                    </a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm  text-gray-900 md:ms-2 dark:text-gray-400">
                            Register Patient</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center gap-3">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400">
                    <x-heroicon-o-user-plus class="w-7 h-7" />
                </div>
                New Patient Registration
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 ml-14">
                Enter the patient's personal details to create a new medical record.
            </p>
        </div>

        {{-- Form Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/60 dark:border-gray-700 overflow-hidden">
            <div class="p-6 md:p-8">
                <form wire:submit.prevent="savePatient" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                    {{-- First Name --}}
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">First Name</label>
                        <input type="text" id="first_name" wire:model.live="first_name" placeholder="e.g. John"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white dark:placeholder-gray-500" />
                        @error('first_name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Last Name --}}
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Last Name</label>
                        <input type="text" id="last_name" wire:model.live="last_name" placeholder="e.g. Doe"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white dark:placeholder-gray-500" />
                        @error('last_name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Age --}}
                    <div>
                        <label for="age" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Age</label>
                        <input type="number" id="age" wire:model.live="age" min="0" placeholder="0"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white dark:placeholder-gray-500" />
                        @error('age') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Gender --}}
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gender</label>
                        <select id="gender" wire:model.live="gender"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        @error('gender') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number</label>
                        <input type="tel" id="phone" wire:model.live="phone" placeholder="+1 (555) 000-0000"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white dark:placeholder-gray-500" />
                        @error('phone') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <input type="email" id="email" wire:model.live="email" placeholder="john@example.com"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white dark:placeholder-gray-500" />
                        @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Address --}}
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Residential Address</label>
                        <textarea id="address" wire:model.live="address" rows="3" placeholder="Street address, apartment, city..."
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white dark:placeholder-gray-500"></textarea>
                        @error('address') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Submit Action --}}
                    <div class="md:col-span-2 pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                        <button type="submit"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-semibold rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all disabled:opacity-70 disabled:cursor-not-allowed">

                            <span wire:loading.remove wire:target="savePatient" class="flex items-center gap-2">
                                <x-heroicon-o-check-circle class="w-5 h-5" />
                                Create Patient Record
                            </span>

                            <span wire:loading wire:target="savePatient" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Registering...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
