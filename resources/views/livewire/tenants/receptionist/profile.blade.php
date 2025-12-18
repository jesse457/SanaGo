<main class="flex-1 overflow-x-hidden overflow-y-auto p-6 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto">
        {{-- Breadcrumbs --}}
        <div class="mb-6 mt-8">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                    <li class="inline-flex items-center">
                        <a href="{{ route('receptionist.dashboard') }}" wire:navigate
                            class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-300 transition-colors duration-150">
                            <x-heroicon-s-home class="w-4 h-4 me-2.5" />
                            Home
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                            <span class="ms-1 text-sm  text-gray-900 md:ms-2 dark:text-gray-400">
                                Profile</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white flex items-center gap-3">

                Profile
            </h1>
            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400 ">
                Manage your account settings, personal information, and view assigned shifts.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Profile Card & Quick Info --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/60 dark:border-gray-700 overflow-hidden">
                    <div class="p-8 flex flex-col items-center text-center">
                        <div class="relative">
                            <img class="h-24 w-24 rounded-full ring-4 ring-white dark:ring-gray-800 shadow-md object-cover"
                                 src="https://ui-avatars.com/api/?name={{ urlencode($name) }}&background=6366f1&color=fff&bold=true"
                                 alt="{{ $name }}">
                            <span class="absolute bottom-1 right-1 h-4 w-4 rounded-full bg-green-500 ring-2 ring-white dark:ring-gray-800"></span>
                        </div>

                        <h2 class="mt-4 text-xl font-bold text-gray-900 dark:text-white">{{ $name }}</h2>
                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 ring-1 ring-inset ring-indigo-700/10 dark:bg-indigo-900/30 dark:text-indigo-400 mt-2">
                            {{ ucfirst(Auth::user()->role ?? 'User') }}
                        </span>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $email }}</p>

                        <div class="w-full mt-8 border-t border-gray-100 dark:border-gray-700 pt-6">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white text-left mb-4 uppercase tracking-wider">Contact Details</h3>
                            <ul class="space-y-4 text-sm">
                                <li class="flex items-start justify-between">
                                    <span class="flex items-center text-gray-500 dark:text-gray-400">
                                        <x-heroicon-o-phone class="w-4 h-4 mr-2" /> Phone
                                    </span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $phone_number ?? 'Not provided' }}</span>
                                </li>
                                <li class="flex items-start justify-between">
                                    <span class="flex items-center text-gray-500 dark:text-gray-400">
                                        <x-heroicon-o-map-pin class="w-4 h-4 mr-2" /> Address
                                    </span>
                                    <span class="font-medium text-gray-900 dark:text-white text-right max-w-[150px] truncate">{{ $address ?? 'Not provided' }}</span>
                                </li>
                                <li class="flex items-start justify-between">
                                    <span class="flex items-center text-gray-500 dark:text-gray-400">
                                        <x-heroicon-o-calendar-days class="w-4 h-4 mr-2" /> Joined
                                    </span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ Auth::user()->created_at->format('M Y') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                {{-- My Shifts Card (Moved here for better layout balance on large screens) --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/60 dark:border-gray-700 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                        <h3 class="font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <x-heroicon-o-clock class="w-5 h-5 text-gray-400" />
                            Upcoming Shifts
                        </h3>
                    </div>
                    <div class="p-4 space-y-3">
                        @if (count($shifts))
                            @foreach ($shifts as $shift)
                                <div class="group relative flex gap-x-4 rounded-xl bg-gray-50 p-4 hover:bg-white border border-transparent hover:border-gray-200 dark:bg-gray-700/30 dark:hover:bg-gray-700 dark:hover:border-gray-600 transition-all">
                                    <div class="flex h-10 w-10 flex-none items-center justify-center rounded-lg bg-white ring-1 ring-gray-200 dark:bg-gray-800 dark:ring-gray-700">
                                        <x-heroicon-o-calendar class="h-5 w-5 text-indigo-600 dark:text-indigo-400" />
                                    </div>
                                    <div class="flex-auto">
                                        <p class="text-sm font-semibold leading-6 text-gray-900 dark:text-white">
                                            {{ $shift['shift_type'] ?? 'Shift' }}
                                        </p>
                                        <div class="flex items-center gap-x-2 text-xs leading-5 text-gray-500 dark:text-gray-400">
                                            <p>{{ \Illuminate\Support\Carbon::parse($shift['shift_date'])->format('D, M d') }}</p>
                                            <svg viewBox="0 0 2 2" class="h-0.5 w-0.5 fill-current"><circle cx="1" cy="1" r="1" /></svg>
                                            <p>
                                                @php
                                                    $start = $shift['start_time'] ? \Carbon\Carbon::parse($shift['start_time'])->format('H:i') : '--';
                                                    $end = $shift['end_time'] ? \Carbon\Carbon::parse($shift['end_time'])->format('H:i') : '--';
                                                @endphp
                                                {{ $start }} - {{ $end }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-6">
                                <x-heroicon-o-calendar class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-600" />
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">No upcoming shifts scheduled.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Right Column: Forms --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Personal Information Form --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/60 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Personal Information</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update your personal details and contact information.</p>
                    </div>

                    <div class="p-6 md:p-8">
                        <form wire:submit.prevent="updateProfile" class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                                <input type="text" wire:model="name" id="name"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white" />
                                @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email Address</label>
                                <input type="email" wire:model="email" id="email"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white" />
                                @error('email') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Phone --}}
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Phone Number</label>
                                <input type="text" wire:model="phone_number" id="phone"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white" />
                                @error('phone_number') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>

                            {{-- Address --}}
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Residential Address</label>
                                <input type="text" wire:model="address" id="address"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white" />
                                @error('address') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2 flex justify-end pt-4">
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                                    <span wire:loading.remove wire:target="updateProfile">Save Changes</span>
                                    <span wire:loading wire:target="updateProfile">Saving...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Change Password Form --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/60 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Security</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ensure your account uses a long, random password to stay secure.</p>
                    </div>

                    <div class="p-6 md:p-8">
                        <form wire:submit.prevent="updatePassword" class="space-y-5 max-w-lg">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Current Password</label>
                                <input type="password" wire:model="current_password" id="current_password"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white" />
                                @error('current_password') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">New Password</label>
                                <input type="password" wire:model="new_password" id="new_password"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white" />
                                @error('new_password') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm New Password</label>
                                <input type="password" wire:model="new_password_confirmation" id="new_password_confirmation"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700/50 dark:border-gray-600 dark:text-white" />
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                                    <span wire:loading.remove wire:target="updatePassword">Update Password</span>
                                    <span wire:loading wire:target="updatePassword">Updating...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
