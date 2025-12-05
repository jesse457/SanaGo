<main class="w-full min-h-screen bg-gray-50 dark:bg-gray-900 font-sans text-gray-900 dark:text-gray-100"
    x-data="{
        activeTab: 'general',
        tabs: {
            'general': { label: '{{ addslashes(__('admin.general_info_header')) }}' },
            'departments': { label: '{{ addslashes(__('admin.department_management_header')) }}' },
            'wards': { label: '{{ addslashes(__('admin.ward_management_header')) }}' },
            'bed-types': { label: '{{ addslashes(__('admin.bed_type_management_header')) }}' },
            'beds': { label: '{{ addslashes(__('admin.bed_management_header')) }}' },
            'supplies': { label: '{{ addslashes(__('admin.supply_management_header')) }}' },
            'subscription': { label: '{{ addslashes(__('admin.subscription_management_header')) }}' }
        }
    }">

    <!-- Sticky Page Header & Tabs -->
    <div
        class=" bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-200 dark:border-gray-800 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Top Bar: Breadcrumbs & Title -->
            <div class="py-4 md:flex md:items-center md:justify-between">
                <div class="min-w-0 flex-1">
                    <nav class="hidden md:flex mb-8 mt-8" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                            <li class="inline-flex items-center">
                                <a href="{{ route('admin.dashboard') }}"
                                    class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors">
                                    <x-heroicon-s-home class="w-4 h-4 me-2" />
                                    {{ __('admin.home') }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                                    <span class="ms-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ __('admin.settings') }}
                                    </span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <div>
                        <h1
                            class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                            {{ __('admin.settings_title') }}
                        </h1>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('admin.manage_users_description') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Scrollable Tabs -->
            <div class="-mb-px">
                <nav class="flex space-x-6 overflow-x-auto no-scrollbar py-1" aria-label="Tabs">
                    <template x-for="[key, tab] in Object.entries(tabs)" :key="key">
                        <button @click="activeTab = key" type="button"
                            class="group relative  py-3 px-1 text-sm font-medium transition-all duration-200 outline-none focus:outline-none"
                            :class="activeTab === key ? 'text-blue-600 dark:text-blue-400' :
                                'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300'">

                            <!-- Icon + Label -->
                            <div class="flex items-center gap-2">
                                <template x-if="key === 'general'"><x-heroicon-o-building-office
                                        class="w-5 h-5 flex-shrink-0" /></template>
                                <template x-if="key === 'departments'"><x-heroicon-o-building-office-2
                                        class="w-5 h-5 flex-shrink-0" /></template>
                                <template x-if="key === 'wards'"><x-heroicon-o-rectangle-group
                                        class="w-5 h-5 flex-shrink-0" /></template>
                                <template x-if="key === 'bed-types'"><x-heroicon-o-tag
                                        class="w-5 h-5 flex-shrink-0" /></template>
                                <template x-if="key === 'beds'"><x-heroicon-o-server
                                        class="w-5 h-5 flex-shrink-0" /></template>
                                <template x-if="key === 'supplies'"><x-heroicon-o-archive-box
                                        class="w-5 h-5 flex-shrink-0" /></template>
                                <template x-if="key === 'subscription'"><x-heroicon-o-credit-card
                                        class="w-5 h-5 flex-shrink-0" /></template>
                                <span x-text="tab.label"></span>
                            </div>

                            <!-- Animated Underline -->
                            <span
                                class="absolute bottom-0 left-0 h-0.5 w-full bg-blue-600 dark:bg-blue-400 transform origin-left transition-transform duration-300 ease-out"
                                :class="activeTab === key ? 'scale-x-100' : 'scale-x-0'"></span>
                        </button>
                    </template>
                </nav>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- 1. GENERAL SETTINGS -->
        <div x-show="activeTab === 'general'" x-transition.opacity.duration.300ms>
            <div
                class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
                <form wire:submit.prevent="saveGeneralSettings">
                    <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Sidebar / Description -->
                        <div class="md:col-span-1">
                            <h3 class="text-base font-semibold leading-7 text-gray-900 dark:text-white">Profile &
                                Branding</h3>
                            <p class="mt-1 text-sm leading-6 text-gray-500 dark:text-gray-400">This information will be
                                displayed publicly on your hospital portal.</p>

                            <!-- Logo Preview (Stacked on mobile, side on desktop) -->
                            <div class="mt-6 flex flex-col items-center md:items-start">
                                <div class="relative group h-32 w-32 rounded-2xl bg-gray-50 dark:bg-gray-700 object-cover ring-1 ring-gray-900/10 dark:ring-white/10 overflow-hidden cursor-pointer"
                                    onclick="document.getElementById('hospital-logo').click()">
                                    <img src="{{ $currentLogoUrl }}"
                                        class="h-full w-full object-cover {{ $currentLogoUrl ? '' : 'hidden' }}">
                                    <div
                                        class="{{ $currentLogoUrl ? 'hidden' : 'flex' }} items-center justify-center h-full w-full text-gray-300">
                                        <x-heroicon-m-photo class="h-10 w-10" />
                                    </div>
                                    <div
                                        class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                        <span class="text-white text-xs font-medium">Change</span>
                                    </div>
                                </div>
                                <input type="file" id="hospital-logo" accept="image/*" wire:model="hospitalLogo"
                                    class="hidden">
                                @error('hospitalLogo')
                                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Main Form -->
                        <div class="md:col-span-2 space-y-6">
                            <div class="grid grid-cols-1 gap-x-6 gap-y-6 sm:grid-cols-2">
                                <div class="sm:col-span-2">
                                    <label
                                        class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">{{ __('admin.label_hospital_name') }}</label>
                                    <div class="mt-2">
                                        <input type="text" wire:model.defer="hospitalName"
                                            class="block w-full rounded-lg border-0 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-900 dark:ring-gray-600 dark:text-white dark:focus:ring-blue-500">
                                    </div>
                                    @error('hospitalName')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label
                                        class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">{{ __('admin.label_hospital_email') }}</label>
                                    <div class="mt-2">
                                        <input type="email" wire:model.defer="hospitalEmail"
                                            class="block w-full rounded-lg border-0 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-900 dark:ring-gray-600 dark:text-white dark:focus:ring-blue-500">
                                    </div>
                                    @error('hospitalEmail')
                                        <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="sm:col-span-2">
                                    <label
                                        class="block text-sm font-medium leading-6 text-gray-900 dark:text-gray-200">{{ __('admin.label_hospital_address') }}</label>
                                    <div class="mt-2">
                                        <input type="text" wire:model.defer="hospitalAddress"
                                            class="block w-full rounded-lg border-0 py-2.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm sm:leading-6 dark:bg-gray-900 dark:ring-gray-600 dark:text-white dark:focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div
                        class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 flex items-center justify-end border-t border-gray-200 dark:border-gray-700">
                        <button type="submit"
                            class="rounded-lg bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all">
                            <span wire:loading.remove
                                wire:target="saveGeneralSettings">{{ __('admin.button_save_general_settings') }}</span>
                            <span wire:loading wire:target="saveGeneralSettings"
                                class="flex items-center gap-2"><x-heroicon-o-arrow-path class="w-4 h-4 animate-spin" />
                                {{ __('Saving...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- 2. DEPARTMENTS (Split Layout) -->
        <div x-show="activeTab === 'departments'" x-cloak>
            <!--
                RESPONSIVE GRID:
                - Mobile: 1 Column.
                - Laptop (lg): 3 Columns (1 for Form, 2 for List).
            -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Left Column: Create Form -->
                <!-- order-first guarantees it's at top on mobile, on desktop it sits in col 1 -->
                <div class="lg:col-span-1 order-first">
                    <!-- sticky only on lg screens to prevent blocking mobile view -->
                    <div class="lg:sticky lg:top-24">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 p-6">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                                {{ __('admin.label_new_department') }}</h2>
                            <form wire:submit.prevent="addDepartment" class="space-y-4">
                                <div>
                                    <label
                                        class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Name</label>
                                    <input type="text" wire:model.defer="newDepartmentName"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                    @error('newDepartmentName')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Description</label>
                                    <textarea rows="4" wire:model.defer="newDepartmentDescription"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white"></textarea>
                                </div>
                                <button type="submit"
                                    class="w-full justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gray-900 hover:bg-black dark:bg-blue-600 dark:hover:bg-blue-500 transition-all flex items-center">
                                    <span wire:loading.remove
                                        wire:target="addDepartment">{{ __('Add Department') }}</span>
                                    <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin" wire:loading
                                        wire:target="addDepartment" />
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column: List -->
                <div class="lg:col-span-2">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ __('admin.label_existing_departments') }}</h3>
                        <div class="relative w-full sm:w-72">
                            <x-heroicon-o-magnifying-glass class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
                            <input type="text" wire:model.live="searchDepartment"
                                class="block w-full rounded-lg border-0 py-2 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm dark:bg-gray-800 dark:ring-gray-700 dark:text-white"
                                placeholder="{{ __('admin.placeholder_search_departments') }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @forelse($filteredDepartments as $department)
                            <div wire:key="dept-{{ $department->id }}"
                                class="group relative flex flex-col justify-between bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 hover:shadow-md hover:ring-blue-500/50 transition-all duration-300">
                                <div>
                                    <div class="flex justify-between items-start mb-2">
                                        <div
                                            class="h-10 w-10 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold text-lg">
                                            {{ substr($department->name, 0, 1) }}
                                        </div>
                                        <!-- Actions visible on hover (desktop) or always (touch) -->
                                        <div
                                            class="flex gap-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                            <button wire:click="editDepartment({{ $department->id }})"
                                                class="p-2 text-gray-400 hover:text-blue-600 bg-transparent hover:bg-blue-50 rounded-lg transition-colors"><x-heroicon-s-pencil-square
                                                    class="w-5 h-5" /></button>
                                            <button wire:click="confirmDeleteDepartment({{ $department->id }})"
                                                class="p-2 text-gray-400 hover:text-red-600 bg-transparent hover:bg-red-50 rounded-lg transition-colors"><x-heroicon-s-trash
                                                    class="w-5 h-5" /></button>
                                        </div>
                                    </div>
                                    <h4 class="text-base font-bold text-gray-900 dark:text-white truncate">
                                        {{ $department->name }}</h4>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                        {{ $department->description ?? 'No description.' }}</p>
                                </div>
                            </div>
                        @empty
                            <div
                                class="col-span-full py-12 flex flex-col items-center justify-center text-center border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-2xl">
                                <x-heroicon-o-folder class="w-12 h-12 text-gray-400 mb-3" />
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">No departments</h3>
                                <p class="text-sm text-gray-500">Create one using the form.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. WARDS -->
        <div x-show="activeTab === 'wards'" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Form Column -->
                <div class="lg:col-span-1 order-first">
                    <div class="lg:sticky lg:top-24">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 p-6">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                                {{ __('admin.label_new_ward') }}</h2>
                            <form wire:submit.prevent="addWard" class="space-y-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="col-span-2 sm:col-span-1">
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Name</label>
                                        <input type="text" wire:model.defer="newWardName"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                        @error('newWardName')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="col-span-2 sm:col-span-1">
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Number</label>
                                        <input type="text" wire:model.defer="newWardNumber"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                        @error('newWardNumber')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Department</label>
                                    <select wire:model.defer="newWardDepartmentId"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                        <option value="">Select...</option>
                                        @foreach ($filteredDepartments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('newWardDepartmentId')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <button type="submit"
                                    class="w-full py-2.5 rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors">
                                    {{ __('Add Ward') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- List Column -->
                <div class="lg:col-span-2">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
                        <div
                            class="p-4 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row gap-4 justify-between items-center bg-gray-50/50 dark:bg-gray-800">
                            <h3 class="font-bold text-gray-900 dark:text-white">All Wards</h3>
                            <div class="relative w-full sm:w-64">
                                <input type="text" wire:model.live.debounce.300ms="searchWard"
                                    class="block w-full rounded-lg border-0 py-2 pl-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-600 sm:text-sm dark:bg-gray-700 dark:ring-gray-600 dark:text-white"
                                    placeholder="Search...">
                            </div>
                        </div>
                        <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($filteredWards as $ward)
                                <li
                                    class="group flex items-center justify-between gap-x-6 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="min-w-0">
                                        <div class="flex items-start gap-x-3">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ $ward->name }}</p>
                                            <span
                                                class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10">#{{ $ward->ward_number }}</span>
                                        </div>
                                        <p class="mt-1 text-xs text-gray-500">
                                            {{ $ward->department->name ?? 'Unassigned' }}</p>
                                    </div>
                                    <div
                                        class="flex items-center gap-3 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                                        <button wire:click="editWard({{ $ward->id }})"
                                            class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">Edit</button>
                                        <button wire:click="confirmDeleteWard({{ $ward->id }})"
                                            class="text-gray-400 hover:text-red-600"><x-heroicon-s-trash
                                                class="w-5 h-5" /></button>
                                    </div>
                                </li>
                            @empty
                                <li class="py-8 text-center text-gray-500 text-sm">No wards found.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. SUPPLIES -->
        <div x-show="activeTab === 'supplies'" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1 order-first">
                    <div class="lg:sticky lg:top-24">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 p-6">
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ __('Add Item') }}</h2>
                            <form wire:submit.prevent="addSupply" class="space-y-4">
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Item Name</label>
                                    <input type="text" wire:model.defer="newSupplyName"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-orange-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-500 uppercase">Unit</label>
                                    <input type="text" wire:model.defer="newSupplyUnitOfMeasure"
                                        placeholder="e.g. Box"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-orange-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Stock</label>
                                        <input type="number" wire:model.defer="newSupplyCurrentStock"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-orange-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-500 uppercase">Min</label>
                                        <input type="number" wire:model.defer="newSupplyMinStockLevel"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-orange-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                                    </div>
                                </div>
                                <button type="submit"
                                    class="w-full py-2.5 rounded-lg text-sm font-medium text-white bg-orange-600 hover:bg-orange-700 transition-colors">
                                    {{ __('Add Supply') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
                        <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                            <input type="text" wire:model.live.debounce.300ms="searchSupply"
                                class="block w-full rounded-lg border-0 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-orange-600 sm:text-sm dark:bg-gray-700 dark:ring-gray-600 dark:text-white"
                                placeholder="Search inventory...">
                        </div>
                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th scope="col"
                                            class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold uppercase text-gray-500">
                                            Item</th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-xs font-semibold uppercase text-gray-500">
                                            Stock</th>
                                        <th scope="col"
                                            class="px-3 py-3.5 text-left text-xs font-semibold uppercase text-gray-500">
                                            Status</th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4"><span
                                                class="sr-only">Actions</span></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($filteredSupplies as $supply)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td
                                                class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $supply->name }}</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                                {{ $supply->current_stock }} {{ $supply->unit_of_measure }}</td>
                                            <td class="whitespace-nowrap px-3 py-4 text-sm">
                                                @if ($supply->current_stock <= $supply->min_stock_level)
                                                    <span
                                                        class="inline-flex items-center rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-700 ring-1 ring-inset ring-red-600/10">Low</span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">OK</span>
                                                @endif
                                            </td>
                                            <td
                                                class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium">
                                                <button wire:click="editSupply({{ $supply->id }})"
                                                    class="text-blue-600 hover:underline mr-3">Edit</button>
                                                <button wire:click="confirmDeleteSupply({{ $supply->id }})"
                                                    class="text-red-600 hover:underline">Delete</button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-6 text-gray-500">No supplies
                                                found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. BED TYPES & BEDS -->
        <!-- Similar responsive grid structure... -->
        <div x-show="activeTab === 'bed-types'" x-cloak>
            <!-- Grid: Mobile 1 Col, Desktop 3 Cols -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Form -->
                <div class="lg:col-span-1 order-first">
                    <div class="lg:sticky lg:top-24">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">New Bed Type</h3>
                            <form wire:submit.prevent="addBedType" class="space-y-4">
                                <input type="text" wire:model.defer="newBedTypeName" placeholder="Name (e.g. ICU)"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-green-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600">
                                <div class="relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm">$</span>
                                    </div>
                                    <input type="number" wire:model.defer="newBedTypePrice"
                                        class="block w-full rounded-lg border-gray-300 pl-7 focus:ring-green-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600"
                                        placeholder="0.00">
                                </div>
                                <textarea wire:model.defer="newBedTypeDescription" rows="2" placeholder="Description"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-green-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600"></textarea>
                                <button type="submit"
                                    class="w-full py-2.5 rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">Add
                                    Type</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Cards -->
                <div class="lg:col-span-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @forelse($filteredBedTypes as $type)
                            <div
                                class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-bold text-gray-900 dark:text-white">{{ $type->name }}</h3>
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded-full">${{ number_format($type->price_per_day, 2) }}</span>
                                </div>
                                <p class="mt-2 text-sm text-gray-500 line-clamp-2">
                                    {{ $type->description ?? 'No details' }}</p>
                                <div class="mt-4 flex justify-end gap-3 text-sm font-medium">
                                    <button wire:click="editBedType({{ $type->id }})"
                                        class="text-blue-600 hover:text-blue-800">Edit</button>
                                    <button wire:click="confirmDeleteBedType({{ $type->id }})"
                                        class="text-red-600 hover:text-red-800">Delete</button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-8 text-gray-500">No bed types found.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div x-show="activeTab === 'beds'" x-cloak>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1 order-first">
                    <div class="lg:sticky lg:top-24">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Add Bed</h3>
                            <form wire:submit.prevent="addBed" class="space-y-4">
                                <input type="text" wire:model.defer="newBedNumber" placeholder="Bed Number"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-purple-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600">
                                <select wire:model.defer="newBedWardId"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-purple-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600">
                                    <option value="">Select Ward</option>
                                    @foreach ($filteredWards as $ward)
                                        <option value="{{ $ward->id }}">{{ $ward->name }}</option>
                                    @endforeach
                                </select>
                                <select wire:model.defer="newBedTypeId"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-purple-500 sm:text-sm dark:bg-gray-900 dark:border-gray-600">
                                    <option value="">Select Type</option>
                                    @foreach ($filteredBedTypes as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit"
                                    class="w-full py-2.5 rounded-lg text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 transition-colors">Save
                                    Bed</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-2">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @forelse($filteredBeds as $bed)
                            <div
                                class="group relative bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-700 text-center hover:shadow-md transition-all">
                                <div
                                    class="mx-auto h-10 w-10 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-xs mb-2">
                                    {{ substr($bed->bed_number, 0, 3) }}
                                </div>
                                <h5 class="font-bold text-gray-900 dark:text-white truncate">{{ $bed->bed_number }}
                                </h5>
                                <p class="text-xs text-gray-500 truncate">{{ $bed->ward->name ?? '-' }}</p>
                                <div
                                    class="absolute inset-0 bg-white/90 dark:bg-gray-800/90 rounded-2xl opacity-0 group-hover:opacity-100 flex items-center justify-center gap-2 transition-opacity">
                                    <button wire:click="editBed({{ $bed->id }})"
                                        class="p-2 bg-gray-100 rounded-full hover:text-blue-600"><x-heroicon-s-pencil-square
                                            class="w-4 h-4" /></button>
                                    <button wire:click="confirmDeleteBed({{ $bed->id }})"
                                        class="p-2 bg-gray-100 rounded-full hover:text-red-600"><x-heroicon-s-trash
                                            class="w-4 h-4" /></button>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-8 text-gray-500">No beds found.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. SUBSCRIPTION -->
        <div x-show="activeTab === 'subscription'" x-cloak>
            @if (!$subscription)
                <div
                    class="flex flex-col items-center justify-center py-16 px-4 text-center bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-200 dark:ring-gray-700">
                    <div class="bg-blue-50 p-4 rounded-full mb-4">
                        <x-heroicon-o-rocket-launch class="w-8 h-8 text-blue-600" />
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Upgrade Plan</h2>
                    <p class="text-gray-500 mt-2 max-w-md">Access premium features by upgrading your subscription.</p>
                    <button wire:click="openUpgradeModal"
                        class="mt-6 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium shadow-lg transition-all">
                        View Plans
                    </button>
                </div>
            @else
                <!-- Subscription Card -->
                <div
                    class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-3xl p-8 text-white shadow-xl mb-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-16 -mt-16"></div>
                    <div class="relative z-10 flex flex-col md:flex-row justify-between md:items-center gap-6">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="text-gray-400 text-sm font-medium uppercase tracking-wider">Current
                                    Plan</span>
                                <span
                                    class="bg-green-500/20 text-green-400 text-xs font-bold px-2 py-1 rounded">{{ $subscription->getStatusDisplayName() }}</span>
                            </div>
                            <h2 class="text-4xl font-bold">{{ $subscription->getPlanDisplayName() }}</h2>
                            <p class="text-gray-400 mt-2">${{ number_format($subscription->amount, 2) }} /
                                {{ $subscription->billing_cycle }}</p>
                        </div>
                        <div class="flex gap-3">
                            <button wire:click="openUpgradeModal"
                                class="px-5 py-3 bg-white text-gray-900 rounded-xl font-bold hover:bg-gray-100 transition-colors">Change
                                Plan</button>
                            @if ($subscription->isActive())
                                <button wire:click="confirmCancelSubscription"
                                    class="px-5 py-3 bg-white/10 text-white rounded-xl font-bold hover:bg-white/20 transition-colors">Cancel</button>
                            @endif
                        </div>
                    </div>
                </div>



                <!-- Usage Statistics -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">
                        {{ __('admin.subscription_usage_statistics') }}
                    </h3>

                    @php
                        $features = $subscription->getPlanFeatures() ?? [];
                        $maxUsers = $features['max_users'] ?? 0;
                        $maxStorage = $features['max_storage'] ?? 0;
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Users Usage -->
                        <div
                            class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-gray-800 dark:text-white">
                                    {{ __('admin.subscription_users') }}</h4>
                                <x-heroicon-o-users class="w-6 h-6 text-blue-500" />
                            </div>
                            <div class="mb-2">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ $currentUsersCount }} / {{ $maxUsers > 0 ? $maxUsers : '∞' }}
                                    </span>
                                    <span class="text-gray-600 dark:text-gray-400">
                                        @if ($maxUsers > 0)
                                            {{ round(($currentUsersCount / $maxUsers) * 100) }}%
                                        @else
                                            0%
                                        @endif
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full"
                                        style="width: {{ $maxUsers > 0 ? min(($currentUsersCount / $maxUsers) * 100, 100) : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Storage Usage -->
                        <div
                            class="bg-gray-50 dark:bg-gray-700/30 rounded-xl p-6 border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="font-semibold text-gray-800 dark:text-white">
                                    {{ __('admin.subscription_storage') }}</h4>
                                <x-heroicon-o-server class="w-6 h-6 text-green-500" />
                            </div>
                            <div class="mb-2">
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ round($currentStorageUsage / 1024, 2) }} GB /
                                        {{ $maxStorage > 0 ? round($maxStorage / 1024, 2) : '∞' }} GB
                                    </span>
                                    <span class="text-gray-600 dark:text-gray-400">
                                        @if ($maxStorage > 0)
                                            {{ round(($currentStorageUsage / $maxStorage) * 100) }}%
                                        @else
                                            0%
                                        @endif
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-green-600 h-2.5 rounded-full"
                                        style="width: {{ $maxStorage > 0 ? min(($currentStorageUsage / $maxStorage) * 100, 100) : 0 }}%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Plan Features -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">
                        {{ __('admin.subscription_plan_features') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse ($features as $feature => $value)
                            <div
                                class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600">
                                @if ($value)
                                    <x-heroicon-o-check-circle class="w-5 h-5 text-green-500 flex-shrink-0" />
                                @else
                                    <x-heroicon-o-x-circle class="w-5 h-5 text-gray-400 flex-shrink-0" />
                                @endif
                                <span class="text-gray-700 dark:text-gray-300">
                                    {{ __('admin.subscription_feature_' . $feature) }}
                                </span>
                            </div>
                        @empty
                            <div class="col-span-3 text-center text-gray-500 py-4">
                                {{ __('admin.subscription_no_features') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Billing History -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">
                        {{ __('admin.subscription_billing_history') }}
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead
                                class="text-xs text-gray-500 uppercase bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                                <tr>
                                    <th class="px-4 py-3">{{ __('admin.billing_date') }}</th>
                                    <th class="px-4 py-3">{{ __('admin.billing_description') }}</th>
                                    <th class="px-4 py-3">{{ __('admin.billing_amount') }}</th>
                                    <th class="px-4 py-3">{{ __('admin.billing_status') }}</th>
                                    <th class="px-4 py-3">{{ __('admin.billing_invoice') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($billingHistory ?? [] as $invoice)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                        <td class="px-4 py-3">
                                            {{ $invoice->date?->format('M j, Y') ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3">{{ $invoice->description ?? '-' }}</td>
                                        <td class="px-4 py-3">${{ number_format($invoice->amount ?? 0, 2) }}</td>
                                        <td class="px-4 py-3">
                                            @if (($invoice->status ?? '') === 'paid')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    {{ __('admin.billing_status_paid') }}
                                                </span>
                                            @elseif(($invoice->status ?? '') === 'pending')
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    {{ __('admin.billing_status_pending') }}
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    {{ __('admin.billing_status_failed') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if (!empty($invoice->invoice_url))
                                                <a href="{{ $invoice->invoice_url }}" target="_blank"
                                                    class="text-blue-600 hover:underline">
                                                    {{ __('admin.billing_view_invoice') }}
                                                </a>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                                            {{ __('admin.billing_no_history') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

    </div>

    <div>
        {{-- (All Modals remain exactly the same as previous code block, just ensured they are inside the main tag scope) --}}
        <!-- 1. Department Edit Modal -->
        <div x-data="{ show: @entangle('showDepartmentEditModal') }" x-show="show" x-cloak class="relative z-50">
            {{-- ... (Rest of Department Edit Modal) ... --}}
            <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="show" @click.away="show = false" x-transition
                        class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4">
                            {{ __('admin.modal_edit_department_title') }}</h3>
                        <form wire:submit.prevent="updateDepartment">
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_department_name') }}</label>
                                    <input type="text" wire:model.defer="editDepartmentName"
                                        class="form-input w-full rounded-md border-gray-300 mt-1">
                                    @error('editDepartmentName')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_department_description') }}</label>
                                    <textarea wire:model.defer="editDepartmentDescription" rows="3"
                                        class="form-input w-full rounded-md border-gray-300 mt-1"></textarea>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" @click="show = false"
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_save_changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{--
         NOTE: For brevity, include all other modals (Delete Department, Ward Edit/Delete, Bed Type Edit/Delete, Bed Edit/Delete, Supply Edit/Delete, Upgrade, Cancel)
         here exactly as they were in your previous code. They are valid, provided the x-data scope doesn't crash.
    --}}

        <!-- 2. Department Delete Modal -->
        <div x-data="{ show: @entangle('showDepartmentDeleteModal') }" x-show="show" x-cloak class="relative z-50">
            <div x-show="show" x-transition.opacity class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="show" @click.away="show = false"
                        class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-red-100 rounded-full text-red-600"><x-heroicon-o-exclamation-triangle
                                    class="w-6 h-6" /></div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ __('admin.modal_delete_department_title') }}</h3>
                        </div>
                        <p class="text-sm text-gray-500 mb-6">{{ __('admin.modal_delete_department_message') }}</p>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="show = false"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                            <button type="button" wire:click="deleteDepartment"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_delete') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Ward Edit Modal -->
        <div x-data="{ show: @entangle('showWardEditModal') }" x-show="show" x-cloak class="relative z-50">
            <div x-show="show" class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="show" @click.away="show = false"
                        class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4">
                            {{ __('admin.modal_edit_ward_title') }}</h3>
                        <form wire:submit.prevent="updateWard">
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_ward_name') }}</label>
                                    <input type="text" wire:model.defer="editWardName"
                                        class="form-input w-full rounded-md border-gray-300 mt-1">
                                    @error('editWardName')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_ward_number') }}</label>
                                    <input type="text" wire:model.defer="editWardNumber"
                                        class="form-input w-full rounded-md border-gray-300 mt-1">
                                    @error('editWardNumber')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_ward_department') }}</label>
                                    <select wire:model.defer="editWardDepartmentId"
                                        class="form-select w-full rounded-md border-gray-300 mt-1">
                                        <option value="">{{ __('admin.option_select_department') }}</option>
                                        @foreach ($filteredDepartments as $department)
                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('editWardDepartmentId')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_ward_description') }}</label>
                                    <textarea wire:model.defer="editWardDescription" rows="3"
                                        class="form-input w-full rounded-md border-gray-300 mt-1"></textarea>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" @click="show = false"
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_save_changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Ward Delete Modal -->
        <div x-data="{ show: @entangle('showWardDeleteModal') }" x-show="show" x-cloak class="relative z-50">
            <div x-show="show" class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="show" @click.away="show = false"
                        class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-red-100 rounded-full text-red-600"><x-heroicon-o-exclamation-triangle
                                    class="w-6 h-6" /></div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.modal_delete_ward_title') }}
                            </h3>
                        </div>
                        <p class="text-sm text-gray-500 mb-6">{{ __('admin.modal_delete_ward_message') }}</p>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="show = false"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                            <button type="button" wire:click="deleteWard"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_delete') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Bed Type Edit Modal -->
        <div x-data="{ show: @entangle('showBedTypeEditModal') }" x-show="show" x-cloak class="relative z-50">
            <div x-show="show" class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="show" @click.away="show = false"
                        class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6 ">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4">
                            {{ __('admin.modal_edit_bed_type_title') }}</h3>
                        <form wire:submit.prevent="updateBedType">
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_bed_type_name') }}</label>
                                    <input type="text" wire:model.defer="editBedTypeName"
                                        class="form-input w-full rounded-md border-gray-300 mt-1">
                                    @error('editBedTypeName')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_bed_type_price') }}</label>
                                    <input type="number" step="0.01" wire:model.defer="editBedTypePrice"
                                        class="form-input w-full rounded-md border-gray-300 mt-1">
                                    @error('editBedTypePrice')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_bed_type_description') }}</label>
                                    <textarea wire:model.defer="editBedTypeDescription" rows="3"
                                        class="form-input w-full rounded-md border-gray-300 mt-1"></textarea>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" @click="show = false"
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_save_changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6. Bed Type Delete Modal -->
        <div x-data="{ show: @entangle('showBedTypeDeleteModal') }" x-show="show" x-cloak class="relative z-50">
            <div x-show="show" class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="show" @click.away="show = false"
                        class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-red-100 rounded-full text-red-600"><x-heroicon-o-exclamation-triangle
                                    class="w-6 h-6" /></div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ __('admin.modal_delete_bed_type_title') }}
                            </h3>
                        </div>
                        <p class="text-sm text-gray-500 mb-6">{{ __('admin.modal_delete_bed_type_message') }}</p>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="show = false"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                            <button type="button" wire:click="deleteBedType"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_delete') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7. Bed Edit Modal -->
        <div x-data="{ show: @entangle('showBedEditModal') }" x-show="show" x-cloak class="relative z-50">
            <div x-show="show" class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="show" @click.away="show = false"
                        class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4">
                            {{ __('admin.modal_edit_bed_title') }}</h3>
                        <form wire:submit.prevent="updateBed">
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_bed_number') }}</label>
                                    <input type="text" wire:model.defer="editBedNumber"
                                        class="form-input w-full rounded-md border-gray-300 mt-1">
                                    @error('editBedNumber')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_bed_ward') }}</label>
                                    <select wire:model.defer="editBedWardId"
                                        class="form-select w-full rounded-md border-gray-300 mt-1">
                                        <option value="">{{ __('admin.option_select_ward') }}</option>
                                        @foreach ($filteredWards as $ward)
                                            <option value="{{ $ward->id }}">{{ $ward->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('editBedWardId')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_bed_type') }}</label>
                                    <select wire:model.defer="editBedTypeId"
                                        class="form-select w-full rounded-md border-gray-300 mt-1">
                                        <option value="">{{ __('admin.option_select_bed_type') }}</option>
                                        @foreach ($filteredBedTypes as $bedType)
                                            <option value="{{ $bedType->id }}">{{ $bedType->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('editBedTypeId')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" @click="show = false"
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_save_changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- 8. Bed Delete Modal -->
        <div x-data="{ show: @entangle('showBedDeleteModal') }" x-show="show" x-cloak class="relative z-50">
            <div x-show="show" class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="show" @click.away="show = false"
                        class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-red-100 rounded-full text-red-600"><x-heroicon-o-exclamation-triangle
                                    class="w-6 h-6" /></div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.modal_delete_bed_title') }}
                            </h3>
                        </div>
                        <p class="text-sm text-gray-500 mb-6">{{ __('admin.modal_delete_bed_message') }}</p>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="show = false"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                            <button type="button" wire:click="deleteBed"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_delete') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 9. Supply Edit Modal -->
        <div x-data="{ show: @entangle('showSupplyEditModal') }" x-show="show" x-cloak class="relative z-50">
            <div x-show="show" class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="show" @click.away="show = false"
                        class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6">
                        <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4">
                            {{ __('admin.modal_edit_supply_title') }}</h3>
                        <form wire:submit.prevent="updateSupply">
                            <div class="space-y-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_supply_name') }}</label>
                                    <input type="text" wire:model.defer="editSupplyName"
                                        class="form-input w-full rounded-md border-gray-300 mt-1">
                                    @error('editSupplyName')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_supply_unit') }}</label>
                                    <input type="text" wire:model.defer="editSupplyUnitOfMeasure"
                                        class="form-input w-full rounded-md border-gray-300 mt-1">
                                    @error('editSupplyUnitOfMeasure')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_supply_stock') }}</label>
                                        <input type="number" wire:model.defer="editSupplyCurrentStock"
                                            class="form-input w-full rounded-md border-gray-300 mt-1">
                                        @error('editSupplyCurrentStock')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_supply_min_stock') }}</label>
                                        <input type="number" wire:model.defer="editSupplyMinStockLevel"
                                            class="form-input w-full rounded-md border-gray-300 mt-1">
                                        @error('editSupplyMinStockLevel')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end gap-3">
                                <button type="button" @click="show = false"
                                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                                <button type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_save_changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- 10. Supply Delete Modal -->
        <div x-data="{ show: @entangle('showSupplyDeleteModal') }" x-show="show" x-cloak class="relative z-50">
            <div x-show="show" class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="show" @click.away="show = false"
                        class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-red-100 rounded-full text-red-600"><x-heroicon-o-exclamation-triangle
                                    class="w-6 h-6" /></div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ __('admin.modal_delete_supply_title') }}
                            </h3>
                        </div>
                        <p class="text-sm text-gray-500 mb-6">{{ __('admin.modal_delete_supply_message') }}</p>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="show = false"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                            <button type="button" wire:click="deleteSupply"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_delete') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Upgrade Modal -->
        <div x-data="{ show: @entangle('showUpgradeModal') }" x-show="show" x-cloak class="relative z-50">
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="show" @click.away="show = false" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl p-6">
                        <h3 class="text-xl font-semibold leading-6 text-gray-900 mb-6">
                            {{ __('admin.subscription_upgrade_plan') }}</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            <!-- Basic Plan -->
                            <div
                                class="border rounded-lg p-4 {{ $subscription?->plan === 'basic' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                <h4 class="font-bold text-lg mb-2">Basic</h4>
                                <p class="text-2xl font-bold mb-4">$150 <span
                                        class="text-sm font-normal">/month</span>
                                </p>
                                <ul class="space-y-2 text-sm">
                                    <li class="flex items-start">
                                        <x-heroicon-o-check class="w-4 h-4 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>10 Users</span>
                                    </li>
                                    <li class="flex items-start">
                                        <x-heroicon-o-check class="w-4 h-4 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>1GB Storage</span>
                                    </li>
                                    <li class="flex items-start">
                                        <x-heroicon-o-x-circle
                                            class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>API Access</span>
                                    </li>
                                    <li class="flex items-start">
                                        <x-heroicon-o-x-circle
                                            class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>Priority Support</span>
                                    </li>
                                </ul>
                                @if ($subscription?->plan !== 'basic')
                                    <button wire:click="selectPlan('basic')"
                                        class="w-full mt-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium transition-all">
                                        {{ __('admin.subscription_select_plan') }}
                                    </button>
                                @else
                                    <div
                                        class="w-full mt-4 py-2 bg-blue-600 text-white rounded-lg font-medium text-center">
                                        {{ __('admin.subscription_current_plan') }}
                                    </div>
                                @endif
                            </div>

                            <!-- Standard Plan -->
                            <div
                                class="border rounded-lg p-4 {{ $subscription?->plan === 'standard' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                <h4 class="font-bold text-lg mb-2">Standard</h4>
                                <p class="text-2xl font-bold mb-4">$300 <span
                                        class="text-sm font-normal">/month</span>
                                </p>
                                <ul class="space-y-2 text-sm">
                                    <li class="flex items-start">
                                        <x-heroicon-o-check class="w-4 h-4 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>50 Users</span>
                                    </li>
                                    <li class="flex items-start">
                                        <x-heroicon-o-check class="w-4 h-4 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>5GB Storage</span>
                                    </li>
                                    <li class="flex items-start">
                                        <x-heroicon-o-check class="w-4 h-4 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>API Access</span>
                                    </li>
                                    <li class="flex items-start">
                                        <x-heroicon-o-x-circle
                                            class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>Priority Support</span>
                                    </li>
                                </ul>
                                @if ($subscription?->plan !== 'standard')
                                    <button wire:click="selectPlan('standard')"
                                        class="w-full mt-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium transition-all">
                                        {{ __('admin.subscription_select_plan') }}
                                    </button>
                                @else
                                    <div
                                        class="w-full mt-4 py-2 bg-blue-600 text-white rounded-lg font-medium text-center">
                                        {{ __('admin.subscription_current_plan') }}
                                    </div>
                                @endif
                            </div>

                            <!-- Premium Plan -->
                            <div
                                class="border rounded-lg p-4 {{ $subscription?->plan === 'premium' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                <h4 class="font-bold text-lg mb-2">Premium</h4>
                                <p class="text-2xl font-bold mb-4">$600 <span
                                        class="text-sm font-normal">/month</span>
                                </p>
                                <ul class="space-y-2 text-sm">
                                    <li class="flex items-start">
                                        <x-heroicon-o-check class="w-4 h-4 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>200 Users</span>
                                    </li>
                                    <li class="flex items-start">
                                        <x-heroicon-o-check class="w-4 h-4 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>20GB Storage</span>
                                    </li>
                                    <li class="flex items-start">
                                        <x-heroicon-o-check class="w-4 h-4 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>API Access</span>
                                    </li>
                                    <li class="flex items-start">
                                        <x-heroicon-o-check class="w-4 h-4 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>Priority Support</span>
                                    </li>
                                </ul>
                                @if ($subscription?->plan !== 'premium')
                                    <button wire:click="selectPlan('premium')"
                                        class="w-full mt-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium transition-all">
                                        {{ __('admin.subscription_select_plan') }}
                                    </button>
                                @else
                                    <div
                                        class="w-full mt-4 py-2 bg-blue-600 text-white rounded-lg font-medium text-center">
                                        {{ __('admin.subscription_current_plan') }}
                                    </div>
                                @endif
                            </div>

                            <!-- Enterprise Plan -->
                            <div
                                class="border rounded-lg p-4 {{ $subscription?->plan === 'enterprise' ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
                                <h4 class="font-bold text-lg mb-2">Enterprise</h4>
                                <p class="text-2xl font-bold mb-4">$1000 <span
                                        class="text-sm font-normal">/month</span>
                                </p>
                                <ul class="space-y-2 text-sm">
                                    <li class="flex items-start">
                                        <x-heroicon-o-check class="w-4 h-4 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>Unlimited Users</span>
                                    </li>
                                    <li class="flex items-start">
                                        <x-heroicon-o-check class="w-4 h-4 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>Unlimited Storage</span>
                                    </li>
                                    <li class="flex items-start">
                                        <x-heroicon-o-check class="w-4 h-4 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>API Access</span>
                                    </li>
                                    <li class="flex items-start">
                                        <x-heroicon-o-check class="w-4 h-4 text-green-500 mr-2 flex-shrink-0 mt-0.5" />
                                        <span>Priority Support</span>
                                    </li>
                                </ul>
                                @if ($subscription?->plan !== 'enterprise')
                                    <button wire:click="selectPlan('enterprise')"
                                        class="w-full mt-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg font-medium transition-all">
                                        {{ __('admin.subscription_select_plan') }}
                                    </button>
                                @else
                                    <div
                                        class="w-full mt-4 py-2 bg-blue-600 text-white rounded-lg font-medium text-center">
                                        {{ __('admin.subscription_current_plan') }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" @click="show = false"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Cancel Modal -->
        <div x-data="{ show: @entangle('showCancelModal') }" x-show="show" x-cloak class="relative z-50">
            <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity"></div>
            <div class="fixed inset-0 z-10 overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="show" @click.away="show = false" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="p-2 bg-red-100 rounded-full text-red-600"><x-heroicon-o-exclamation-triangle
                                    class="w-6 h-6" /></div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ __('admin.subscription_cancel_plan') }}
                            </h3>
                        </div>
                        <p class="text-sm text-gray-500 mb-6">{{ __('admin.subscription_cancel_message') }}</p>

                        <div class="mb-6">
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.subscription_cancel_reason') }}</label>
                            <select wire:model.defer="cancelReason"
                                class="form-select w-full rounded-md border-gray-300">
                                <option value="">{{ __('admin.subscription_select_reason') }}</option>
                                <option value="too_expensive">{{ __('admin.subscription_reason_too_expensive') }}
                                </option>
                                <option value="missing_features">
                                    {{ __('admin.subscription_reason_missing_features') }}
                                </option>
                                <option value="switching_service">
                                    {{ __('admin.subscription_reason_switching_service') }}
                                </option>
                                <option value="no_longer_needed">
                                    {{ __('admin.subscription_reason_no_longer_needed') }}
                                </option>
                                <option value="other">{{ __('admin.subscription_reason_other') }}</option>
                            </select>
                            @error('cancelReason')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label
                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.subscription_cancel_feedback') }}</label>
                            <textarea wire:model.defer="cancelFeedback" rows="3" class="form-input w-full rounded-md border-gray-300"
                                placeholder="{{ __('admin.subscription_cancel_feedback_placeholder') }}"></textarea>
                        </div>

                        <div class="flex justify-end gap-3">
                            <button type="button" @click="show = false"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                            <button type="button" wire:click="cancelSubscription"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">{{ __('admin.subscription_confirm_cancel') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</main>
