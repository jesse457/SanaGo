<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4 md:flex md:items-center md:justify-between space-y-3 md:space-y-0">

                {{-- Title & Breadcrumbs --}}
                <div class="flex-1 min-w-0">
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-1" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('doctor.dashboard') }}" wire:navigate class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    {{ __('Home') }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">Duty Roster</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                        Duty Roster
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Manage staff coverage and daily shift rotations.
                    </p>
                </div>

                {{-- Right Actions --}}
                <div class="flex items-center gap-4">
                    <button wire:click="openModal" class="group relative inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl shadow-md hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all overflow-hidden">
                        <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                        <x-heroicon-o-plus class="w-5 h-5" />
                        <span>Add New Shift</span>
                    </button>
                </div>
            </div>

            {{-- Filters Bar --}}
            <div class="px-4 sm:px-6 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex flex-col md:flex-row gap-3 items-center justify-between">
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto flex-1">
                    {{-- Search Staff --}}
                    <div class="relative w-full sm:max-w-xs group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-m-magnifying-glass class="h-4 w-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                        </div>
                        <input type="text" wire:model.live.debounce.400ms="search"
                            class="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 sm:text-sm"
                            placeholder="Search by Staff Name...">
                    </div>

                    <div class="flex gap-3 w-full sm:w-auto">
                        <select wire:model.live="filterType" class="block w-full sm:w-40 border border-gray-300 dark:border-gray-600 rounded-lg py-2 pl-3 pr-8 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-1 focus:ring-blue-500 sm:text-sm">
                            <option value="">All Shift Types</option>
                            <option value="Morning">Morning</option>
                            <option value="Afternoon">Afternoon</option>
                            <option value="Night">Night</option>
                        </select>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="relative min-h-[400px] p-4 sm:p-6">

            {{-- Loading Overlay --}}
            <div wire:loading.flex class="absolute inset-0 bg-white/60 dark:bg-gray-900/60 backdrop-blur-[2px] z-20 flex items-start justify-center rounded-2xl pt-20">
                <div class="flex items-center gap-3 px-6 py-3 bg-white dark:bg-gray-800 rounded-full shadow-xl border border-slate-100 dark:border-gray-700 animate-bounce">
                    <x-heroicon-o-arrow-path class="animate-spin h-5 w-5 text-blue-600" />
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Updating Roster...</span>
                </div>
            </div>

            {{-- DESKTOP: Table View --}}
            <div class="hidden md:block bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                <table class="min-w-full divide-y divide-slate-100 dark:divide-gray-800">
                    <thead class="bg-slate-50 dark:bg-gray-950">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Shift Type</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Time Range</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Assigned Staff</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                        @forelse ($shifts as $shift)
                            <tr wire:key="row-{{ $shift->id }}" class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ $shift->shift_date->format('l, M d') }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $shift->shift_date->format('Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $colors = match($shift->shift_type) {
                                            'Morning' => 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800',
                                            'Afternoon' => 'bg-sky-50 text-sky-700 border-sky-100 dark:bg-sky-900/30 dark:text-sky-300 dark:border-sky-800',
                                            'Night' => 'bg-indigo-50 text-indigo-700 border-indigo-100 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-800',
                                            default => 'bg-slate-50 text-slate-700 border-slate-100 dark:bg-gray-800 dark:text-slate-300',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border shadow-sm {{ $colors }}">
                                        {{ $shift->shift_type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-slate-400 bg-slate-100 dark:bg-gray-800 px-3 py-1.5 rounded-lg w-fit">
                                        <x-heroicon-o-clock class="w-4 h-4 text-blue-500" />
                                        {{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex -space-x-2">
                                        @foreach($shift->user->take(4) as $staff)
                                            <img src="{{ $staff->profile_picture ? Storage::disk('s3')->temporaryUrl($staff->profile_picture, now()->addMinutes(5)) : asset('images/default_profile.png') }}"
                                                 class="h-8 w-8 rounded-full border-2 border-white dark:border-gray-900 shadow-sm object-cover"
                                                 title="{{ $staff->name }}">
                                        @endforeach
                                        @if($shift->user_count > 4)
                                            <div class="h-8 w-8 rounded-full bg-slate-900 text-white flex items-center justify-center text-[10px] font-bold border-2 border-white dark:border-gray-900">+{{ $shift->user_count - 4 }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="edit({{ $shift->id }})" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                                            <x-heroicon-s-pencil-square class="w-4 h-4" />
                                        </button>
                                        <button wire:click="delete({{ $shift->id }})" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                            <x-heroicon-s-trash class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <x-heroicon-o-calendar class="mx-auto h-12 w-12 text-slate-300" />
                                    <h3 class="mt-2 text-sm font-bold text-slate-900 dark:text-white">No shifts found</h3>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE: Card View --}}
            <div class="md:hidden space-y-4">
                @foreach ($shifts as $shift)
                    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-slate-200 dark:border-gray-800 p-4">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $shift->shift_date->format('l, M d') }}</h3>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }}</p>
                            </div>
                            <span class="px-2 py-1 rounded text-[10px] font-bold border uppercase {{ $colors }}">
                                {{ $shift->shift_type }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between border-t border-slate-100 dark:border-gray-800 pt-3">
                            <div class="flex -space-x-1.5">
                                @foreach($shift->user->take(3) as $staff)
                                    <img src="{{ asset('images/default_profile.png') }}" class="h-6 w-6 rounded-full border border-white dark:border-gray-900 shadow-sm">
                                @endforeach
                            </div>
                            <div class="flex gap-1">
                                <button wire:click="edit({{ $shift->id }})" class="p-2 text-blue-600 bg-blue-50 dark:bg-blue-900/30 rounded-lg"><x-heroicon-o-pencil-square class="w-4 h-4" /></button>
                                <button wire:click="delete({{ $shift->id }})" class="p-2 text-red-600 bg-red-50 dark:bg-red-900/30 rounded-lg"><x-heroicon-o-trash class="w-4 h-4" /></button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- MODAL (Teleported) --}}
    <div x-data="{ show: @entangle('showModal') }" x-show="show" class="relative z-50" style="display: none;">
        <div x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity"></div>

        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div x-show="show" @click.away="show = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all w-full max-w-md border border-slate-100 dark:border-gray-800">

                    {{-- Modal Header --}}
                    <div class="bg-white dark:bg-gray-900 px-6 py-4 border-b border-slate-100 dark:border-gray-800 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $shiftId ? 'Edit Shift' : 'Create New Shift' }}</h3>
                        <button @click="show = false" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 dark:hover:bg-gray-800"><x-heroicon-o-x-mark class="h-5 w-5" /></button>
                    </div>

                    <form wire:submit.prevent="save">
                        <div class="px-6 py-6 space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Shift Type</label>
                                <select wire:model="shift_type" class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                                    <option value="Morning">Morning</option>
                                    <option value="Afternoon">Afternoon</option>
                                    <option value="Night">Night</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Shift Date</label>
                                <input type="date" wire:model="shift_date" class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Start Time</label>
                                    <input type="time" wire:model="start_time" class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">End Time</label>
                                    <input type="time" wire:model="end_time" class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                                </div>
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="bg-slate-50 dark:bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 rounded-b-2xl border-t border-slate-100 dark:border-gray-800">
                            <button type="submit" class="inline-flex justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-700 transition-all">
                                Save Shift
                            </button>
                            <button type="button" @click="show = false" class="inline-flex justify-center rounded-xl bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-gray-700">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
