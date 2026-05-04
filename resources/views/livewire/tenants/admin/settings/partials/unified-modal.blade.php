<div x-data="{ show: @entangle('showModal') }" x-show="show" x-cloak class="relative z-50">
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4">
            <div x-show="show" @click.away="$wire.closeModal()" x-transition.scale.95 class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 shadow-2xl overflow-hidden">

                @if($modalAction === 'delete')
                    <div class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-red-100 dark:bg-red-900/30 rounded-xl text-red-600">
                                <x-heroicon-o-exclamation-triangle class="w-6 h-6" />
                            </div>
                            <h3 class="text-lg font-bold">Delete {{ ucfirst(str_replace('-', ' ', $modalType)) }}?</h3>
                        </div>
                        <p class="mt-4 text-slate-500">Are you sure? All related data may be affected.</p>
                        <div class="mt-6 flex justify-end gap-3">
                            <button wire:click="closeModal" class="btn-secondary">Cancel</button>
                            <button wire:click="confirmDelete" class="btn-danger">Delete Now</button>
                        </div>
                    </div>
                @else
                    <form wire:submit.prevent="saveForm">
                        <div class="p-6 border-b dark:border-gray-700">
                            <h3 class="text-lg font-bold">{{ $modalAction === 'create' ? 'Add New' : 'Edit' }} {{ ucfirst(str_replace('-', ' ', $modalType)) }}</h3>
                        </div>

                        <div class="p-6 space-y-4">
                            @if($modalType === 'department')
                                <x-settings-input label="Name" wire:model="form.name" required />
                                <x-settings-textarea label="Description" wire:model="form.description" />

                            @elseif($modalType === 'ward')
                                <x-settings-input label="Ward Name" wire:model="form.name" required />
                                <x-settings-input label="Ward Number" wire:model="form.ward_number" />
                                <x-settings-select label="Department" wire:model="form.department_id" :options="$this->allDepartments" required />

                            @elseif($modalType === 'bed-type')
                                <x-settings-input label="Name" wire:model="form.name" required />
                                <x-settings-input label="Price per Day" type="number" wire:model="form.price_per_day" required />
                                <x-settings-textarea label="Description" wire:model="form.description" />

                            @elseif($modalType === 'bed')
                                <x-settings-input label="Bed Number" wire:model="form.bed_number" required />
                                <x-settings-select label="Ward" wire:model="form.ward_id" :options="$this->allWards" required />
                                <x-settings-select label="Bed Type" wire:model="form.bed_type_id" :options="$this->allBedTypes" required />

                            @elseif($modalType === 'supply')
                                <x-settings-input label="Supply Name" wire:model="form.name" required />
                                <x-settings-input label="Unit" wire:model="form.unit_of_measure" />
                                <div class="grid grid-cols-2 gap-4">
                                    <x-settings-input label="Stock" type="number" wire:model="form.current_stock" />
                                    <x-settings-input label="Min Level" type="number" wire:model="form.min_stock_level" />
                                </div>
                            @endif
                        </div>

                        <div class="p-6 bg-slate-50 dark:bg-gray-900/50 flex justify-end gap-3">
                            <button type="button" wire:click="closeModal" class="btn-secondary">Cancel</button>
                            <button type="submit" class="btn-primary">Save Changes</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
