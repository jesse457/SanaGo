<main class="flex-1 p-4 mt-8 bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">
  {{-- Breadcrumbs --}}
  <div class="mb-6">
    <nav class="flex" aria-label="Breadcrumb">
      <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
        <li class="inline-flex items-center">
          <a href="{{ route('lab-technician.dashboard') }}" wire:navigate
             class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-300 dark:hover:text-indigo-400 transition-colors">
            <x-heroicon-s-home class="h-4 w-4 me-2.5" />
            Home
          </a>
        </li>
        <li>
          <div class="flex items-center">
            <x-heroicon-s-chevron-right class="rtl:rotate-180 w-4 h-4 text-gray-400 mx-1" />
            <span class="ms-1 text-sm text-gray-400 md:ms-2 dark:text-gray-200">Lab Tests</span>
          </div>
        </li>
      </ol>
    </nav>
  </div>

  {{-- Header --}}
  <header class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
    <div>
      <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-white mb-1 flex items-center gap-3">
        <x-heroicon-s-beaker class="w-7 h-7 text-indigo-600" />
        Manage Lab Tests
      </h1>
      <p class="text-sm md:text-base text-gray-600 dark:text-gray-400">View, search, and manage all lab test definitions.</p>
    </div>

    <a href="{{ route('lab-technician.create-lab-definitions') }}" wire:navigate
       class="inline-flex items-center gap-2 px-5 py-2 md:px-6 md:py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition duration-150 ease-in-out shadow focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
      <x-heroicon-s-plus class="w-5 h-5" aria-hidden="true" />
      <span class="hidden sm:inline">Add Lab Test</span>
    </a>
  </header>

  <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
    <div class="p-4 md:p-6 border-b border-gray-200 dark:border-gray-700">
      <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="relative w-full md:max-w-lg">
          <label for="search" class="sr-only">Search lab tests</label>
          <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <x-heroicon-s-magnifying-glass class="w-5 h-5 text-gray-400 dark:text-gray-500" />
          </div>
          <input id="search" type="search" wire:model.live.debounce.400ms="search"
                 placeholder="Search by test name or code..."
                 class="w-full pl-12 pr-4 py-3 rounded-xl bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition text-gray-900 dark:text-gray-200"
                 aria-label="Search lab tests">
        </div>

        {{-- Optional: Add quick actions like Export / Import --}}
        <div class="flex items-center gap-2">
          {{-- Example utility: clear search --}}
          <button type="button" wire:click="$set('search','')"
                  class="hidden sm:inline-flex items-center gap-2 px-3 py-2 bg-gray-100 dark:bg-gray-700 text-sm rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            Clear
          </button>
        </div>
      </div>
    </div>

    <div class="overflow-x-auto custom-scrollbar">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700/50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
              Test Name
            </th>
            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
              Price
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
              Units
            </th>
            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider dark:text-gray-300">
              Actions
            </th>
          </tr>
        </thead>

        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
          @forelse ($labTests as $test)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                <div class="flex flex-col">
                  <span>{{ $test->test_name }}</span>
                  @if($test->code ?? false)
                    <small class="text-xs text-gray-500 dark:text-gray-400">Code: {{ $test->code }}</small>
                  @endif
                </div>
              </td>

              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 text-center">
                ${{ number_format($test->price, 2) }}
              </td>

              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                {{ $test->units }}
              </td>

              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex items-center justify-center space-x-2">
                  <button wire:click="viewEditTest({{ $test->id }})" title="Edit test"
                          class="ms-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium text-blue-700 bg-blue-100 hover:bg-blue-600 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 dark:bg-blue-900/40 dark:text-blue-300 dark:hover:bg-blue-900/60 transition-all duration-200 transform hover:scale-105">
                    <x-heroicon-s-pencil-square class="w-5 h-5" />
                    <span >Edit</span>
                  </button>

                  <button wire:click="viewDeleteTest({{ $test->id }})" title="Delete test"
                        class="ms-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium text-red-700 bg-red-100 hover:bg-red-600 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400 dark:bg-red-900/40 dark:text-red-300 dark:hover:bg-red-900/60 transition-all duration-200 transform hover:scale-105">
                    <x-heroicon-s-trash class="w-5 h-5" />
                    <span >Delete</span>
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                  <x-heroicon-s-clipboard-document-list class="w-16 h-16 mb-4 text-gray-300 dark:text-gray-600" />
                  <p class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-1">No Lab Tests Found</p>
                  @if ($search)
                    <p class="text-sm">No results for "<span class="font-medium text-gray-700 dark:text-gray-200">{{ $search }}</span>". Try adjusting your search.</p>
                  @else
                    <p class="text-sm">Get started by creating a new lab test definition.</p>
                    <a href="{{ route('lab-technician.create-lab-definitions') }}" wire:navigate
                       class="mt-3 inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                      <x-heroicon-s-plus class="w-4 h-4 mr-2" /> Create Test
                    </a>
                  @endif
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Edit Modal (Test) --}}
  <div x-data="{ show: @entangle('showTestEditModal') }" x-show="show" x-cloak x-trap.noscroll
       class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50">
    <div @click.away="$wire.set('showTestEditModal', false)"
         class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-lg w-full mx-auto transform transition-all">
      <div class="flex justify-between items-start">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Test</h3>
        <button type="button" @click="$wire.set('showTestEditModal', false)"
                class="text-gray-400 hover:bg-gray-200 hover:text-gray-900 rounded-lg p-1.5 inline-flex items-center focus:outline-none">
          <x-heroicon-s-x-mark class="w-5 h-5" />
        </button>
      </div>

      <form wire:submit.prevent="updateTest" class="mt-4">
        <div class="space-y-4">
          <div>
            <label for="editing-test-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Test Name</label>
            <input id="editing-test-name" type="text" wire:model.defer="test_name"
                   class="form-input" />
            @error('test_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
          </div>

           <div>
            <label for="editing-units-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Test Name</label>
            <input id="editing-units-name" type="text" wire:model.defer="units"
                   class="form-input" />
            @error('units') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
          </div>


          <div>
            <label for="editing-price" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Price (USD)</label>
            <input id="editing-price" type="number" step="0.01" min="0" wire:model.defer="price"
                   class="form-input" />
            @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
          </div>

       <div>
            <label for="editing-description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
            <textarea id="editing-description" rows="2" wire:model.defer="description"
                      class="form-input"></textarea>
            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
          <button type="button" @click="$wire.set('showTestEditModal', false)"
                  class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200">
            Cancel
          </button>

          <button type="submit"
                  class="inline-flex justify-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- Delete Modal (Test) --}}
  <div x-data="{ show: @entangle('showTestDeleteModal') }" x-show="show" x-cloak x-trap.noscroll
       class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50">
    <div @click.away="$wire.set('showTestDeleteModal', false)"
         class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 max-w-sm w-full mx-auto transform transition-all">
      <div class="flex justify-between items-start">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Delete Test</h3>
        <button type="button" @click="$wire.set('showTestDeleteModal', false)"
                class="text-gray-400 hover:bg-gray-200 hover:text-gray-900 rounded-lg p-1.5 inline-flex items-center focus:outline-none">
          <x-heroicon-s-x-mark class="w-5 h-5" />
        </button>
      </div>

      <div class="mt-4">
        <p class="text-sm text-gray-500 dark:text-gray-400">Are you sure you want to delete this test? This action cannot be undone.</p>

        <div class="mt-6 flex justify-end gap-3">
          <button type="button" @click="$wire.set('showTestDeleteModal', false)"
                  class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-200">
            Cancel
          </button>

          <button type="button" wire:click="deleteTest" @click="$wire.set('showTestDeleteModal', false)"
                  class="inline-flex justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
            Delete
          </button>
        </div>
      </div>
    </div>
  </div>
</main>
