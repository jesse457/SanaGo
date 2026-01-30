<main class="w-full min-h-screen bg-slate-50 dark:bg-zinc-950 p-4 sm:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header --}}
        <header class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tighter">Duty Roster</h1>
                <p class="text-sm text-zinc-500 font-medium">Manage staff coverage and daily shift rotations.</p>
            </div>
            <button wire:click="openModal" class="flex items-center gap-2 py-3 px-6 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-2xl font-bold shadow-xl">
                <x-heroicon-o-plus class="w-5 h-5" /> Add New Shift
            </button>
        </header>

        {{-- Table --}}
        <div class="bg-white dark:bg-zinc-900 rounded-[2rem] border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm">
            <table class="w-full text-left">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-[10px] font-black text-zinc-400 uppercase tracking-widest border-b border-zinc-100 dark:border-zinc-800">
                    <tr>
                        <th class="px-8 py-5">Date</th>
                        <th class="px-8 py-5">Shift Type</th>
                        <th class="px-8 py-5">Hours</th>
                        <th class="px-8 py-5">Staff</th>
                        <th class="px-8 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-50 dark:divide-zinc-800">
                    @forelse($shifts as $shift)
                        <tr class="group hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors">
                            <td class="px-8 py-5">
                                <p class="text-sm font-black text-zinc-900 dark:text-white">{{ $shift->shift_date->format('l, M d') }}</p>
                                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-widest">Year {{ $shift->shift_date->format('Y') }}</p>
                            </td>
                            <td class="px-8 py-5">
                                @php
                                    $colors = match($shift->shift_type) {
                                        'Morning' => 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400',
                                        'Afternoon' => 'bg-sky-50 text-sky-700 border-sky-100 dark:bg-sky-500/10 dark:text-sky-400',
                                        'Night' => 'bg-indigo-50 text-indigo-700 border-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-lg text-[10px] font-black border {{ $colors }}">{{ strtoupper($shift->shift_type) }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-2 text-xs font-bold text-zinc-600 dark:text-zinc-400 bg-zinc-100 dark:bg-zinc-800 px-3 py-1.5 rounded-xl w-fit">
                                    <x-heroicon-o-clock class="w-4 h-4 text-indigo-500" />
                                    {{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }}
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex -space-x-2">
                                    @foreach($shift->user->take(3) as $staff)
                                        <img src="{{ $staff->profile_picture ?? 'https://ui-avatars.com/api/?name='.urlencode($staff->name) }}" class="w-9 h-9 rounded-full border-2 border-white dark:border-zinc-900 shadow-sm" title="{{ $staff->name }}">
                                    @endforeach
                                    @if($shift->user_count > 3)
                                        <div class="w-9 h-9 rounded-full bg-zinc-900 text-white flex items-center justify-center text-[10px] font-bold border-2 border-white">+{{ $shift->user_count - 3 }}</div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button wire:click="edit({{ $shift->id }})" class="p-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:text-indigo-600"><x-heroicon-m-pencil-square class="w-4 h-4"/></button>
                                    <button wire:click="delete({{ $shift->id }})" wire:confirm="Delete this shift?" class="p-2 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl hover:text-rose-600"><x-heroicon-m-trash class="w-4 h-4"/></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-20 text-center text-zinc-400 italic font-bold">No shifts planned yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal logic (Condensed for brevity) --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/60 backdrop-blur-sm">
             <div class="bg-white dark:bg-zinc-900 w-full max-w-md rounded-[2.5rem] p-8 shadow-2xl border border-zinc-200 dark:border-zinc-800">
                <h2 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tighter mb-6">{{ $shiftId ? 'Edit Shift' : 'New Shift' }}</h2>
                <form wire:submit.prevent="save" class="space-y-4">
                    <select wire:model="shift_type" class="w-full bg-zinc-50 dark:bg-zinc-800 border-none rounded-xl p-3 text-sm font-bold">
                        <option value="Morning">Morning</option>
                        <option value="Afternoon">Afternoon</option>
                        <option value="Night">Night</option>
                    </select>
                    <input type="date" wire:model="shift_date" class="w-full bg-zinc-50 dark:bg-zinc-800 border-none rounded-xl p-3 text-sm font-bold">
                    <div class="grid grid-cols-2 gap-4">
                        <input type="time" wire:model="start_time" class="bg-zinc-50 dark:bg-zinc-800 border-none rounded-xl p-3 text-sm font-bold">
                        <input type="time" wire:model="end_time" class="bg-zinc-50 dark:bg-zinc-800 border-none rounded-xl p-3 text-sm font-bold">
                    </div>
                    <div class="flex gap-3 mt-6">
                        <button type="button" wire:click="closeModal" class="flex-1 py-3 text-sm font-bold text-zinc-500">Cancel</button>
                        <button type="submit" class="flex-1 py-3 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 rounded-xl font-bold shadow-lg">Save Shift</button>
                    </div>
                </form>
             </div>
        </div>
    @endif
</main>
