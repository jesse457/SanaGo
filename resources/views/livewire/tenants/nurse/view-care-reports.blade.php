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
                                    <span class="text-gray-900 dark:text-white">{{ __('nurse.history_breadcrumb') }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title and Button --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7 flex items-center gap-2">
                                <x-heroicon-s-clock class="w-6 h-6 text-pink-500" />
                                {{ __('nurse.patient_history_title') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Review past nursing notes, shift observations, and interventions.
                            </p>
                        </div>

                        {{-- LINK TO CREATE REPORT --}}
                        <a href="{{ route('nurse.create-care-report') }}" wire:navigate
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-bold text-pink-600 bg-pink-50 hover:bg-pink-100 dark:bg-pink-900/20 dark:text-pink-300 dark:hover:bg-pink-900/40 rounded-xl transition-colors">
                            <x-heroicon-o-pencil-square class="w-4 h-4" />
                            {{ __('Write New Report') }}
                        </a>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="p-4 sm:p-6 pb-20">

            {{-- Filter Bar --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 p-6 mb-6">
                <label for="patientSelect" class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                    {{ __('Select Patient to View History') }}
                </label>
                <div class="relative max-w-xl">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <x-heroicon-o-magnifying-glass class="h-5 w-5 text-slate-400" />
                    </div>
                    <select id="patientSelect" wire:model.live="patient_id"
                        class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-3 pl-10 shadow-sm focus:border-pink-500 focus:ring-pink-500 sm:text-sm">
                        <option value="">Select a Patient...</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">
                                {{ $patient->name }} (ID: {{ $patient->patient_uid ?? $patient->id }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Timeline Container --}}
            <div class="space-y-6">
                @if(!$patient_id)
                    <div class="text-center py-12 bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-slate-200 dark:border-gray-800">
                        <x-heroicon-o-user class="w-12 h-12 text-slate-300 mx-auto mb-3" />
                        <h3 class="text-lg font-medium text-slate-900 dark:text-white">No Patient Selected</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm">Please select a patient above.</p>
                    </div>
                @elseif(count($reports) === 0)
                    <div class="text-center py-12 bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-slate-200 dark:border-gray-800">
                        <x-heroicon-o-document-magnifying-glass class="w-12 h-12 text-slate-300 mx-auto mb-3" />
                        <h3 class="text-lg font-medium text-slate-900 dark:text-white">No Care Reports Found</h3>
                    </div>
                @else
                    <div class="relative border-l-2 border-slate-200 dark:border-gray-800 ml-4 space-y-8">
                        @foreach($reports as $report)
                            <div class="relative pl-8">
                                <div class="absolute -left-[9px] top-0 bg-white dark:bg-gray-950 p-1">
                                    <div class="w-3 h-3 rounded-full {{ $report->shift_type === 'Night' ? 'bg-indigo-500' : ($report->shift_type === 'Afternoon' ? 'bg-orange-500' : 'bg-yellow-500') }}"></div>
                                </div>
                                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                                    <div class="px-6 py-4 border-b border-slate-100 dark:border-gray-800 bg-slate-50/50 dark:bg-gray-800/50 flex justify-between gap-2">
                                        <div class="flex items-center gap-3">
                                            <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wide bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">{{ $report->shift_type }} Shift</span>
                                            <span class="text-sm font-semibold text-slate-900 dark:text-white">{{ $report->report_time->format('M d, Y • h:i A') }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-slate-500"><x-heroicon-o-user-circle class="w-4 h-4" /> {{ $report->nurse->name ?? 'Unknown' }}</div>
                                    </div>
                                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <h4 class="text-xs font-bold uppercase text-slate-400 mb-2">Interventions</h4>
                                            <div class="text-sm text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-gray-800 p-3 rounded-xl border border-slate-100 dark:border-gray-700">{{ $report->interventions }}</div>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold uppercase text-slate-400 mb-2">Observations</h4>
                                            <div class="text-sm text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-gray-800 p-3 rounded-xl border border-slate-100 dark:border-gray-700">{{ $report->observations }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</main>
