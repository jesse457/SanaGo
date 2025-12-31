{{-- Unified Modal --}}
<div x-data="{ show: @entangle('showModal') }" x-show="show" x-cloak class="relative z-50">
    {{-- Backdrop --}}
    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

    {{-- Modal Container --}}
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="show" @click.away="$wire.closeModal()"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="relative w-full max-w-lg transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 shadow-2xl transition-all">

                @if($modalAction === 'delete')
                    {{-- Delete Confirmation --}}
                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-xl text-red-600 dark:text-red-400">
                                <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Delete {{ ucfirst(str_replace('-', ' ', $modalType)) }}?</h3>
                                <p class="text-sm text-slate-500">This action cannot be undone.</p>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                            <button type="button" wire:click="confirmDelete" class="btn-danger">
                                <span wire:loading.remove wire:target="confirmDelete">Delete</span>
                                <span wire:loading wire:target="confirmDelete">Deleting...</span>
                            </button>
                        </div>
                    </div>
                @else
                    {{-- Create/Edit Form --}}
                    <form wire:submit.prevent="saveForm">
                        <div class="p-6 border-b border-slate-100 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                                {{ $modalAction === 'create' ? 'Add New' : 'Edit' }} {{ ucfirst(str_replace('-', ' ', $modalType)) }}
                            </h3>
                        </div>

                        <div class="p-6 space-y-5">
                            @if($modalType === 'department')
                                <x-settings-input label="Name" wire:model="form.name" type="text" required />
                                <x-settings-textarea label="Description" wire:model="form.description" rows="3" />
                            @elseif($modalType === 'ward')
                                <div class="grid grid-cols-2 gap-4">
                                    <x-settings-input label="Name" wire:model="form.name" type="text" required />
                                    <x-settings-input label="Number" wire:model="form.ward_number" type="text" required />
                                </div>
                                <x-settings-select label="Department" wire:model="form.department_id" :options="$this->allDepartments" required />
                            @elseif($modalType === 'bed-type')
                                <x-settings-input label="Name" wire:model="form.name" type="text" required />
                                <x-settings-input label="Price per Day" wire:model="form.price_per_day" type="number" step="0.01" prefix="$" required />
                                <x-settings-textarea label="Description" wire:model="form.description" rows="2" />
                            @elseif($modalType === 'bed')
                                <x-settings-input label="Bed Number" wire:model="form.bed_number" type="text" required />
                                <x-settings-select label="Ward" wire:model="form.ward_id" :options="$this->allWards" required />
                                <x-settings-select label="Bed Type" wire:model="form.bed_type_id" :options="$this->allBedTypes" required />
                            @elseif($modalType === 'supply')
                                <x-settings-input label="Name" wire:model="form.name" type="text" required />
                                <x-settings-input label="Unit of Measure" wire:model="form.unit_of_measure" type="text" placeholder="e.g. Box, Piece" />
                                <div class="grid grid-cols-2 gap-4">
                                    <x-settings-input label="Current Stock" wire:model="form.current_stock" type="number" required />
                                    <x-settings-input label="Min Stock Level" wire:model="form.min_stock_level" type="number" />
                                </div>
                            @endif
                        </div>

                        <div class="p-6 bg-slate-50 dark:bg-gray-900/50 border-t border-slate-100 dark:border-gray-700 flex justify-end gap-3">
                            <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                            <button type="submit" class="btn-primary">
                                <span wire:loading.remove wire:target="saveForm">
                                    {{ $modalAction === 'create' ? 'Create' : 'Save Changes' }}
                                </span>
                                <span wire:loading wire:target="saveForm" class="flex items-center gap-2">
                                    <x-heroicon-o-arrow-path class="w-4 h-4 animate-spin" /> Saving...
                                </span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
