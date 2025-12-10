


<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class=" mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4">
                <div class="flex flex-col gap-2">
                    {{-- Breadcrumbs --}}
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-1 mt-2" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('nurse.dashboard') }}" wire:navigate
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    Home
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <a href="{{ route('nurse.feedbacks') }}" wire:navigate
                                        class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        Feedbacks
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">Submit Feedback</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            Submit Feedback
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                            Your thoughts are important! Help us make it better.
                        </p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="p-4 sm:p-6 pb-20">
            <div class=" mx-auto">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">

                    {{-- Card Header --}}
                    <div class="px-6 py-5 border-b border-slate-100 dark:border-gray-800 flex items-center justify-between bg-slate-50/50 dark:bg-gray-800/30">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">Help Us Make It Better</h3>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Your thoughts are important! Tell us what you think to help us improve.</p>
                        </div>
                        <span class="hidden sm:inline-flex items-center rounded-full bg-blue-50 dark:bg-blue-900/30 px-3 py-1 text-xs font-bold text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800">SanaGo</span>
                    </div>

                    {{-- Form --}}
                    <form wire:submit="submit" class="p-6 sm:p-8 space-y-8" >

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Subject --}}
                            <div class="space-y-1.5">
                                <label for="subject" class="block text-sm font-bold text-slate-700 dark:text-slate-300">What is this about? <span class="text-red-500">*</span></label>
                                <input id="subject" type="text" wire:model.defer="subject"
                                    placeholder="e.g., I have a problem with appointments"
                                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white py-2.5 px-3" />
                                @error('subject')
                                    <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Category --}}
                            <div class="space-y-1.5">
                                <label for="category" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Which part of the system? <span class="text-red-500">*</span></label>
                                  <select id="category" wire:model.defer="category"
                                    class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white py-2.5 px-3">
                                    <option value="dashboard">Dashboard</option>
                        <option value="supply-usage">Supply Usage</option>
                        <option value="record-vitals">Record Vitals</option>
                         <option value="profile">Profile</option>
                                </select>


                                @error('category')
                                    <p class="mt-1 text-xs text-red-500 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Message --}}
                        <div class="space-y-1.5">
                            <label for="message" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Tell us what happened <span class="text-red-500">*</span></label>
                            <textarea id="message" rows="6" wire:model.defer="message"
                                placeholder="Please describe what you experienced in your own words. The more details you give us, the better we can help."
                                class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white p-3"></textarea>

                            <div class="flex justify-between items-start mt-1">
                                @error('message')
                                    <p class="text-xs text-red-500 font-medium">{{ $message }}</p>
                                @else
                                    <span></span> {{-- Spacer --}}
                                @enderror
                                <p class="text-xs text-slate-500 dark:text-slate-400 text-right">
                                   <b>Tip:</b> Tell us what you were trying to do, what happened, and what you expected to happen instead.
                                </p>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 border-t border-slate-100 dark:border-gray-800">
                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                By sending this, you agree to our <a href="#"
                                    class="text-blue-600 hover:underline font-medium">rules</a> and <a href="#"
                                    class="text-blue-600 hover:underline font-medium">privacy policy</a>.
                            </div>
                            <div class="flex flex-col-reverse sm:flex-row gap-3 w-full sm:w-auto">
                                <button type="button"
                                    wire:click="clearData()"
                                    class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-300 dark:border-gray-600 text-slate-700 dark:text-slate-200 bg-white dark:bg-gray-800 hover:bg-slate-50 dark:hover:bg-gray-700 text-sm font-bold transition-all duration-200">
                                    Start Over
                                </button>
                                <button type="submit"
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-2.5 rounded-xl text-white bg-blue-600 hover:bg-blue-700 shadow-md hover:shadow-lg text-sm font-bold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                    wire:loading.attr="disabled" wire:target="submit">
                                    <span wire:loading.remove wire:target="submit" class="flex items-center gap-2">
                                        <x-heroicon-s-paper-airplane class="w-4 h-4 -rotate-45" />
                                        Send
                                    </span>
                                    <span wire:loading wire:target="submit" class="flex items-center gap-2">
                                        <x-heroicon-s-arrow-path class="w-4 h-4 animate-spin" />
                                        Sending...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>


