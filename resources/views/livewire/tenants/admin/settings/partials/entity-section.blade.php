<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border dark:border-gray-700 overflow-hidden">
    <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b dark:border-gray-700">
        <div>
            <h3 class="text-xl font-bold">{{ $title }}</h3>
            <p class="text-sm text-slate-500">Manage your {{ strtolower($title) }} here.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" />
                <input type="text" wire:model.live.debounce.300ms="search{{ ucfirst($type) }}"
                    placeholder="Search..."
                    class="pl-9 pr-4 py-2 rounded-xl border-slate-200 dark:bg-gray-900 dark:border-gray-700 text-sm focus:ring-blue-500">
            </div>
            <button wire:click="openModal('{{ $type }}', 'create')" class="btn-primary py-2 px-4 text-xs">
                <x-heroicon-o-plus class="w-4 h-4" /> Add
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 dark:bg-gray-900/50 text-slate-500 text-xs uppercase font-bold">
                <tr>
                    <th class="px-6 py-4">Name</th>
                    @if($type === 'ward') <th class="px-6 py-4">Number</th> @endif
                    @if($type === 'bed') <th class="px-6 py-4">Type</th> @endif
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-gray-700">
                @forelse($items as $item)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4 font-medium">{{ $item->name ?? $item->bed_number }}</td>
                        @if($type === 'ward') <td class="px-6 py-4">{{ $item->ward_number }}</td> @endif
                        @if($type === 'bed') <td class="px-6 py-4">{{ $item->bedType->name ?? 'N/A' }}</td> @endif
                        <td class="px-6 py-4 text-right">
                            <button wire:click="openModal('{{ $type }}', 'edit', {{ $item->id }})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg"><x-heroicon-o-pencil-square class="w-5 h-5" /></button>
                            <button wire:click="openModal('{{ $type }}', 'delete', {{ $item->id }})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg"><x-heroicon-o-trash class="w-5 h-5" /></button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-10 text-center text-slate-400">No records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t dark:border-gray-700">
        {{ $items->links() }}
    </div>
</div>
