<div class="flex-1 p-4 md:p-6 lg:ml-64 bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">
    <!-- Container for the settings form -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden p-8 transition-colors duration-300">
        <!-- Header Section -->
        <header class="mb-8">
            <h3 class="text-3xl font-bold text-slate-800 dark:text-white mb-2">General Settings</h3>
            <p class="text-slate-500 dark:text-slate-400">Manage your profile information and application preferences with ease.</p>
        </header>

        <!-- Success Message (Styled for clarity) -->
        @if (session()->has('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 dark:bg-green-900 dark:border-green-700 dark:text-green-300 px-6 py-4 rounded-xl relative mb-8 flex items-center gap-3 transition-opacity duration-300 opacity-100" role="alert">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="block sm:inline font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <!-- Settings Form -->
        <form wire:submit.prevent="saveSettings" class="space-y-8">
            <!-- Account Details Section -->
            <div class="space-y-6">
                <h4 class="text-xl font-semibold text-slate-800 dark:text-white">Account Details</h4>
                <!-- Your Name Input -->
                <div>
                    <label for="landlord-name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Your Name</label>
                    <input type="text" id="landlord-name" wire:model="landlordName"
                        class="form-input block w-full rounded-xl border-slate-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200 p-3"
                        placeholder="Enter your full name">
                </div>
                <!-- Email Address Input -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email Address</label>
                    <input type="email" id="email" wire:model="email"
                        class="form-input block w-full rounded-xl border-slate-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200 p-3"
                        placeholder="your.email@example.com">
                </div>
            </div>

            <!-- Separator -->
            <hr class="border-slate-200 dark:border-gray-700">

            <!-- Application Preferences Section -->
            <div class="space-y-6">
                <h4 class="text-xl font-semibold text-slate-800 dark:text-white">Application Preferences</h4>
                <!-- Timezone Select -->
                <div>
                    <label for="timezone" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Timezone</label>
                    <select id="timezone" wire:model="timezone"
                        class="form-select block w-full rounded-xl border-slate-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200 p-3">
                        <option value="America/New_York">America/New York</option>
                        <option value="Europe/London">Europe/London</option>
                        <option value="Africa/Douala">Africa/Douala</option>
                    </select>
                </div>
                <!-- Currency Select -->
                <div>
                    <label for="currency" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Currency</label>
                    <select id="currency" wire:model="currency"
                        class="form-select block w-full rounded-xl border-slate-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors duration-200 p-3">
                        <option value="USD">USD ($)</option>
                        <option value="EUR">EUR (€)</option>
                        <option value="XAF">XAF (FCFA)</option>
                    </select>
                </div>
                <!-- Dark Mode Toggle (Styled) -->
                <div class="flex items-center justify-between">
                    <label for="dark-mode" class="block text-sm font-medium text-slate-700 dark:text-slate-300">Dark Mode</label>
                    <div class="relative inline-block w-14 align-middle select-none transition duration-200 ease-in">
                        <input type="checkbox" id="dark-mode" wire:model.live="darkMode" class="toggle-checkbox absolute block w-8 h-8 rounded-full bg-white border-4 appearance-none cursor-pointer dark:bg-gray-700 transition-transform duration-200" />
                        <label for="dark-mode" class="toggle-label block overflow-hidden h-8 rounded-full bg-slate-300 dark:bg-gray-600 cursor-pointer transition-colors duration-200"></label>
                    </div>
                </div>
            </div>

            <!-- Separator -->
            <hr class="border-slate-200 dark:border-gray-700">

            <!-- Submit Button -->
            <div class="flex justify-end pt-6">
                <button type="submit" class="rounded-xl bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-300">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Custom CSS for the toggle switch -->
    <style>
        .toggle-checkbox:checked {
            right: 0;
            transform: translateX(100%);
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #6366f1; /* Tailwind indigo-500 */
        }
        .toggle-checkbox {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .toggle-checkbox:focus + .toggle-label {
            outline: 2px solid transparent;
            outline-offset: 2px;
            box-shadow: 0 0 0 2px #fff, 0 0 0 4px #6366f1;
        }
    </style>
</div>
