<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4 md:flex md:items-center md:justify-between space-y-3 md:space-y-0">
                {{-- Title & Breadcrumbs --}}
                <div class="flex-1 min-w-0">
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-1" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('admin.dashboard') }}" wire:navigate
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    {{ __('admin.home') }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">{{ __('admin.shifts_bar') }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                        {{ __('admin.shifts_bar') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Create, view and assign work shifts to manage staff coverage efficiently.
                    </p>
                </div>

                {{-- Action Toolbar --}}
                <div class="flex items-center gap-3">
                    <button wire:click="openModal"
                        class="group relative inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl shadow-md hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all overflow-hidden dark:focus:ring-offset-gray-900">
                        <div
                            class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                        </div>
                        <x-heroicon-m-plus class="w-5 h-5" />
                        <span>{{ __('admin.add_new_user_button') }}</span>
                    </button>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="relative min-h-[400px] p-4 sm:p-6">

            {{-- Loading Overlay --}}
            <div wire:loading.flex
                class="absolute inset-0 bg-white/60 dark:bg-gray-900/60 backdrop-blur-[2px] z-20 flex items-start justify-center rounded-2xl pt-20 transition-all">
                <div
                    class="flex items-center gap-3 px-6 py-3 bg-white dark:bg-gray-800 rounded-full shadow-xl border border-slate-100 dark:border-gray-700 animate-bounce">
                    <x-heroicon-o-arrow-path class="animate-spin h-5 w-5 text-blue-600" />
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Updating list...</span>
                </div>
            </div>

            {{-- DESKTOP: Table View --}}
            <div
                class="hidden md:block bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-gray-800">
                        <thead class="bg-slate-50 dark:bg-gray-950">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ __('admin.activities_filter_date') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ __('admin.activities_filter_type') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ __('admin.activities_table_time') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-center text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ __('admin.shifts_assigned') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ __('admin.activities_table_actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                            @forelse ($shifts as $shift)
                                <tr wire:key="shift-{{ $shift->id }}"
                                    class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150">

                                    {{-- Date Column --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-slate-900 dark:text-white">
                                                {{ $shift->shift_date->format('l') }}
                                            </span>
                                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                                {{ $shift->shift_date->format('M j, Y') }}
                                            </span>
                                        </div>
                                    </td>

                                    {{-- Type Column --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $typeStyles = match (strtolower($shift->shift_type)) {
                                                'morning' => 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800',
                                                'afternoon' => 'bg-sky-50 text-sky-700 border-sky-100 dark:bg-sky-900/30 dark:text-sky-300 dark:border-sky-800',
                                                'night' => 'bg-indigo-50 text-indigo-700 border-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800',
                                                default => 'bg-slate-50 text-slate-700 border-slate-100 dark:bg-gray-800 dark:text-slate-300 dark:border-gray-700',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold capitalize border shadow-sm {{ $typeStyles }}">
                                            {{ $shift->shift_type }}
                                        </span>
                                    </td>

                                    {{-- Time Column --}}
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center text-sm font-medium text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-gray-800/50 w-fit px-2 py-1 rounded-lg border border-slate-100 dark:border-gray-700">
                                            <x-heroicon-o-clock class="w-4 h-4 mr-2 text-slate-400" />
                                            <span>{{ $shift->start_time->format('h:i A') }}</span>
                                            <span class="mx-2 text-slate-300 dark:text-slate-600">&rarr;</span>
                                            <span>{{ $shift->end_time->format('h:i A') }}</span>
                                        </div>
                                    </td>

                                    {{-- Assigned Count --}}
                                    <td class="px-6 py-4 text-center">
                                        @if ($shift->user_count > 0)
                                            <div class="inline-flex -space-x-2 overflow-hidden items-center">
                                                <div class="h-8 w-8 rounded-full ring-2 ring-white dark:ring-gray-900 bg-slate-200 dark:bg-gray-700 flex items-center justify-center text-slate-500">
                                                    <x-heroicon-s-user class="w-4 h-4" />
                                                </div>
                                                @if ($shift->user_count > 1)
                                                    <div class="h-8 w-8 rounded-full ring-2 ring-white dark:ring-gray-900 bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center text-xs font-bold text-blue-600 dark:text-blue-300 z-10">
                                                        +{{ $shift->user_count - 1 }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-500 dark:bg-gray-800 dark:text-gray-500 border border-slate-200 dark:border-gray-700">
                                                <div class="w-1.5 h-1.5 rounded-full bg-slate-400"></div>
                                                Unassigned
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <button wire:click="edit({{ $shift->id }})"
                                                class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                title="{{ __('admin.action_edit') }}">
                                                <x-heroicon-s-pencil-square class="w-4 h-4" />
                                            </button>
                                            <button wire:click="delete({{ $shift->id }})"
                                                wire:confirm="{{ __('admin.modal_delete_department_message') }}"
                                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500"
                                                title="{{ __('admin.action_delete') }}">
                                                <x-heroicon-s-trash class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="w-16 h-16 bg-slate-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-gray-700">
                                            <x-heroicon-o-calendar-days class="h-8 w-8 text-slate-400" />
                                        </div>
                                        <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ __('admin.shifts_empty') }}</h3>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400 mb-4">
                                            Get started by creating a new shift schedule for your team.
                                        </p>
                                        <button wire:click="openModal" class="text-sm font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                            {{ __('admin.add_new_user_button') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MOBILE: Card View (Hidden on Desktop) --}}
            <div class="md:hidden space-y-4">
                @forelse ($shifts as $shift)
                    <div wire:key="mobile-card-{{ $shift->id }}"
                        class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-slate-200 dark:border-gray-800 p-4 active:scale-[0.99] transition-transform">

                        {{-- Top: Date & Type --}}
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-slate-900 dark:text-white">
                                    {{ $shift->shift_date->format('l') }}
                                </span>
                                <span class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $shift->shift_date->format('M j, Y') }}
                                </span>
                            </div>
                             @php
                                $typeStyles = match (strtolower($shift->shift_type)) {
                                    'morning' => 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800',
                                    'afternoon' => 'bg-sky-50 text-sky-700 border-sky-100 dark:bg-sky-900/30 dark:text-sky-300 dark:border-sky-800',
                                    'night' => 'bg-indigo-50 text-indigo-700 border-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800',
                                    default => 'bg-slate-50 text-slate-700 border-slate-100 dark:bg-gray-800 dark:text-slate-300 dark:border-gray-700',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold border capitalize {{ $typeStyles }}">
                                {{ $shift->shift_type }}
                            </span>
                        </div>

                        {{-- Middle: Time & Users --}}
                        <div class="flex items-center justify-between bg-slate-50 dark:bg-gray-800/50 p-3 rounded-lg border border-slate-100 dark:border-gray-800 mb-3">
                            <div class="flex items-center text-xs font-bold text-slate-600 dark:text-slate-300">
                                <x-heroicon-o-clock class="w-3.5 h-3.5 mr-1.5 text-slate-400" />
                                <span>{{ $shift->start_time->format('h:i A') }}</span>
                                <span class="mx-1 text-slate-300">&rarr;</span>
                                <span>{{ $shift->end_time->format('h:i A') }}</span>
                            </div>

                            @if ($shift->user_count > 0)
                                <div class="flex items-center gap-1">
                                    <x-heroicon-s-user class="w-3 h-3 text-slate-400" />
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">{{ $shift->user_count }}</span>
                                </div>
                            @else
                                <span class="text-[10px] text-slate-400 italic">Unassigned</span>
                            @endif
                        </div>

                        {{-- Bottom: Actions --}}
                        <div class="flex items-center justify-end gap-2 border-t border-slate-100 dark:border-gray-800 pt-3">
                            <button wire:click="edit({{ $shift->id }})"
                                class="p-2 text-blue-600 bg-blue-50 dark:bg-blue-900/30 rounded-lg hover:bg-blue-100 transition-colors border border-blue-100 dark:border-blue-800">
                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                            </button>
                            <button wire:click="delete({{ $shift->id }})"
                                wire:confirm="{{ __('admin.modal_delete_department_message') }}"
                                class="p-2 text-red-600 bg-red-50 dark:bg-red-900/30 rounded-lg hover:bg-red-100 transition-colors border border-red-100 dark:border-red-800">
                                <x-heroicon-o-trash class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-slate-300 dark:border-gray-700">
                        <x-heroicon-o-calendar-days class="mx-auto h-12 w-12 text-slate-300" />
                        <h3 class="mt-2 text-sm font-bold text-slate-900 dark:text-white">
                             {{ __('admin.shifts_empty') }}
                        </h3>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($shifts->hasPages())
                <div class="mt-8">
                    {{ $shifts->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL (Consistent Design) --}}
    <div
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        x-init="$watch('show', value => { if (value) { document.body.style.overflow = 'hidden'; } else { document.body.style.overflow = ''; } })"
        style="display: none;"
    >
        <template x-teleport="body">
            <div x-show="show" class="relative z-50" role="dialog" aria-modal="true">
                {{-- Backdrop --}}
                <div x-show="show"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity"
                     wire:click="closeModal"></div>

                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <form wire:submit.prevent="save"
                              x-show="show"
                              x-transition:enter="ease-out duration-300"
                              x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                              x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                              x-transition:leave="ease-in duration-200"
                              x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                              x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                              class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all sm:w-full sm:max-w-lg border border-slate-100 dark:border-gray-800">

                            {{-- Modal Header --}}
                            <div class="bg-white dark:bg-gray-900 px-6 py-4 border-b border-slate-100 dark:border-gray-800 flex items-center justify-between sticky top-0 z-10">
                                <div>
                                    <h3 class="text-lg font-bold leading-6 text-slate-900 dark:text-white">
                                        {{ $shiftId ? __('admin.modal_edit_department_title') : __('admin.button_create_user') }}
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Fill in the details below to schedule a shift.</p>
                                </div>
                                <button type="button" wire:click="closeModal"
                                    class="rounded-lg bg-slate-50 dark:bg-gray-800 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400">
                                    <x-heroicon-o-x-mark class="h-5 w-5" />
                                </button>
                            </div>

                            <div class="p-6 sm:p-8 space-y-6">
                                {{-- Date & Type Group --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                    <div class="space-y-1.5">
                                        <label for="shift_date" class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('admin.activities_filter_date') }}</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <x-heroicon-o-calendar class="h-5 w-5 text-slate-400" />
                                            </div>
                                            <input type="date" id="shift_date" wire:model="shift_date"
                                                   class="pl-10 block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:text-white py-2.5 @error('shift_date') border-red-500 text-red-900 placeholder-red-300 @enderror">
                                        </div>
                                        @error('shift_date') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="space-y-1.5">
                                        <label for="shift_type" class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('admin.activities_filter_type') }}</label>
                                        <div class="relative">
                                            <select id="shift_type" wire:model="shift_type"
                                                    class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:text-white py-2.5 @error('shift_type') border-red-500 @enderror">
                                                <option value="">Select Type</option>
                                                <option value="Morning">Morning</option>
                                                <option value="Afternoon">Afternoon</option>
                                                <option value="Night">Night</option>
                                            </select>
                                        </div>
                                        @error('shift_type') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                {{-- Time Range Group --}}
                                <div class="bg-blue-50/50 dark:bg-blue-900/10 p-5 rounded-xl border border-blue-100 dark:border-blue-900/30">
                                    <div class="flex items-center gap-2 mb-3">
                                        <x-heroicon-o-clock class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                                        <h3 class="text-xs font-bold text-blue-900 dark:text-blue-200 uppercase tracking-wider">Shift Duration</h3>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-1.5">
                                            <label for="start_time" class="block text-xs font-bold text-slate-500 dark:text-slate-400">{{ __('admin.shifts_start') }}</label>
                                            <input type="time" id="start_time" wire:model="start_time"
                                                   class="block w-full rounded-lg border-slate-200 dark:border-gray-600 dark:bg-gray-800 text-sm focus:border-blue-500 focus:ring-blue-500 dark:text-white @error('start_time') border-red-500 @enderror">
                                            @error('start_time') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="space-y-1.5">
                                            <label for="end_time" class="block text-xs font-bold text-slate-500 dark:text-slate-400">End Time</label>
                                            <input type="time" id="end_time" wire:model="end_time"
                                                   class="block w-full rounded-lg border-slate-200 dark:border-gray-600 dark:bg-gray-800 text-sm focus:border-blue-500 focus:ring-blue-500 dark:text-white @error('end_time') border-red-500 @enderror">
                                            @error('end_time') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="px-6 py-4 bg-slate-50 dark:bg-gray-900/50 flex flex-row-reverse gap-3 rounded-b-2xl border-t border-slate-100 dark:border-gray-800">
                                <button type="submit"
                                        class="inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 text-white text-sm font-bold rounded-xl shadow-md hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 disabled:opacity-50 disabled:cursor-not-allowed transition-all w-full sm:w-auto"
                                        wire:loading.attr="disabled">
                                    <x-heroicon-o-check class="w-4 h-4 mr-2" wire:loading.remove />
                                    <x-heroicon-o-arrow-path class="w-4 h-4 mr-2 animate-spin" wire:loading />
                                    <span wire:loading.remove>{{ __('admin.modal_button_save_changes') }}</span>
                                    <span wire:loading>{{ __('admin.modal_button_saving') }}</span>
                                </button>
                                <button type="button" wire:click="closeModal"
                                        class="inline-flex items-center justify-center px-5 py-2.5 bg-white dark:bg-gray-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-gray-600 rounded-xl text-sm font-bold shadow-sm hover:bg-slate-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-colors w-full sm:w-auto">
                                    {{ __('admin.modal_button_cancel') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>
</main>
