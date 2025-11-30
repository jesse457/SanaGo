<main class="flex-1 p-4  bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">

    {{-- Mobile hamburger --}}
    <button @click="open = true"
        class="lg:hidden p-3 rounded-lg text-gray-700 bg-white shadow-md hover:bg-gray-100 transition-all mb-6">
        <x-heroicon-o-bars-3 class="w-6 h-6" />
    </button>

    {{-- Breadcrumb --}}
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white transition-colors duration-200">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" />
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">AI Assistant</span>
                    </div>
                </li>

            </ol>
        </nav>
    </div>

    {{-- AI Assistant Card --}}
    <div class="bg-white rounded-xl shadow-lg p-6 mb-8 border border-gray-100 transition-all duration-300">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-3 sm:gap-0">
            <h2 class="text-2xl md:text-3xl font-extrabold text-gray-800">AI Assistant</h2>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500 font-medium">Powered by LLM</span>
                <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg shadow-sm hover:bg-gray-200 transition-colors duration-200 flex items-center gap-1.5 text-sm">
                    <x-heroicon-o-sparkles class="w-4 h-4 text-purple-500" />
                    New Chat
                </button>
            </div>
        </div>

        {{-- Chat History --}}
        <div class="flex flex-col gap-5 h-[400px] overflow-y-auto px-4 py-4 bg-gray-50 rounded-lg border border-gray-200 scroll-smooth shadow-inner custom-scrollbar">
            {{-- Assistant Welcome Message --}}
            <div class="flex justify-start items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-sm">AI</div>
                <div class="bg-gray-200 text-gray-800 p-3 rounded-xl rounded-tl-none max-w-[80%] md:max-w-[65%] text-base shadow-sm">
                    Hello! I'm your AI Assistant. How can I help you with patient data or hospital operations today?
                </div>
            </div>

            {{-- User Question --}}
            <div class="flex justify-end items-start gap-3">
                <div class="bg-blue-600 text-white p-3 rounded-xl rounded-tr-none max-w-[80%] md:max-w-[65%] text-base shadow-sm">
                    How many patients were admitted last month?
                </div>
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-700 font-bold text-sm">You</div>
            </div>

            {{-- Assistant Reply --}}
            <div class="flex justify-start items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-sm">AI</div>
                <div class="bg-gray-200 text-gray-800 p-3 rounded-xl rounded-tl-none max-w-[80%] md:max-w-[65%] text-base shadow-sm">
                    Last month, 250 new patients were admitted.
                </div>
            </div>

            {{-- User Question --}}
            <div class="flex justify-end items-start gap-3">
                <div class="bg-blue-600 text-white p-3 rounded-xl rounded-tr-none max-w-[80%] md:max-w-[65%] text-base shadow-sm">
                    Show me the busiest department.
                </div>
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-700 font-bold text-sm">You</div>
            </div>

            {{-- Assistant Reply --}}
            <div class="flex justify-start items-start gap-3">
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-sm">AI</div>
                <div class="bg-gray-200 text-gray-800 p-3 rounded-xl rounded-tl-none max-w-[80%] md:max-w-[65%] text-base shadow-sm">
                    Cardiology had the highest patient visits.
                </div>
            </div>

            {{-- Example of a loading message (can be dynamically added) --}}
            <div class="flex justify-start items-start gap-3" x-show="false" x-transition> {{-- Use x-show and toggle with Alpine.js --}}
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-sm">AI</div>
                <div class="bg-gray-200 text-gray-800 p-3 rounded-xl rounded-tl-none max-w-[70%] text-base shadow-sm">
                    <div class="flex items-center space-x-2">
                        <svg class="animate-spin h-4 w-4 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Thinking...</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Input Box --}}
        <div class="mt-6 flex items-center gap-3">
            <input type="text" placeholder="Ask the AI assistant..."
                class="flex-1 px-5 py-3 border border-gray-300 rounded-xl text-base focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm placeholder-gray-400">
            <button
                class="p-3.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors duration-200 shadow-md flex items-center justify-center"
                aria-label="Send">
                <x-heroicon-o-paper-airplane class="w-6 h-6 -rotate-45" />
            </button>
        </div>
    </div>
</main>


