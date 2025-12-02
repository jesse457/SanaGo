<div class="flex-1 p-4 bg-slate-50 dark:bg-gray-900 overflow-y-auto min-h-screen">
    {{-- Breadcrumbs & Header --}}
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}"
                        class="inline-flex items-center text-sm font-medium text-slate-700 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-white">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" />
                        {{ __('admin.home') }}
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-slate-400 mx-1" />
                        <a href="{{ route('admin.user-management') }}" wire:navigate
                            class="ms-1 text-sm font-medium text-slate-700 hover:text-indigo-600 md:ms-2 dark:text-slate-400 dark:hover:text-white">{{ __('admin.manage_users_breadcrumb') }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-slate-400 mx-1" />
                        <span class="ms-1 text-sm font-medium text-slate-500 md:ms-2 dark:text-slate-400">{{ __('admin.add_new_user_breadcrumb') }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h2 class="text-3xl font-bold text-slate-800 mt-4">{{ __('admin.create_user_profile_title') }}</h2>
    </div>

    {{-- Main Form Card --}}
    <div class="bg-white rounded-xl shadow-lg">
        <form wire:submit.prevent="saveUser" class="p-6 sm:p-8">

            @if (session()->has('error'))
                <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400"
                    role="alert">
                    <span class="font-medium">{{ __('admin.error_prefix') }}</span> {{ __('admin.session_error') }}
                </div>
            @endif

            {{-- Section 1: Personal Information --}}
            <div class="pb-8">
                <h3 class="text-lg font-semibold leading-7 text-slate-900 flex items-center gap-3 mb-6">
                    <x-heroicon-o-user-circle class="w-6 h-6 text-indigo-600" />
                    {{ __('admin.section_personal_info') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    {{-- Full Name --}}
                    <div>
                        <label for="userName" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.label_full_name') }}</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-s-user class="h-5 w-5 text-slate-400" />
                            </div>
                            <input type="text" id="userName" wire:model.live.debounce.300ms="name"
                                placeholder="{{ __('admin.placeholder_full_name') }}"
                                class=" block w-full rounded-lg border border-slate-300 py-2.5 pl-10 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200">
                        </div>
                        @error('name')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone Number --}}
                    <div>
                        <label for="userPhone" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.label_phone_number') }}</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-s-phone class="h-5 w-5 text-slate-400" />
                            </div>
                            <input type="text" id="userPhone" wire:model="phone_number"
                                placeholder="{{ __('admin.placeholder_phone_number') }}"
                                class=" block w-full rounded-lg border border-slate-300 py-2.5 pl-10 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200">
                        </div>
                        @error('phone_number')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.label_email') }}</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-s-user class="h-5 w-5 text-slate-400" />
                            </div>
                            <input type="email" id="email" wire:model.live.debounce.300ms="email"
                                placeholder="{{ __('admin.placeholder_email') }}"
                                class=" block w-full rounded-lg border border-slate-300 py-2.5 pl-10 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200">
                        </div>
                        @error('email')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div>
                        <label for="userAddress" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.label_address') }}</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-s-map-pin class="h-5 w-5 text-slate-400" />
                            </div>
                            <input type="text" id="userAddress" wire:model="address"
                                placeholder="{{ __('admin.placeholder_address') }}"
                                class=" block w-full rounded-lg border border-slate-300 py-2.5 pl-10 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200">
                        </div>
                        @error('address')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Gender --}}
                    <div>
                        <label for="userGender" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.label_gender') }}</label>
                        <select id="userGender" wire:model="gender"
                            class="form-select block w-full rounded-lg border-slate-300 py-2.5 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200">
                            <option value="">{{ __('admin.select_gender') }}</option>
                            <option value="Male">{{ __('admin.gender_male') }}</option>
                            <option value="Female">{{ __('admin.gender_female') }}</option>
                            <option value="Other">{{ __('admin.gender_other') }}</option>
                        </select>
                        @error('gender')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- ** S3 Optimized Image Upload Section --}}
                <div class="md:col-span-2 mt-8"
                    x-data="{
                        isUploading: false,
                        progress: 0,
                        isDragging: false,
                        imagePreviewUrl: null,
                        init() {
                            // If Livewire already has a temporary URL (from S3), use it
                            @if ($profile_picture && !is_string($profile_picture))
                                this.imagePreviewUrl = '{{ $profile_picture->temporaryUrl() }}';
                            @endif
                        },
                        handleFile(file) {
                            if (!file) return;

                            // Basic validation
                            if (!['image/png', 'image/jpeg', 'image/jpg', 'image/gif'].includes(file.type)) {
                                alert('{{ __('admin.upload_error_invalid_type') }}');
                                return;
                            }
                            if (file.size > 2 * 1024 * 1024) {
                                alert('{{ __('admin.upload_error_max_size') }}');
                                return;
                            }

                            // 1. Show Local Preview Immediately (Instant UI feedback)
                            this.imagePreviewUrl = URL.createObjectURL(file);

                            // 2. Upload to S3 via Livewire
                            this.$wire.upload('profile_picture', file,
                                (uploadedFilename) => {
                                    // Success: Livewire now has the file on S3.
                                    // We don't need to do anything else, Blade will handle the URL on next render.
                                },
                                () => {
                                    alert('{{ __('admin.upload_error_failed') }}');
                                    this.imagePreviewUrl = null;
                                },
                                (event) => {
                                    this.progress = event.detail.progress;
                                }
                            );
                        }
                    }"
                    x-on:livewire-upload-start="isUploading = true"
                    x-on:livewire-upload-finish="isUploading = false"
                    x-on:livewire-upload-error="isUploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress">

                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.label_profile_picture') }}</label>
                    <div class="flex items-center gap-6">

                        {{-- Circle Preview --}}
                        <div class="w-24 h-24 rounded-full overflow-hidden flex-shrink-0 bg-slate-100 border-2 border-dashed border-slate-300 flex items-center justify-center relative">
                            <!-- Image Display -->
                            <template x-if="imagePreviewUrl">
                                <img :src="imagePreviewUrl" class="object-cover w-full h-full">
                            </template>
                            <!-- Placeholder Icon -->
                            <template x-if="!imagePreviewUrl">
                                <x-heroicon-o-photo class="h-8 w-8 text-slate-400" />
                            </template>
                            <!-- Loading Spinner -->
                            <div x-show="isUploading" class="absolute inset-0 bg-black/30 flex items-center justify-center">
                                <svg class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>

                        <div class="flex-1">
                            {{-- Drag & Drop Area --}}
                            <div
                                @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="isDragging = false; handleFile($event.dataTransfer.files[0])"
                                class="relative w-full">

                                <div class="relative flex flex-col justify-center items-center w-full h-48 px-6 pt-5 pb-6 border-2 border-dashed rounded-lg transition-all duration-300 ease-in-out"
                                    :class="isDragging ? 'border-indigo-500 bg-indigo-50 scale-[1.01]' : 'border-gray-300 bg-white hover:border-gray-400'">

                                    <!-- Main Upload UI -->
                                    <div class="space-y-3 text-center">
                                        <div class="flex justify-center">
                                            <template x-if="!imagePreviewUrl">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                </svg>
                                            </template>
                                            <template x-if="imagePreviewUrl">
                                                <img :src="imagePreviewUrl" class="h-20 w-auto rounded-md shadow-sm border border-gray-200">
                                            </template>
                                        </div>

                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="userProfilePic" class="relative cursor-pointer rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>{{ __('admin.upload_file_button') }}</span>
                                                <input id="userProfilePic" type="file" class="sr-only" accept="image/*"
                                                    @change="handleFile($event.target.files[0])">
                                            </label>
                                            <p class="pl-1">{{ __('admin.upload_drag_drop') }}</p>
                                        </div>
                                        <p class="text-xs text-gray-500">{{ __('admin.upload_requirements') }}</p>
                                    </div>

                                    <!-- Clear Button (Only shows if image exists) -->
                                    <div x-show="imagePreviewUrl" class="absolute top-2 right-2">
                                        <button type="button" @click="imagePreviewUrl = null; $wire.set('profile_picture', null)" class="text-gray-400 hover:text-red-500 transition">
                                            <x-heroicon-s-x-circle class="w-6 h-6" />
                                        </button>
                                    </div>
                                </div>
                            </div>

                            @error('profile_picture')
                                <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                            @enderror

                            {{-- Progress Bar --}}
                            <div x-show="isUploading" class="w-full bg-slate-200 rounded-full h-2.5 mt-3">
                                <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300" :style="`width: ${progress}%`"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Employment Details --}}
            <div class="border-t border-slate-200 pt-8">
                <h3 class="text-lg font-semibold leading-7 text-slate-900 flex items-center gap-3 mb-6">
                    <x-heroicon-o-briefcase class="w-6 h-6 text-indigo-600" />
                    {{ __('admin.section_employment_details') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    {{-- Department --}}
                    <div>
                        <label for="userDepartment" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.label_department') }}</label>
                        <select id="userDepartment" wire:model="department_id" class="form-select block w-full rounded-lg border-slate-300 py-2.5 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200">
                            <option value="">{{ __('admin.select_department') }}</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                        @error('department_id')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Hire Date --}}
                    <div>
                        <label for="userHireDate" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.label_hire_date') }}</label>
                        <input type="date" id="userHireDate" wire:model="hire_date" class=" block w-full rounded-lg border-slate-300 py-2.5 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200">
                        @error('hire_date')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <label for="userRole" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.label_role') }}</label>
                        <select id="userRole" wire:model="role" class="form-select block w-full rounded-lg border-slate-300 py-2.5 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200">
                            <option value="">{{ __('admin.select_role') }}</option>
                            <option value="admin">{{ __('admin.role_admin') }}</option>
                            <option value="doctor">{{ __('admin.role_doctor') }}</option>
                            <option value="nurse">{{ __('admin.role_nurse') }}</option>
                            <option value="receptionist">{{ __('admin.role_receptionist') }}</option>
                            <option value="lab-technician">{{ __('admin.role_lab_technician') }}</option>
                            <option value="pharmacist">{{ __('admin.role_pharmacist') }}</option>
                        </select>
                        @error('role')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Is Active Toggle Switch --}}
                    <div class="flex items-center" x-data="{ enabled: @entangle('is_active') }">
                        <label class="block text-sm font-medium text-slate-700 mr-4">{{ __('admin.label_account_status') }}</label>
                        <button type="button" @click="enabled = !enabled"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
                            :class="enabled ? 'bg-indigo-600' : 'bg-slate-200'" role="switch"
                            :aria-checked="enabled.toString()">
                            <span aria-hidden="true" :class="enabled ? 'translate-x-5' : 'translate-x-0'"
                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                        <span class="ml-3 text-sm font-medium" :class="enabled ? 'text-indigo-600' : 'text-slate-500'"
                            x-text="enabled ? '{{ __('admin.status_active_toggle') }}' : '{{ __('admin.status_inactive_toggle') }}'"></span>
                    </div>

                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end gap-4 mt-10 pt-6 border-t border-slate-200">
                <button type="button" wire:click="redirectToUserManagement"
                    class="rounded-lg bg-white px-6 py-2.5 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 transition-all duration-200 flex items-center gap-2">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                    {{ __('admin.button_cancel') }}
                </button>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2.5 rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-200 transform hover:-translate-y-0.5"
                    wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-not-allowed">
                    <div wire:loading wire:target="saveUser">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ __('admin.button_creating') }}</span>
                    </div>
                    <span wire:loading.remove wire:target="saveUser" class="flex items-center gap-2">
                        <x-heroicon-o-check class="w-5 h-5" />
                        {{ __('admin.button_create_user') }}
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
