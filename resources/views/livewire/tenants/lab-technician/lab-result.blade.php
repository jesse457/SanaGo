   <section id="lab-results-section"
       class="flex-1 p-4 md:p-6 lg:ml-64 bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">
       {{-- Breadcrumbs --}}
       <div class="mb-8">
           <nav class="flex" aria-label="Breadcrumb">
               <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                   <li class="inline-flex items-center">
                       <a href="{{ route('lab-technician.dashboard') }}" wire:navigate
                           class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors duration-150">
                           <x-heroicon-s-home class="h-4 w-4 me-2.5" />
                           Home
                       </a>
                   </li>
                   <li>
                       <div class="flex items-center">
                           <x-heroicon-s-chevron-right class="rtl:rotate-180 w-4 h-4 text-gray-400 mx-1" />
                           <span class="ms-1 text-sm  text-gray-400 md:ms-2 dark:text-gray-200">Lab
                               Results</span>
                       </div>
                   </li>
               </ol>
           </nav>
       </div>
       <header class="flex items-center justify-between mb-6">
           <div>
               <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-1 flex items-center gap-3">
                   <x-heroicon-o-clipboard-document-check class="h-8 w-10  text-green-600 dark:text-green-400" />
                   View Lab Results
               </h1>
               <p class="text-gray-600 dark:text-gray-400">View, search, and manage all lab results.</p>
           </div>

       </header>

       <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl">
           <!-- Filters and Search -->
           <div class="p-6 lg:p-8 border-b border-gray-200 dark:border-gray-700">
               <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                   <div class="relative w-full md:flex-grow">
                       <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                           <x-heroicon-s-magnifying-glass class="w-5 h-5 text-gray-400" />
                       </div>
                       <input type="text" wire:model.live.debounce.400ms="search"
                           placeholder="Search by Patient or Test..."
                           class="w-full pl-12 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                   </div>
                   <div class="relative w-full md:w-auto md:min-w-[200px]">
                       <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                           <x-heroicon-s-calendar-days class="w-5 h-5 text-gray-400" />
                       </div>
                       <input type="date" wire:model.live="dateFilter"
                           class="w-full appearance-none pl-12 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 focus:ring-blue-500 focus:border-blue-500 transition duration-150">
                   </div>
               </div>
           </div>

           <!-- Table -->
           <div class="overflow-x-auto custom-scrollbar">
               <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                   <thead class="bg-gray-50 dark:bg-gray-700/50">
                       <tr>
                           <th scope="col"
                               class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                               Patient</th>
                           <th scope="col"
                               class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                               Test Name</th>
                           <th scope="col"
                               class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                               Result Date</th>
                           <th scope="col"
                               class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                               Status</th>
                           <th scope="col"
                               class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
                               Actions</th>
                       </tr>
                   </thead>
                   <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                       {{-- Assuming $results is passed from your Livewire component --}}
                       @forelse ($results as $result)
                           <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                               <td
                                   class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-200">
                                   {{ $result->labRequest?->patient?->first_name }}
                                   {{ $result->labRequest?->patient?->last_name }}

                                   <span
                                       class="block text-xs text-gray-500 font-normal">{{ $result->labRequest?->patient?->patient_uid }}</span>
                               </td>
                               <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                   {{ $result->labRequest?->testDefinition?->test_name }}</td>
                               <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                   {{ $result->result_date->format('M d, Y H:i A') }}</td>
                               <td class="px-6 py-4 whitespace-nowrap text-sm">
                                   <span @class([
                                       'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium',
                                       'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300' =>
                                           $result->status == 'Completed',
                                       'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' =>
                                           $result->status == 'Cancelled',
                                       'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' =>
                                           $result->status == 'In Progress',
                                   ])>
                                       {{ $result->status }}
                                   </span>
                               </td>
                               <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                   <a href="{{ route('lab-technician.enter-results', $result->lab_request_id) }}"
                                       wire:navigate
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm  text-blue-600 hover:text-white bg-blue-50 hover:bg-blue-600 dark:bg-blue-900/30 dark:hover:bg-blue-600 dark:text-blue-300 dark:hover:text-white transition-all duration-200 transform hover:scale-105"
                                       title="View/Edit Details">
                                       <x-heroicon-s-eye class="h-5 w-5" />
                                       View Result

                                   </a>

                               </td>
                           </tr>
                       @empty
                           <tr>
                               <td colspan="5" class="px-6 py-16 text-center">
                                   <div
                                       class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                       <x-heroicon-o-clipboard-document-list class="w-12 h-12 mb-4 text-gray-400" />
                                       <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-1">You have not
                                           submitted any results yet.</p>
                                       <p class="text-sm">Completed lab results will appear here.</p>
                                   </div>
                               </td>
                           </tr>
                       @endforelse
                   </tbody>
               </table>
           </div>

           <!-- Pagination -->
           @if ($results->hasPages())
               <div class="p-6 lg:p-8 border-t border-gray-200 dark:border-gray-700">
                   {{ $results->links() }}
               </div>
           @endif
       </div>
   </section>
