@props(['label', 'options', 'required' => false])

<div>
    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-1.5">
        {{ $label }} @if($required)<span class="text-red-500">*</span>@endif
    </label>
    <div class="relative">
        <select {{ $attributes->merge(['class' => 'block w-full rounded-xl border-0 py-2.5 pl-4 pr-10 text-slate-900 dark:text-white bg-white dark:bg-gray-900 shadow-sm ring-1 ring-inset ring-slate-200 dark:ring-gray-700 focus:ring-2 focus:ring-inset focus:ring-blue-500 sm:text-sm transition-shadow hover:shadow-md appearance-none cursor-pointer']) }}>
            <option value="">Select...</option>
            @foreach($options as $option)
                <option value="{{ $option->id }}">{{ $option->name }}</option>
            @endforeach
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
            <x-heroicon-s-chevron-down class="w-4 h-4 text-slate-400" />
        </div>
    </div>
    @error($attributes->whereStartsWith('wire:model')->first())
        <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
            <x-heroicon-s-exclamation-circle class="w-3.5 h-3.5" />
            {{ $message }}
        </p>
    @enderror
</div>
