<main class="flex-1  sm:p-6 bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">

    {{-- Hero section --}}
    <div class="mb-8 relative p-2">
        {{-- Breadcrumb --}}
        <nav class="flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 mb-8 mt-4">
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                <x-heroicon-s-home class="w-4 h-4 mr-1.5" />
                {{ __('admin.home') }}
            </a>
            <x-heroicon-s-chevron-right class="w-4 h-4 mx-2 text-gray-300 dark:text-gray-600" />
            <span class="text-gray-900 dark:text-gray-100 font-meduim">{{ __('admin.shifts_bar') }}</span>
        </nav>

        {{-- Header Content --}}
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white flex items-center gap-3 tracking-tight">

                    {{ __('admin.shifts_bar') }}
                </h1>
                  <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">    Create, view and assign work shifts to manage staff coverage efficiently.
                </p>
            </div>

            {{-- Action Toolbar --}}
            <div class="flex items-center gap-3">
                 {{-- Placeholder for Search/Filter (Visual enhancement) --}}
                <div class="relative hidden sm:block">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
                    <input type="text" placeholder="Search shifts..." class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-white shadow-sm" />
                </div>

                <button wire:click="openModal"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 dark:focus:ring-offset-gray-900">
                    <x-heroicon-m-plus class="w-5 h-5" />
                    {{ __('admin.add_new_user_button') }}
                </button>
            </div>
        </div>
    </div>

    {{-- Shifts table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden ring-1 ring-black/5">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.activities_filter_date') }}</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.activities_filter_type') }}</th>
                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.activities_table_time') }}</th>
                    <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('admin.shifts_assigned') }}</th>
                    <th scope="col" class="relative px-6 py-4"><span class="sr-only">{{ __('admin.activities_table_actions') }}</span></th>
                </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                @forelse ($shifts as $shift)
                    <tr wire:key="shift-{{ $shift->id }}"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 group">

                        {{-- Date Column --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $shift->shift_date->format('l') }}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $shift->shift_date->format('M j, Y') }}
                                </span>
                            </div>
                        </td>

                        {{-- Type Column with Badges --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $typeStyles = match(strtolower($shift->shift_type)) {
                                    'morning' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                                    'afternoon' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/30 dark:text-sky-300 border-sky-200 dark:border-sky-800',
                                    'night' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300 border-indigo-200 dark:border-indigo-800',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border-gray-200 dark:border-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $typeStyles }} capitalize">
                                {{ $shift->shift_type }}
                            </span>
                        </td>

                        {{-- Time Column --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                                <x-heroicon-o-clock class="w-4 h-4 mr-2 text-gray-400" />
                                <span>{{ $shift->start_time->format('h:i A') }}</span>
                                <span class="mx-2 text-gray-300 dark:text-gray-600">&rarr;</span>
                                <span>{{ $shift->end_time->format('h:i A') }}</span>
                            </div>
                        </td>

                        {{-- Assigned Count --}}
                        <td class="px-6 py-4 text-center">
                            @if($shift->user_count > 0)
                                <div class="inline-flex -space-x-2 overflow-hidden">
                                    {{-- Visual fake avatars for enhanced UI feel --}}
                                    <div class="inline-block h-6 w-6 rounded-full ring-2 ring-white dark:ring-gray-800 bg-gray-200 flex items-center justify-center text-xs text-gray-500">
                                        <x-heroicon-s-user class="w-3 h-3" />
                                    </div>
                                    @if($shift->user_count > 1)
                                        <div class="inline-block h-6 w-6 rounded-full ring-2 ring-white dark:ring-gray-800 bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-[10px] font-bold text-indigo-600 dark:text-indigo-300">
                                            +{{ $shift->user_count - 1 }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-500">
                                    Unassigned
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-2 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                <button wire:click="edit({{ $shift->id }})"
                                        class="p-1.5 text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 dark:hover:text-indigo-400 rounded-lg transition-colors"
                                        title="{{ __('admin.action_edit') }}">
                                    <x-heroicon-m-pencil-square class="w-5 h-5" />
                                </button>
                                <button wire:click="delete({{ $shift->id }})"
                                        wire:confirm="{{ __('admin.modal_delete_department_message') }}"
                                        class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 dark:hover:text-red-400 rounded-lg transition-colors"
                                        title="{{ __('admin.action_delete') }}">
                                    <x-heroicon-m-trash class="w-5 h-5" />
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="flex flex-col items-center justify-center py-16 text-center">
                                <div class="bg-gray-100 dark:bg-gray-700/50 p-4 rounded-full mb-3">
                                    <x-heroicon-o-calendar-days class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                                </div>
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('admin.shifts_empty') }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm">
                                    Get started by creating a new shift schedule for your team.
                                </p>
                                <button wire:click="openModal" class="mt-4 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ __('admin.add_new_user_button') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($shifts->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                {{ $shifts->links() }}
            </div>
        @endif
    </div>

    {{-- Add / Edit Modal --}}
    <!--
      The modal uses Alpine transition classes (x-transition) for smoothness.
      Ensure you have Alpine.js installed. If not, remove the x-transition attributes.
    -->
    <div
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="display: none;"
    >
        {{-- Backdrop --}}
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="closeModal"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <form wire:submit.prevent="save"
                  x-show="show"
                  x-transition:enter="ease-out duration-300"
                  x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                  x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                  x-transition:leave="ease-in duration-200"
                  x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                  x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                  class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 dark:border-gray-700">

                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50/50 dark:bg-gray-800">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $shiftId ? __('admin.modal_edit_department_title') : __('admin.button_create_user') }}
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Fill in the details below.</p>
                    </div>
                    <button type="button" wire:click="closeModal"
                            class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 bg-transparent hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full p-1 transition">
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Date & Type Group --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="space-y-1">
                            <label for="shift_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.activities_filter_date') }}</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <x-heroicon-o-calendar class="h-5 w-5 text-gray-400" />
                                </div>
                                <input type="date" id="shift_date" wire:model="shift_date"
                                       class="pl-10 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:text-white @error('shift_date') border-red-500 text-red-900 placeholder-red-300 @enderror">
                            </div>
                            @error('shift_date') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-1">
                            <label for="shift_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.activities_filter_type') }}</label>
                            <div class="relative">
                                <select id="shift_type" wire:model="shift_type"
                                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:text-white @error('shift_type') border-red-500 @enderror">
                                    <option value="">Select Type</option>
                                    <option value="Morning">Morning</option>
                                    <option value="Afternoon">Afternoon</option>
                                    <option value="Night">Night</option>
                                </select>
                            </div>
                            @error('shift_type') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- Time Range Group --}}
                    <div class="bg-indigo-50 dark:bg-indigo-900/10 p-4 rounded-xl space-y-4">
                        <h3 class="text-xs font-semibold text-indigo-900 dark:text-indigo-200 uppercase tracking-wider mb-2">Duration</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label for="start_time" class="block text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('admin.shifts_start') }}</label>
                                <input type="time" id="start_time" wire:model="start_time"
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:text-white @error('start_time') border-red-500 @enderror">
                                @error('start_time') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-1">
                                <label for="end_time" class="block text-xs font-medium text-gray-600 dark:text-gray-400">End Time</label>
                                <input type="time" id="end_time" wire:model="end_time"
                                       class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:text-white @error('end_time') border-red-500 @enderror">
                                @error('end_time') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 flex items-center justify-end gap-3 rounded-b-2xl">
                    <button type="button" wire:click="closeModal"
                            class="px-4 py-2 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-colors">
                        {{ __('admin.modal_button_cancel') }}
                    </button>
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                            wire:loading.attr="disabled">
                        <x-heroicon-o-check class="w-4 h-4 mr-2" wire:loading.remove />
                        <x-heroicon-o-arrow-path class="w-4 h-4 mr-2 animate-spin" wire:loading />
                        <span wire:loading.remove>{{ __('admin.modal_button_save_changes') }}</span>
                        <span wire:loading>{{ __('admin.modal_button_saving') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>
