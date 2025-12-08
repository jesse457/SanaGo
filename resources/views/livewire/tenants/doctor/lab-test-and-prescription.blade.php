<main class="w-full h-full bg-gray-50 dark:bg-gray-900 font-sans text-gray-600 dark:text-gray-300 overflow-y-auto">
    <div class="max-w-7xl mx-auto p-6">

        {{-- Breadcrumb --}}
        <nav class="mb-6 flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2">
                <li>
                    <a href="{{ route('doctor.dashboard') }}" wire:navigate class="text-xs font-medium text-gray-500 hover:text-blue-600 dark:text-gray-400">
                        <x-heroicon-s-home class="w-3 h-3 inline mr-1" /> {{ __('doctor.home') }}
                    </a>
                </li>
                <li><x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300" /></li>
                <li>
                    <a href="{{ route('doctor.patient-info', $consultation->patient->id) }}" wire:navigate class="text-xs font-medium text-gray-500 hover:text-blue-600 dark:text-gray-400">
                         {{ optional($consultation->patient)->first_name }} {{ optional($consultation->patient)->last_name }}
                    </a>
                </li>
                <li><x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300" /></li>
                <li><span class="text-xs font-medium text-gray-900 dark:text-white">Consultation #{{ $consultation->id }}</span></li>
            </ol>
        </nav>

        {{-- Consultation Header Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <x-heroicon-m-calendar-days class="w-5 h-5 text-blue-600" />
                        {{ optional($consultation->created_at)->format('F j, Y') }}
                        <span class="text-gray-400 font-normal">at {{ optional($consultation->created_at)->format('g:i A') }}</span>
                    </h2>
                    <p class="text-sm text-gray-500 mt-1 pl-7">
                        {{ __('doctor.consultation_id') }}: #{{ $consultation->id }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 font-bold">
                        {{ substr(optional($consultation->doctor)->name ?? 'D', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 uppercase font-bold">{{ __('doctor.attended_by') }}</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ optional($consultation->doctor)->name ?? __('doctor.not_available') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Diagnosis --}}
                <div>
                    <h3 class="font-bold text-xs uppercase text-gray-500 mb-2">{{ __('doctor.diagnosis') }}</h3>
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-100 dark:border-blue-800">
                        <p class="text-gray-900 dark:text-gray-100 text-sm font-medium">
                            {{ $consultation->diagnosis_text ?? __('doctor.no_diagnosis_provided') }}
                        </p>
                    </div>
                </div>
                {{-- Notes --}}
                <div>
                    <h3 class="font-bold text-xs uppercase text-gray-500 mb-2">{{ __('doctor.notes') }}</h3>
                    <div class="bg-gray-50 dark:bg-gray-900/50 p-4 rounded-lg border border-gray-100 dark:border-gray-700">
                        <p class="text-gray-600 dark:text-gray-400 text-sm">
                            {{ $consultation->general_notes ?? ($consultation->soap_notes ?? __('doctor.no_notes_available')) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs --}}
        <div x-data="{ tab: 'prescriptions' }">
             <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button @click="tab = 'prescriptions'"
                        class="group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                        x-bind:class="tab === 'prescriptions' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'">
                        <x-heroicon-m-document-text class="w-5 h-5 mr-2" x-bind:class="tab === 'prescriptions' ? 'text-blue-500' : 'text-gray-400'" />
                        {{ __('doctor.prescriptions') }}
                    </button>

                    <button @click="tab = 'labResults'"
                        class="group inline-flex items-center py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                        x-bind:class="tab === 'labResults' ? 'border-teal-500 text-teal-600 dark:text-teal-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400'">
                        <x-heroicon-m-beaker class="w-5 h-5 mr-2" x-bind:class="tab === 'labResults' ? 'text-teal-500' : 'text-gray-400'" />
                        {{ __('doctor.lab_results') }}
                    </button>
                </nav>
            </div>

            <div class="space-y-6">
                {{-- Prescriptions --}}
                <div x-show="tab === 'prescriptions'">
                    @forelse($consultation->prescription ?? [] as $prescription)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
                            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white text-sm">
                                        {{ __('doctor.prescription_num', ['id' => $prescription->id]) }}
                                    </h3>
                                    <span class="text-xs text-gray-500">{{ optional($prescription->prescription_date)->format('F j, Y') }}</span>
                                </div>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-800">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">{{ __('doctor.medication') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">{{ __('doctor.dosage') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">{{ __('doctor.frequency') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">{{ __('doctor.duration') }}</th>
                                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">{{ __('doctor.qty') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                        @foreach ($prescription->items ?? [] as $item)
                                            <tr>
                                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-medium">{{ optional($item->medication)->name }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $item->dosage }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $item->frequency }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $item->duration }}</td>
                                                <td class="px-6 py-4 text-sm text-center font-bold">{{ $item->quantity_prescribed }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-8 text-center border border-gray-200 dark:border-gray-700">
                            <span class="text-gray-500">{{ __('doctor.no_prescriptions_found') }}</span>
                        </div>
                    @endforelse
                </div>

                {{-- Lab Results --}}
                <div x-show="tab === 'labResults'">
                    @forelse($consultation->labResults ?? [] as $result)
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                <div>
                                    <h3 class="font-bold text-gray-900 dark:text-white text-sm">{{ __('doctor.lab_result_num', ['id' => $result->id]) }}</h3>
                                    <span class="text-xs text-gray-500">{{ optional($result->result_date)->format('F j, Y') }}</span>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $result->status === 'Completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $result->status }}
                                </span>
                            </div>
                            <div class="p-6">
                                <div class="mb-4">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase mb-1">{{ __('doctor.results_findings') }}</h4>
                                    <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap">{{ $result->results_text }}</p>
                                </div>
                                @if ($result->attachments?->isNotEmpty())
                                    <div class="flex gap-2 pt-4 border-t border-gray-100 dark:border-gray-700">
                                        @foreach ($result->attachments as $att)
                                            <button wire:click="previewAttachment({{ $att->id }})" class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded text-xs font-medium text-gray-700 bg-white hover:bg-gray-50">
                                                <x-heroicon-m-paper-clip class="w-3.5 h-3.5 mr-1.5" /> {{ Str::limit($att->file_name, 20) }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                         <div class="bg-white dark:bg-gray-800 rounded-lg p-8 text-center border border-gray-200 dark:border-gray-700">
                            <span class="text-gray-500">{{ __('doctor.no_lab_results_found') }}</span>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</main>
