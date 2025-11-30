<div class="max-w-3xl mx-auto p-4 sm:p-6">
    <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-200 dark:ring-gray-800 rounded-xl overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Help Us Make It Better</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Your thoughts are important! Tell us what you
                    think to help us improve.</p>
            </div>
            <span
                class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-800 px-2.5 py-0.5 text-xs font-medium text-gray-700 dark:text-gray-300">SanaGo</span>
        </div>

        @if ($submitted)
            <div
                class="px-4 sm:px-6 py-4 bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200 border-b border-green-200/60 dark:border-green-800">
                <p class="text-sm">Thank you! Your feedback has been sent. We really appreciate your help.</p>
            </div>
        @endif

        <form wire:submit.prevent="submit" class="px-4 sm:px-6 py-6 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="col-span-1 sm:col-span-2">
                    <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300">What is
                        this about?</label>
                    <input id="subject" type="text" wire:model.defer="subject"
                        placeholder="e.g., I have a problem with appointments"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                    @error('subject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Choose a
                        topic</label>
                    <select id="category" wire:model.defer="category"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="issue">A problem or error</option>
                        <option value="suggestion">A new idea or suggestion</option>
                        <option value="general">A question or general comment</option>
                        <option value="other">Something else</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="priority" class="block text-sm font-medium text-gray-700 dark:text-gray-300">How
                        important is this?</label>
                    <div class="mt-1">
                        <div class="grid grid-cols-4 gap-2">
                            @php $priorities = ['low' => 'Low','normal'=>'Normal','high'=>'High','urgent'=>'Urgent']; @endphp
                            @foreach ($priorities as $key => $label)
                                <button type="button" wire:click="$set('priority','{{ $key }}')"
                                    class="px-3 py-2 rounded-lg text-sm border transition
                          {{ $priority === $key
                              ? 'bg-blue-600 text-white border-primary-600'
                              : 'bg-white dark:bg-gray-900 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                    @error('priority')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Which
                        part of the app is this about? (optional)</label>
                    <input id="department" type="text" wire:model.defer="department"
                        placeholder="e.g., Appointments, Billing"
                        class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500" />
                    @error('department')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tell us what
                    happened</label>
                <textarea id="message" rows="6" wire:model.defer="message"
                    placeholder="Please describe what you experienced in your own words. The more details you give us, the better we can help."
                    class="mt-1 block w-full rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    Tip: Tell us what you were trying to do, what happened, and what you expected to happen instead.
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Add a picture or file
                    (optional)</label>
                <div
                    class="mt-1 border-2 border-dashed rounded-lg p-4 text-center text-sm text-gray-600 dark:text-gray-400 border-gray-300 dark:border-gray-700">
                    <p>Drag files here or click to add them</p>
                    <p class="mt-1 text-xs">Accepted: pictures, documents. Up to 5 files.</p>
                </div>
                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach ($attachments as $i => $file)
                        <div
                            class="flex items-center justify-between rounded-md border border-gray-200 dark:border-gray-800 px-3 py-2 text-sm">
                            <div class="truncate">
                                <span
                                    class="font-medium text-gray-800 dark:text-gray-200">{{ $file['name'] ?? 'file.png' }}</span>
                                <span class="ml-2 text-gray-500 text-xs">{{ $file['size'] ?? '200 KB' }}</span>
                            </div>
                            <button type="button" class="text-red-600 hover:text-red-700"
                                wire:click="$set('attachments', array_values(collect($attachments)->except($i)->toArray()))">Remove</button>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- THIS SECTION IS NOW INSIDE THE FORM TAG --}}
            <div class="flex items-center justify-between">
                <div class="text-xs text-gray-500 dark:text-gray-400">
                    By sending this, you agree to our rules and privacy policy.
                </div>
                <div class="flex  gap-3">
                    <button type="button"
                        class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800"
                        wire:click="$refresh">Start Over</button>
                    <button type="submit"
                        class="relative inline-flex items-center justify-center px-4 py-2 rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-primary-500 disabled:opacity-70 disabled:cursor-not-allowed"
                        >
                        <span wire:loading.remove>
                            <x-heroicon-o-paper-airplane class="w-5 h-5 inline-block -ml-1 mr-2 transform" />
                            Send
                        </span>
                        <span wire:loading>
                            <x-heroicon-o-arrow-path class="w-5 h-5 inline-block -ml-1 mr-2 animate-spin" />
                            Sending...
                        </span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
