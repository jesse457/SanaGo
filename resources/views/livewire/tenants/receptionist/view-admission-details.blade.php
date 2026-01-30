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
                                <a href="{{ route($userRole . '.dashboard') }}" wire:navigate
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    {{ __('Home') }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <a href="{{ route($userRole . '.checkin') }}" wire:navigate
                                        class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        {{ __('Admissions') }}
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">{{ __('Admission Details') }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            {{ __('Admission Overview') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                            {{ __('Detailed view of patient admission history and current status.') }}
                        </p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="p-4 sm:p-6 pb-20">
            <div class="max-w-5xl mx-auto">

                {{-- Main Content Card --}}
                <div
                    class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">

                    {{-- Patient Header Info --}}
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
                                    <span class="bg-slate-100 dark:bg-gray-800/50 px-1.5 py-0.5 rounded text-xs font-bold text-slate-700 dark:text-slate-300">{{ $patient->patient_uid }}</span>
                                    <span class="w-1 h-1 bg-slate-400 rounded-full"></span>
                                    <span class="capitalize">{{ __($patient->gender) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 md:p-8">
                        @if ($admissions->isNotEmpty())

                            {{-- Admission Selector --}}
                            <div class="mb-8 max-w-md">
                                <label for="admissionSelector" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">
                                    {{ __('Select Admission Record') }}
                                </label>
                                <div class="relative">
                                    <select wire:model.live="selectedAdmissionId" id="admissionSelector"
                                        class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white py-2.5 pl-3 pr-10">
                                        @foreach ($admissions as $admissionOption)
                                            <option value="{{ $admissionOption->id }}">
                                                {{ $admissionOption->admission_date->format('M d, Y') }} — {{ $admissionOption->status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            @if ($selectedAdmission)
                                {{-- Status Banner --}}
                                <div class="mb-8 flex items-center justify-between p-4 rounded-xl border shadow-sm
                                    @if($selectedAdmission->status === 'Admitted') bg-emerald-50 border-emerald-200 text-emerald-800 dark:bg-emerald-900/20 dark:border-emerald-800 dark:text-emerald-300
                                    @elseif($selectedAdmission->status === 'Discharged') bg-slate-100 border-slate-200 text-slate-600 dark:bg-gray-700/40 dark:border-gray-700 dark:text-slate-400
                                    @else bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-300 @endif">
                                    <div class="flex items-center gap-3">
                                        <div class="p-1 rounded-full border-2 border-current">
                                            <x-heroicon-s-information-circle class="w-5 h-5" />
                                        </div>
                                        <span class="font-bold text-lg">{{ __($selectedAdmission->status) }}</span>
                                    </div>
                                    <span class="text-xs font-bold opacity-75 uppercase tracking-wider">{{ __('Current Status') }}</span>
                                </div>

                                {{-- Details Grid --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                    {{-- Card: Doctor --}}
                                    <div class="bg-slate-50 dark:bg-gray-800/50 p-5 rounded-xl border border-slate-100 dark:border-gray-800 flex items-start gap-4 transition-colors">
                                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg text-blue-600 dark:text-blue-400 shadow-sm">
                                            <x-heroicon-o-user-circle class="w-6 h-6" />
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Assigned Doctor') }}</p>
                                            <p class="text-base font-bold text-slate-900 dark:text-white mt-1">
                                                {{ $selectedAdmission->doctor->name ?? __('Not Assigned') }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Card: Bed --}}
                                    <div class="bg-slate-50 dark:bg-gray-800/50 p-5 rounded-xl border border-slate-100 dark:border-gray-800 flex items-start gap-4 transition-colors">
                                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg text-purple-600 dark:text-purple-400 shadow-sm">
                                            <x-heroicon-o-home-modern class="w-6 h-6" />
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Bed Allocation') }}</p>
                                            <p class="text-base font-bold text-slate-900 dark:text-white mt-1">
                                                {{ __('Bed') }} {{ $selectedAdmission->bed->bed_number ?? 'N/A' }}
                                                <span class="text-sm font-medium text-slate-500 dark:text-slate-400 ml-1">
                                                    ({{ $selectedAdmission->bed->ward->name ?? __('Ward N/A') }})
                                                </span>
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Card: Dates --}}
                                    <div class="bg-slate-50 dark:bg-gray-800/50 p-5 rounded-xl border border-slate-100 dark:border-gray-800 flex items-start gap-4 transition-colors">
                                        <div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg text-emerald-600 dark:text-emerald-400 shadow-sm">
                                            <x-heroicon-o-calendar-days class="w-6 h-6" />
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Admission Date') }}</p>
                                            <p class="text-base font-bold text-slate-900 dark:text-white mt-1">
                                                {{ $selectedAdmission->admission_date->format('M d, Y') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="bg-slate-50 dark:bg-gray-800/50 p-5 rounded-xl border border-slate-100 dark:border-gray-800 flex items-start gap-4 transition-colors">
                                        <div class="p-2 bg-rose-100 dark:bg-rose-900/30 rounded-lg text-rose-600 dark:text-rose-400 shadow-sm">
                                            <x-heroicon-o-arrow-right-on-rectangle class="w-6 h-6" />
                                        </div>
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Discharge Date') }}</p>
                                            <p class="text-base font-bold text-slate-900 dark:text-white mt-1">
                                                {{ $selectedAdmission->discharge_date?->format('M d, Y') ?? __('Ongoing') }}
                                            </p>
                                        </div>
                                    </div>

                                    {{-- Card: Reason (Full Width) --}}
                                    <div class="md:col-span-2 bg-slate-50 dark:bg-gray-800/50 p-5 rounded-xl border border-slate-100 dark:border-gray-800">
                                        <div class="flex items-start gap-4">
                                            <div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg text-amber-600 dark:text-amber-400 shadow-sm">
                                                <x-heroicon-o-document-text class="w-6 h-6" />
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Reason for Admission') }}</p>
                                                <p class="text-base text-slate-900 dark:text-white mt-1 leading-relaxed">
                                                    {{ $selectedAdmission->reason_for_admission }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Actions Footer --}}
                                <div class="mt-8 pt-6 border-t border-slate-100 dark:border-gray-800 flex flex-col sm:flex-row justify-end gap-3">

                                    <a wire:navigate href="{{ route($userRole . '.checkin') }}"
                                        class="inline-flex items-center justify-center px-6 py-2.5 border border-slate-300 dark:border-gray-600 shadow-sm text-sm font-bold rounded-xl text-slate-700 dark:text-slate-300 bg-white dark:bg-gray-800 hover:bg-slate-50 dark:hover:bg-gray-700 transition-colors w-full sm:w-auto">
                                        <x-heroicon-s-arrow-left class="w-4 h-4 mr-2" />
                                        {{ __('Back to List') }}
                                    </a>

                                    @if ($selectedAdmission->status === 'Admitted')
                                        <button wire:click="updateAdmissionStatus({{ $selectedAdmission->id }}, 'Discharged')"
                                            wire:loading.attr="disabled"
                                            class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-bold rounded-xl shadow-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all w-full sm:w-auto disabled:opacity-70 disabled:cursor-not-allowed">

                                            <span wire:loading.remove wire:target="updateAdmissionStatus" class="flex items-center gap-2">
                                                <x-heroicon-s-arrow-right-on-rectangle class="w-5 h-5" />
                                                {{ __('Discharge Patient') }}
                                            </span>

                                            <span wire:loading wire:target="updateAdmissionStatus" class="flex items-center gap-2">
                                                <x-heroicon-o-arrow-path class="animate-spin h-5 w-5 text-white" />
                                                {{ __('Processing...') }}
                                            </span>
                                        </button>
                                    @elseif ($selectedAdmission->status === 'Discharged')
                                        <button disabled
                                            class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-bold rounded-xl text-slate-400 bg-slate-100 dark:bg-gray-800/50 dark:text-slate-500 cursor-not-allowed w-full sm:w-auto">
                                            <x-heroicon-s-check-circle class="w-5 h-5 mr-2" />
                                            {{ __('Patient Discharged') }}
                                        </button>
                                    @endif
                                </div>
                            @else
                                {{-- Should not happen if admissions->isNotEmpty() is true and selectedId is set --}}
                                <div class="flex flex-col items-center justify-center py-12 text-center border-2 border-dashed border-slate-200 dark:border-gray-700 rounded-xl bg-slate-50 dark:bg-gray-800/50">
                                    <x-heroicon-o-inbox class="w-16 h-16 text-slate-300 dark:text-gray-600 mb-4" />
                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('No record selected') }}</h3>
                                    <p class="text-slate-500 dark:text-slate-400">{{ __('Please select an admission record from the dropdown above.') }}</p>
                                </div>
                            @endif

                        @else
                            {{-- Empty State (No Admissions) --}}
                            <div class="flex flex-col items-center justify-center py-16 text-center">
                                <div class="h-20 w-20 bg-slate-50 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4 border border-slate-100 dark:border-gray-700">
                                    <x-heroicon-o-folder-open class="w-10 h-10 text-slate-400" />
                                </div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ __('No Admission Records Found') }}</h3>
                                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400 max-w-sm">
                                    {{ __('This patient has no admission history on file.') }}
                                </p>
                                <a href="{{ route($userRole . '.checkin') }}" wire:navigate
                                    class="mt-6 inline-flex items-center text-sm font-bold text-blue-600 hover:text-blue-500 dark:text-blue-400">
                                    <x-heroicon-s-arrow-left class="w-4 h-4 mr-1" />
                                    {{ __('Return to Patient List') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
