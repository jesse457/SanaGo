<main class="flex-1 p-4  bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors duration-150">
                        <x-heroicon-s-home class="h-4 w-4 me-2.5" />
                        {{ __('admin.home') }}
                    </a>
                </li>

                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-4 h-4 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm text-gray-400 md:ms-2 dark:text-gray-200">{{ __('admin.profile_title') }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>
    {{-- Profile Sections --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Profile Card (Quick Info) --}}
        <div
            class="lg:col-span-1 bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 border border-gray-100 dark:border-gray-700 flex flex-col items-center text-center">

            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-1">{{ $name }}</h2>
            <p class="text-md text-blue-600 dark:text-blue-400 font-medium mb-3">
                {{-- Fallback to profile_role_default if role is empty --}}
                {{ ucfirst(Auth::user()->role ?? __('admin.profile_role_default')) }}
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $email }}</p>

            <div class="mt-6 w-full text-left border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-3">{{ __('admin.profile_quick_info') }}</h3>
                <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                    <p class="flex items-center"><x-heroicon-o-phone class="w-5 h-5 mr-2 text-gray-500" /> {{ __('admin.profile_info_phone') }}
                        <span class="ml-auto font-medium">{{ $phone_number ?? __('admin.profile_info_na') }}</span>
                    </p>
                    <p class="flex items-center"><x-heroicon-o-map-pin class="w-5 h-5 mr-2 text-gray-500" /> {{ __('admin.profile_info_address') }}
                        <span class="ml-auto font-medium">{{ $address ?? __('admin.profile_info_na') }}</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Details & Settings --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Personal Information Form --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6 border-b pb-3">{{ __('admin.profile_personal_info_title') }}
                </h3>
                <form wire:submit.prevent="updateProfile" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.profile_label_name') }}</label>
                        <input type="text" wire:model="name" id="name"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="email"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.profile_label_email') }}</label>
                        <input type="email" wire:model="email" id="email"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('email')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="phone"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.profile_label_phone') }}</label>
                        <input type="text" wire:model="phone_number" id="phone"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('phone_number')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="address"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.profile_label_address') }}</label>
                        <input type="text" wire:model="address" id="address"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('address')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-span-2 flex justify-end">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">{{ __('admin.profile_button_update_profile') }}</button>
                    </div>
                </form>
            </div>

            {{-- Change Password Form --}}
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-xl p-6 border border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-6  pb-3">{{ __('admin.profile_change_password_title') }}</h3>
                <form wire:submit.prevent="updatePassword" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.profile_label_current_password') }}</label>
                        <input type="password" wire:model="current_password"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('current_password') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.profile_label_new_password') }}</label>
                        <input type="password" wire:model="new_password"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('new_password')
                            <span class="text-sm text-red-600">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.profile_label_confirm_password') }}</label>
                        <input type="password" wire:model="new_password_confirmation"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">{{ __('admin.profile_button_update_password') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
