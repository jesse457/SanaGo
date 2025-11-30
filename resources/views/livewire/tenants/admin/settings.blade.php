      <!-- Settings Page Content -->
            <main class="dark:bg-gray-900 overflow-hidden w-auto flex flex-col"
                  x-data="{
                    activeTab: 'general',
                    tabs: {
                        'general': { icon: 'building-office', label: '{{ __("admin.general_info_header") }}' },
                        'departments': { icon: 'building-office-2', label: '{{ __("admin.department_management_header") }}' },
                        'wards': { icon: 'rectangle-group', label: '{{ __("admin.ward_management_header") }}' },
                        'bed-types': { icon: 'tag', label: '{{ __("admin.bed_type_management_header") }}' },
                        'beds': { icon: 'server', label: '{{ __("admin.bed_management_header") }}' },
                        'supplies': { icon: 'archive-box', label: '{{ __("admin.supply_management_header") }}' }
                    }
                  }">

                <!-- Main Scrollable Area -->
                <div class="flex-1 overflow-y-auto custom-scrollbar relative">

                    <!-- Breadcrumb & Page Header -->
                    <div class="mb-6 px-4 pt-6">
                        <nav class="flex mb-4" aria-label="Breadcrumb">
                            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                                <li class="inline-flex items-center">
                                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-500 transition-colors">
                                        <x-heroicon-m-home class="w-4 h-4 me-2" />
                                        {{ __('admin.home') }}
                                    </a>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <x-heroicon-m-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                                        <span class="ms-1 text-sm font-medium text-gray-800 dark:text-gray-200">{{ __('admin.settings_breadcrumb') }}</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                        <div>
                            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                                <x-heroicon-o-cog-6-tooth class="w-8 h-8 text-blue-600" />
                                {{ __('admin.settings_title') }}
                            </h1>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ __('admin.settings_description') }}</p>
                        </div>
                    </div>

    <!-- Sticky Navigation Tabs -->
<!-- Added 'w-full' to outer div to ensure it fills available space -->
<div class="sticky top-0 z-30 bg-gray-50 dark:bg-gray-900 pt-2 px-4 shadow-sm lg:shadow-none w-full">
    <div class="border-b border-gray-200 dark:border-gray-700 w-full">

        <!--
           CRITICAL CHANGES IN <NAV>:
           1. w-full: Takes full width of parent.
           2. overflow-x-auto: Enables scrolling.
           3. flex: Lays buttons in a row.
           4. no-scrollbar (optional utility) or custom-scrollbar.
        -->
        <nav class="tab-nav-container custom-scrollbar -mb-px flex gap-x-6 overflow-x-auto w-full"
             aria-label="Tabs">

            <template x-for="[key, tab] in Object.entries(tabs)" :key="key">
                <button @click="activeTab = key"
                        type="button"
                        x-bind:class="activeTab === key
                             ? 'border-blue-600 text-blue-600 dark:text-blue-400 dark:border-blue-400'
                             : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'">

                    <!-- Icon Logic -->
                    <template x-if="key === 'general'">
                        <x-heroicon-o-building-office class="inline-block w-5 h-5 mr-2 flex-shrink-0" x-bind:class="activeTab === key ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500'" />
                    </template>
                    <template x-if="key === 'departments'">
                        <x-heroicon-o-building-office-2 class="inline-block w-5 h-5 mr-2 flex-shrink-0" x-bind:class="activeTab === key ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500'" />
                    </template>
                    <template x-if="key === 'wards'">
                        <x-heroicon-o-rectangle-group class=" inline-block w-5 h-5 mr-2 flex-shrink-0" x-bind:class="activeTab === key ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500'" />
                    </template>
                    <template x-if="key === 'bed-types'">
                        <x-heroicon-o-tag class="inline-block w-5 h-5 mr-2 flex-shrink-0" x-bind:class="activeTab === key ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500'" />
                    </template>
                    <template x-if="key === 'beds'">
                        <x-heroicon-o-server class="inline-block w-5 h-5 mr-2 flex-shrink-0" x-bind:class="activeTab === key ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500'" />
                    </template>
                    <template x-if="key === 'supplies'">
                        <x-heroicon-o-archive-box class="inline-block w-5 h-5 mr-2 flex-shrink-0" x-bind:class="activeTab === key ? 'text-blue-600' : 'text-gray-400 group-hover:text-gray-500'" />
                    </template>

                    <span x-text="tab.label"></span>
                </button>
            </template>
        </nav>
    </div>
</div>

                    <!-- Tab Content Area -->
                    <div class="px-4 pb-8 pt-6 min-h-[500px]">

                        <!-- 1. General Settings -->
                        <div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 lg:p-8">
                                <form wire:submit.prevent="saveGeneralSettings">
                                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                                        <!-- Logo Upload -->
                                        <div class="lg:col-span-1 flex flex-col items-center justify-center p-8 bg-gray-50 dark:bg-gray-700/50 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 transition-colors hover:border-blue-400">
                                            <div class="relative group cursor-pointer" onclick="document.getElementById('hospital-logo').click()">
                                                <img id="hospitalLogoPreview"
                                                    class="w-32 h-32 rounded-full object-cover shadow-md border-4 border-white dark:border-gray-800 {{ $currentLogoUrl ? '' : 'hidden' }}"
                                                    src="{{ $currentLogoUrl }}" alt="Hospital Logo">
                                                <div class="absolute inset-0 bg-black/40 rounded-full opacity-0 group-hover:opacity-100 flex items-center justify-center transition-opacity">
                                                    <x-heroicon-o-camera class="w-8 h-8 text-white" />
                                                </div>
                                            </div>
                                            <label for="hospital-logo" class="mt-4 text-sm font-medium text-blue-600 cursor-pointer hover:text-blue-500">
                                                {{ __('admin.label_hospital_logo') }}
                                            </label>
                                            <input type="file" id="hospital-logo" accept="image/*" wire:model="hospitalLogo" class="hidden">
                                            <p class="text-xs text-gray-500 mt-1 text-center max-w-xs">{{ __('admin.logo_upload_tip') }}</p>
                                            @error('hospitalLogo') <span class="text-red-500 text-xs mt-2">{{ $message }}</span> @enderror
                                        </div>

                                        <!-- Input Fields -->
                                        <div class="lg:col-span-2 space-y-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('admin.label_hospital_name') }}</label>
                                                <input type="text" wire:model.defer="hospitalName" class="form-input w-full px-4 py-2.5 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-shadow shadow-sm" placeholder="{{ __('admin.placeholder_hospital_name') }}">
                                                @error('hospitalName') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('admin.label_hospital_address') }}</label>
                                                <input type="text" wire:model.defer="hospitalAddress" class="form-input w-full px-4 py-2.5 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-shadow shadow-sm" placeholder="{{ __('admin.placeholder_hospital_address') }}">
                                                @error('hospitalAddress') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">{{ __('admin.label_hospital_email') }}</label>
                                                <input type="email" wire:model.defer="hospitalEmail" class="form-input w-full px-4 py-2.5 rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white transition-shadow shadow-sm" placeholder="{{ __('admin.placeholder_hospital_email') }}">
                                                @error('hospitalEmail') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="pt-4 flex justify-end">
                                                <button type="submit" class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow-md transition-all duration-200 transform hover:-translate-y-0.5" wire:loading.attr="disabled" wire:target="saveGeneralSettings">
                                                    <x-heroicon-m-check class="w-5 h-5 mr-2" wire:loading.remove wire:target="saveGeneralSettings" />
                                                    <x-heroicon-o-arrow-path class="w-5 h-5 mr-2 animate-spin" wire:loading wire:target="saveGeneralSettings" />
                                                    <span>{{ __('admin.button_save_general_settings') }}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- 2. Departments -->
                        <div x-show="activeTab === 'departments'" x-cloak class="h-full">
                            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 h-full">
                                <!-- Form Side -->
                                <div class="xl:col-span-1">
                                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 xl:sticky xl:top-24">
                                        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                                            <span class="bg-blue-100 text-blue-600 p-2 rounded-lg mr-3"><x-heroicon-o-plus class="w-5 h-5" /></span>
                                            {{ __('admin.label_new_department') }}
                                        </h3>
                                        <form wire:submit.prevent="addDepartment">
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-xs uppercase font-bold text-gray-500 mb-1 tracking-wider">{{ __('admin.label_new_department') }}</label>
                                                    <input type="text" wire:model.defer="newDepartmentName" class="form-input w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="{{ __('admin.placeholder_new_department') }}">
                                                    @error('newDepartmentName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                </div>
                                                <div>
                                                    <label class="block text-xs uppercase font-bold text-gray-500 mb-1 tracking-wider">{{ __('admin.label_department_description') }}</label>
                                                    <textarea rows="3" wire:model.defer="newDepartmentDescription" class="form-input w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="{{ __('admin.placeholder_department_description') }}"></textarea>
                                                    @error('newDepartmentDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                </div>
                                                <button type="submit" class="w-full py-2.5 bg-gray-900 hover:bg-black text-white rounded-lg font-medium shadow-lg transition-all flex justify-center items-center" wire:loading.attr="disabled" wire:target="addDepartment">
                                                    <span wire:loading.remove wire:target="addDepartment">{{ __('admin.button_add_department') }}</span>
                                                    <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin" wire:loading wire:target="addDepartment" />
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- List Side -->
                                <div class="xl:col-span-2 flex flex-col h-full">
                                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col h-full">
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-4">
                                            <h3 class="font-bold text-gray-800 dark:text-white">{{ __('admin.label_existing_departments') }}</h3>
                                            <div class="relative w-full sm:w-64">
                                                <input type="text" wire:model.live="searchDepartment" class="w-full pl-9 py-2 text-sm rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 bg-gray-50" placeholder="{{ __('admin.placeholder_search_departments') }}">
                                                <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400 absolute left-2.5 top-3" />
                                            </div>
                                        </div>

                                        <div class="flex-1 overflow-y-auto custom-scrollbar pr-1">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                @forelse($filteredDepartments as $department)
                                                <div wire:key="dept-{{ $department->id }}" class="group p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-transparent hover:border-blue-300 hover:bg-white dark:hover:bg-gray-700 transition-all duration-200 relative">
                                                    <div class="pr-8">
                                                        <h4 class="font-bold text-gray-900 dark:text-gray-100">{{ $department->name }}</h4>
                                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $department->description ?? __('department_no_description') }}</p>
                                                    </div>
                                                    <div class="absolute top-3 right-3 flex flex-col gap-1 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                                        <button wire:click="editDepartment({{ $department->id }})" class="p-1.5 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-md transition-colors"><x-heroicon-s-pencil-square class="w-4 h-4"/></button>
                                                        <button wire:click="confirmDeleteDepartment({{ $department->id }})" class="p-1.5 text-red-600 bg-red-50 hover:bg-red-100 rounded-md transition-colors"><x-heroicon-s-trash class="w-4 h-4"/></button>
                                                    </div>
                                                </div>
                                                @empty
                                                <div class="col-span-full py-10 flex flex-col items-center justify-center text-gray-400">
                                                    <x-heroicon-o-inbox-stack class="w-12 h-12 mb-2 opacity-50" />
                                                    <span>{{ __('admin.no_departments_found_title') }}</span>
                                                </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. Wards -->
                        <div x-show="activeTab === 'wards'" x-cloak class="h-full">
                            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 h-full">
                                <!-- Form -->
                                <div class="xl:col-span-1">
                                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 xl:sticky xl:top-24">
                                        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 flex items-center">
                                            <span class="bg-indigo-100 text-indigo-600 p-2 rounded-lg mr-3"><x-heroicon-o-plus class="w-5 h-5" /></span>
                                            {{ __('admin.label_new_ward') }}
                                        </h3>
                                        <form wire:submit.prevent="addWard">
                                            <div class="space-y-4">
                                                <div class="grid grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="text-xs font-bold text-gray-500 uppercase">{{ __('admin.label_new_ward') }}</label>
                                                        <input type="text" wire:model.defer="newWardName" class="form-input w-full rounded-lg border-gray-300 mt-1" placeholder="{{ __('admin.placeholder_new_ward') }}">
                                                        @error('newWardName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                    </div>
                                                    <div>
                                                        <label class="text-xs font-bold text-gray-500 uppercase">{{ __('admin.label_ward_number') }}</label>
                                                        <input type="text" wire:model.defer="newWardNumber" class="form-input w-full rounded-lg border-gray-300 mt-1" placeholder="{{ __('admin.placeholder_ward_number') }}">
                                                        @error('newWardNumber') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="text-xs font-bold text-gray-500 uppercase">{{ __('admin.label_assign_to_department') }}</label>
                                                    <select wire:model.defer="newWardDepartmentId" class="form-select w-full rounded-lg border-gray-300 mt-1">
                                                        <option value="">{{ __('admin.option_select_department') }}</option>
                                                        @foreach ($filteredDepartments as $department)
                                                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    @error('newWardDepartmentId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                </div>
                                                <div>
                                                    <label class="text-xs font-bold text-gray-500 uppercase">{{ __('admin.label_ward_description') }}</label>
                                                    <textarea rows="2" wire:model.defer="newWardDescription" class="form-input w-full rounded-lg border-gray-300 mt-1" placeholder="{{ __('admin.placeholder_ward_description') }}"></textarea>
                                                    @error('newWardDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                </div>
                                                <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium shadow-md transition-all flex justify-center items-center" wire:loading.attr="disabled" wire:target="addWard">
                                                    <span wire:loading.remove wire:target="addWard">{{ __('admin.button_add_ward') }}</span>
                                                    <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin" wire:loading wire:target="addWard" />
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- List -->
                                <div class="xl:col-span-2 flex flex-col h-full">
                                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col h-full">
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-4">
                                            <h3 class="font-bold text-gray-800">{{ __('admin.label_existing_wards') }}</h3>
                                            <div class="relative w-full sm:w-64">
                                                <input type="text" wire:model.live.debounce.300ms="searchWard" class="w-full pl-9 py-2 text-sm rounded-lg border-gray-300 bg-gray-50" placeholder="{{ __('admin.placeholder_search_wards') }}">
                                                <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400 absolute left-2.5 top-3" />
                                            </div>
                                        </div>
                                        <div class="flex-1 overflow-y-auto custom-scrollbar pr-1">
                                            <div class="space-y-2">
                                                @forelse($filteredWards as $ward)
                                                <div wire:key="ward-{{ $ward->id }}" class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg hover:shadow-sm hover:border-indigo-300 transition-all group">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-10 h-10 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                                            {{ $ward->ward_number }}
                                                        </div>
                                                        <div>
                                                            <p class="font-semibold text-gray-900">{{ $ward->name }}</p>
                                                            <p class="text-xs text-gray-500 flex items-center gap-1">
                                                                <x-heroicon-o-building-office-2 class="w-3 h-3"/> {{ $ward->department->name ?? __('ward_info_na') }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div class="flex gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                                        <button wire:click="editWard({{ $ward->id }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-md" title="{{ __('admin.modal_edit_ward_title') }}"><x-heroicon-s-pencil-square class="w-5 h-5"/></button>
                                                        <button wire:click="confirmDeleteWard({{ $ward->id }})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-md" title="{{ __('admin.modal_delete_ward_title') }}"><x-heroicon-s-trash class="w-5 h-5"/></button>
                                                    </div>
                                                </div>
                                                @empty
                                                <div class="text-center py-10 text-gray-400">
                                                    <x-heroicon-o-inbox class="w-12 h-12 mx-auto mb-2 opacity-50"/>
                                                    {{ __('admin.no_wards_found_title') }}
                                                </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. Bed Types -->
                        <div x-show="activeTab === 'bed-types'" x-cloak class="h-full">
                            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 h-full">
                                <div class="xl:col-span-1">
                                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 xl:sticky xl:top-24">
                                        <h3 class="text-lg font-bold mb-4 flex items-center text-gray-800 dark:text-white">
                                            <span class="bg-green-100 text-green-600 p-2 rounded-lg mr-3"><x-heroicon-o-tag class="w-5 h-5"/></span>
                                            {{ __('admin.label_new_bed_type_name') }}
                                        </h3>
                                        <form wire:submit.prevent="addBedType" class="space-y-4">
                                            <div>
                                                <label class="text-xs font-bold text-gray-500 uppercase">{{ __('admin.label_new_bed_type_name') }}</label>
                                                <input type="text" wire:model.defer="newBedTypeName" class="form-input w-full rounded-lg border-gray-300 mt-1" placeholder="{{ __('admin.placeholder_new_bed_type_name') }}">
                                                @error('newBedTypeName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="relative">
                                                <label class="text-xs font-bold text-gray-500 uppercase">{{ __('admin.label_bed_type_price') }}</label>
                                                <div class="relative mt-1">
                                                    <span class="absolute left-3 top-2.5 text-gray-500">$</span>
                                                    <input type="number" step="0.01" wire:model.defer="newBedTypePrice" class="form-input w-full pl-7 rounded-lg border-gray-300" placeholder="{{ __('admin.placeholder_bed_type_price') }}">
                                                </div>
                                                @error('newBedTypePrice') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="text-xs font-bold text-gray-500 uppercase">{{ __('admin.label_bed_type_description') }}</label>
                                                <textarea wire:model.defer="newBedTypeDescription" rows="2" class="form-input w-full rounded-lg border-gray-300 mt-1" placeholder="{{ __('admin.placeholder_bed_type_description') }}"></textarea>
                                                @error('newBedTypeDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>

                                            <button type="submit" class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium shadow-md transition-all flex justify-center items-center" wire:loading.attr="disabled" wire:target="addBedType">
                                                <span wire:loading.remove wire:target="addBedType">{{ __('admin.button_add_bed_type') }}</span>
                                                <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin" wire:loading wire:target="addBedType" />
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="xl:col-span-2 flex flex-col h-full">
                                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col h-full">
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-4">
                                            <h3 class="font-bold text-gray-800">{{ __('admin.label_existing_bed_types') }}</h3>
                                            <div class="relative w-full sm:w-64">
                                                <input type="text" wire:model.live.debounce.300ms="searchBedType" class="w-full pl-9 py-2 text-sm rounded-lg border-gray-300 bg-gray-50" placeholder="{{ __('admin.placeholder_search_bed_types') }}">
                                                <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400 absolute left-2.5 top-3" />
                                            </div>
                                        </div>
                                        <div class="flex-1 overflow-y-auto custom-scrollbar pr-1">
                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                                @forelse($filteredBedTypes as $bedType)
                                                <div wire:key="bedtype-{{ $bedType->id }}" class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition-shadow relative group">
                                                    <div class="flex justify-between items-start">
                                                        <h4 class="font-bold text-gray-800">{{ $bedType->name }}</h4>
                                                        <span class="bg-green-100 text-green-800 text-xs font-bold px-2 py-1 rounded-full">${{ number_format($bedType->price_per_day, 2) }}</span>
                                                    </div>
                                                    <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ $bedType->description ?? __('bed_type_no_description') }}</p>
                                                    <div class="mt-4 flex justify-end gap-2 opacity-100 lg:opacity-0 lg:group-hover:opacity-100 transition-opacity">
                                                        <button wire:click="editBedType({{ $bedType->id }})" class="text-blue-600 text-xs font-medium hover:underline">{{ __('admin.modal_edit_bed_type_title') }}</button>
                                                        <button wire:click="confirmDeleteBedType({{ $bedType->id }})" class="text-red-600 text-xs font-medium hover:underline">{{ __('admin.modal_delete_bed_type_title') }}</button>
                                                    </div>
                                                </div>
                                                @empty
                                                <div class="col-span-full text-center text-gray-400 py-10">{{ __('admin.no_bed_types_found_title') }}</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Beds -->
                        <div x-show="activeTab === 'beds'" x-cloak class="h-full">
                            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 h-full">
                                <div class="xl:col-span-1">
                                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 xl:sticky xl:top-24">
                                        <h3 class="text-lg font-bold mb-4 flex items-center text-gray-800 dark:text-white">
                                            <span class="bg-purple-100 text-purple-600 p-2 rounded-lg mr-3"><x-heroicon-o-server class="w-5 h-5"/></span>
                                            {{ __('admin.label_new_bed_number') }}
                                        </h3>
                                        <form wire:submit.prevent="addBed" class="space-y-4">
                                            <div>
                                                <label class="text-xs font-bold text-gray-500 uppercase">{{ __('admin.label_new_bed_number') }}</label>
                                                <input type="text" wire:model.defer="newBedNumber" class="form-input w-full rounded-lg border-gray-300 mt-1" placeholder="{{ __('admin.placeholder_new_bed_number') }}">
                                                @error('newBedNumber') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="text-xs font-bold text-gray-500 uppercase">{{ __('admin.label_bed_assign_ward') }}</label>
                                                <select wire:model.defer="newBedWardId" class="form-select w-full rounded-lg border-gray-300 mt-1">
                                                    <option value="">{{ __('admin.option_select_ward') }}</option>
                                                    @foreach ($filteredWards as $ward)
                                                        <option value="{{ $ward->id }}">{{ $ward->name }} ({{ $ward->department->name ?? 'N/A' }})</option>
                                                    @endforeach
                                                </select>
                                                @error('newBedWardId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="text-xs font-bold text-gray-500 uppercase">{{ __('admin.label_bed_type') }}</label>
                                                <select wire:model.defer="newBedTypeId" class="form-select w-full rounded-lg border-gray-300 mt-1">
                                                    <option value="">{{ __('admin.option_select_bed_type') }}</option>
                                                    @foreach ($filteredBedTypes as $bedType)
                                                        <option value="{{ $bedType->id }}">{{ $bedType->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('newBedTypeId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>

                                            <button type="submit" class="w-full py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium shadow-md transition-all flex justify-center items-center" wire:loading.attr="disabled" wire:target="addBed">
                                                <span wire:loading.remove wire:target="addBed">{{ __('admin.button_add_bed') }}</span>
                                                <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin" wire:loading wire:target="addBed" />
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="xl:col-span-2 flex flex-col h-full">
                                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col h-full">
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 gap-4">
                                            <h3 class="font-bold text-gray-800">{{ __('admin.label_existing_beds') }}</h3>
                                            <div class="relative w-full sm:w-64">
                                                <input type="text" wire:model.live.debounce.300ms="searchBed" class="w-full pl-9 py-2 text-sm rounded-lg border-gray-300 bg-gray-50" placeholder="{{ __('admin.placeholder_search_beds') }}">
                                                <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400 absolute left-2.5 top-3" />
                                            </div>
                                        </div>
                                        <div class="flex-1 overflow-y-auto custom-scrollbar pr-1">
                                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                                @forelse($filteredBeds as $bed)
                                                <div wire:key="bed-{{ $bed->id }}" class="relative group bg-gray-50 border border-gray-200 rounded-lg p-3 text-center hover:bg-white hover:shadow-md transition-all">
                                                    <div class="mx-auto w-10 h-10 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs mb-2">
                                                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::limit($bed->bed_number, 3, '')) }}
                                                    </div>
                                                    <p class="font-bold text-gray-800 text-sm truncate">{{ $bed->bed_number }}</p>
                                                    <p class="text-xs text-gray-500 truncate">{{ $bed->ward->name ?? __('ward_info_na') }}</p>

                                                    <div class="absolute inset-0 bg-black/5 rounded-lg opacity-0 group-hover:opacity-100 flex items-center justify-center gap-2 transition-opacity backdrop-blur-[1px]">
                                                        <button wire:click="editBed({{ $bed->id }})" class="bg-white p-1.5 rounded-full text-blue-600 shadow-sm hover:scale-110 transition-transform"><x-heroicon-s-pencil-square class="w-4 h-4"/></button>
                                                        <button wire:click="confirmDeleteBed({{ $bed->id }})" class="bg-white p-1.5 rounded-full text-red-600 shadow-sm hover:scale-110 transition-transform"><x-heroicon-s-trash class="w-4 h-4"/></button>
                                                    </div>
                                                </div>
                                                @empty
                                                <div class="col-span-full text-center text-gray-400 py-10">{{ __('admin.no_beds_found_title') }}</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 6. Supplies -->
                        <div x-show="activeTab === 'supplies'" x-cloak class="h-full">
                            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 h-full">
                                <div class="xl:col-span-1">
                                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 xl:sticky xl:top-24">
                                        <h3 class="text-lg font-bold mb-4 flex items-center text-gray-800 dark:text-white">
                                            <span class="bg-orange-100 text-orange-600 p-2 rounded-lg mr-3"><x-heroicon-o-archive-box class="w-5 h-5"/></span>
                                            {{ __('admin.label_new_supply_name') }}
                                        </h3>
                                        <form wire:submit.prevent="addSupply" class="space-y-4">
                                            <div>
                                                <label class="text-xs font-bold text-gray-500 uppercase">{{ __('admin.label_new_supply_name') }}</label>
                                                <input type="text" wire:model.defer="newSupplyName" class="form-input w-full rounded-lg border-gray-300 mt-1" placeholder="{{ __('admin.placeholder_new_supply_name') }}">
                                                @error('newSupplyName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>

                                            <div>
                                                <label class="text-xs font-bold text-gray-500 uppercase">{{ __('admin.label_supply_unit_of_measure') }}</label>
                                                <input type="text" wire:model.defer="newSupplyUnitOfMeasure" class="form-input w-full rounded-lg border-gray-300 mt-1" placeholder="{{ __('admin.placeholder_supply_unit_of_measure') }}">
                                                @error('newSupplyUnitOfMeasure') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="text-xs font-bold text-gray-500 uppercase">{{ __('admin.label_supply_current_stock') }}</label>
                                                    <input type="number" wire:model.defer="newSupplyCurrentStock" class="form-input w-full rounded-lg border-gray-300 mt-1" placeholder="0">
                                                    @error('newSupplyCurrentStock') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                </div>
                                                <div>
                                                    <label class="text-xs font-bold text-gray-500 uppercase">{{ __('admin.label_supply_min_stock_level') }}</label>
                                                    <input type="number" wire:model.defer="newSupplyMinStockLevel" class="form-input w-full rounded-lg border-gray-300 mt-1" placeholder="0">
                                                    @error('newSupplyMinStockLevel') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                </div>
                                            </div>

                                            <button type="submit" class="w-full py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium shadow-md transition-all flex justify-center items-center" wire:loading.attr="disabled" wire:target="addSupply">
                                                <span wire:loading.remove wire:target="addSupply">{{ __('admin.button_add_supply') }}</span>
                                                <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin" wire:loading wire:target="addSupply" />
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="xl:col-span-2 flex flex-col h-full">
                                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col h-full">
                                        <div class="p-4 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center bg-gray-50 gap-4">
                                            <h3 class="font-bold text-gray-700">{{ __('admin.label_existing_supplies') }}</h3>
                                            <div class="relative w-full sm:w-64">
                                                <input type="text" wire:model.live.debounce.300ms="searchSupply" class="w-full pl-9 py-2 text-sm rounded-lg border-gray-300 bg-white" placeholder="{{ __('admin.placeholder_search_supplies') }}">
                                                <x-heroicon-o-magnifying-glass class="w-4 h-4 text-gray-400 absolute left-2.5 top-3" />
                                            </div>
                                        </div>
                                        <div class="flex-1 overflow-x-auto">
                                            <table class="w-full text-sm text-left">
                                                <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                                                    <tr>
                                                        <th class="px-4 py-3 whitespace-nowrap">{{ __('admin.supply_list_name') ?? 'Item' }}</th>
                                                        <th class="px-4 py-3 whitespace-nowrap">{{ __('admin.supply_list_stock') }}</th>
                                                        <th class="px-4 py-3 whitespace-nowrap">Status</th>
                                                        <th class="px-4 py-3 text-right whitespace-nowrap">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-100">
                                                    @forelse($filteredSupplies as $supply)
                                                    <tr wire:key="supply-{{ $supply->id }}" class="hover:bg-gray-50 transition-colors">
                                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $supply->name }}</td>
                                                        <td class="px-4 py-3">
                                                            {{ $supply->current_stock }} <span class="text-gray-400 text-xs">{{ $supply->unit_of_measure }}</span>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            @if($supply->current_stock <= $supply->min_stock_level)
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                    Low Stock
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                    In Stock
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-4 py-3 text-right space-x-2">
                                                            <button wire:click="editSupply({{ $supply->id }})" class="text-blue-600 hover:underline text-xs font-semibold">{{ __('admin.modal_button_edit') ?? 'Edit' }}</button>
                                                            <button wire:click="confirmDeleteSupply({{ $supply->id }})" class="text-red-600 hover:underline text-xs font-semibold">{{ __('admin.modal_button_delete') ?? 'Delete' }}</button>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="4" class="px-4 py-10 text-center text-gray-500">{{ __('admin.no_supplies_found_title') }}</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> <!-- End Tab Content -->
                </div> <!-- End Main Scroll -->

                <!-- ==========================================
                     MODALS (Standardized with Alpine Transitions)
                     ========================================== -->

                <!-- 1. Department Edit Modal -->
                <div x-data="{ show: @entangle('showDepartmentEditModal') }" x-show="show" x-cloak class="relative z-50">
                    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity"></div>
                    <div class="fixed inset-0 z-10 overflow-y-auto">
                        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                            <div x-show="show" @click.away="show = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4">{{ __('admin.modal_edit_department_title') }}</h3>
                                <form wire:submit.prevent="updateDepartment">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_department_name') }}</label>
                                            <input type="text" wire:model.defer="editDepartmentName" class="form-input w-full rounded-md border-gray-300 mt-1">
                                            @error('editDepartmentName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_department_description') }}</label>
                                            <textarea wire:model.defer="editDepartmentDescription" rows="3" class="form-input w-full rounded-md border-gray-300 mt-1"></textarea>
                                            @error('editDepartmentDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button" @click="show = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_save_changes') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Department Delete Modal -->
                <div x-data="{ show: @entangle('showDepartmentDeleteModal') }" x-show="show" x-cloak class="relative z-50">
                    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-900/70 backdrop-blur-sm transition-opacity"></div>
                    <div class="fixed inset-0 z-10 overflow-y-auto">
                        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                            <div x-show="show" @click.away="show = false" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm p-6">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="p-2 bg-red-100 rounded-full text-red-600"><x-heroicon-o-exclamation-triangle class="w-6 h-6" /></div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.modal_delete_department_title') }}</h3>
                                </div>
                                <p class="text-sm text-gray-500 mb-6">{{ __('admin.modal_delete_department_message') }}</p>
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="show = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                                    <button type="button" wire:click="deleteDepartment" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_delete') }}</button>
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
                            <div x-show="show" @click.away="show = false" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4">{{ __('admin.modal_edit_ward_title') }}</h3>
                                <form wire:submit.prevent="updateWard">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_ward_name') }}</label>
                                            <input type="text" wire:model.defer="editWardName" class="form-input w-full rounded-md border-gray-300 mt-1">
                                            @error('editWardName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_ward_number') }}</label>
                                            <input type="text" wire:model.defer="editWardNumber" class="form-input w-full rounded-md border-gray-300 mt-1">
                                            @error('editWardNumber') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_ward_department') }}</label>
                                            <select wire:model.defer="editWardDepartmentId" class="form-select w-full rounded-md border-gray-300 mt-1">
                                                <option value="">{{ __('admin.option_select_department') }}</option>
                                                @foreach ($filteredDepartments as $department)
                                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('editWardDepartmentId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_ward_description') }}</label>
                                            <textarea wire:model.defer="editWardDescription" rows="3" class="form-input w-full rounded-md border-gray-300 mt-1"></textarea>
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button" @click="show = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_save_changes') }}</button>
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
                            <div x-show="show" @click.away="show = false" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm p-6">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="p-2 bg-red-100 rounded-full text-red-600"><x-heroicon-o-exclamation-triangle class="w-6 h-6" /></div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.modal_delete_ward_title') }}</h3>
                                </div>
                                <p class="text-sm text-gray-500 mb-6">{{ __('admin.modal_delete_ward_message') }}</p>
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="show = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                                    <button type="button" wire:click="deleteWard" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_delete') }}</button>
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
                            <div x-show="show" @click.away="show = false" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4">{{ __('admin.modal_edit_bed_type_title') }}</h3>
                                <form wire:submit.prevent="updateBedType">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_bed_type_name') }}</label>
                                            <input type="text" wire:model.defer="editBedTypeName" class="form-input w-full rounded-md border-gray-300 mt-1">
                                            @error('editBedTypeName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_bed_type_price') }}</label>
                                            <input type="number" step="0.01" wire:model.defer="editBedTypePrice" class="form-input w-full rounded-md border-gray-300 mt-1">
                                            @error('editBedTypePrice') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_bed_type_description') }}</label>
                                            <textarea wire:model.defer="editBedTypeDescription" rows="3" class="form-input w-full rounded-md border-gray-300 mt-1"></textarea>
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button" @click="show = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_save_changes') }}</button>
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
                            <div x-show="show" @click.away="show = false" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm p-6">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="p-2 bg-red-100 rounded-full text-red-600"><x-heroicon-o-exclamation-triangle class="w-6 h-6" /></div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.modal_delete_bed_type_title') }}</h3>
                                </div>
                                <p class="text-sm text-gray-500 mb-6">{{ __('admin.modal_delete_bed_type_message') }}</p>
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="show = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                                    <button type="button" wire:click="deleteBedType" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_delete') }}</button>
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
                            <div x-show="show" @click.away="show = false" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4">{{ __('admin.modal_edit_bed_title') }}</h3>
                                <form wire:submit.prevent="updateBed">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_bed_number') }}</label>
                                            <input type="text" wire:model.defer="editBedNumber" class="form-input w-full rounded-md border-gray-300 mt-1">
                                            @error('editBedNumber') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_bed_ward') }}</label>
                                            <select wire:model.defer="editBedWardId" class="form-select w-full rounded-md border-gray-300 mt-1">
                                                <option value="">{{ __('admin.option_select_ward') }}</option>
                                                @foreach ($filteredWards as $ward)
                                                    <option value="{{ $ward->id }}">{{ $ward->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('editBedWardId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_bed_type') }}</label>
                                            <select wire:model.defer="editBedTypeId" class="form-select w-full rounded-md border-gray-300 mt-1">
                                                <option value="">{{ __('admin.option_select_bed_type') }}</option>
                                                @foreach ($filteredBedTypes as $bedType)
                                                    <option value="{{ $bedType->id }}">{{ $bedType->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('editBedTypeId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button" @click="show = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_save_changes') }}</button>
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
                            <div x-show="show" @click.away="show = false" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm p-6">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="p-2 bg-red-100 rounded-full text-red-600"><x-heroicon-o-exclamation-triangle class="w-6 h-6" /></div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.modal_delete_bed_title') }}</h3>
                                </div>
                                <p class="text-sm text-gray-500 mb-6">{{ __('admin.modal_delete_bed_message') }}</p>
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="show = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                                    <button type="button" wire:click="deleteBed" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_delete') }}</button>
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
                            <div x-show="show" @click.away="show = false" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6">
                                <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4">{{ __('admin.modal_edit_supply_title') }}</h3>
                                <form wire:submit.prevent="updateSupply">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_supply_name') }}</label>
                                            <input type="text" wire:model.defer="editSupplyName" class="form-input w-full rounded-md border-gray-300 mt-1">
                                            @error('editSupplyName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_supply_unit') }}</label>
                                            <input type="text" wire:model.defer="editSupplyUnitOfMeasure" class="form-input w-full rounded-md border-gray-300 mt-1">
                                            @error('editSupplyUnitOfMeasure') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_supply_stock') }}</label>
                                                <input type="number" wire:model.defer="editSupplyCurrentStock" class="form-input w-full rounded-md border-gray-300 mt-1">
                                                @error('editSupplyCurrentStock') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">{{ __('admin.label_edit_supply_min_stock') }}</label>
                                                <input type="number" wire:model.defer="editSupplyMinStockLevel" class="form-input w-full rounded-md border-gray-300 mt-1">
                                                @error('editSupplyMinStockLevel') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-end gap-3">
                                        <button type="button" @click="show = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_save_changes') }}</button>
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
                            <div x-show="show" @click.away="show = false" class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm p-6">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="p-2 bg-red-100 rounded-full text-red-600"><x-heroicon-o-exclamation-triangle class="w-6 h-6" /></div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.modal_delete_supply_title') }}</h3>
                                </div>
                                <p class="text-sm text-gray-500 mb-6">{{ __('admin.modal_delete_supply_message') }}</p>
                                <div class="flex justify-end gap-3">
                                    <button type="button" @click="show = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">{{ __('admin.modal_button_cancel') }}</button>
                                    <button type="button" wire:click="deleteSupply" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">{{ __('admin.modal_button_delete') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </main>
