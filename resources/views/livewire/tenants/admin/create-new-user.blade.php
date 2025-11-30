<div class="flex-1 p-4 bg-slate-50 dark:bg-gray-900 overflow-y-auto min-h-screen" x-data>
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
    <div id="addUserFormContainer" class="bg-white rounded-xl shadow-lg">
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
                        <label for="userPhone" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.label_phone_number') }}
                            </label>
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

                {{-- ** NEW ** Interactive Profile Picture Upload with S3 Support --}}
                <div class="md:col-span-2 mt-8" x-data="{
                    isUploading: false,
                    progress: 0,
                    isDragging: false,
                    imagePreviewUrl: null,
                    s3TempUrl: null,
                    showError: false,
                    errorMessage: ''
                }"
                x-on:livewire-upload-start="isUploading = true"
                x-on:livewire-upload-finish="isUploading = false; progress = 0;"
                x-on:livewire-upload-error="isUploading = false; progress = 0; showError = true; errorMessage = '{{ __('admin.upload_error_failed') }}';"
                x-on:livewire-upload-progress="progress = $event.detail.progress">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.label_profile_picture') }}</label>
                    <div class="flex items-center gap-6">
                        {{-- Image Preview Circle --}}
                        <div
                            class="w-24 h-24 rounded-full overflow-hidden flex-shrink-0 bg-slate-100 border-2 border-dashed border-slate-300 flex items-center justify-center">
                            <template x-if=" {{ json_encode($profile_picture != null) }}">
                                <img :src="'{{ $profile_picture ? $profile_picture->temporaryUrl() : '' }}'"
                                    alt="Preview" class="object-cover w-full h-full">
                            </template>
                            <template x-if="!imagePreviewUrl && !s3TempUrl && !{{ json_encode($profile_picture != null) }}">
                                <x-heroicon-o-photo class="h-8 w-8 text-slate-400" />
                            </template>
                        </div>
                        <div class="flex-1">
                            {{-- Drag & Drop Area --}}
                            <div @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="
        isDragging = false;
        const files = $event.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    "
                                class="relative w-full">
                                <div class="relative flex flex-col justify-center items-center w-full h-48 px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg transition-all duration-300 ease-in-out"
                                    :class="{
                                        'border-indigo-500 bg-indigo-50 scale-[1.02]': isDragging,
                                        'bg-white hover:border-gray-400': !isDragging
                                    }">
                                    <div x-show="imagePreviewUrl || s3TempUrl" class="absolute inset-0 rounded-lg overflow-hidden">
                                        <img :src="imagePreviewUrl || s3TempUrl" alt="Preview" class="w-full h-full object-cover">
                                        <div
                                            class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                                            <button @click="clearImage()" type="button"
                                                class="p-2 bg-white rounded-full shadow-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div x-show="!imagePreviewUrl && !s3TempUrl" class="space-y-3 text-center">
                                        <div class="flex justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="1.5"
                                                    d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                        </div>
                                        <div class="flex text-sm text-gray-600">
                                            <label for="userProfilePic"
                                                class="relative cursor-pointer rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500 transition-colors">
                                                <span>{{ __('admin.upload_file_button') }}</span>
                                                <input id="userProfilePic" wire:model="profile_picture"
                                                    name="profile_picture" type="file" class="sr-only"
                                                    accept="image/*" x-ref="fileInput"
                                                    x-on:change="
                            const file = $event.target.files[0];
                            if (file) {
                                handleFile(file);
                            }
                        ">
                                            </label>
                                            <p class="pl-1">{{ __('admin.upload_drag_drop') }}</p>
                                        </div>
                                        <p class="text-xs text-gray-500">{{ __('admin.upload_requirements') }}</p>
                                    </div>

                                    <div x-show="showError" x-transition
                                        class="absolute bottom-2 left-0 right-0 mx-4 p-2 bg-red-100 border border-red-400 text-red-700 rounded-md text-sm">
                                        <p x-text="errorMessage"></p>
                                    </div>
                                </div>

                                <script>
                                    function handleFile(file) {
                                        // Reset error state
                                        this.showError = false;
                                        this.errorMessage = '';

                                        // Check file type
                                        const validTypes = ['image/png', 'image/jpeg', 'image/gif'];
                                        if (!validTypes.includes(file.type)) {
                                            this.showError = true;
                                            this.errorMessage = '{{ __('admin.upload_error_invalid_type') }}';
                                            return;
                                        }

                                        // Check file size (2MB)
                                        const maxSize = 2 * 1024 * 1024;
                                        if (file.size > maxSize) {
                                            this.showError = true;
                                            this.errorMessage = '{{ __('admin.upload_error_max_size') }}';
                                            return;
                                        }

                                        // Create preview
                                        this.imagePreviewUrl = URL.createObjectURL(file);

                                        // Upload to S3 via Livewire
                                        this.$wire.upload('profile_picture', file, () => {
                                            // On success, get the S3 temporary URL
                                            this.s3TempUrl = this.$wire.get('profile_picture').temporaryUrl();
                                            // Clear local preview to use S3 URL
                                            this.imagePreviewUrl = null;
                                        }, (error) => {
                                            this.showError = true;
                                            this.errorMessage = error || '{{ __('admin.upload_error_failed') }}';
                                        }, (progress) => {
                                            this.progress = progress.percentage;
                                        });
                                    }

                                    function clearImage() {
                                        this.imagePreviewUrl = null;
                                        this.s3TempUrl = null;
                                        this.$refs.fileInput.value = '';
                                        this.$wire.set('profile_picture', null);
                                    }
                                </script>
                            </div>
                            @error('profile_picture')
                                <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                            {{-- Progress Bar --}}
                            <div x-show="isUploading" class="w-full bg-slate-200 rounded-full h-2.5 mt-3">
                                <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300" x-bind:style="`width: ${progress}%`">
                                </div>
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
                        <label for="userDepartment"
                            class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.label_department') }}</label>
                        <select id="userDepartment" wire:model="department_id"
                            class="form-select block w-full rounded-lg border-slate-300 py-2.5 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200">
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
                        <label for="userHireDate" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.label_hire_date') }}
                            </label>
                        <input type="date" id="userHireDate" wire:model="hire_date"
                            class=" block w-full rounded-lg border-slate-300 py-2.5 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200">
                        @error('hire_date')
                            <p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <label for="userRole" class="block text-sm font-medium text-slate-700 mb-1.5">{{ __('admin.label_role') }}</label>
                        <select id="userRole" wire:model="role"
                            class="form-select block w-full rounded-lg border-slate-300 py-2.5 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200">
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

                    {{-- ** NEW ** Is Active Toggle Switch --}}
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

            {{-- Form Actions / Footer --}}
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
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
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
