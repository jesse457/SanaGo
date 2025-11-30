<div class="w-full flex-1 p-4 sm:p-6 overflow-y-auto h-screen bg-gray-50 dark:bg-gray-900">
    <div class="">

        <!-- Header Navigation -->
        <header class="mb-8">
            <nav class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 mb-4">
                <a href="{{ route('lab-technician.dashboard') }}" wire:navigate
                    class="flex items-center hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    <x-heroicon-s-home class="w-4 h-4 mr-1.5" />
                    Home
                </a>
                <x-heroicon-s-chevron-right class="w-4 h-4 mx-2 text-gray-300 dark:text-gray-600" />
                <a href="{{ route('lab-technician.feedbacks') }}" wire:navigate
                    class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    Feedbacks
                </a>
                <x-heroicon-s-chevron-right class="w-4 h-4 mx-2 text-gray-300 dark:text-gray-600" />
                <span class="text-gray-900 dark:text-white font-semibold">Submit</span>
            </nav>

            <div class="flex items-start gap-4">
                <div class="p-3 bg-indigo-600 rounded-xl shadow-lg shadow-indigo-600/20 hidden sm:block">
                    <x-heroicon-o-chat-bubble-left-right class="w-8 h-8 text-white" />
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">
                        Submit Feedback
                    </h1>
                    <p class="mt-2 text-lg text-gray-600 dark:text-gray-400 leading-relaxed">
                        Found a bug or have a suggestion? We'd love to hear from you to make SanaGo better.
                    </p>
                </div>
            </div>
        </header>

        <!-- Main Form Card -->
        <div class="bg-white dark:bg-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">

            <!-- Card Header -->
            <div class="bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800 px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                    <x-heroicon-s-pencil-square class="w-5 h-5 text-indigo-500" />
                    Feedback Form
                </h2>
                <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10 dark:bg-indigo-400/10 dark:text-indigo-400 dark:ring-indigo-400/20">
                    SanaGo Lab
                </span>
            </div>

            <!-- Form Body -->
            <form wire:submit="submit" class="p-6 sm:p-8 space-y-8">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Subject Input -->
                    <div class="col-span-1">
                        <label for="subject" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">
                            Summary / Subject <span class="text-red-500">*</span>
                        </label>
                        <div class="relative mt-2">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-s-tag class="h-5 w-5 text-gray-400" />
                            </div>
                            <input type="text" id="subject" wire:model.defer="subject"
                                class="block w-full rounded-lg border-0 py-2.5 pl-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:bg-gray-900 dark:ring-gray-700 dark:text-white sm:text-sm sm:leading-6 transition-shadow"
                                placeholder="Briefly summarize the issue">
                        </div>
                        @error('subject')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                                <x-heroicon-s-exclamation-circle class="w-4 h-4"/> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Category Select -->
                    <div class="col-span-1">
                        <label for="category" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">
                            Related Feature <span class="text-red-500">*</span>
                        </label>
                        <div class="relative mt-2">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-s-squares-2x2 class="h-5 w-5 text-gray-400" />
                            </div>
                            <select id="category" wire:model.defer="category"
                                class="block w-full rounded-lg border-0 py-2.5 pl-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:bg-gray-900 dark:ring-gray-700 dark:text-white sm:text-sm sm:leading-6 cursor-pointer">
                                <option value="" disabled selected>Select a category</option>
                                <option value="dashboard">Dashboard</option>
                                <option value="patient">Patient Management</option>
                                <option value="appoitntments">Appointments</option>
                                <option value="consultation">Consultation</option>
                                <option value="test-request">Test Requests</option>
                                <option value="view-patient-info">Patient Info View</option>
                                <option value="view-lab-test-and-prescription">Lab Tests & Prescriptions</option>
                                <option value="profile">User Profile</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        @error('category')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                                <x-heroicon-s-exclamation-circle class="w-4 h-4"/> {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Message Textarea -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label for="message" class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Be as specific as possible</span>
                    </div>
                    <div class="relative">
                        <textarea id="message" rows="6" wire:model.defer="message"
                            class="block w-full rounded-lg border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 dark:bg-gray-900 dark:ring-gray-700 dark:text-white sm:text-sm sm:leading-6 transition-shadow resize-y"
                            placeholder="What were you trying to do? What happened? What did you expect to happen?"></textarea>

                        <!-- Corner decoration -->
                        <div class="absolute bottom-2 right-2 pointer-events-none">
                            <x-heroicon-s-pencil class="w-4 h-4 text-gray-300 dark:text-gray-600" />
                        </div>
                    </div>
                    @error('message')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
                            <x-heroicon-s-exclamation-circle class="w-4 h-4"/> {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Footer Actions -->
                <div class="pt-6 border-t border-gray-100 dark:border-gray-700 flex flex-col-reverse sm:flex-row sm:justify-between items-center gap-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400 text-center sm:text-left">
                        By submitting, you agree to our internal <a href="#" class="text-indigo-600 hover:underline dark:text-indigo-400">data policy</a>.
                    </div>

                    <div class="flex w-full sm:w-auto gap-3">
                        <button type="button" wire:click="clearData"
                            class="flex-1 sm:flex-none inline-flex justify-center items-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-all duration-200">
                            <x-heroicon-s-arrow-path class="w-4 h-4 mr-2 text-gray-400" />
                            Reset
                        </button>

                        <button type="submit"
                            wire:loading.attr="disabled"
                            class="flex-1 sm:flex-none relative inline-flex justify-center items-center px-6 py-2.5 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-70 disabled:cursor-not-allowed shadow-md shadow-indigo-200 dark:shadow-none transition-all duration-200">
                            <span wire:loading.remove wire:target="submit" class="flex items-center">
                                Send Feedback
                                <x-heroicon-s-paper-airplane class="w-4 h-4 ml-2 -rotate-45" />
                            </span>
                            <span wire:loading wire:target="submit" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
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
