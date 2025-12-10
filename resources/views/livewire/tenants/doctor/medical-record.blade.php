<div class="bg-gray-50 dark:bg-gray-900 overflow-hidden h-screen flex flex-col"
     x-data="{ activeTab: 'clinical' }">

    {{-- HEADER --}}
    <header class="flex-shrink-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm z-20">
        <div class="px-6 py-4 flex items-center justify-between">
            <div>
                <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                    <ol class="flex items-center space-x-2">
                        <li>
                            <a href="{{ route('doctor.dashboard') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                {{ __('doctor.home') }}
                            </a>
                        </li>
                        <li><x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300" /></li>
                        <li class="text-gray-900 dark:text-white">{{ __('doctor.patients') }}</li>
                    </ol>
                </nav>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white tracking-tight">
                    {{ __('doctor.patient_consultation') }}
                </h1>
            </div>
        </div>
    </header>

    {{-- MAIN CONTENT WRAPPER --}}
    <div class="flex flex-1 overflow-hidden">

        {{-- 🟢 LEFT SIDEBAR (PATIENT SEARCH) --}}
        <aside class="w-full md:w-80 lg:w-96 flex-shrink-0 flex flex-col border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-800 z-10 h-full">

            {{-- Search Bar Section --}}
            <div class="p-4 border-b border-gray-100 dark:border-gray-700 relative z-20">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Find Patient</label>
                <div class="relative">
                    <x-heroicon-o-magnifying-glass class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" />

                    {{-- Loading Icon --}}
                    <div wire:loading wire:target="patientQuery" class="absolute right-3 top-2.5">
                        <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <input type="text"
                           wire:model.live.debounce.300ms="patientQuery"
                           class="w-full pl-10 pr-10 py-2 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                           placeholder="Name or ID..."
                           autocomplete="off">

                    {{-- Patient Results --}}
                    @if(strlen($patientQuery) >= 2)
                        <div class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50">
                            @if($patientResults->isNotEmpty())
                                @foreach($patientResults as $res)
                                    <button wire:click="selectPatient({{ $res->id }})"
                                            class="w-full text-left px-4 py-3 hover:bg-blue-50 dark:hover:bg-gray-700 border-b border-gray-50 dark:border-gray-700 last:border-0 transition-colors flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                            {{ substr($res->first_name, 0, 1) }}{{ substr($res->last_name, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-gray-900 dark:text-white text-sm truncate">{{ $res->first_name }} {{ $res->last_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $res->patient_uid }}</div>
                                        </div>
                                    </button>
                                @endforeach
                            @else
                                <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                    <x-heroicon-o-face-frown class="w-8 h-8 mx-auto mb-2 text-gray-400"/>
                                    <p class="text-sm font-medium">No patient found</p>
                                    <p class="text-xs">Try a different name or ID</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Patient Card --}}
            @if($patient)
                <div class="flex-1 overflow-y-auto p-5 custom-scrollbar">
                    <div class="flex flex-col items-center text-center mb-6">
                        @if($patient->profile_picture)
                            <img src="{{ Storage::disk('s3')->temporaryUrl($patient->profile_picture, now()->addMinutes(10)) }}" class="w-20 h-20 rounded-full object-cover border-4 border-white shadow-md mb-3">
                        @else
                            <div class="w-20 h-20 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold mb-3 shadow-md">
                                {{ substr($patient->first_name, 0, 1) }}
                            </div>
                        @endif
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $patient->first_name }} {{ $patient->last_name }}</h2>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 mt-1">
                            {{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} Years • {{ ucfirst($patient->gender) }}
                        </span>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-lg border border-gray-100 dark:border-gray-700">
                            <span class="text-xs font-semibold text-gray-400 uppercase">Contact</span>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mt-0.5">{{ $patient->phone_number ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-red-50 dark:bg-red-900/10 p-3 rounded-lg border border-red-100 dark:border-red-800/30">
                            <span class="text-xs font-semibold text-red-400 uppercase flex items-center gap-1">
                                <x-heroicon-s-exclamation-triangle class="w-3 h-3"/> Allergies
                            </span>
                            <p class="text-sm font-medium text-red-800 dark:text-red-300 mt-0.5">
                                {{ $patient->allergies ?? 'No known allergies' }}
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center text-gray-400 p-6 text-center">
                    <x-heroicon-o-user class="w-16 h-16 mb-2 opacity-50"/>
                    <p class="text-sm">Select a patient to begin</p>
                </div>
            @endif
        </aside>

        {{-- 🔵 RIGHT MAIN --}}
        <main class="flex-1 flex flex-col h-full bg-gray-50 dark:bg-gray-900 min-w-0 relative">
            @if($selectedPatientId)
                {{-- Tabs Header --}}
                <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 pt-4 flex items-center justify-between flex-shrink-0 z-10">
                    <div class="flex space-x-6 overflow-x-auto no-scrollbar">
                        @foreach(['clinical' => 'Clinical Notes', 'rx' => 'Prescriptions', 'labs' => 'Labs & Tests', 'files' => 'Attachments'] as $key => $label)
                            <button @click="activeTab = '{{ $key }}'"
                                    :class="activeTab === '{{ $key }}' ? 'border-blue-600 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 hover:border-gray-300'"
                                    class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition-colors flex items-center gap-2 outline-none focus:outline-none">
                                 {{ $label }}
                                 @if($key === 'files' && (count($attachments) > 0 || count($storedAttachments) > 0))
                                    <span class="bg-blue-100 text-blue-600 text-[10px] px-1.5 py-0.5 rounded-full">{{ count($attachments) + count($storedAttachments) }}</span>
                                 @endif
                            </button>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-2 pb-2 pl-4">
                        <span wire:loading class="text-xs text-gray-500 animate-pulse">Saving...</span>
                        <button wire:click="saveDraft" wire:loading.attr="disabled"
                                class="px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-600">
                            Draft
                        </button>
                        <button wire:click="saveAndSign" wire:loading.attr="disabled"
                                wire:confirm="Finalize this record?"
                                class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium flex items-center gap-2">
                            <x-heroicon-m-check-badge class="w-4 h-4"/> Sign
                        </button>
                    </div>
                </div>

                {{-- Scrollable Content Area --}}
                <div class="flex-1 overflow-y-auto p-6 custom-scrollbar">

                    {{-- 1. CLINICAL TAB --}}
                    <div x-show="activeTab === 'clinical'" class="space-y-6 max-w-4xl mx-auto" x-transition.opacity>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-2">Chief Complaint <span class="text-red-500">*</span></label>
                            <textarea wire:model.blur="complaint" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 focus:ring-blue-500 focus:border-blue-500" placeholder="Reason for visit..."></textarea>
                            @error('complaint') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-2">Clinical Notes & Exam</label>
                            <textarea wire:model.blur="clinicalNotes" rows="6" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 focus:ring-blue-500 focus:border-blue-500" placeholder="Detailed notes..."></textarea>
                        </div>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-200 mb-2">Diagnosis</label>
                            <textarea wire:model.blur="diagnosisText" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 focus:ring-blue-500 focus:border-blue-500" placeholder="Final diagnosis..."></textarea>
                        </div>
                    </div>

                    {{-- 2. PRESCRIPTIONS TAB (DROPDOWN) --}}
                    <div x-show="activeTab === 'rx'" class="space-y-6 max-w-5xl mx-auto" x-cloak>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-visible">

                            {{-- Dropdown Selection for Medications --}}
                            <div class="mb-6 relative z-30" x-data="{ tempMedId: '' }">
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Add Medication</label>
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <select x-model="tempMedId"
                                                class="w-full pl-3 pr-10 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 text-gray-900 dark:text-white">
                                            <option value="">Select a medication...</option>
                                            @foreach($allMedications as $med)
                                                <option wire:key="med-opt-{{ $med->id }}" value="{{ $med->id }}">{{ $med->name }} (Stock: {{ $med->stock_quantity }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button
                                        type="button"
                                        :disabled="!tempMedId"
                                        wire:click="addMedication(tempMedId)"
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg flex items-center gap-2 transition-colors">
                                        <x-heroicon-m-plus class="w-5 h-5"/>
                                        Add
                                    </button>
                                </div>
                            </div>

                            {{-- Data Grid --}}
                            @if(count($prescriptionItems) > 0)
                                <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-700">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Drug</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Dosage</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Freq</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-32">Duration</th>
                                                <th class="w-10"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach($prescriptionItems as $index => $item)
                                                <tr wire:key="rx-item-{{ $index }}">
                                                    <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ $item['name'] }}</td>
                                                    <td class="px-4 py-2"><input type="text" wire:model="prescriptionItems.{{ $index }}.dosage" class="w-full text-sm border-gray-200 rounded p-1" placeholder="500mg"></td>
                                                    <td class="px-4 py-2"><input type="text" wire:model="prescriptionItems.{{ $index }}.frequency" class="w-full text-sm border-gray-200 rounded p-1" placeholder="1-0-1"></td>
                                                    <td class="px-4 py-2"><input type="text" wire:model="prescriptionItems.{{ $index }}.duration" class="w-full text-sm border-gray-200 rounded p-1" placeholder="5 days"></td>
                                                    <td class="px-4 py-2 text-center">
                                                        <button wire:click="removeMedication({{ $index }})" class="text-red-500 hover:text-red-700"><x-heroicon-m-trash class="w-4 h-4"/></button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-8 bg-gray-50 dark:bg-gray-900 rounded-lg border border-dashed border-gray-300">
                                    <p class="text-gray-500">No medications added.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 3. LABS TAB (DROPDOWN) --}}
                    <div x-show="activeTab === 'labs'" class="space-y-6 max-w-5xl mx-auto" x-cloak>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 relative overflow-visible">

                             {{-- Dropdown Selection for Lab Tests --}}
                             <div class="mb-6 relative z-30" x-data="{ tempLabId: '' }">
                                <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Add Lab Test</label>
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <select x-model="tempLabId"
                                                class="w-full pl-3 pr-10 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 text-gray-900 dark:text-white">
                                            <option value="">Select a lab test...</option>
                                            @foreach($allLabTests as $test)
                                                <option wire:key="lab-opt-{{ $test->id }}" value="{{ $test->id }}">{{ $test->test_name }} ({{ $test->code }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button
                                        type="button"
                                        :disabled="!tempLabId"
                                        wire:click="addLabTest(tempLabId)"
                                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg flex items-center gap-2 transition-colors">
                                        <x-heroicon-m-plus class="w-5 h-5"/>
                                        Add
                                    </button>
                                </div>
                            </div>

                            @if(count($labItems) > 0)
                                <div class="space-y-3">
                                    @foreach($labItems as $index => $item)
                                        <div wire:key="lab-item-{{ $index }}" class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 flex flex-col md:flex-row gap-4 items-start md:items-center relative z-10">
                                            <div class="w-full md:w-1/4">
                                                <div class="font-bold text-gray-900 dark:text-white">{{ $item['test_name'] }}</div>
                                                <select wire:model="labItems.{{ $index }}.urgency" class="mt-1 text-xs py-1 pl-2 pr-6 border-gray-200 rounded-md">
                                                    <option value="normal">Normal</option>
                                                    <option value="urgent">Urgent</option>
                                                    <option value="critical">Critical</option>
                                                </select>
                                            </div>

                                            <div class="flex-1 w-full grid grid-cols-1 md:grid-cols-2 gap-3">
                                                <div>
                                                    <label class="text-[10px] uppercase text-gray-400 font-bold">Lab Tech</label>
                                                    <select wire:model="labItems.{{ $index }}.lab_tech_id" class="w-full text-sm border-gray-200 rounded p-1.5">
                                                        <option value="">Any Available</option>
                                                        @foreach($labTechnicianOptions as $tech)
                                                            <option wire:key="tech-{{ $tech->id }}" value="{{ $tech->id }}">{{ $tech->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="text-[10px] uppercase text-gray-400 font-bold">Reason</label>
                                                    <input type="text" wire:model="labItems.{{ $index }}.reason" class="w-full text-sm border-gray-200 rounded p-1.5" placeholder="Reason...">
                                                </div>
                                            </div>

                                            <button wire:click="removeLabTest({{ $index }})" class="text-red-500 hover:bg-red-50 p-2 rounded-full"><x-heroicon-m-x-mark class="w-5 h-5"/></button>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 bg-gray-50 dark:bg-gray-900 rounded-lg border border-dashed border-gray-300">
                                    <p class="text-gray-500">No lab tests requested.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- 4. FILES TAB --}}
                    <div x-show="activeTab === 'files'" class="space-y-6 max-w-5xl mx-auto" x-cloak>
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                            {{-- Dropzone --}}
                            <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors relative">
                                <input type="file" wire:model="attachments" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <x-heroicon-o-cloud-arrow-up class="w-12 h-12 text-blue-500 mx-auto mb-3"/>
                                <h3 class="text-gray-900 dark:text-white font-medium">Click or Drag files here</h3>
                                <p class="text-sm text-gray-500 mt-1">PDF, PNG, JPG up to 10MB</p>
                            </div>

                            {{-- Temp Files --}}
                            @if($attachments)
                                <h4 class="text-xs font-bold text-gray-500 uppercase mt-6 mb-3">Ready to Upload</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @foreach($attachments as $index => $file)
                                        <div wire:key="tmp-file-{{ $index }}" class="relative group bg-gray-50 border rounded-lg p-2">
                                            @if(in_array($file->getMimeType(), ['image/jpeg', 'image/png']))
                                                <img src="{{ $file->temporaryUrl() }}" class="h-24 w-full object-cover rounded mb-2">
                                            @else
                                                <div class="h-24 w-full flex items-center justify-center bg-gray-200 rounded mb-2 text-gray-500">
                                                    <x-heroicon-o-document class="w-10 h-10"/>
                                                </div>
                                            @endif
                                            <p class="text-xs truncate px-1">{{ $file->getClientOriginalName() }}</p>
                                            <button wire:click="removeTempAttachment({{ $index }})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <x-heroicon-s-x-mark class="w-3 h-3"/>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Stored Files --}}
                            @if(count($storedAttachments) > 0)
                                <h4 class="text-xs font-bold text-gray-500 uppercase mt-6 mb-3">Saved Attachments</h4>
                                <div class="space-y-2">
                                    @foreach($storedAttachments as $att)
                                        <div wire:key="stored-file-{{ $att->id }}" class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg hover:shadow-sm transition-shadow">
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                                                    <x-heroicon-o-paper-clip class="w-5 h-5"/>
                                                </div>
                                                <div>
                                                    <a href="{{ $attachmentUrls[$att->id] ?? '#' }}" target="_blank" class="text-sm font-medium text-blue-600 hover:underline">
                                                        {{ $att->file_name }}
                                                    </a>
                                                    <p class="text-xs text-gray-500">{{ $att->created_at->format('M d, Y H:i') }}</p>
                                                </div>
                                            </div>
                                            <button wire:click="removeStoredAttachment({{ $att->id }})" wire:confirm="Delete file?" class="text-gray-400 hover:text-red-500 p-2">
                                                <x-heroicon-m-trash class="w-4 h-4"/>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            @else
                {{-- 🔴 CENTERED EMPTY STATE --}}
                <div class="absolute inset-0 flex items-center justify-center z-0 pointer-events-none">
                    <div class="flex flex-col items-center justify-center bg-gray-50 dark:bg-gray-900 text-gray-400 p-6 rounded-lg">
                        <div class="w-24 h-24 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4 shadow-sm">
                            <x-heroicon-o-user-plus class="w-10 h-10 text-gray-300"/>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Ready for Consultation</h3>
                        <p class="max-w-xs text-center mt-2 text-sm text-gray-500">
                            Search for a patient on the left to view their history and start a new medical record.
                        </p>
                    </div>
                </div>
            @endif
        </main>
    </div>
</div>
