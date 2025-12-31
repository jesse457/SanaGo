@props(['label', 'type' => 'text', 'prefix' => null, 'required' => false])

<div>
    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1.5">
        {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
    </label>
    <div class="relative">
        @if($prefix)
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5">
                <span class="text-slate-500 text-sm font-medium">{{ $prefix }}</span>
            </div>
        @endif
        <input type="{{ $type }}" {{ $attributes->merge(['class' => 'block w-full rounded-xl border-0 py-2.5 text-slate-900 dark:text-white bg-white dark:bg-gray-900 shadow-sm ring-1 ring-inset ring-slate-200 dark:ring-gray-700 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm transition-shadow hover:shadow-md ' . ($prefix ? 'pl-8' : 'px-4')]) }}>
    </div>
    @error($attributes->whereStartsWith('wire:model')->first())
        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
            <x-heroicon-s-exclamation-circle class="w-3.5 h-3.5" />
            {{ $message }}
        </p>
    @enderror
</div>
