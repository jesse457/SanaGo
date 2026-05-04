<main class="w-full min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-slate-100 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800 font-sans"
    x-data="{ activeTab: @entangle('activeTab') }">

    {{-- Sticky Header --}}
    <div class="sticky top-0 z-30 bg-white/90 dark:bg-gray-900/90 backdrop-blur-xl border-b border-slate-200/80 dark:border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-5 flex items-center justify-between">
                <div>
                    <nav class="hidden md:flex mb-1" aria-label="Breadcrumb">
                        <ol class="flex items-center gap-2 text-xs text-slate-500">
                            <li><a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600 transition-colors flex items-center gap-1"><x-heroicon-s-home class="w-3.5 h-3.5" />{{ __('admin.home') }}</a></li>
                            <li><x-heroicon-s-chevron-right class="w-3 h-3 text-slate-400" /></li>
                            <li class="font-medium text-slate-800 dark:text-white">{{ __('admin.settings') }}</li>
                        </ol>
                    </nav>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">{{ __('admin.settings_title') }}</h1>
                </div>
            </div>

            {{-- Tab Navigation --}}
            <nav class="flex gap-1 overflow-x-auto pb-px no-scrollbar" aria-label="Tabs">
                @foreach([
                    'general' => ['icon' => 'building-office', 'label' => __('admin.general_info_header')],
                    'departments' => ['icon' => 'building-office-2', 'label' => __('admin.department_management_header')],
                    'wards' => ['icon' => 'rectangle-group', 'label' => __('admin.ward_management_header')],
                    'bed-types' => ['icon' => 'tag', 'label' => __('admin.bed_type_management_header')],
                    'beds' => ['icon' => 'server', 'label' => __('admin.bed_management_header')],
                    'supplies' => ['icon' => 'archive-box', 'label' => __('admin.supply_management_header')],
                    'subscription' => ['icon' => 'credit-card', 'label' => __('admin.subscription_management_header')],
                ] as $key => $tab)
                <button wire:click="$set('activeTab', '{{ $key }}')" type="button"
                    class="relative px-4 py-3 text-sm font-medium rounded-t-xl transition-all whitespace-nowrap flex items-center gap-2"
                    :class="activeTab === '{{ $key }}' ? 'bg-white dark:bg-gray-800 text-blue-600 shadow-sm border-t border-x border-slate-200 dark:border-gray-700' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 hover:bg-white/50'">
                    @svg('heroicon-o-' . $tab['icon'], 'w-4 h-4')
                    <span class="hidden sm:inline">{{ $tab['label'] }}</span>
                </button>
                @endforeach
            </nav>
        </div>
    </div>

    {{-- Content Area --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- General Settings --}}
        <div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200/80 dark:border-gray-700 overflow-hidden">
                <form wire:submit.prevent="saveGeneralSettings">
                    <div class="p-8 grid grid-cols-1 lg:grid-cols-4 gap-8">
                        <div class="lg:col-span-1">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Profile & Branding</h3>
                            <p class="mt-1 text-sm text-slate-500">Your hospital's public information.</p>
                            <div class="mt-6 flex justify-center lg:justify-start">
                                <label class="relative group cursor-pointer">
                                    <div class="h-28 w-28 rounded-2xl bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/30 dark:to-indigo-900/30 border-2 border-dashed border-slate-300 dark:border-gray-600 overflow-hidden hover:border-blue-500 hover:scale-105 transition-all duration-300">
                                        @if ($hospitalLogo)
                                            <img src="{{ $hospitalLogo->temporaryUrl() }}" class="h-full w-full object-cover">
                                        @elseif($currentLogoUrl)
                                            <img src="{{ $currentLogoUrl }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex flex-col items-center justify-center text-slate-400">
                                                <x-heroicon-o-photo class="w-8 h-8 mb-1" />
                                                <span class="text-[10px] font-bold uppercase">Upload</span>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                            <x-heroicon-o-camera class="w-6 h-6 text-white" />
                                        </div>
                                    </div>
                                    <input type="file" accept="image/*" wire:model="hospitalLogo" class="hidden">
                                </label>
                            </div>
                            @error('hospitalLogo') <p class="text-red-500 text-xs mt-2 text-center lg:text-left">{{ $message }}</p> @enderror
                        </div>
                        <div class="lg:col-span-3 space-y-5">
                            <x-settings-input label="{{ __('admin.label_hospital_name') }}" wire:model="hospitalName" type="text" required />
                            <x-settings-input label="{{ __('admin.label_hospital_email') }}" wire:model="hospitalEmail" type="email" />
                            <x-settings-input label="{{ __('admin.label_hospital_address') }}" wire:model="hospitalAddress" type="text" />
                        </div>
                    </div>
                    <div class="bg-slate-50 dark:bg-gray-900/50 px-8 py-4 flex justify-end border-t border-slate-200 dark:border-gray-700">
                        <button type="submit" class="btn-primary">
                            <span wire:loading.remove wire:target="saveGeneralSettings">{{ __('admin.button_save_general_settings') }}</span>
                            <span wire:loading wire:target="saveGeneralSettings" class="flex items-center gap-2"><x-heroicon-o-arrow-path class="w-4 h-4 animate-spin" /> Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Departments --}}
        <div x-show="activeTab === 'departments'" x-cloak x-transition>
            @include('livewire.tenants.admin.settings.partials.entity-section', [
                'title' => 'Departments',
                'type' => 'department',
                'items' => $this->filteredDepartments,
                'color' => 'blue',
                'fields' => [['name' => 'name', 'label' => 'Name', 'type' => 'text'], ['name' => 'description', 'label' => 'Description', 'type' => 'textarea']],
            ])
        </div>

        {{-- Wards --}}
        <div x-show="activeTab === 'wards'" x-cloak x-transition>
            @include('livewire.tenants.admin.settings.partials.entity-section', [
                'title' => 'Wards',
                'type' => 'ward',
                'items' => $this->filteredWards,
                'color' => 'indigo',
                'fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
                    ['name' => 'ward_number', 'label' => 'Number', 'type' => 'text'],
                    ['name' => 'department_id', 'label' => 'Department', 'type' => 'select', 'options' => $this->allDepartments],
                ],
            ])
        </div>

        {{-- Bed Types --}}
        <div x-show="activeTab === 'bed-types'" x-cloak x-transition>
            @include('livewire.tenants.admin.settings.partials.entity-section', [
                'title' => 'Bed Types',
                'type' => 'bed-type',
                'items' => $this->filteredBedTypes,
                'color' => 'emerald',
                'fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
                    ['name' => 'price_per_day', 'label' => 'Price/Day', 'type' => 'number', 'prefix' => '$'],
                    ['name' => 'description', 'label' => 'Description', 'type' => 'textarea'],
                ],
            ])
        </div>

        {{-- Beds --}}
        <div x-show="activeTab === 'beds'" x-cloak x-transition>
            @include('livewire.tenants.admin.settings.partials.entity-section', [
                'title' => 'Beds',
                'type' => 'bed',
                'items' => $this->filteredBeds,
                'color' => 'violet',
                'fields' => [
                    ['name' => 'bed_number', 'label' => 'Bed Number', 'type' => 'text'],
                    ['name' => 'ward_id', 'label' => 'Ward', 'type' => 'select', 'options' => $this->allWards],
                    ['name' => 'bed_type_id', 'label' => 'Type', 'type' => 'select', 'options' => $this->allBedTypes],
                ],
            ])
        </div>

        {{-- Supplies --}}
        <div x-show="activeTab === 'supplies'" x-cloak x-transition>
            @include('livewire.tenants.admin.settings.partials.entity-section', [
                'title' => 'Supplies',
                'type' => 'supply',
                'items' => $this->filteredSupplies,
                'color' => 'teal',
                'fields' => [
                    ['name' => 'name', 'label' => 'Name', 'type' => 'text'],
                    ['name' => 'unit_of_measure', 'label' => 'Unit', 'type' => 'text'],
                    ['name' => 'current_stock', 'label' => 'Stock', 'type' => 'number'],
                    ['name' => 'min_stock_level', 'label' => 'Min Level', 'type' => 'number'],
                ],
            ])
        </div>

        {{-- Subscription --}}
        <div x-show="activeTab === 'subscription'" x-cloak x-transition>
            @include('livewire.tenants.admin.settings.partials.subscription-section')
        </div>
    </div>

    {{-- Unified Modal --}}
    @include('livewire.tenants.admin.settings.partials.unified-modal')
<style>
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    .btn-primary { @apply inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 hover:shadow-xl hover:shadow-blue-500/30 hover:-translate-y-0.5 transition-all duration-200; }
    .btn-secondary { @apply px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-medium transition-colors; }
    .btn-danger { @apply px-4 py-2.5 bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 text-white rounded-xl text-sm font-semibold shadow-lg shadow-red-500/25 transition-all; }
</style>
</main>


