<div  class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4" x-data="{ open: true }" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-90" style="display: none;">
  <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700 transition-all duration-300" @click.stop>

    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
      <h2 class="text-xl font-bold text-gray-800 dark:text-white">Add Prescription 💊</h2>
      <button type="button" wire:click="$dispatch('close-prescription-modal')" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </div>

    <form wire:submit.prevent="save" class="px-6 py-5 space-y-6">

      <div class="space-y-4">
        @foreach($items as $i => $item)
        <div wire:key="item-{{ $i }}" class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5 shadow-sm transition-all relative">

          @if(count($items) > 1)
          <button type="button" wire:click="removeItem({{ $i }})" class="absolute top-2 right-2 text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-600 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
          </button>
          @endif

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label for="medication-{{ $i }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Medication</label>
              <select id="medication-{{ $i }}" wire:model.live="items.{{ $i }}.medication_id" class="form-input">
                <option value="">Choose a medication…</option>
                @foreach($medications as $m)
                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->form }})</option>
                @endforeach
              </select>
              @error('items.'.$i.'.medication_id') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
              <label for="dosage-{{ $i }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dosage</label>
              <input id="dosage-{{ $i }}" type="text" wire:model="items.{{ $i }}.dosage" placeholder="e.g. 500 mg" class="form-input" />
              @error('items.'.$i.'.dosage') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
            <div>
              <label for="frequency-{{ $i }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Frequency</label>
              <input id="frequency-{{ $i }}" type="text" wire:model="items.{{ $i }}.frequency" placeholder="e.g. 2 times daily" class="form-input" />
              @error('items.'.$i.'.frequency') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
              <label for="duration-{{ $i }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Duration</label>
              <input id="duration-{{ $i }}" type="text" wire:model="items.{{ $i }}.duration" placeholder="e.g. 7 days" class="form-input" />
              @error('items.'.$i.'.duration') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
              <label for="qty-{{ $i }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity</label>
              <input id="qty-{{ $i }}" type="number" wire:model="items.{{ $i }}.qty" min="1" class="form-input" />
              @error('items.'.$i.'.qty') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>
          </div>

          <div class="mt-4">
            <label for="notes-{{ $i }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
            <input id="notes-{{ $i }}" type="text" wire:model="items.{{ $i }}.notes" placeholder="e.g. Take with food" class="form-input" />
          </div>

        </div>
        @endforeach
      </div>

      <div>
        <button type="button" wire:click="addItem" class="flex items-center space-x-1 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
          </svg>
          <span>Add another medication</span>
        </button>
      </div>

      <div class="mt-6">
        <label for="generalNotes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">General Notes for Pharmacist</label>
        <textarea id="generalNotes" wire:model="generalNotes" rows="3" placeholder="Any additional instructions or notes for the pharmacist (e.g., 'Do not substitute')." class="w-full px-4 py-2 border rounded-lg bg-white dark:bg-gray-800 dark:text-white dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none transition-colors duration-200"></textarea>
        @error('generalNotes') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
      </div>

      <div class="flex justify-end gap-3 pt-4 border-t dark:border-gray-700">
        <button type="button" wire:click="$dispatch('close-prescription-modal')" class="px-5 py-2 rounded-lg border text-sm font-medium bg-white text-gray-700 border-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition">
          Cancel
        </button>
        <button type="submit" wire:loading.attr="disabled" wire:target="save" class="px-5 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2">
          <span wire:loading.remove wire:target="save">Save Prescription</span>
          <span wire:loading wire:target="save" class="flex items-center">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="ml-2">Saving...</span>
          </span>
        </button>
      </div>
    </form>
  </div>
</div>
