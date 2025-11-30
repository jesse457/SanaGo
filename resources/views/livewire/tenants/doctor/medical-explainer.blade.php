<main id="doctor-dashboard" class="flex-1 p-4 md:p-6 lg:ml-64 bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">

    {{-- Mobile hamburger --}}
    <button @click="open = true"
        class="lg:hidden p-2 rounded-md text-gray-700 bg-white shadow hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 transition-colors mb-4">
        <x-heroicon-o-bars-3 class="w-6 h-6" />
    </button>

    {{-- Breadcrumbs --}}
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('doctor.dashboard') }}"
                       class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-300 transition-colors duration-150">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" />{{ __('doctor.home') }}
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm font-medium text-gray-700 md:ms-2 dark:text-gray-400">{{ __('doctor.clinical_assistant') }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-6 flex items-center">
        <x-heroicon-o-light-bulb class="w-8 h-8 mr-3 text-purple-600 dark:text-purple-400"/>
        {{ __('doctor.ai_clinical_assistant') }}
    </h1>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-8 border border-gray-200 dark:border-gray-700">
        <p class="text-gray-700 dark:text-gray-300 mb-6 text-lg leading-relaxed">
            {{ __('doctor.ai_assistant_description') }}
        </p>

        <hr class="border-gray-200 dark:border-gray-700 my-6">

        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <x-heroicon-o-users class="w-6 h-6 text-blue-600 dark:text-blue-400"/> {{ __('doctor.patient_context') }}
        </h2>

        <div class="mb-6">
            <label for="patient-search" class="block text-base font-medium text-gray-700 dark:text-gray-300 mb-2">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 inline-block mr-1"/> {{ __('doctor.find_or_select_patient') }}:
            </label>
            <div class="relative flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="text" id="patient-search"
                           class="form-input w-full p-3 pl-10 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-base shadow-sm
                                  dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                           placeholder="{{ __('doctor.search_patient_placeholder') }}">
                    <x-heroicon-o-magnifying-glass class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
                </div>
                <div class="relative flex-1">
                    <select id="patient-select" class="form-select w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-base shadow-sm appearance-none pr-10
                                   dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400">
                        <option value="">{{ __('doctor.no_specific_patient_selected') }}</option>
                        {{-- NOTE: Patient data below would ideally be translated dynamically if their profile stored locale, but for static examples we must leave as-is or use variables --}}
                        <option value="patient-jane-smith">Jane Smith (DOB: 1990-05-15) - Chronic Hypertension</option>
                        <option value="patient-michael-adams">Michael Adams (DOB: 1982-11-22) - Type 2 Diabetes</option>
                        <option value="patient-emily-white">Emily White (DOB: 1975-03-01) - Osteoarthritis</option>
                        <option value="patient-david-wilson">David Wilson (DOB: 1992-09-10) - Asthma</option>
                    </select>
                    <x-heroicon-s-chevron-down class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"/>
                </div>
            </div>
        </div>

        <hr class="border-gray-200 dark:border-gray-700 my-6">

        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <x-heroicon-o-light-bulb class="w-6 h-6 text-blue-600 dark:text-blue-400"/> {{ __('doctor.ask_the_ai') }}
        </h2>

        <div class="flex flex-col sm:flex-row items-end space-y-4 sm:space-y-0 sm:space-x-4 mb-6">
            <textarea id="ai-prompt-input"
                class="flex-1 p-4 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 text-base resize-y min-h-[100px] max-h-[250px] shadow-sm placeholder-gray-500
                       dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400"
                placeholder="{{ __('doctor.ai_prompt_placeholder') }}"></textarea>
            <button id="explain-button"
                class="px-8 py-4 bg-blue-600 text-white rounded-xl shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200
                       flex items-center justify-center gap-3 text-lg font-semibold whitespace-nowrap dark:bg-blue-700 dark:hover:bg-blue-800 dark:focus:ring-offset-gray-800">
                <x-heroicon-o-sparkles class="w-6 h-6" />
                {{ __('doctor.generate') }}
            </button>
        </div>

        <div id="loading-indicator" class="hidden text-center text-blue-600 dark:text-blue-400 font-medium mb-6">
            <div class="flex items-center justify-center">
                <x-heroicon-o-arrow-path class="animate-spin -ml-1 mr-3 h-6 w-6 text-blue-600 dark:text-blue-400" />
                {{ __('doctor.generating_insights') }}...
            </div>
        </div>

        <hr class="border-gray-200 dark:border-gray-700 my-6">

        <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            <x-heroicon-o-chat-bubble-left-right class="w-6 h-6 text-blue-600 dark:text-blue-400"/> {{ __('doctor.ai_responses') }}
        </h2>

        <div id="ai-output-area"
            class="min-h-[500px] max-h-[700px] bg-gray-50 dark:bg-gray-700/50 p-6 rounded-xl border border-gray-200 dark:border-gray-700 overflow-y-auto flex flex-col space-y-5 shadow-inner">

            {{-- Example AI Message Bubble --}}
            <div class="flex justify-start">
                <div class="bg-blue-100 text-blue-800 p-4 rounded-xl max-w-2xl shadow-md border border-blue-200
                            dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-700">
                    <p class="font-bold text-lg mb-2 flex items-center gap-2">
                        <x-heroicon-o-magnifying-glass-plus class="w-5 h-5 text-blue-600 dark:text-blue-400"/> {{ __('doctor.ai_clinical_assistant') }}
                    </p>
                    <p class="text-base leading-relaxed">{{ __('doctor.ai_initial_greeting') }}</p>
                </div>
            </div>

            {{-- Example User Message Bubble (Hidden by default, for future use) --}}
            <div class="flex justify-end hidden">
                <div class="bg-green-100 text-green-800 p-4 rounded-xl max-w-2xl shadow-md border border-green-200
                            dark:bg-green-900/30 dark:text-green-200 dark:border-green-700">
                    <p class="font-bold text-lg mb-2 flex items-center justify-end gap-2">
                        {{ __('doctor.you') }} <x-heroicon-o-user-circle class="w-5 h-5 text-green-600 dark:text-green-400"/>
                    </p>
                    <p class="text-base leading-relaxed">{{ __('doctor.example_user_prompt') }}</p>
                </div>
            </div>

            {{-- Example AI Response to User Message (Hidden by default, for future use) --}}
            <div class="flex justify-start hidden">
                <div class="bg-blue-100 text-blue-800 p-4 rounded-xl max-w-2xl shadow-md border border-blue-200
                            dark:bg-blue-900/30 dark:text-blue-200 dark:border-blue-700">
                    <p class="font-bold text-lg mb-2 flex items-center gap-2">
                        <x-heroicon-o-magnifying-glass-plus class="w-5 h-5 text-blue-600 dark:text-blue-400"/> {{ __('doctor.ai_clinical_assistant') }}
                    </p>
                    <p class="text-base leading-relaxed">{{ __('doctor.example_ai_response') }}</p>
                </div>
            </div>

        </div>
    </div>
</main>
