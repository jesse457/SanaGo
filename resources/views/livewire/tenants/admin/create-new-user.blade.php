<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4">
                <div class="flex flex-col gap-2">
                    {{-- Breadcrumbs --}}
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('admin.dashboard') }}" wire:navigate
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    {{ __('admin.home') }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <a href="{{ route('admin.user-management') }}" wire:navigate
                                        class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        {{ __('admin.manage_users_breadcrumb') }}
                                    </a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">{{ __('admin.add_new_user_breadcrumb') }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            {{ __('admin.create_user_profile_title') }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                            Fill in the information below to register a new staff member. Ensure all required fields are accurate to set up their access permissions.
                        </p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="p-4 sm:p-6 pb-20">
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                <form wire:submit.prevent="saveUser" class="p-6 sm:p-8">

                    {{-- Error Alert --}}
                    @if (session()->has('error'))
                        <div class="p-4 mb-8 text-sm text-red-800 rounded-xl bg-red-50 dark:bg-red-900/20 dark:text-red-300 border border-red-100 dark:border-red-800 flex items-start gap-3"
                            role="alert">
                            <x-heroicon-m-exclamation-circle class="w-5 h-5 flex-shrink-0 mt-0.5" />
                            <div>
                                <span class="font-bold">{{ __('admin.error_prefix') }}</span> {{ __('admin.session_error') }}
                            </div>
                        </div>
                    @endif

                    {{-- Section 1: Personal Information --}}
                    <div class="pb-8">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-6 border-b border-slate-100 dark:border-gray-800 pb-3">
                            <div class="p-1.5 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                <x-heroicon-o-user-circle class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            </div>
                            {{ __('admin.section_personal_info') }}
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            {{-- Full Name --}}
                            <div class="space-y-1.5">
                                <label for="userName" class="block text-sm font-bold text-slate-700 dark:text-slate-300">
                                    {{ __('admin.label_full_name') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <x-heroicon-o-user class="h-5 w-5 text-slate-400" />
                                    </div>
                                    <input type="text" id="userName" wire:model.live.debounce.300ms="name"
                                        placeholder="{{ __('admin.placeholder_full_name') }}"
                                        class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2.5 pl-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all duration-200">
                                </div>
                                @error('name') <p class="text-red-500 text-xs font-medium">{{ $message }}</p> @enderror
                            </div>

                            {{-- Phone Number --}}
                            <div class="space-y-1.5">
                                <label for="userPhone" class="block text-sm font-bold text-slate-700 dark:text-slate-300">
                                    {{ __('admin.label_phone_number') }}
                                </label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <x-heroicon-o-phone class="h-5 w-5 text-slate-400" />
                                    </div>
                                    <input type="text" id="userPhone" wire:model="phone_number"
                                        placeholder="{{ __('admin.placeholder_phone_number') }}"
                                        class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2.5 pl-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all duration-200">
                                </div>
                                @error('phone_number') <p class="text-red-500 text-xs font-medium">{{ $message }}</p> @enderror
                            </div>

                            {{-- Email --}}
                            <div class="space-y-1.5">
                                <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300">
                                    {{ __('admin.label_email') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <x-heroicon-o-envelope class="h-5 w-5 text-slate-400" />
                                    </div>
                                    <input type="email" id="email" wire:model.live.debounce.300ms="email"
                                        placeholder="{{ __('admin.placeholder_email') }}"
                                        class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2.5 pl-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all duration-200">
                                </div>
                                @error('email') <p class="text-red-500 text-xs font-medium">{{ $message }}</p> @enderror
                            </div>

                            {{-- Address --}}
                            <div class="space-y-1.5">
                                <label for="userAddress" class="block text-sm font-bold text-slate-700 dark:text-slate-300">
                                    {{ __('admin.label_address') }}
                                </label>
                                <div class="relative">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <x-heroicon-o-map-pin class="h-5 w-5 text-slate-400" />
                                    </div>
                                    <input type="text" id="userAddress" wire:model="address"
                                        placeholder="{{ __('admin.placeholder_address') }}"
                                        class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2.5 pl-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all duration-200">
                                </div>
                                @error('address') <p class="text-red-500 text-xs font-medium">{{ $message }}</p> @enderror
                            </div>

                            {{-- Gender --}}
                            <div class="space-y-1.5">
                                <label for="userGender" class="block text-sm font-bold text-slate-700 dark:text-slate-300">
                                    {{ __('admin.label_gender') }}
                                </label>
                                <select id="userGender" wire:model="gender"
                                    class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2.5 pl-3 pr-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all duration-200">
                                    <option value="">{{ __('admin.select_gender') }}</option>
                                    <option value="Male">{{ __('admin.gender_male') }}</option>
                                    <option value="Female">{{ __('admin.gender_female') }}</option>
                                    <option value="Other">{{ __('admin.gender_other') }}</option>
                                </select>
                                @error('gender') <p class="text-red-500 text-xs font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- ** S3 Optimized Image Upload Section --}}
                        <div class="mt-8" x-data="{
                            isUploading: false,
                            progress: 0,
                            isDragging: false,
                            imagePreviewUrl: null,
                            init() {
                                @if ($profile_picture && !is_string($profile_picture)) this.imagePreviewUrl = '{{ $profile_picture->temporaryUrl() }}'; @endif
                            },
                            handleFile(file) {
                                if (!file) return;
                                if (!['image/png', 'image/jpeg', 'image/jpg', 'image/gif'].includes(file.type)) {
                                    alert('{{ __('admin.upload_error_invalid_type') }}');
                                    return;
                                }
                                if (file.size > 2 * 1024 * 1024) {
                                    alert('{{ __('admin.upload_error_max_size') }}');
                                    return;
                                }
                                this.imagePreviewUrl = URL.createObjectURL(file);
                                this.$wire.upload('profile_picture', file,
                                    (uploadedFilename) => { /* Success */ },
                                    () => { alert('{{ __('admin.upload_error_failed') }}'); this.imagePreviewUrl = null; },
                                    (event) => { this.progress = event.detail.progress; }
                                );
                            }
                        }"
                            x-on:livewire-upload-start="isUploading = true"
                            x-on:livewire-upload-finish="isUploading = false"
                            x-on:livewire-upload-error="isUploading = false"
                            x-on:livewire-upload-progress="progress = $event.detail.progress">

                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">
                                {{ __('admin.label_profile_picture') }}
                            </label>

                            <div class="flex items-start gap-6 bg-slate-50 dark:bg-gray-800/50 p-5 rounded-xl border border-slate-100 dark:border-gray-800">
                                {{-- Circle Preview --}}
                                <div class="relative">
                                    <div class="w-24 h-24 rounded-full overflow-hidden bg-white dark:bg-gray-700 border-4 border-white dark:border-gray-600 shadow-md flex items-center justify-center">
                                        <template x-if="imagePreviewUrl">
                                            <img :src="imagePreviewUrl" class="object-cover w-full h-full">
                                        </template>
                                        <template x-if="!imagePreviewUrl">
                                            <x-heroicon-o-photo class="h-8 w-8 text-slate-300 dark:text-slate-500" />
                                        </template>
                                    </div>
                                    {{-- Loading Spinner Overlay --}}
                                    <div x-show="isUploading" class="absolute inset-0 rounded-full bg-black/40 flex items-center justify-center z-10">
                                        <x-heroicon-o-arrow-path class="animate-spin h-6 w-6 text-white" />
                                    </div>
                                </div>

                                <div class="flex-1">
                                    {{-- Drag & Drop Area --}}
                                    <div @dragover.prevent="isDragging = true" @dragleave.prevent="isDragging = false"
                                        @drop.prevent="isDragging = false; handleFile($event.dataTransfer.files[0])"
                                        class="relative w-full">

                                        <div class="relative flex flex-col justify-center items-center w-full h-32 px-6 border-2 border-dashed rounded-xl transition-all duration-200 ease-in-out"
                                            :class="isDragging ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/10' : 'border-slate-300 dark:border-gray-600 bg-white dark:bg-gray-800 hover:border-slate-400'">

                                            <div class="space-y-2 text-center">
                                                <div class="flex text-sm text-slate-600 dark:text-slate-400 justify-center">
                                                    <label for="userProfilePic"
                                                        class="relative cursor-pointer rounded-md font-bold text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                        <span>{{ __('admin.upload_file_button') }}</span>
                                                        <input id="userProfilePic" type="file" class="sr-only"
                                                            accept="image/*" @change="handleFile($event.target.files[0])">
                                                    </label>
                                                    <p class="pl-1">{{ __('admin.upload_drag_drop') }}</p>
                                                </div>
                                                <p class="text-xs text-slate-500">PNG, JPG, GIF up to 2MB</p>
                                            </div>

                                            {{-- Clear Button --}}
                                            <div x-show="imagePreviewUrl" class="absolute top-2 right-2">
                                                <button type="button"
                                                    @click="imagePreviewUrl = null; $wire.set('profile_picture', null)"
                                                    class="text-slate-400 hover:text-red-500 transition-colors p-1 bg-white dark:bg-gray-700 rounded-full shadow-sm">
                                                    <x-heroicon-s-x-mark class="w-4 h-4" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    @error('profile_picture') <p class="text-red-500 text-xs font-medium mt-1.5">{{ $message }}</p> @enderror

                                    {{-- Progress Bar --}}
                                    <div x-show="isUploading" class="w-full bg-slate-200 dark:bg-gray-700 rounded-full h-1.5 mt-3 overflow-hidden">
                                        <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-300"
                                            :style="`width: ${progress}%`"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Employment Details --}}
                    <div class="border-t border-slate-100 dark:border-gray-800 pt-8">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 mb-6 border-b border-slate-100 dark:border-gray-800 pb-3">
                            <div class="p-1.5 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                <x-heroicon-o-briefcase class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                            </div>
                            {{ __('admin.section_employment_details') }}
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            {{-- Department --}}
                            <div class="space-y-1.5">
                                <label for="userDepartment" class="block text-sm font-bold text-slate-700 dark:text-slate-300">
                                    {{ __('admin.label_department') }} <span class="text-red-500">*</span>
                                </label>
                                <select id="userDepartment" wire:model="department_id"
                                    class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2.5 pl-3 pr-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all duration-200">
                                    <option value="">{{ __('admin.select_department') }}</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @error('department_id') <p class="text-red-500 text-xs font-medium">{{ $message }}</p> @enderror
                            </div>

                            {{-- Role --}}
                            <div class="space-y-1.5">
                                <label for="userRole" class="block text-sm font-bold text-slate-700 dark:text-slate-300">
                                    {{ __('admin.label_role') }} <span class="text-red-500">*</span>
                                </label>
                                <select id="userRole" wire:model="role"
                                    class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white py-2.5 pl-3 pr-10 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm transition-all duration-200">
                                    <option value="">{{ __('admin.select_role') }}</option>
                                    <option value="admin">{{ __('admin.role_admin') }}</option>
                                    <option value="doctor">{{ __('admin.role_doctor') }}</option>
                                    <option value="nurse">{{ __('admin.role_nurse') }}</option>
                                    <option value="receptionist">{{ __('admin.role_receptionist') }}</option>
                                    <option value="lab-technician">{{ __('admin.role_lab_technician') }}</option>
                                    <option value="pharmacist">{{ __('admin.role_pharmacist') }}</option>
                                </select>
                                @error('role') <p class="text-red-500 text-xs font-medium">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-3 mt-10 pt-6 border-t border-slate-100 dark:border-gray-800">
                        <button type="button" wire:click="redirectToUserManagement"
                            class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl bg-white dark:bg-gray-800 px-6 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-gray-600 hover:bg-slate-50 dark:hover:bg-gray-700 transition-all duration-200">
                            {{ __('admin.button_cancel') }}
                        </button>
                        <button type="submit"
                            class="w-full sm:w-auto inline-flex justify-center items-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-700 hover:shadow-lg focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all duration-200"
                            wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-not-allowed">
                            <span wire:loading.remove wire:target="saveUser" class="flex items-center gap-2">
                                <x-heroicon-o-check class="w-5 h-5" />
                                {{ __('admin.button_create_user') }}
                            </span>
                            <span wire:loading wire:target="saveUser" class="flex items-center gap-2">
                                <x-heroicon-o-arrow-path class="animate-spin h-5 w-5" />
                                {{ __('admin.button_creating') }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
