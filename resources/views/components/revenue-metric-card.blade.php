@props(['title', 'amount', 'total', 'icon', 'color'])

@php
    $colors = [
        'emerald' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-600 dark:text-emerald-400', 'bar' => 'bg-emerald-500'],
        'blue'    => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-600 dark:text-blue-400', 'bar' => 'bg-blue-500'],
        'amber'   => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-600 dark:text-amber-400', 'bar' => 'bg-amber-500'],
        'purple'  => ['bg' => 'bg-purple-100 dark:bg-purple-900/30', 'text' => 'text-purple-600 dark:text-purple-400', 'bar' => 'bg-purple-500'],
        'rose'    => ['bg' => 'bg-rose-100 dark:bg-rose-900/30', 'text' => 'text-rose-600 dark:text-rose-400', 'bar' => 'bg-rose-500'],
    ];
    $theme = $colors[$color] ?? $colors['blue'];
    $percentage = $total > 0 ? round(($amount / $total) * 100) : 0;
@endphp

<div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-6 flex flex-col justify-between hover:shadow-lg transition-all duration-300 group">
    <div class="flex items-start justify-between mb-4">
        <div>
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">{{ $title }}</p>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                {{ number_format($amount, 0) }}
            </h3>
        </div>
        <div class="p-2.5 rounded-lg {{ $theme['bg'] }} group-hover:scale-110 transition-transform duration-300">
            @if($icon === 'beaker') <x-heroicon-s-beaker class="w-5 h-5 {{ $theme['text'] }}" /> @endif
            @if($icon === 'calendar-days') <x-heroicon-s-calendar-days class="w-5 h-5 {{ $theme['text'] }}" /> @endif
            @if($icon === 'clipboard-document-check') <x-heroicon-s-clipboard-document-check class="w-5 h-5 {{ $theme['text'] }}" /> @endif
            @if($icon === 'building-office-2') <x-heroicon-s-building-office-2 class="w-5 h-5 {{ $theme['text'] }}" /> @endif
            @if($icon === 'archive-box') <x-heroicon-s-archive-box class="w-5 h-5 {{ $theme['text'] }}" /> @endif
        </div>
    </div>

    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
        <div class="h-1.5 rounded-full {{ $theme['bar'] }} transition-all duration-1000 ease-out" style="width: {{ $percentage }}%"></div>
    </div>
    <div class="mt-2 text-xs font-medium text-slate-400 text-right">
        {{ $percentage }}% of total
    </div>
</div>
