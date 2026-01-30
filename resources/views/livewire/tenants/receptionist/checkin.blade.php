<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4 md:flex md:items-center md:justify-between space-y-3 md:space-y-0">

                {{-- Title & Breadcrumbs --}}
                <div class="flex-1 min-w-0">
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-1" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('receptionist.dashboard') }}" wire:navigate
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    {{ __('Home') }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">{{ __('Admissions') }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                        {{ __('Patient Admissions') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('Manage patient admission requests, bed assignments, and discharge status.') }}
                    </p>
                </div>
            </div>

            {{-- Filters Bar --}}
            <div class="px-4 sm:px-6 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                    {{-- Search --}}
                    <div class="relative w-full md:max-w-md group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-m-magnifying-glass class="h-4 w-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                        </div>
                        <input type="text" wire:model.live.debounce.400ms="search"
                            class="block w-full pl-9 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
                            placeholder="{{ __('Search by UID, name, or bed...') }}">
                        <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <x-heroicon-o-arrow-path class="animate-spin h-4 w-4 text-blue-500" />
                        </div>
                    </div>

                    {{-- Toggle Switch --}}
                    <div class="flex items-center gap-3">
                        <span class="text-sm font-bold text-slate-600 dark:text-slate-400">{{ __('Show Admitted Only') }}</span>
                        <button type="button"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent bg-slate-200 dark:bg-gray-700 transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <span aria-hidden="true" class="translate-x-0 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="relative min-h-[400px] p-4 sm:p-6 pb-20">

            {{-- Loading Overlay --}}
            <div wire:loading.flex wire:target="search, admitPatient, confirmAdmission"
                class="absolute inset-0 bg-white/60 dark:bg-gray-900/60 backdrop-blur-[2px] z-20 flex items-start justify-center rounded-2xl pt-20 transition-all">
                <div class="flex items-center gap-3 px-6 py-3 bg-white dark:bg-gray-800 rounded-full shadow-xl border border-slate-100 dark:border-gray-700 animate-bounce">
                    <x-heroicon-o-arrow-path class="animate-spin h-5 w-5 text-blue-600" />
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ __('Processing...') }}</span>
                </div>
            </div>

            {{-- DESKTOP: Table View --}}
            <div class="hidden md:block bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-gray-800">
                        <thead class="bg-slate-50 dark:bg-gray-950">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Patient') }}</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Doctor') }}</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Admission Date') }}</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                            @forelse ($patients as $patient)
                                <tr wire:key="patient-{{ $patient->id }}" class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150">
                                    {{-- Patient --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-4">
                                            <div class="h-10 w-10 flex-shrink-0 rounded-full bg-pink-100 dark:bg-pink-900/50 flex items-center justify-center text-pink-700 dark:text-pink-300 font-bold text-xs ring-2 ring-white dark:ring-gray-800">
                                                {{ substr($patient->first_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white group-hover:text-pink-600 dark:group-hover:text-pink-400 transition-colors">
                                                    {{ $patient->first_name }} {{ $patient->last_name }}
                                                </div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400 font-mono bg-slate-100 dark:bg-slate-800/50 px-1.5 py-0.5 rounded w-fit mt-0.5">
                                                    {{ $patient->patient_uid }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Doctor --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                            {{ $patient->admissions->first()?->doctor->name ?? __('Not Assigned') }}
                                        </div>
                                    </td>

                                    {{-- Status --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $latestAdmission = $patient->admissions->first();
                                            $admissionStatus = $latestAdmission?->status ?? 'No Admission';

                                            $config = match ($admissionStatus) {
                                                'Pending'   => ['bg' => 'bg-amber-50 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400', 'border' => 'border-amber-100 dark:border-amber-800', 'icon' => 'clock'],
                                                'Discharged'=> ['bg' => 'bg-slate-100 dark:bg-gray-800', 'text' => 'text-slate-600 dark:text-slate-400', 'border' => 'border-slate-200 dark:border-slate-700', 'icon' => 'arrow-right-on-rectangle'],
                                                'Admitted'  => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'border' => 'border-emerald-100 dark:border-emerald-800', 'icon' => 'check-circle'],
                                                default     => ['bg' => 'bg-slate-50 dark:bg-gray-800', 'text' => 'text-slate-500 dark:text-slate-400', 'border' => 'border-slate-200 dark:border-slate-700', 'icon' => 'minus-circle'],
                                            };

                                            $statusLabel = match ($admissionStatus) {
                                                'Pending'  => __('Pending Request'),
                                                'Admitted' => __('Admitted') . ($latestAdmission?->bed_id ? " (" . __('Bed') . " {$latestAdmission->bed_id})" : ''),
                                                default    => $admissionStatus,
                                            };
                                        @endphp

                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold border shadow-sm {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }}">
                                            @if ($config['icon'] == 'clock') <x-heroicon-s-clock class="w-3.5 h-3.5" /> @endif
                                            @if ($config['icon'] == 'check-circle') <x-heroicon-s-check-circle class="w-3.5 h-3.5" /> @endif
                                            @if ($config['icon'] == 'arrow-right-on-rectangle') <x-heroicon-s-arrow-right-on-rectangle class="w-3.5 h-3.5" /> @endif
                                            @if ($config['icon'] == 'minus-circle') <x-heroicon-s-minus-circle class="w-3.5 h-3.5" /> @endif
                                            {{ $statusLabel }}
                                        </span>
                                    </td>

                                    {{-- Date --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($latestAdmission?->admission_date)
                                            <div class="flex items-center gap-1.5 text-sm font-medium text-slate-600 dark:text-slate-300">
                                                <x-heroicon-m-calendar-days class="w-4 h-4 text-slate-400" />
                                                {{ \Carbon\Carbon::parse($latestAdmission->admission_date)->format('M d, Y') }}
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-400 italic">N/A</span>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            @if ($admissionStatus === 'Admitted')
                                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-500 dark:bg-gray-800 dark:text-gray-500 cursor-not-allowed border border-transparent">
                                                    <x-heroicon-s-check class="w-3.5 h-3.5 mr-1" /> {{ __('Active') }}
                                                </span>
                                            @else
                                                <button wire:click="admitPatient({{ $patient->id }})"
                                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-pink-50 text-pink-700 text-xs font-bold rounded-lg border border-pink-200 hover:bg-pink-100 dark:bg-pink-900/20 dark:text-pink-400 dark:border-pink-800 transition-colors shadow-sm">
                                                    <x-heroicon-s-plus class="w-3.5 h-3.5" /> {{ __('Admit') }}
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="w-16 h-16 bg-slate-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-gray-700">
                                            <x-heroicon-o-magnifying-glass class="h-8 w-8 text-slate-400" />
                                        </div>
                                        <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ __('No patients found') }}</h3>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MOBILE: Card View --}}
            <div class="md:hidden space-y-4">
                @forelse ($patients as $patient)
                    @php $latestAdmission = $patient->admissions->first(); @endphp
                    <div wire:key="mobile-card-{{ $patient->id }}" class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-slate-200 dark:border-gray-800 p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 flex-shrink-0 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-xs">
                                    {{ substr($patient->first_name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ $patient->first_name }} {{ $patient->last_name }}
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ $patient->patient_uid }}</p>
                                </div>
                            </div>

                            @php
                                $statusLabel = $latestAdmission?->status ?? __('None');
                                $statusClass = match($latestAdmission?->status ?? '') {
                                    'Pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'Admitted' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    default => 'bg-slate-50 text-slate-600 border-slate-100'
                                };
                            @endphp
                            <span class="px-2 py-1 rounded text-[10px] font-bold border {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                        <div class="flex justify-end border-t border-slate-100 dark:border-gray-800 pt-3">
                            @if (($latestAdmission?->status ?? '') !== 'Admitted')
                                <button wire:click="admitPatient({{ $patient->id }})" class="p-2 text-blue-600 bg-blue-50 rounded-lg">
                                    <x-heroicon-o-plus class="w-5 h-5" />
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-slate-300 dark:border-gray-700">
                        <x-heroicon-o-magnifying-glass class="mx-auto h-12 w-12 text-slate-300" />
                        <h3 class="mt-2 text-sm font-bold text-slate-900 dark:text-white">{{ __('No patients found') }}</h3>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($patients->hasPages())
                <div class="mt-8">
                    {{ $patients->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Admission Selection Modal --}}
    <div x-data="{ show: @entangle('showAdmissionModal') }" x-show="show" x-cloak class="relative z-50">
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="show" @click.away="$wire.set('showAdmissionModal', false)"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all sm:w-full sm:max-w-2xl border border-slate-100 dark:border-gray-800">

                    {{-- Modal Header --}}
                    <div class="bg-white dark:bg-gray-900 px-6 py-5 border-b border-slate-100 dark:border-gray-800 flex items-center justify-between sticky top-0 z-10">
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ __('Select Admission Request') }}</h3>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Choose a request to process for admission.') }}</p>
                        </div>
                        <button type="button" wire:click="$wire.set('showAdmissionModal', false)" class="rounded-xl bg-slate-50 dark:bg-gray-800 p-2 text-slate-400 hover:text-slate-600">
                            <x-heroicon-o-x-mark class="h-6 w-6" />
                        </button>
                    </div>

                    <div class="px-6 py-6 bg-slate-50 dark:bg-gray-900/50 max-h-[60vh] overflow-y-auto custom-scrollbar">
                        @if ($recentAdmissions->isEmpty())
                            <div class="text-center py-8">
                                <x-heroicon-o-clipboard-document class="w-12 h-12 text-slate-400 mx-auto mb-3" />
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ __('No pending requests') }}</h3>
                                <div class="mt-6 flex justify-center gap-3">
                                    <button wire:click="confirmAdmission(false)" class="inline-flex items-center px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-bold shadow-md hover:bg-blue-700">
                                        {{ __('Create New Admission') }}
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($recentAdmissions as $ad)
                                    <div wire:click="$set('selectedAdmissionId', {{ $ad->id }})"
                                        class="cursor-pointer group relative flex items-start gap-4 p-4 rounded-xl border transition-all duration-200
                                        {{ $selectedAdmissionId === $ad->id ? 'bg-pink-50 border-pink-200 shadow-sm dark:bg-pink-900/20' : 'bg-white border-slate-200 hover:border-blue-300 dark:bg-gray-800' }}">

                                        <div class="flex h-5 items-center mt-1">
                                            <input type="radio" name="selectedAdmission" value="{{ $ad->id }}" class="h-4 w-4 text-pink-600" @checked($selectedAdmissionId === $ad->id)>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start mb-1">
                                                <p class="text-sm font-bold text-slate-900 dark:text-white">
                                                    {{ $ad->created_at->format('M d, Y') }} <span class="text-slate-400 font-normal mx-1">•</span> {{ $ad->created_at->format('H:i') }}
                                                </p>
                                                <span class="inline-flex items-center rounded-lg px-2 py-1 text-xs font-bold {{ $ad->status === 'Admitted' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-800' }}">
                                                    {{ __($ad->status) }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-slate-600 dark:text-slate-300 mb-1">
                                                <span class="font-bold">{{ __('Requested by:') }}</span> {{ $ad->doctor->name ?? __('Unknown') }}
                                            </p>
                                            <p class="text-sm text-slate-500 italic">"{{ $ad->reason_for_admission ?? __('No reason provided.') }}"</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-gray-800">
                                <button type="button" wire:click="$wire.set('showAdmissionModal', false)" class="px-4 py-2.5 rounded-xl text-sm font-bold text-slate-500">{{ __('Cancel') }}</button>
                                <button type="button" wire:click="confirmAdmission(true)" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-pink-600 text-white text-sm font-bold shadow-md hover:bg-pink-700" {{ !$selectedAdmissionId ? 'disabled' : '' }}>
                                    {{ __('Process Selected') }}
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
