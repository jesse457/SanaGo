<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4">
                <div class="flex flex-col gap-2">
                    {{-- Breadcrumbs --}}
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-1" aria-label="Breadcrumb">
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
                                    <a href="{{ route('receptionist.patients') }}" wire:navigate
                                        class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        Patients
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">Register Patient</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            New Patient Registration
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                            Enter the patient's personal details to create a new medical record.
                        </p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="p-4 sm:p-6 pb-20">
            <div class="max-w-4xl mx-auto">

                {{-- Form Card --}}
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                    <div class="p-6 md:p-8">
                        <form wire:submit.prevent="savePatient" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                            {{-- First Name --}}
                            <div class="space-y-1.5">
                                <label for="first_name" class="block text-sm font-bold text-slate-700 dark:text-slate-300">First Name <span class="text-red-500">*</span></label>
                                <input type="text" id="first_name" wire:model.live="first_name" placeholder="e.g. John"
                                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white py-2.5 px-3" />
                                @error('first_name') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Last Name --}}
                            <div class="space-y-1.5">
                                <label for="last_name" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Last Name <span class="text-red-500">*</span></label>
                                <input type="text" id="last_name" wire:model.live="last_name" placeholder="e.g. Doe"
                                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white py-2.5 px-3" />
                                @error('last_name') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Age --}}
                            <div class="space-y-1.5">
                                <label for="age" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Age <span class="text-red-500">*</span></label>
                                <input type="number" id="age" wire:model.live="age" min="0" placeholder="0"
                                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white py-2.5 px-3" />
                                @error('age') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Gender --}}
                            <div class="space-y-1.5">
                                <label for="gender" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Gender <span class="text-red-500">*</span></label>
                                <select id="gender" wire:model.live="gender"
                                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white py-2.5 px-3">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                                @error('gender') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Phone --}}
                            <div class="space-y-1.5">
                                <label for="phone" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Phone Number <span class="text-red-500">*</span></label>
                                <input type="tel" id="phone" wire:model.live="phone" placeholder="+1 (555) 000-0000"
                                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white py-2.5 px-3" />
                                @error('phone') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="space-y-1.5">
                                <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Email Address <span class="text-slate-400 font-normal ml-1 text-xs">(Optional)</span></label>
                                <input type="email" id="email" wire:model.live="email" placeholder="john@example.com"
                                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white py-2.5 px-3" />
                                @error('email') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Address --}}
                            <div class="md:col-span-2 space-y-1.5">
                                <label for="address" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Residential Address <span class="text-red-500">*</span></label>
                                <textarea id="address" wire:model.live="address" rows="3" placeholder="Street address, apartment, city..."
                                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white py-2.5 px-3"></textarea>
                                @error('address') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Submit Action --}}
                            <div class="md:col-span-2 pt-6 border-t border-slate-100 dark:border-gray-800 flex justify-end">
                                <button type="submit"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-bold rounded-xl shadow-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all disabled:opacity-70 disabled:cursor-not-allowed">

                                    <span wire:loading.remove wire:target="savePatient" class="flex items-center gap-2">
                                        <x-heroicon-o-check-circle class="w-5 h-5" />
                                        Create Patient Record
                                    </span>

                                    <span wire:loading wire:target="savePatient" class="flex items-center gap-2">
                                        <x-heroicon-o-arrow-path class="animate-spin h-5 w-5 text-white" />
                                        Registering...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
