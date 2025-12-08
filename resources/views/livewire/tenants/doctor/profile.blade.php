<main class="flex-1 flex flex-col h-screen overflow-hidden bg-gray-50 dark:bg-gray-900">

    {{-- 1. HEADER SECTION (Sticky) --}}
    <header class="flex-shrink-0 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm z-20">
        <div class="px-6 py-5 flex items-center justify-between">
            <div>
                <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                    <ol class="flex items-center space-x-2">
                        <li>
                            <a href="{{ route('doctor.dashboard') }}" wire:navigate class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                {{ __('doctor.home') }}
                            </a>
                        </li>
                        <li><x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300" /></li>
                        <li class="text-gray-900 dark:text-white">{{ __('doctor.profile') }}</li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">
                    {{ __('doctor.account_settings') }}
                </h1>
            </div>
        </div>
    </header>

    {{-- 2. MAIN SCROLLABLE CONTENT --}}
    <div class="flex-1 overflow-y-auto p-6 lg:p-8 custom-scrollbar">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8">

            {{-- LEFT COLUMN: Profile Summary Card --}}
            <div class="lg:col-span-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-6">

                    {{-- Decorative Banner --}}
                    <div class="h-32 bg-gradient-to-r from-blue-600 to-indigo-600 relative">
                        <div class="absolute inset-0 bg-white/10" style="background-image: radial-gradient(circle, transparent 20%, #ffffff 20%, #ffffff 80%, transparent 80%, transparent); background-size: 20px 20px; opacity: 0.1;"></div>
                    </div>

                    {{-- Avatar & Identity --}}
                    <div class="px-6 pb-6 text-center relative">
                        <div class="relative -mt-16 mb-4 inline-block">
                            @if(Auth::user()->profile_picture)
                                <img src="{{ Storage::disk('s3')->temporaryUrl(Auth::user()->profile_picture, now()->addMinutes(60)) }}"
                                     alt="Profile"
                                     class="w-32 h-32 rounded-full object-cover border-4 border-white dark:border-gray-800 shadow-lg">
                            @else
                                <div class="w-32 h-32 rounded-full bg-blue-100 dark:bg-blue-900/50 border-4 border-white dark:border-gray-800 shadow-lg flex items-center justify-center text-4xl font-bold text-blue-600 dark:text-blue-400">
                                    {{ substr($name ?? 'U', 0, 1) }}
                                </div>
                            @endif

                            {{-- Edit Avatar Button (Visual only unless functionality added) --}}
                            <label for="avatar-upload" class="absolute bottom-1 right-1 p-2 bg-white dark:bg-gray-700 rounded-full shadow-md border border-gray-100 dark:border-gray-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                <x-heroicon-m-camera class="w-4 h-4 text-gray-600 dark:text-gray-300" />
                                <input type="file" id="avatar-upload" class="hidden">
                            </label>
                        </div>

                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $name }}</h2>
                        <div class="flex items-center justify-center gap-2 mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                                {{ ucfirst(Auth::user()->role ?? __('doctor.user')) }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ $email }}</p>

                        {{-- Quick Stats / Info --}}
                        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 text-left">{{ __('doctor.contact_details') }}</h4>

                            <ul class="space-y-4 text-sm">
                                <li class="flex items-start">
                                    <x-heroicon-o-phone class="w-5 h-5 text-gray-400 mr-3 mt-0.5" />
                                    <div class="flex-1 text-left">
                                        <p class="text-gray-500 dark:text-gray-400 text-xs">{{ __('doctor.phone') }}</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $phone_number ?? 'Not provided' }}</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <x-heroicon-o-map-pin class="w-5 h-5 text-gray-400 mr-3 mt-0.5" />
                                    <div class="flex-1 text-left">
                                        <p class="text-gray-500 dark:text-gray-400 text-xs">{{ __('doctor.address') }}</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $address ?? 'Not provided' }}</p>
                                    </div>
                                </li>
                                <li class="flex items-start">
                                    <x-heroicon-o-building-office class="w-5 h-5 text-gray-400 mr-3 mt-0.5" />
                                    <div class="flex-1 text-left">
                                        <p class="text-gray-500 dark:text-gray-400 text-xs">Facility</p>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ tenant('hospital_name') ?? 'Main Clinic' }}</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Forms --}}
            <div class="lg:col-span-8 space-y-8">

                {{-- Personal Information Form --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <x-heroicon-o-user-circle class="w-5 h-5 text-blue-500" />
                            {{ __('doctor.personal_information') }}
                        </h3>
                    </div>

                    <div class="p-6">
                        <form wire:submit.prevent="updateProfile" class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Name --}}
                            <div class="col-span-1 md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('doctor.full_name') }}</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-heroicon-o-user class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <input type="text" wire:model="name" id="name"
                                        class="block w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                                </div>
                                @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('doctor.email') }}</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-heroicon-o-envelope class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <input type="email" wire:model="email" id="email"
                                        class="block w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                                </div>
                                @error('email') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Phone --}}
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('doctor.phone') }}</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-heroicon-o-phone class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <input type="text" wire:model="phone_number" id="phone"
                                        class="block w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                                </div>
                                @error('phone_number') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Address --}}
                            <div class="col-span-1 md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('doctor.address') }}</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-heroicon-o-map-pin class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <input type="text" wire:model="address" id="address"
                                        class="block w-full pl-10 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                                </div>
                                @error('address') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Action --}}
                            <div class="col-span-1 md:col-span-2 flex justify-end pt-4 border-t border-gray-100 dark:border-gray-700">
                                <button type="submit" wire:loading.attr="disabled"
                                    class="inline-flex items-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50">
                                    <span wire:loading.remove wire:target="updateProfile">{{ __('doctor.save_changes') }}</span>
                                    <span wire:loading wire:target="updateProfile" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Saving...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Security Form --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <x-heroicon-o-lock-closed class="w-5 h-5 text-red-500" />
                            {{ __('doctor.security_settings') }}
                        </h3>
                    </div>

                    <div class="p-6">
                        <form wire:submit.prevent="updatePassword" class="space-y-6">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('doctor.current_password') }}</label>
                                <input type="password" wire:model="current_password"
                                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm py-2.5">
                                @error('current_password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('doctor.new_password') }}</label>
                                    <input type="password" wire:model="new_password"
                                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm py-2.5">
                                    @error('new_password') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('doctor.confirm_password') }}</label>
                                    <input type="password" wire:model="new_password_confirmation"
                                        class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm py-2.5">
                                </div>
                            </div>

                            <div class="flex justify-end pt-4 border-t border-gray-100 dark:border-gray-700">
                                <button type="submit" wire:loading.attr="disabled"
                                    class="inline-flex items-center px-6 py-2.5 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-gray-800 hover:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors disabled:opacity-50">
                                    <span wire:loading.remove wire:target="updatePassword">{{ __('doctor.update_password') }}</span>
                                    <span wire:loading wire:target="updatePassword" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Updating...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</main>
