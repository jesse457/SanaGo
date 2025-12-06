<main class="flex-1 bg-white dark:bg-gray-900 overflow-y-auto min-h-screen font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:p-8">



        {{-- Breadcrumbs --}}
        <nav class="hidden md:flex mb-8 mt-5" aria-label="Breadcrumb">
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
                            {{ __('admin.user_management_title') }}
                        </span>
                    </div>
                </li>
            </ol>
        </nav>

        {{-- Header Section --}}
        <div class="sm:flex sm:items-center sm:justify-between mb-8">
            <div>
              <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight flex items-center gap-3">
                     {{ __('admin.manage_users_title') }}
                </h1>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('admin.manage_users_description') }}
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.create-new-user') }}" wire:navigate
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm hover:shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200">
                    <x-heroicon-o-plus class="w-5 h-5" />
                    {{ __('admin.add_user_button') }}
                </a>
            </div>
        </div>

        {{-- Search & Filters Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                {{-- Search --}}
                <div class="md:col-span-6 lg:col-span-5 relative">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" />
                    </div>
                    <input type="text" wire:model.live="search"
                        placeholder="{{ __('admin.search_placeholder_users') }}"
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                </div>

                {{-- Filters --}}
                <div class="md:col-span-3 lg:col-span-3">
                    <select wire:model.live="filterRole"
                        class="block w-full py-2.5 pl-3 pr-10 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">{{ __('admin.filter_all_roles') }}</option>
                        <option value="admin">{{ __('admin.role_admin') }}</option>
                        <option value="doctor">{{ __('admin.role_doctor') }}</option>
                        <option value="nurse">{{ __('admin.role_nurse') }}</option>
                        <option value="receptionist">{{ __('admin.role_receptionist') }}</option>
                        <option value="lab-technician">{{ __('admin.role_lab_technician') }}</option>
                        <option value="pharmacist">{{ __('admin.role_pharmacist') }}</option>
                    </select>
                </div>
                <div class="md:col-span-3 lg:col-span-4">
                    <select wire:model.live="filterStatus"
                        class="block w-full py-2.5 pl-3 pr-10 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">{{ __('admin.filter_all_statuses') }}</option>
                        <option value="active">{{ __('admin.status_active') }}</option>
                        <option value="inactive">{{ __('admin.status_inactive') }}</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Content Area --}}
        <div class="relative min-h-[400px]">
            {{-- Loading State --}}
            <div wire:loading.flex
                class="absolute inset-0 bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm z-20 flex items-center justify-center rounded-xl">
                <div class="flex items-center gap-3 px-4 py-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                    <x-heroicon-o-arrow-path class="animate-spin h-6 w-6 text-indigo-600" />
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Loading...</span>
                </div>
            </div>

            {{-- DESKTOP: Table View --}}
            <div class="hidden md:block bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('admin.table_header_name') }}
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('admin.form_label_role') }}
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    {{ __('admin.table_header_status') }}
                                </th>
                                <th scope="col" class="relative px-6 py-4">
                                    <span class="sr-only">{{ __('admin.table_header_action') }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                            @forelse ($users as $user)
                                <tr wire:key="row-{{ $user->id }}" class="group hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full object-cover ring-2 ring-white dark:ring-gray-700"
                                                    src="{{ $user->profile_picture ? Storage::disk('s3')->temporaryUrl($user->profile_picture, now()->addMinutes(5)) : asset('images/default_profile.png') }}"
                                                    alt="{{ $user->name }}">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                                                    {{ $user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                            {{ match($user->role) {
                                                'admin' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                                'doctor' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                                'nurse' => 'bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-300',
                                                default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'
                                            } }}">
                                            {{ str_replace('_', ' ', $user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="flex w-2 h-2 rounded-full mr-2 {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                            <span class="text-sm text-gray-700 dark:text-gray-300">
                                                {{ $user->is_active ? __('admin.status_active') : __('admin.status_inactive') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            <button wire:click="editUser({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300" title="{{ __('admin.action_edit') }}">
                                                <x-heroicon-o-pencil-square class="w-5 h-5" />
                                            </button>
                                            <button wire:click="viewDeleteUser({{ $user->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="{{ __('admin.action_delete') }}">
                                                <x-heroicon-o-trash class="w-5 h-5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <x-heroicon-o-user-group class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" />
                                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('admin.no_users_found_title') }}</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('admin.no_users_found_text') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MOBILE: Card View (Hidden on Desktop) --}}
            <div class="md:hidden space-y-4">
                @forelse ($users as $user)
                    <div wire:key="mobile-card-{{ $user->id }}" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <img class="h-12 w-12 rounded-full object-cover ring-2 ring-gray-100 dark:ring-gray-700"
                                    src="{{ $user->profile_picture ? Storage::disk('s3')->temporaryUrl($user->profile_picture, now()->addMinutes(5)) : asset('images/default_profile.png') }}"
                                    alt="{{ $user->name }}">
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 capitalize">
                                {{ str_replace('_', ' ', $user->role) }}
                            </span>
                        </div>

                        <div class="mt-4 flex items-center justify-between border-t border-gray-100 dark:border-gray-700 pt-3">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                {{ $user->is_active ? __('admin.status_active') : __('admin.status_inactive') }}
                            </span>

                            <div class="flex gap-2">
                                <button wire:click="editUser({{ $user->id }})" class="p-2 text-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg hover:bg-indigo-100">
                                    <x-heroicon-o-pencil-square class="w-5 h-5" />
                                </button>
                                <button wire:click="viewDeleteUser({{ $user->id }})" class="p-2 text-red-600 bg-red-50 dark:bg-red-900/30 rounded-lg hover:bg-red-100">
                                    <x-heroicon-o-trash class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-white dark:bg-gray-800 rounded-xl border border-dashed border-gray-300 dark:border-gray-600">
                        <x-heroicon-o-user-group class="mx-auto h-12 w-12 text-gray-300" />
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('admin.no_users_found_title') }}</h3>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($this->users->hasPages())
                <div class="mt-6">
                    {{ $this->users->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- MODAL --}}
    <div x-data="{
        showEditModal: false,
        init() {
            $watch('showEditModal', value => {
                if (value) { document.body.style.overflow = 'hidden'; }
                else { document.body.style.overflow = ''; }
            });
        }
    }"
    x-on:open-edit-modal.window="showEditModal = true"
    x-on:close-edit-modal.window="showEditModal = false"
    x-on:user-updated.window="$dispatch('show-toast', { message: '{{ __('admin.toast_user_updated') }}' })">

        <template x-teleport="body">
            <div x-show="showEditModal"
                 class="relative z-50"
                 role="dialog"
                 aria-modal="true">

                {{-- Backdrop --}}
                <div x-show="showEditModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

                {{-- Modal Panel --}}
                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                        <div x-show="showEditModal"
                             @click.away="$wire.call('closeEditModal')"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                             class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">

                            {{-- Modal Header --}}
                            <div class="bg-white dark:bg-gray-800 px-4 py-5 sm:px-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between sticky top-0 z-10">
                                <div>
                                    <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-white" id="modal-title">
                                        {{ __('admin.edit_user_modal_title') }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Editing: <span class="font-medium text-indigo-600 dark:text-indigo-400" x-text="$wire.name"></span>
                                    </p>
                                </div>
                                <button @click="$wire.call('closeEditModal')" class="rounded-md bg-transparent text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <span class="sr-only">Close</span>
                                    <x-heroicon-o-x-mark class="h-6 w-6" />
                                </button>
                            </div>

                            <form wire:submit.prevent="updateUser">
                                <div class="px-4 py-5 sm:p-6 max-h-[70vh] overflow-y-auto">

                                    {{-- Section: Personal Info --}}
                                    <div class="mb-6">
                                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">{{ __('admin.section_personal_info') }}</h4>
                                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">

                                            <div class="sm:col-span-3">
                                                <label for="edit-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.form_label_name') }}</label>
                                                <input type="text" id="edit-name" wire:model="name" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 px-3">
                                                @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="edit-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.form_label_email') }}</label>
                                                <input type="email" id="edit-email" wire:model="email" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 px-3">
                                                @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="edit-role" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.form_label_role') }}</label>
                                                <select id="edit-role" wire:model="role" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 px-3">
                                                    <option value="admin">{{ __('admin.role_admin') }}</option>
                                                    <option value="doctor">{{ __('admin.role_doctor') }}</option>
                                                    <option value="nurse">{{ __('admin.role_nurse') }}</option>
                                                    <option value="receptionist">{{ __('admin.role_receptionist') }}</option>
                                                    <option value="lab-technician">{{ __('admin.role_lab_technician') }}</option>
                                                    <option value="pharmacist">{{ __('admin.role_pharmacist') }}</option>
                                                </select>
                                                @error('role') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="edit-phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.form_label_phone') }}</label>
                                                <input type="tel" id="edit-phone" wire:model="phone_number" class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm h-10 px-3">
                                                @error('phone_number') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                            </div>

                                            <div class="sm:col-span-6">
                                                <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg border border-gray-200 dark:border-gray-600" x-data="{ isActive: @entangle('is_active') }">
                                                    <div class="flex flex-col">
                                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ __('admin.account_status') }}</span>
                                                        <span class="text-xs text-gray-500" x-text="isActive ? '{{ __('admin.account_active_desc') }}' : '{{ __('admin.account_disabled_desc') }}'"></span>
                                                    </div>
                                                    <button type="button" @click="isActive = !isActive"
                                                        :class="isActive ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-600'"
                                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                        <span class="sr-only">Use setting</span>
                                                        <span aria-hidden="true"
                                                            :class="isActive ? 'translate-x-5' : 'translate-x-0'"
                                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                                                    </button>
                                                    <input type="checkbox" wire:model="is_active" class="hidden">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Section: Shift Management --}}
                                    <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
                                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">{{ __('admin.section_shift_management') }}</h4>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            {{-- Upcoming Shifts --}}
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('admin.assign_upcoming_shift_title') }}</label>
                                                <div class="relative rounded-md shadow-sm mb-2">
                                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                        <x-heroicon-o-magnifying-glass class="h-4 w-4 text-gray-400" />
                                                    </div>
                                                    <input x-ref="shiftSearch" type="text"
                                                        class="block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-xs py-2"
                                                        placeholder="{{ __('admin.search_shifts_placeholder') }}"
                                                        x-on:input="$refs.shiftList.querySelectorAll('li').forEach(li => { li.style.display = li.textContent.toLowerCase().includes($event.target.value.toLowerCase()) ? '' : 'none'; });">
                                                </div>

                                                <div class="h-40 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700/30">
                                                    <ul x-ref="shiftList" class="divide-y divide-gray-200 dark:divide-gray-600">
                                                        <li>
                                                            <label class="flex items-center px-4 py-2 hover:bg-white dark:hover:bg-gray-600 cursor-pointer transition-colors">
                                                                <input type="radio" wire:model.live="selected_shift_id" value="" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                                <span class="ml-3 block text-sm font-medium text-gray-500 italic">{{ __('admin.no_upcoming_shift') }}</span>
                                                            </label>
                                                        </li>
                                                        @foreach ($this->availableShifts as $shift)
                                                            <li wire:key="shift-option-{{ $shift->id }}">
                                                                <label class="flex items-center px-4 py-2 hover:bg-white dark:hover:bg-gray-600 cursor-pointer transition-colors">
                                                                    <input type="radio" wire:model.live="selected_shift_id" value="{{ $shift->id }}" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                                    <div class="ml-3">
                                                                        <span class="block text-sm font-medium text-gray-900 dark:text-white">{{ $shift->shift_date->format('D, M j') }}</span>
                                                                        <span class="block text-xs text-gray-500">{{ $shift->start_time->format('H:i') }} - {{ $shift->end_time->format('H:i') }}</span>
                                                                    </div>
                                                                </label>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                                @error('selected_shift_id') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                                            </div>

                                            {{-- History --}}
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('admin.shift_history_title') }}</label>
                                                <div class="h-[10.5rem] overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700/30 p-2">
                                                    @if ($userShiftHistory->isNotEmpty())
                                                        <ul class="space-y-2">
                                                            @foreach ($userShiftHistory as $pastShift)
                                                                <li class="flex items-center justify-between px-3 py-2 bg-white dark:bg-gray-600 rounded shadow-sm border border-gray-100 dark:border-gray-500">
                                                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-200">{{ $pastShift->shift_date->format('M j, Y') }}</span>
                                                                    <span class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $pastShift->start_time->format('H:i') }}-{{ $pastShift->end_time->format('H:i') }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <div class="flex h-full flex-col items-center justify-center text-center">
                                                            <x-heroicon-o-clock class="h-8 w-8 text-gray-300" />
                                                            <p class="mt-1 text-xs text-gray-500">{{ __('admin.no_past_shifts_recorded') }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                {{-- Footer Actions --}}
                                <div class="bg-gray-50 dark:bg-gray-700/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 rounded-b-2xl border-t border-gray-100 dark:border-gray-700">
                                    <button type="submit"
                                        wire:loading.attr="disabled"
                                        class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span wire:loading.remove wire:target="updateUser">{{ __('admin.modal_button_save_changes') }}</span>
                                        <span wire:loading wire:target="updateUser" class="flex items-center">
                                            <x-heroicon-o-arrow-path class="animate-spin -ml-1 mr-2 h-4 w-4" />
                                            {{ __('admin.modal_button_saving') }}
                                        </span>
                                    </button>
                                    <button type="button" @click="$wire.call('closeEditModal')" class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-gray-800 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto">
                                        {{ __('admin.modal_button_cancel') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</main>
