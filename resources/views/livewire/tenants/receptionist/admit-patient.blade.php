<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class=" ">

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
                                    <a href="{{ route('receptionist.checkin') }}" wire:navigate
                                        class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        Admissions
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">Admit Patient</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            Patient Admission
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                            Assign a bed and record admission details for inpatient care.
                        </p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="p-4 sm:p-6 pb-20">
            <div class="max-w-4xl mx-auto">

                {{-- Main Content Card --}}
                <div
                    class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">

                    {{-- Patient Summary Header --}}
                    <div class="px-6 py-5 bg-slate-50 dark:bg-gray-800/50 border-b border-slate-100 dark:border-gray-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-700 dark:text-blue-300 font-bold text-lg ring-4 ring-white dark:ring-gray-900 shadow-md">
                                {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                                    {{ $patient->first_name }} {{ $patient->last_name }}
                                </h2>
                                <div class="flex items-center gap-3 text-sm text-slate-500 dark:text-slate-400 font-mono mt-0.5">
                                    <span>UID: {{ $patient->patient_uid }}</span>
                                    <span class="w-1 h-1 bg-slate-400 rounded-full"></span>
                                    <span class="capitalize">{{ $patient->gender }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Status Badge --}}
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-100 dark:border-amber-800 shadow-sm">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            Admission Pending
                        </span>
                    </div>

                    <div class="p-6 md:p-8">
                        <form wire:submit.prevent="saveAdmission" class="space-y-8">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Bed Selection --}}
                                <div class="space-y-1.5">
                                    <label for="bedId" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Select Bed <span class="text-red-500">*</span></label>
                                    <select id="bedId" wire:model="bedId"
                                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white py-2.5 px-3">
                                        <option value="">-- Choose an Available Bed --</option>
                                        @foreach ($availableBeds as $bed)
                                            <option value="{{ $bed->id }}">
                                                Bed {{ $bed->bed_number }} ({{ $bed->type ?? 'General' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bedId') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                                </div>

                                {{-- Admission Date --}}
                                <div class="space-y-1.5">
                                    <label for="admissionDate" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Admission Date <span class="text-red-500">*</span></label>
                                    <input type="date" id="admissionDate" wire:model="admissionDate"
                                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white py-2.5 px-3" />
                                    @error('admissionDate') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                                </div>

                                {{-- Reason --}}
                                <div class="col-span-1 md:col-span-2 space-y-1.5">
                                    <label for="reasonForAdmission" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Reason for Admission <span class="text-red-500">*</span></label>
                                    <input type="text" id="reasonForAdmission" wire:model="reasonForAdmission" placeholder="e.g. Post-operative observation, Severe dehydration..."
                                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white py-2.5 px-3" />
                                    @error('reasonForAdmission') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                                </div>

                                {{-- Observation Fee --}}
                                <div class="col-span-1 space-y-1.5">
                                    <label for="observationFee" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Observation Fee / Deposit <span class="text-slate-400 font-normal ml-1 text-xs">(Optional)</span></label>
                                    <div class="relative rounded-xl shadow-sm">
                                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                            <span class="text-slate-500 sm:text-sm font-bold">$</span>
                                        </div>
                                        <input type="number" id="observationFee" wire:model="observationFee" placeholder="0.00"
                                            class="block w-full rounded-xl border-slate-300 pl-7 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white py-2.5 px-3" />
                                    </div>
                                    @error('observationFee') <span class="text-xs text-red-500 font-medium mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="pt-8 border-t border-slate-100 dark:border-gray-800 flex justify-end gap-3">
                                <a href="{{ route('receptionist.checkin') }}" wire:navigate
                                   class="inline-flex items-center px-6 py-2.5 border border-slate-300 dark:border-gray-600 shadow-sm text-sm font-bold rounded-xl text-slate-700 dark:text-slate-300 bg-white dark:bg-gray-800 hover:bg-slate-50 dark:hover:bg-gray-700 focus:outline-none transition-colors">
                                    Cancel
                                </a>

                                <button type="submit" wire:loading.attr="disabled"
                                    class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-bold rounded-xl shadow-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-70 disabled:cursor-not-allowed transition-all">
                                    <span wire:loading.remove wire:target="saveAdmission" class="flex items-center gap-2">
                                        <x-heroicon-s-arrow-right-on-rectangle class="w-5 h-5" />
                                        Confirm Admission
                                    </span>
                                    <span wire:loading wire:target="saveAdmission" class="flex items-center gap-2">
                                        <x-heroicon-o-arrow-path class="animate-spin h-5 w-5 text-white" />
                                        Processing...
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
