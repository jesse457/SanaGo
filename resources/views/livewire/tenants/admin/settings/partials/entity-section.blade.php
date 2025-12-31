@props(['title', 'type', 'items', 'color', 'fields'])

@php
    $searchVar = 'search' . ucfirst(str_replace('-', '', $type));
    $colorClasses = [
        'blue' => ['bg' => 'bg-blue-50 dark:bg-blue-900/30', 'text' => 'text-blue-600 dark:text-blue-400', 'ring' => 'ring-blue-500/20'],
        'indigo' => ['bg' => 'bg-indigo-50 dark:bg-indigo-900/30', 'text' => 'text-indigo-600 dark:text-indigo-400', 'ring' => 'ring-indigo-500/20'],
        'emerald' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/30', 'text' => 'text-emerald-600 dark:text-emerald-400', 'ring' => 'ring-emerald-500/20'],
        'violet' => ['bg' => 'bg-violet-50 dark:bg-violet-900/30', 'text' => 'text-violet-600 dark:text-violet-400', 'ring' => 'ring-violet-500/20'],
        'teal' => ['bg' => 'bg-teal-50 dark:bg-teal-900/30', 'text' => 'text-teal-600 dark:text-teal-400', 'ring' => 'ring-teal-500/20'],
    ];
    $c = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Create Card --}}
    <div class="lg:col-span-1">
        <div class="lg:sticky lg:top-28 bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/80 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="p-2.5 rounded-xl {{ $c['bg'] }} {{ $c['text'] }}">
                    <x-heroicon-o-plus class="w-5 h-5" />
                </div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">New {{ ucfirst(str_replace('-', ' ', $type)) }}</h2>
            </div>
            <button wire:click="openModal('{{ $type }}', 'create')" 
                class="w-full py-3 px-4 rounded-xl border-2 border-dashed border-slate-300 dark:border-gray-600 text-slate-500 dark:text-slate-400 hover:border-{{ $color }}-500 hover:text-{{ $color }}-600 hover:bg-{{ $color }}-50 dark:hover:bg-{{ $color }}-900/20 transition-all duration-200 flex items-center justify-center gap-2 font-medium">
                <x-heroicon-o-plus-circle class="w-5 h-5" />
                Add {{ ucfirst(str_replace('-', ' ', $type)) }}
            </button>
        </div>
    </div>

    {{-- List --}}
    <div class="lg:col-span-2">
        {{-- Search Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h3 class="text-xl font-bold text-slate-900 dark:text-white">All {{ $title }}</h3>
                <p class="text-sm text-slate-500 mt-0.5">{{ $items->total() }} {{ Str::plural('item', $items->total()) }} total</p>
            </div>
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                <input type="text" wire:model.live.debounce.300ms="{{ $searchVar }}" 
                    class="w-full sm:w-72 pl-10 pr-4 py-2.5 rounded-xl border-0 bg-white dark:bg-gray-800 ring-1 ring-slate-200 dark:ring-gray-700 focus:ring-2 focus:ring-{{ $color }}-500 text-sm shadow-sm transition-shadow hover:shadow-md" 
                    placeholder="Search {{ strtolower($title) }}...">
            </div>
        </div>

        {{-- Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @forelse($items as $item)
                <div wire:key="{{ $type }}-{{ $item->id }}" 
                    class="group relative bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-slate-200/80 dark:border-gray-700 hover:shadow-xl hover:shadow-slate-200/50 dark:hover:shadow-none hover:border-{{ $color }}-300 dark:hover:border-{{ $color }}-700 transition-all duration-300 hover:-translate-y-1">
                    
                    <div class="flex justify-between items-start mb-3">
                        <div class="h-11 w-11 rounded-xl {{ $c['bg'] }} {{ $c['text'] }} flex items-center justify-center font-bold text-lg ring-4 {{ $c['ring'] }}">
                            {{ strtoupper(substr($item->name ?? $item->bed_number ?? '-', 0, 2)) }}
                        </div>
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click="openModal('{{ $type }}', 'edit', {{ $item->id }})" 
                                class="p-2 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors">
                                <x-heroicon-s-pencil-square class="w-4 h-4" />
                            </button>
                            <button wire:click="openModal('{{ $type }}', 'delete', {{ $item->id }})" 
                                class="p-2 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                <x-heroicon-s-trash class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <h4 class="font-bold text-slate-900 dark:text-white truncate text-base">
                        {{ $item->name ?? $item->bed_number }}
                    </h4>

                    @if($type === 'ward' && $item->department)
                        <div class="mt-2 flex items-center gap-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                #{{ $item->ward_number }}
                            </span>
                            <span class="text-xs text-slate-500">{{ $item->department->name }}</span>
                        </div>
                    @elseif($type === 'bed-type')
                        <p class="mt-1.5 text-sm text-slate-500 line-clamp-2">{{ $item->description ?? 'No description' }}</p>
                        <div class="mt-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                ${{ number_format($item->price_per_day, 2) }}/day
                            </span>
                        </div>
                    @elseif($type === 'supply')
                        <div class="mt-2 flex items-center justify-between">
                            <span class="text-sm text-slate-600 dark:text-slate-300">{{ $item->current_stock }} {{ $item->unit_of_measure }}</span>
                            @if($item->current_stock <= $item->min_stock_level)
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                    <x-heroicon-s-exclamation-triangle class="w-3 h-3" /> Low
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                    <x-heroicon-s-check-circle class="w-3 h-3" /> OK
                                </span>
                            @endif
                        </div>
                    @elseif($type === 'bed' && $item->ward)
                        <p class="mt-1 text-sm text-slate-500">{{ $item->ward->name }} · {{ $item->bedType->name ?? 'Standard' }}</p>
                    @else
                        <p class="mt-1.5 text-sm text-slate-500 line-clamp-2">{{ $item->description ?? 'No description available.' }}</p>
                    @endif
                </div>
            @empty
                <div class="col-span-full py-16 flex flex-col items-center justify-center text-center bg-white dark:bg-gray-800 rounded-2xl border-2 border-dashed border-slate-200 dark:border-gray-700">
                    <div class="p-4 rounded-2xl {{ $c['bg'] }} mb-4">
                        <x-heroicon-o-inbox class="w-8 h-8 {{ $c['text'] }}" />
                    </div>
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">No {{ strtolower($title) }} found</h3>
                    <p class="text-sm text-slate-500 mt-1">Get started by creating a new one.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-6">{{ $items->links() }}</div>
    </div>
</div>
