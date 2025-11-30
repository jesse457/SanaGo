<div
   x-data="{ open: true }" x-show="open"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-90"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-90"
    class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
    style="display: none;"
>
    <div
        class="w-full max-w-lg bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-700 transition-all duration-300"
        @click.stop
    >
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Request Lab Test 🧪</h2>
            <button wire:click="$dispatch('close-lab-request-modal')"
                class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-400 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form wire:submit.prevent="save" class="px-6 py-5 space-y-5">
            <div>
                <label for="testId" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Lab Test
                </label>
                <select id="testId" wire:model.live="testId"
                    class="form-input">
                    <option value="">Select a test…</option>
                    @foreach ($tests as $t)
                        <option value="{{ $t->id }}">{{ $t->test_name }}</option>
                    @endforeach
                </select>
                @error('testId')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="urgency" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Urgency
                </label>
                <select id="urgency" wire:model="urgency"
                    class="form-input">
                    <option value="Routine">Routine</option>
                    <option value="STAT">Normal</option>
                    <option value="Urgent">Urgent</option>
                </select>
                 @error('urgency')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="labTechId" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Assign to Lab Technician (Optional)
                </label>
                <select id="labTechId" wire:model="labTechId"
                    class="form-input">
                    <option value="">Do not assign</option>
                    @foreach ($labTechnicians as $tech)
                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                    @endforeach
                </select>
                @error('labTechId')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="reason" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                    Reason / Notes
                </label>
                <textarea id="reason" wire:model="reason" rows="3"
                    placeholder="Add any relevant notes or reasons for the test…"
                    class="form-input"></textarea>
                     @error('reason')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" wire:click="$dispatch('close-lab-request-modal')"
                    class="px-4 py-2 rounded-lg border text-sm font-medium bg-white text-gray-700 border-gray-300 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition">
                    Cancel
                </button>
                <button type="submit" wire:loading.attr="disabled" wire:target="save"
                    class="px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition disabled:opacity-50 flex items-center space-x-2">
                    <span wire:loading.remove wire:target="save">Send Request</span>
                    <span wire:loading wire:target="save" class="flex items-center">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="ml-2">Sending...</span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
