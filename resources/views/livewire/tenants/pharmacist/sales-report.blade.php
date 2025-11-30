<div class="flex-1 p-4 md:p-6 lg:ml-64 bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">
    {{-- Mobile hamburger --}}
    <button @click="open = true"
        class="lg:hidden p-2 rounded-md text-gray-700 bg-white shadow hover:bg-gray-100 mb-4 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700">
        <x-heroicon-o-bars-3 class="w-6 h-6" />
    </button>

    {{-- Breadcrumbs --}}
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('pharmacist.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300 transition-colors duration-150">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" />Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm font-medium text-gray-700 md:ms-2 dark:text-gray-400">Sales Report</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-800 dark:text-white mb-8">
        Sales Report
        <span class="block text-base font-normal text-gray-500 mt-1 dark:text-gray-400">Comprehensive overview of medication sales.</span>
    </h2>

    <div class="bg-white rounded-2xl shadow p-6 mb-6 dark:bg-gray-800">
        <h3 class="text-2xl font-semibold text-gray-800 mb-4 dark:text-white">Top Selling Medications</h3>

        <div class="flex flex-col sm:flex-row justify-between mb-4 space-y-3 sm:space-y-0 sm:space-x-4">
            {{-- Search Bar --}}
            <div class="relative max-w-lg mx-auto md:mx-0">
            <input type="text" wire:model.live.debounce.400ms="search"
                placeholder="Search medications..."
                class="w-full pl-14 pr-5 py-3 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700
                       focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 shadow-md">
            <x-heroicon-s-magnifying-glass class="absolute left-4 top-1/2 -translate-y-1/2 w-6 h-6 text-gray-400" />
        </div>

            {{-- Download Buttons --}}
            <div x-data="{}" class="flex space-x-2">
                <button @click="@this.call('downloadCsv')"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-150">
                    <x-heroicon-s-arrow-down-tray class="w-4 h-4 mr-2" /> Download CSV
                </button>
                <button @click="@this.call('downloadExcel')"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                    <x-heroicon-s-arrow-down-tray class="w-4 h-4 mr-2" /> Download Excel
                </button>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Rank</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Medication Name</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">Quantity Sold</th>
                    </tr>
                </thead>
                <tbody id="topSellingMedsBody" class="bg-white divide-y divide-gray-100 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse($topSellingMedications as $index => $med)
                        <tr>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $index + 1 }}</td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $med->name }}</td>
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-200">{{ $med->total_quantity_sold }} units</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-center text-sm text-gray-500 dark:text-gray-400">No top selling
                                medications data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
