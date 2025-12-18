<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-5xl mx-auto">

        {{-- HEADER SECTION --}}
        <header class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4">
                <div class="flex flex-col gap-2">
                    {{-- Breadcrumbs --}}
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('nurse.dashboard') }}" wire:navigate class="hover:text-pink-600 dark:hover:text-pink-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" /> {{ __('nurse.home') }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">{{ __('nurse.care_report_breadcrumb') }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title and Button --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7 flex items-center gap-2">
                                <x-heroicon-s-clipboard-document-list class="w-6 h-6 text-pink-500" />
                                {{ __('nurse.patient_care_report_title') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                                Document daily nursing care, interventions performed, and patient observations.
                            </p>
                        </div>

                        {{-- LINK TO HISTORY VIEW --}}
                        <a href="{{ route('nurse.create-care-report') }}" wire:navigate
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 hover:text-slate-900 dark:bg-gray-800 dark:border-gray-700 dark:text-slate-300 dark:hover:bg-gray-700 rounded-xl shadow-sm transition-all duration-200">
                            <x-heroicon-o-clock class="w-4 h-4" />
                            {{ __('View Previous Reports') }}
                        </a>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="p-4 sm:p-6 pb-20">
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                <form wire:submit.prevent="saveCareReport" class="p-6 sm:p-8">

                    {{-- Success Alert --}}
                    @if (session()->has('success'))
                        <div class="p-4 mb-6 text-sm text-green-800 rounded-xl bg-green-50 dark:bg-green-900/20 dark:text-green-300 border border-green-100 dark:border-green-800 flex items-start gap-3" role="alert">
                            <x-heroicon-m-check-circle class="w-5 h-5 flex-shrink-0 mt-0.5" />
                            <div><span class="font-bold">{{ __('Success') }}</span> {{ session('success') }}</div>
                        </div>
                    @endif

                    {{-- Section 1: Patient Identification --}}
                    <div class="pb-8">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-6 border-b border-slate-100 dark:border-gray-800 pb-3">
                            <div class="p-1.5 bg-pink-50 dark:bg-pink-900/30 rounded-lg">
                                <x-heroicon-o-user-group class="w-5 h-5 text-pink-600 dark:text-pink-400" />
                            </div>
                            {{ __('nurse.section_patient_selection') }}
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                            {{-- SEARCHABLE Patient Input --}}
                            <div class="space-y-1.5 md:col-span-2 relative" x-data="{ open: @entangle('showDropdown') }">
                                <label for="patientSearch" class="block text-sm font-bold text-slate-700 dark:text-slate-300">
                                    {{ __('nurse.label_select_patient') }} <span class="text-red-500">*</span>
                                </label>

                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <x-heroicon-o-magnifying-glass class="h-5 w-5 text-slate-400" />
                                    </div>
                                    <input type="text" id="patientSearch" wire:model.live.debounce.300ms="patientSearch"
                                        placeholder="Type name or ID to search..." autocomplete="off"
                                        @focus="open = true" @click.outside="open = false"
                                        class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 pl-10 pr-10 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm transition-all duration-200">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <x-heroicon-o-arrow-path wire:loading wire:target="patientSearch" class="h-5 w-5 text-pink-500 animate-spin" />
                                    </div>
                                </div>

                                {{-- Dropdown Results --}}
                                <div x-show="open && $wire.searchResults.length > 0" x-transition
                                     class="absolute z-50 mt-1 w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-slate-100 dark:border-gray-700 max-h-60 overflow-auto">
                                    <ul class="py-1">
                                        @foreach($searchResults as $result)
                                            <li>
                                                <button type="button" wire:click="selectPatient('{{ $result->id }}', '{{ addslashes($result->name) }}')"
                                                    class="w-full text-left px-4 py-3 text-sm hover:bg-pink-50 dark:hover:bg-pink-900/20 transition-colors flex items-center justify-between group">
                                                    <div>
                                                        <span class="block font-bold text-slate-700 dark:text-slate-200 group-hover:text-pink-700 dark:group-hover:text-pink-300">{{ $result->name }}</span>
                                                        <span class="block text-xs text-slate-500 dark:text-slate-400">ID: {{ $result->patient_uid }}</span>
                                                    </div>
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @error('patient_id') <p class="text-red-500 text-xs font-medium mt-1">Please select a valid patient.</p> @enderror
                            </div>

                            {{-- Vital Signs Quick Input --}}
                            <div class="md:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-4 bg-slate-50 dark:bg-gray-800/50 p-4 rounded-xl border border-slate-100 dark:border-gray-700/50">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase">BP (mmHg)</label>
                                    <input type="text" wire:model="vitals_bp" placeholder="120/80" class="mt-1 block w-full rounded-lg border-slate-200 dark:border-gray-700 dark:bg-gray-800 text-sm py-1.5 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase">HR (bpm)</label>
                                    <input type="number" wire:model="vitals_hr" placeholder="72" class="mt-1 block w-full rounded-lg border-slate-200 dark:border-gray-700 dark:bg-gray-800 text-sm py-1.5 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase">Temp (°C)</label>
                                    <input type="number" step="0.1" wire:model="vitals_temp" placeholder="36.5" class="mt-1 block w-full rounded-lg border-slate-200 dark:border-gray-700 dark:bg-gray-800 text-sm py-1.5 focus:border-pink-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 uppercase">SpO2 (%)</label>
                                    <input type="number" wire:model="vitals_spo2" placeholder="98" class="mt-1 block w-full rounded-lg border-slate-200 dark:border-gray-700 dark:bg-gray-800 text-sm py-1.5 focus:border-pink-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Care Report Details --}}
                    <div class="border-t border-slate-100 dark:border-gray-800 pt-8">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-6 border-b border-slate-100 dark:border-gray-800 pb-3">
                            <div class="p-1.5 bg-pink-50 dark:bg-pink-900/30 rounded-lg">
                                <x-heroicon-o-document-text class="w-5 h-5 text-pink-600 dark:text-pink-400" />
                            </div>
                            {{ __('nurse.section_report_details') }}
                        </h3>

                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('nurse.label_report_date_time') }}</label>
                                    <input type="datetime-local" wire:model="report_time" class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2.5 shadow-sm focus:border-pink-500 sm:text-sm">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('nurse.label_shift_type') }}</label>
                                    <select wire:model="shift_type" class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2.5 shadow-sm focus:border-pink-500 sm:text-sm">
                                        <option value="Morning">Morning Shift</option>
                                        <option value="Afternoon">Afternoon Shift</option>
                                        <option value="Night">Night Shift</option>
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('nurse.label_interventions') }} <span class="text-red-500">*</span></label>
                                <textarea wire:model="interventions" rows="4" placeholder="Describe procedures..." class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 shadow-sm focus:border-pink-500 sm:text-sm"></textarea>
                                @error('interventions') <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('nurse.label_observations') }} <span class="text-red-500">*</span></label>
                                <textarea wire:model="observations" rows="4" placeholder="Note patient condition..." class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 px-4 shadow-sm focus:border-pink-500 sm:text-sm"></textarea>
                                @error('observations') <p class="text-red-500 text-xs font-medium mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 mt-10 pt-6 border-t border-slate-100 dark:border-gray-800">
                        <button type="button" wire:click="cancelReport" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl bg-white dark:bg-gray-800 px-6 py-2.5 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">{{ __('nurse.button_cancel') }}</button>
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl bg-pink-600 px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-pink-700" wire:loading.attr="disabled">
                            <span wire:loading.remove>{{ __('nurse.button_submit_report') }}</span>
                            <span wire:loading><x-heroicon-o-arrow-path class="animate-spin h-5 w-5" /></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
