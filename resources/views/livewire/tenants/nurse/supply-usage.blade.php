<section id="medical-supplies"
    class="flex-1 p-4 md:p-6 lg:ml-64 bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">
    {{-- This button might be part of a larger layout component,
     but kept here for completeness if it's specific to this page. --}}
    {{-- Consider using 'hidden lg:hidden' if this button is part of a responsive sidebar toggle --}}
    <button
        class="lg:hidden p-3 rounded-xl text-gray-700 bg-white shadow-md hover:bg-gray-100 mb-6 transition-colors duration-200
           dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    {{-- Breadcrumbs --}}
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    {{-- Dynamically set the home route based on user role --}}
                    <a href="{{ route('nurse.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors duration-150">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" />
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm font-medium text-gray-400 md:ms-2 dark:text-gray-400">
                            Supply Usage</span>

                    </div>
                </li>
            </ol>
        </nav>
    </div>
    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-3 flex items-center">
        <x-heroicon-o-archive-box class="h-8 w-8 mr-4 text-blue-600 dark:text-blue-400" />
        Supply Usage
    </h2>
    <p class="text-gray-600 dark:text-gray-400 mb-3">Record supply used by you</p>
    <div class="card bg-white dark:bg-gray-800 shadow-lg rounded-lg p-6"> {{-- Added dark mode background and better shadow --}}

        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <div class="flex flex-col gap-6"> {{-- Increased gap for better spacing on mobile --}}
            @forelse ($supplies as $supply)
                {{-- Changed from flex-row to flex-col on mobile, then back to flex-row on medium screens and up --}}
                <div
                    class="flex flex-col md:flex-row items-start md:items-center gap-3 md:gap-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg bg-gray-50 dark:bg-gray-700">
                    <label class="form-label w-full md:w-32 font-medium text-gray-700 dark:text-gray-200">
                        {{-- Adjusted label width for mobile --}}
                        {{ $supply->name }} ({{ $supply->unit_of_measure }})
                        <span class="text-sm text-gray-500 dark:text-gray-400 block md:inline-block"> (Current Stock:
                            {{ $supply->current_stock }})</span> {{-- Block for better stacking on mobile --}}
                    </label>
                    <input type="number"
                        class="form-input w-full md:flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-900 dark:text-white"
                        {{-- Added full width for mobile --}} wire:model.live.defer="quantitiesUsed.{{ $supply->id }}"
                        placeholder="Enter quantity used">
                    <button
                        class="btn-outlined w-full md:w-auto mt-2 md:mt-0 px-4 py-2 text-blue-600 border border-blue-600 rounded-md hover:bg-blue-50 dark:text-blue-400 dark:border-blue-400 dark:hover:bg-gray-600 transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        {{-- Full width button on mobile --}} wire:click="recordSupplyUsage({{ $supply->id }})">Record Usage</button>
                </div>
                @error('quantitiesUsed.' . $supply->id)
                    <p class="text-red-500 text-xs mt-1 ml-4 md:ml-36">{{ $message }}</p> {{-- Adjusted error message margin --}}
                @enderror
            @empty
                <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                    <x-heroicon-o-clipboard-document-list class="w-16 h-16 mb-2 text-gray-300 dark:text-gray-600" />
                       <p class="text-gray-600 dark:text-gray-400 p-4 text-center">No medical supplies found. Please add some
                    supplies.</p>
                </div>
            @endforelse
        </div>
    </div>


</section>
