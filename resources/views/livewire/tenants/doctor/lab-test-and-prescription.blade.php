<main class="h-screen overflow-y-auto w-full bg-slate-50 dark:bg-gray-900 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8">

        {{-- Breadcrumb --}}
        <nav class="mb-6 flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li>
                    <a href="{{ route('doctor.dashboard') }}" wire:navigate
                        class="text-sm text-gray-700 hover:text-blue-600 dark:text-gray-400">
                        <x-heroicon-s-home class="w-4 h-4 inline mr-1" /> {{ __('doctor.home') }}
                    </a>
                </li>
                <li><x-heroicon-s-chevron-right class="w-3 h-3 text-gray-400" /></li>
                <li>
                    <a href="{{ route('doctor.patients') }}" wire:navigate
                        class="text-sm text-gray-700 hover:text-blue-600 dark:text-gray-400">{{ __('doctor.patients') }}</a>
                </li>

                <li><x-heroicon-s-chevron-right class="w-3 h-3 text-slate-300 mx-1" /></li>
                <li>
                    <a href="{{ route('doctor.patient-info', $consultation->patient->id) }}" wire:navigate
                        class="text-sm text-gray-700 hover:text-blue-600 dark:text-gray-400">
                        {{ optional($consultation->patient)->first_name }}
                        {{ optional($consultation->patient)->last_name }}
                    </a>
                </li>

                <li><x-heroicon-s-chevron-right class="w-3 h-3 text-slate-300 mx-1" /></li>
                <li>
                    <a href="#" wire:navigate
                        class="text-sm text-gray-400 dark:text-gray-400">
                        Details
                    </a>
                </li>
            </ol>
        </nav>

        {{-- Consultation Header Card --}}
        <div
            class="relative bg-white dark:bg-gray-800 rounded-2xl p-6 sm:p-8 shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] mb-8 border border-slate-100 dark:border-gray-700 overflow-hidden">

            {{-- Decorative background --}}
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-blue-50 dark:bg-blue-900/20 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none">
            </div>

            <div class="relative z-10">
                <div
                    class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6 pb-6 border-b border-slate-100 dark:border-gray-700">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                            <div
                                class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg text-blue-600 dark:text-blue-400">
                                <x-heroicon-s-calendar-days class="w-5 h-5" />
                            </div>
                            <h2 class="text-xl font-bold text-slate-800 dark:text-white">
                                {{ optional($consultation->created_at)->format('F j, Y') }}
                                <span class="text-slate-400 font-normal text-lg">at
                                    {{ optional($consultation->created_at)->format('g:i A') }}</span>
                            </h2>
                        </div>
                        <p class="text-sm text-slate-500 ml-12">
                            {{ __('doctor.consultation_id') }}: #{{ $consultation->id }}
                        </p>
                    </div>
                    <div
                        class="flex items-center gap-3 bg-slate-50 dark:bg-gray-700/50 px-4 py-3 rounded-xl border border-slate-100 dark:border-gray-600">
                        <div
                            class="h-10 w-10 rounded-full bg-white dark:bg-gray-600 flex items-center justify-center shadow-sm text-slate-600 dark:text-slate-300 font-bold">
                            {{ substr(optional($consultation->doctor)->name ?? 'D', 0, 1) }}
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 uppercase font-bold tracking-wide">
                                {{ __('doctor.attended_by') }}</p>
                            <p class="font-semibold text-slate-700 dark:text-slate-200">
                                {{ optional($consultation->doctor)->name ?? __('doctor.not_available') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Diagnosis Section --}}
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400">
                            <x-heroicon-o-clipboard-document-check class="w-5 h-5" />
                            <h3 class="font-bold text-sm uppercase tracking-wide">{{ __('doctor.diagnosis') }}</h3>
                        </div>
                        <div
                            class="bg-blue-50/50 dark:bg-blue-900/10 p-4 rounded-xl border border-blue-100 dark:border-blue-800/30">
                            <p class="text-slate-800 dark:text-slate-200 font-medium leading-relaxed">
                                {{ $consultation->diagnosis_text ?? __('doctor.no_diagnosis_provided') }}
                            </p>
                        </div>
                    </div>

                    {{-- Notes Section --}}
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-slate-500 dark:text-slate-400">
                            <x-heroicon-o-pencil-square class="w-5 h-5" />
                            <h3 class="font-bold text-sm uppercase tracking-wide">{{ __('doctor.notes') }}</h3>
                        </div>
                        <div
                            class="bg-slate-50 dark:bg-gray-900/50 p-4 rounded-xl border border-slate-100 dark:border-gray-700">
                            <p class="text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
                                {{ $consultation->general_notes ?? ($consultation->soap_notes ?? __('doctor.no_notes_available')) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alpine Tabs --}}
        <div x-data="{ tab: 'prescriptions' }" class="min-h-[400px]">

            {{-- Tab Navigation --}}
            <div class="mb-6 border-b border-slate-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="tab = 'prescriptions'"
                        class="group relative py-4 px-1 flex items-center gap-2 text-sm font-medium transition-colors duration-200"
                        x-bind:class="tab === 'prescriptions' ? 'text-blue-600 dark:text-blue-400' :
                            'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'">

                        <x-heroicon-o-document-text class="w-5 h-5"
                            x-bind:class="tab === 'prescriptions' ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-500'" />
                        {{ __('doctor.prescriptions') }}

                        <span
                            class="absolute bottom-0 left-0 w-full h-0.5 bg-blue-600 rounded-t-full transition-transform duration-300 origin-left"
                            x-bind:class="tab === 'prescriptions' ? 'scale-x-100' : 'scale-x-0'"></span>
                    </button>

                    <button @click="tab = 'labResults'"
                        class="group relative py-4 px-1 flex items-center gap-2 text-sm font-medium transition-colors duration-200"
                        x-bind:class="tab === 'labResults' ? 'text-teal-600 dark:text-teal-400' :
                            'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'">

                        <x-heroicon-o-beaker class="w-5 h-5"
                            x-bind:class="tab === 'labResults' ? 'text-teal-600' : 'text-slate-400 group-hover:text-slate-500'" />
                        {{ __('doctor.lab_results') }}

                        <span
                            class="absolute bottom-0 left-0 w-full h-0.5 bg-teal-600 rounded-t-full transition-transform duration-300 origin-left"
                            x-bind:class="tab === 'labResults' ? 'scale-x-100' : 'scale-x-0'"></span>
                    </button>
                </nav>
            </div>

            {{-- Tab Content Area --}}
            <div class="space-y-6">

                {{-- 1. Prescriptions Content --}}
                <div x-show="tab === 'prescriptions'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0">

                    @forelse($consultation->prescription ?? [] as $prescription)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 overflow-hidden mb-6">
                            {{-- Card Header --}}
                            <div
                                class="px-6 py-4 border-b border-slate-100 dark:border-gray-700 bg-slate-50/50 dark:bg-gray-700/30 flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="p-2 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-slate-100 dark:border-gray-600">
                                        <x-heroicon-m-receipt-percent
                                            class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-800 dark:text-white">
                                            {{ __('doctor.prescription_num', ['id' => $prescription->id]) }}</h3>
                                        <span
                                            class="text-xs text-slate-500">{{ optional($prescription->prescription_date)->format('F j, Y') }}</span>
                                    </div>
                                </div>

                                @if ($prescription->general_notes)
                                    <div class="hidden sm:block max-w-md text-right">
                                        <p class="text-xs text-slate-400 italic truncate"
                                            title="{{ $prescription->general_notes }}">
                                            "{{ $prescription->general_notes }}"
                                        </p>
                                    </div>
                                @endif
                            </div>

                            {{-- Responsive Table --}}
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200 dark:divide-gray-700">
                                    <thead class="bg-slate-50 dark:bg-gray-700/50">
                                        <tr>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                {{ __('doctor.medication') }}</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                {{ __('doctor.dosage') }}</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                {{ __('doctor.frequency') }}</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                {{ __('doctor.duration') }}</th>
                                            <th scope="col"
                                                class="px-6 py-3 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                                                {{ __('doctor.qty') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody
                                        class="bg-white dark:bg-gray-800 divide-y divide-slate-100 dark:divide-gray-700">
                                        @foreach ($prescription->items ?? [] as $item)
                                            <tr class="hover:bg-slate-50 dark:hover:bg-gray-700/50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div
                                                            class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                                                            <span class="font-bold text-xs">Rx</span>
                                                        </div>
                                                        <div class="ml-4">
                                                            <div
                                                                class="text-sm font-semibold text-slate-900 dark:text-white">
                                                                {{ optional($item->medication)->name ?? __('doctor.unknown') }}
                                                            </div>
                                                            <div class="text-xs text-slate-500">
                                                                {{ optional($item->medication)->generic_name ?? '' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                                    <span
                                                        class="px-2 py-1 bg-slate-100 dark:bg-gray-700 rounded text-xs font-medium">{{ $item->dosage ?? '-' }}</span>
                                                </td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                                    {{ $item->frequency ?? '-' }}</td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300">
                                                    {{ $item->duration ?? '-' }}</td>
                                                <td
                                                    class="px-6 py-4 whitespace-nowrap text-sm font-bold text-center text-slate-800 dark:text-slate-200">
                                                    {{ $item->quantity_prescribed ?? 0 }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @empty
                        <div
                            class="flex flex-col items-center justify-center py-16 text-center bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-slate-300 dark:border-gray-700">
                            <div
                                class="w-16 h-16 bg-slate-50 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                <x-heroicon-o-archive-box-x-mark class="w-8 h-8 text-slate-400" />
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
                                {{ __('doctor.no_prescriptions_found') }}</h3>
                            <p class="text-slate-500 dark:text-slate-400 mt-1">
                                {{ __('doctor.no_prescriptions_subtext') }}</p>
                        </div>
                    @endforelse
                </div>

                {{-- 2. Lab Results Content --}}
                <div x-show="tab === 'labResults'" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0">

                    @forelse($consultation->labResults ?? [] as $result)
                        <div
                            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-slate-200 dark:border-gray-700 mb-6 hover:shadow-md transition-shadow">
                            {{-- Lab Header --}}
                            <div
                                class="px-6 py-4 border-b border-slate-100 dark:border-gray-700 flex flex-col sm:flex-row justify-between sm:items-center gap-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="p-2 bg-teal-50 dark:bg-teal-900/30 rounded-lg text-teal-600 dark:text-teal-400">
                                        <x-heroicon-o-beaker class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-800 dark:text-white">
                                            {{ __('doctor.lab_result_num', ['id' => $result->id]) }}</h3>
                                        <span
                                            class="text-xs text-slate-500">{{ optional($result->result_date)->format('F j, Y') }}</span>
                                    </div>
                                </div>

                                {{-- Status Badge --}}
                                <div @class([
                                    'flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold border',
                                    'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-900/30 dark:border-amber-800 dark:text-amber-200' =>
                                        $result->status === 'Pending',
                                    'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-900/30 dark:border-emerald-800 dark:text-emerald-200' =>
                                        $result->status === 'Completed',
                                    'bg-red-50 border-red-200 text-red-700 dark:bg-red-900/30 dark:border-red-800 dark:text-red-200' =>
                                        $result->status === 'Cancelled',
                                ])>
                                    <span class="relative flex h-2 w-2">
                                        <span
                                            class="animate-ping absolute inline-flex h-full w-full rounded-full opacity-75 bg-current"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-current"></span>
                                    </span>
                                    {{ __("doctor.status_{$result->status}") }}
                                </div>
                            </div>

                            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
                                {{-- Results Text --}}
                                <div class="space-y-2">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide">
                                        {{ __('doctor.results_findings') }}</h4>
                                    <div
                                        class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">
                                        {{ $result->results_text ?? __('doctor.no_result_text_provided') }}
                                    </div>
                                </div>

                                {{-- Analysis --}}
                                <div class="space-y-2">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide">
                                        {{ __('doctor.analysis_comments') }}</h4>
                                    <div
                                        class="bg-slate-50 dark:bg-gray-900/50 p-4 rounded-lg border border-slate-100 dark:border-gray-700 text-sm text-slate-600 dark:text-slate-400 italic">
                                        "{{ $result->analysis_comments ?? __('doctor.no_comments_provided') }}"
                                    </div>
                                </div>
                            </div>

                            {{-- Attachments Footer --}}
                            @if ($result->attachments?->isNotEmpty())
                                <div
                                    class="px-6 py-4 bg-slate-50/50 dark:bg-gray-700/20 border-t border-slate-100 dark:border-gray-700 flex flex-wrap items-center gap-3">
                                    <span
                                        class="text-xs font-medium text-slate-500">{{ __('doctor.attachments') }}:</span>
                                    @foreach ($result->attachments as $att)
                                        <button type="button" wire:click="previewAttachment({{ $att->id }})"
                                            class="group flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-600 rounded-lg hover:border-blue-400 dark:hover:border-blue-500 transition-all shadow-sm">
                                            <div
                                                class="p-1 rounded bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                                <x-heroicon-o-paper-clip class="w-3 h-3" />
                                            </div>
                                            <span
                                                class="text-xs font-medium text-slate-600 dark:text-slate-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                {{ Str::limit($att->file_name, 25) }}
                                            </span>
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div
                            class="flex flex-col items-center justify-center py-16 text-center bg-white dark:bg-gray-800 rounded-2xl border border-dashed border-slate-300 dark:border-gray-700">
                            <div
                                class="w-16 h-16 bg-teal-50 dark:bg-teal-900/20 rounded-full flex items-center justify-center mb-4">
                                <x-heroicon-o-beaker class="w-8 h-8 text-teal-400" />
                            </div>
                            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">
                                {{ __('doctor.no_lab_results_found') }}</h3>
                            <p class="text-slate-500 dark:text-slate-400 mt-1">
                                {{ __('doctor.no_lab_results_subtext') }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Glassmorphism Attachment Preview Modal --}}
    <div x-data="{
        open: @entangle('showAttachmentPreview'),
        isFullscreen: false
    }" x-show="open" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 backdrop-blur-none" x-transition:enter-end="opacity-100 backdrop-blur-md"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 backdrop-blur-md"
        x-transition:leave-end="opacity-0 backdrop-blur-none"
        class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 p-4 backdrop-blur-sm">

        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden transform transition-all"
            @click.outside="open = false" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0">

            {{-- Modal Header --}}
            <div
                class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-900 z-10">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <x-heroicon-s-document-text class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white">
                            {{ __('doctor.attachment_preview') }}</h2>
                    </div>
                </div>
                <button @click="open = false"
                    class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-gray-800 text-slate-400 hover:text-red-500 transition-colors">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="flex-1 overflow-auto bg-slate-50 dark:bg-gray-950 p-6 relative">
                @if ($attachmentPreviewUrl)
                    @if (Str::startsWith($attachmentPreviewMime, 'image/'))
                        <div class="flex items-center justify-center min-h-full">
                            <img src="{{ $attachmentPreviewUrl }}" alt="{{ __('doctor.attachment_preview') }}"
                                class="max-w-full max-h-full object-contain rounded-lg shadow-lg border border-slate-200 dark:border-gray-700" />
                        </div>
                    @elseif(Str::startsWith($attachmentPreviewMime, 'application/pdf'))
                        <iframe src="{{ $attachmentPreviewUrl }}"
                            class="w-full h-full rounded-lg shadow-sm border border-slate-200 dark:border-gray-700"
                            frameborder="0"></iframe>
                    @else
                        <div class="flex flex-col items-center justify-center h-full text-center py-12">
                            <div
                                class="w-20 h-20 bg-slate-200 dark:bg-gray-800 rounded-2xl flex items-center justify-center mb-6 shadow-inner">
                                <x-heroicon-o-document-arrow-down class="w-10 h-10 text-slate-400" />
                            </div>
                            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">
                                {{ __('doctor.file_type_not_previewable') }}</h3>
                            <p class="text-slate-500 dark:text-slate-400 mb-8 max-w-md">
                                {{ __('doctor.unsupported_file_type_desc') }}</p>
                            <a href="{{ $attachmentPreviewUrl }}" target="_blank"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-600/20 transition-all hover:-translate-y-0.5">
                                <x-heroicon-m-arrow-down-tray class="w-5 h-5" />
                                {{ __('doctor.download_file') }}
                            </a>
                        </div>
                    @endif
                @else
                    <div class="flex items-center justify-center h-full">
                        <x-heroicon-o-arrow-path class="w-8 h-8 animate-spin text-slate-400" />
                    </div>
                @endif
            </div>
        </div>
    </div>
</main>

@script
    <script>
        $wire.on('open-new-tab', (event) => {
            window.open(event.url, '_blank');
        });
    </script>
@endscript
