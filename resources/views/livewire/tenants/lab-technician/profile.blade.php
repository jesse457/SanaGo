<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4">
                <div class="flex flex-col gap-2">
                    {{-- Breadcrumbs --}}
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-1" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('lab-technician.dashboard') }}" wire:navigate
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    Home
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">Profile</span>
                                </div>
                            </li>
                        </ol>
                    </nav>

                    {{-- Title --}}
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                            User Profile
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 max-w-3xl">
                            Update your personal information and manage your account security settings.
                        </p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="p-4 sm:p-6 pb-20">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Profile Card --}}
                <div
                    class="lg:col-span-1 bg-white dark:bg-gray-900 shadow-sm rounded-2xl p-6 border border-slate-200 dark:border-gray-800 flex flex-col items-center text-center">

                    <div class="h-20 w-20 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-700 dark:text-blue-300 font-bold text-3xl ring-4 ring-white dark:ring-gray-900 shadow-lg mb-4">
                        {{ substr($name, 0, 1) }}
                    </div>

                    <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-1">{{ $name }}</h2>
                    <p class="text-sm font-bold text-blue-600 dark:text-blue-400 mb-3">
                        {{ ucfirst(Auth::user()->role ?? 'User') }}
                    </p>
                    <p class="text-sm text-slate-600 dark:text-slate-300">{{ $email }}</p>

                    <div class="mt-6 w-full text-left border-t border-slate-200 dark:border-gray-800 pt-6">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-3">Quick Info</h3>
                        <div class="space-y-3 text-sm font-medium text-slate-700 dark:text-slate-300">
                            <p class="flex items-center"><x-heroicon-o-phone class="w-5 h-5 mr-3 text-slate-500" /> Phone:
                                <span class="ml-auto font-bold">{{ $phone_number ?? 'N/A' }}</span>
                            </p>
                            <p class="flex items-center"><x-heroicon-o-map-pin class="w-5 h-5 mr-3 text-slate-500" /> Address:
                                <span class="ml-auto font-bold truncate max-w-[150px]">{{ $address ?? 'N/A' }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Details & Settings --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Personal Information --}}
                    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-2xl p-6 border border-slate-200 dark:border-gray-800">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-200 dark:border-gray-800 pb-3">Personal Information</h3>
                        <form wire:submit.prevent="updateProfile" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            <div class="space-y-1.5">
                                <label for="name" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Name</label>
                                <input type="text" wire:model="name" id="name"
                                    class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5"
                                    @error('name') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label for="email" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Email</label>
                                <input type="email" wire:model="email" id="email"
                                    class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                                @error('email') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label for="phone" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Phone</label>
                                <input type="text" wire:model="phone_number" id="phone"
                                    class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                                @error('phone_number') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-1.5">
                                <label for="address" class="block text-sm font-bold text-slate-700 dark:text-slate-300">Address</label>
                                <input type="text" wire:model="address" id="address"
                                    class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                                @error('address') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-2 flex justify-end pt-3 border-t border-slate-100 dark:border-gray-800">
                                <button type="submit"
                                    class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 shadow-md transition-all"
                                    wire:loading.attr="disabled">
                                    Update Profile
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Change Password --}}
                    <div class="bg-white dark:bg-gray-900 shadow-sm rounded-2xl p-6 border border-slate-200 dark:border-gray-800">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-6 border-b border-slate-200 dark:border-gray-800 pb-3">Change Password</h3>
                        <form wire:submit.prevent="updatePassword" class="space-y-4">
                            <div class="space-y-1.5">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Current Password</label>
                                <input type="password" wire:model="current_password"
                                    class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                                @error('current_password') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">New Password</label>
                                <input type="password" wire:model="new_password"
                                    class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                                @error('new_password')
                                    <span class="text-xs text-red-500 font-medium">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">Confirm Password</label>
                                <input type="password" wire:model="new_password_confirmation"
                                    class="block w-full rounded-xl border-slate-300 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                            </div>
                            <div class="flex justify-end pt-3 border-t border-slate-100 dark:border-gray-800">
                                <button type="submit"
                                    class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 shadow-md transition-all"
                                    wire:loading.attr="disabled">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
