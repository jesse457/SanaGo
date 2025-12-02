<main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50/50 p-6 dark:bg-gray-900">
    <div class=" mx-auto">
        {{-- Breadcrumbs --}}
         <div class="mb-6 mt-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('receptionist.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-300 transition-colors duration-150">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" />
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                          <a href="{{ route('receptionist.feedback-history') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-300 transition-colors duration-150">
                          Feedbacks</a>
                    </div>
                </li>
                 <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm  text-gray-900 md:ms-2 dark:text-gray-400">
                          Book  Feedback</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center gap-3">
                <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400">
                    <x-heroicon-o-pencil-square class="w-7 h-7" />
                </div>
                Submit Feedback
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 ml-14">
                Your thoughts matter. Help us improve the system by reporting issues or sharing ideas.
            </p>
        </div>

        {{-- Main Form Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/60 dark:border-gray-700 overflow-hidden">

            {{-- Form Header --}}
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="hidden sm:flex h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900/50 items-center justify-center text-blue-600 dark:text-blue-400">
                        <x-heroicon-o-chat-bubble-bottom-center-text class="w-6 h-6" />
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Tell us what you think</h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400">We appreciate your contribution to SanaGo.</p>
                    </div>
                </div>
                <span class="inline-flex self-start sm:self-center items-center rounded-full bg-indigo-50 dark:bg-indigo-900/30 px-3 py-1 text-xs font-medium text-indigo-700 dark:text-indigo-400 ring-1 ring-inset ring-indigo-700/10">
                    Feedback Form
                </span>
            </div>

            <div class="p-6 md:p-8">
                <form wire:submit="submit" class="space-y-8">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Subject --}}
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Subject
                            </label>
                            <input id="subject" type="text" wire:model.defer="subject"
                                placeholder="e.g., Issue with appointment calendar"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white placeholder-gray-400" />
                            @error('subject')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                                    <x-heroicon-s-exclamation-circle class="w-3 h-3" /> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Related Area
                            </label>
                            <select id="category" wire:model.defer="category"
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white">
                                <option value="" disabled selected>Select a category</option>
                                <option value="dashboard">Dashboard</option>
                                <option value="appointments">Appointments</option>
                                <option value="book-appointment">Book Appointments</option>
                                <option value="patients">Patients</option>
                                <option value="register-patient">Register Patients</option>
                                <option value="patient-admission">Patient Admissions</option>
                                <option value="admit-patient">Admit Patient</option>
                                <option value="other">Other / General Inquiry</option>
                            </select>
                            @error('category')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                                    <x-heroicon-s-exclamation-circle class="w-3 h-3" /> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    {{-- Message --}}
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Detailed Description
                        </label>
                        <div class="relative">
                            <textarea id="message" rows="6" wire:model.defer="message"
                                placeholder="Describe your experience, what you were trying to do, and any specific error messages..."
                                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white placeholder-gray-400"></textarea>

                            {{-- Optional corner hint --}}
                            <div class="absolute bottom-2 right-2">
                                <x-heroicon-o-pencil class="w-4 h-4 text-gray-300 dark:text-gray-600" />
                            </div>
                        </div>

                        @error('message')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400 flex items-center gap-1">
                                <x-heroicon-s-exclamation-circle class="w-3 h-3" /> {{ $message }}
                            </p>
                        @enderror

                        <div class="mt-2 flex items-start gap-2 bg-blue-50 dark:bg-blue-900/20 p-3 rounded-lg border border-blue-100 dark:border-blue-800">
                            <x-heroicon-o-light-bulb class="w-5 h-5 text-blue-500 dark:text-blue-400 flex-shrink-0" />
                            <p class="text-xs text-blue-700 dark:text-blue-300">
                                <strong>Tip:</strong> Be as specific as possible. If reporting a bug, include steps to reproduce it.
                            </p>
                        </div>
                    </div>

                    {{-- Footer Actions --}}
                    <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex flex-col-reverse sm:flex-row sm:items-center justify-between gap-4">
                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center sm:text-left">
                            By submitting, you agree to our internal <a href="#" class="text-indigo-600 hover:text-indigo-500 underline decoration-indigo-300">communication policy</a>.
                        </p>

                        <div class="flex items-center justify-end gap-3 w-full sm:w-auto">
                            <button type="button"
                                wire:click="reset(['subject', 'category', 'message'])"
                                class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-colors">
                                Reset Form
                            </button>

                            <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-md shadow-indigo-600/20 transition-all disabled:opacity-70 disabled:cursor-not-allowed"
                                wire:loading.attr="disabled" wire:target="submit">

                                <span wire:loading.remove wire:target="submit" class="flex items-center gap-2">
                                    <x-heroicon-s-paper-airplane class="w-4 h-4 -rotate-45 mb-1" />
                                    Submit Feedback
                                </span>

                                <span wire:loading wire:target="submit" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Sending...
                                </span>
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</main>
