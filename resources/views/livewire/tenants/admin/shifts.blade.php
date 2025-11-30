{{-- resources/views/livewire/admin/manage-shifts.blade.php --}}
<main class="flex-1 p-4  bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">

    {{-- Hero section --}}
    <div class="mb-8">
        <nav class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center hover:text-gray-700 dark:hover:text-gray-200">
                <x-heroicon-s-home class="w-4 h-4 mr-1" />
                {{ __('admin.home') }}
            </a>
            <x-heroicon-s-chevron-right class="w-4 h-4 mx-1" />
            <span class="text-gray-800 dark:text-gray-200">{{ __('admin.shifts_bar') }}</span>
        </nav>

        <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between mt-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white flex items-center gap-3">
                    <x-hugeicons-bed-single-02 class="w-8 h-8 text-indigo-500" />
                    {{ __('admin.shifts_bar') }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">
                    {{-- This string is new, I'll map it to shifts_description if you want a blank string, or use a new key --}}
                    Create, view and assign work shifts.
                </p>
            </div>

            <button wire:click="openModal"
                    class="mt-4 sm:mt-0 inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white font-semibold rounded-lg shadow hover:bg-indigo-700 transition">
                <x-heroicon-o-plus class="w-5 h-5" />
                {{ __('admin.add_new_user_button') }} {{-- Reusing 'Add New User' as 'Add Shift' for now --}}
            </button>
        </header>
    </div>

    {{-- Shifts table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">{{ __('admin.activities_filter_date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">{{ __('admin.activities_filter_type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">{{ __('admin.activities_table_time') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">{{ __('admin.shifts_assigned') }}</th>
                    <th class="relative px-6 py-3"><span class="sr-only">{{ __('admin.activities_table_actions') }}</span></th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($shifts as $shift)
                    <tr wire:key="shift-{{ $shift->id }}"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                            {{ $shift->shift_date->format('D, M j, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300 capitalize">
                            {{ $shift->shift_type }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $shift->start_time->format('h:i A') }} – {{ $shift->end_time->format('h:i A') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $shift->user_count
                                    ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-600/20 dark:text-indigo-300'
                                    : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400' }}">
                                {{ $shift->user_count ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-3">
                            <button wire:click="edit({{ $shift->id }})"
                                    class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200">
                                <x-heroicon-o-pencil class="w-4 h-4 inline mb-px" /> {{ __('admin.action_edit') }}
                            </button>
                            <button wire:click="delete({{ $shift->id }})"
                                    wire:confirm="{{ __('admin.modal_delete_department_message') }}" {{-- Reusing general delete confirmation message --}}
                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-200">
                                <x-heroicon-o-trash class="w-4 h-4 inline mb-px" /> {{ __('admin.action_delete') }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-gray-500 dark:text-gray-400">
                            <x-heroicon-o-calendar-days class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-2" />
                            {{ __('admin.shifts_empty') }}
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($shifts->hasPages())
            <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                {{ $shifts->links() }}
            </div>
        @endif
    </div>

    {{-- Add / Edit Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 bg-gray-900/50 flex items-center justify-center p-4 z-50">
            <form wire:submit.prevent="save"
                  class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-md">
                <div class="flex items-center justify-between px-6 py-4 border-b dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $shiftId ? __('admin.modal_edit_department_title') : __('admin.button_create_user') }} {{-- Using existing keys, ideally would be 'Edit Shift'/'Create Shift' --}}
                    </h2>
                    <button type="button" wire:click="closeModal"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>

                <div class="p-6 space-y-5">
                    <div>
                        <label for="shift_date" class="form-label">{{ __('admin.activities_filter_date') }}</label>
                        <input type="date" id="shift_date" wire:model="shift_date"
                               class="form-input @error('shift_date') border-red-500 @enderror">
                        @error('shift_date') <span class="form-error">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="shift_type" class="form-label">{{ __('admin.activities_filter_type') }}</label>
                        <select id="shift_type" wire:model="shift_type"
                                class="form-select @error('shift_type') border-red-500 @enderror">
                            <option>Morning</option> {{-- These options need specific keys if they change --}}
                            <option>Afternoon</option>
                            <option>Night</option>
                        </select>
                        @error('shift_type') <span class="form-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="start_time" class="form-label">{{ __('admin.shifts_start') }}</label>
                            <input type="time" id="start_time" wire:model="start_time"
                                   class="form-input @error('start_time') border-red-500 @enderror">
                            @error('start_time') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label for="end_time" class="form-label">End</label> {{-- No specific 'end' key, using 'End' --}}
                            <input type="time" id="end_time" wire:model="end_time"
                                   class="form-input @error('end_time') border-red-500 @enderror">
                            @error('end_time') <span class="form-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-t dark:border-gray-700 flex justify-end space-x-3">
                    <button type="button" wire:click="closeModal"
                            class="btn-secondary">{{ __('admin.modal_button_cancel') }}</button>
                    <button type="submit" class="btn-primary"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>{{ __('admin.modal_button_save_changes') }}</span>
                        <span wire:loading>{{ __('admin.modal_button_saving') }}</span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</main>
