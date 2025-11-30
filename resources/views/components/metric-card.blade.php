@props(['title', 'value', 'icon', 'color' => 'bg-indigo-600', 'secondaryValue' => null])

<div class="card p-5 shadow-xl transition-all duration-500 transform hover:scale-[1.02] {{ $color }} rounded-xl text-white">
<div class="flex justify-between items-start">
<!-- Title and Icon -->
<div class="text-xl font-semibold opacity-80">{{ $title }}</div>
<div class="p-2 rounded-full bg-white/20">
{{ $icon }}
</div>
</div>

<!-- Value -->
<div class="mt-4 text-4xl font-extrabold tracking-tight">
    {{ $value }}
</div>

<!-- Secondary Value/Trend (optional) -->
@if ($secondaryValue)
    <div class="mt-2 text-sm opacity-90">
        {{ $secondaryValue }}
    </div>
@endif
