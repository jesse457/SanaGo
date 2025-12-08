<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        {{-- FIX: Added 'sticky top-0' to make it actually sticky --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4 md:flex md:items-center md:justify-between space-y-3 md:space-y-0">

                {{-- Title & Breadcrumbs --}}
                <div class="flex-1 min-w-0">
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-1" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('doctor.dashboard') }}" wire:navigate
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                    {{ __('doctor.home') }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">{{ __('admin.manage_users_title') }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                        {{ __('admin.manage_users_title') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('admin.manage_users_description') }}
                    </p>
                </div>

                {{-- Right Actions / Stats --}}
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        <a href="{{ route('admin.create-new-user') }}" wire:navigate
                            class="group relative inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl shadow-md hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all overflow-hidden dark:focus:ring-offset-gray-900">
                            <div
                                class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                            </div>
                            <x-heroicon-o-plus class="w-5 h-5" />
                            <span>{{ __('admin.add_user_button') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Filters Bar --}}
            <div
                class="px-4 sm:px-6 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700 flex flex-col md:flex-row gap-3 items-center justify-between">
                <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto flex-1">
                    {{-- Search --}}
                    <div class="relative w-full sm:max-w-xs group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-m-magnifying-glass
                                class="h-4 w-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                        </div>
                        <input type="text" wire:model.live.debounce.400ms="search"
                            class="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
                            placeholder="Search by Name...">
                    </div>

                    <div class="flex gap-3 w-full sm:w-auto">
                        <select wire:model.live="filterRole"
                            class="block w-full sm:w-40 border border-gray-300 dark:border-gray-600 rounded-lg py-2 pl-3 pr-8 leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">{{ __('admin.filter_all_roles') }}</option>
                            <option value="admin">{{ __('admin.role_admin') }}</option>
                            <option value="doctor">{{ __('admin.role_doctor') }}</option>
                            <option value="nurse">{{ __('admin.role_nurse') }}</option>
                            <option value="receptionist">{{ __('admin.role_receptionist') }}</option>
                            <option value="lab-technician">{{ __('admin.role_lab_technician') }}</option>
                            <option value="pharmacist">{{ __('admin.role_pharmacist') }}</option>
                        </select>

                        <select wire:model.live="filterStatus"
                            class="block w-full sm:w-40 border border-gray-300 dark:border-gray-600 rounded-lg py-2 pl-3 pr-8 leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">{{ __('admin.filter_all_statuses') }}</option>
                            <option value="active">{{ __('admin.status_active') }}</option>
                            <option value="inactive">{{ __('admin.status_inactive') }}</option>
                        </select>
                    </div>
                </div>

                {{-- Active Filters Badges --}}
                @if ($search || $filterRole || $filterStatus)
                    <div class="flex items-center justify-end w-full md:w-auto">
                        <button wire:click="$set('search', ''); $set('filterRole', ''); $set('filterStatus', '')"
                            class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium hover:underline transition-colors flex items-center gap-1">
                            <x-heroicon-m-trash class="w-3 h-3" /> Clear Filters
                        </button>
                    </div>
                @endif
            </div>
        </header>


        {{-- Content Area --}}
        <div class="relative min-h-[400px] p-4 sm:p-6">
            {{-- Loading Overlay --}}
            <div wire:loading.flex
                class="absolute inset-0 bg-white/60 dark:bg-gray-900/60 backdrop-blur-[2px] z-20 flex items-start justify-center rounded-2xl pt-20 transition-all">
                <div
                    class="flex items-center gap-3 px-6 py-3 bg-white dark:bg-gray-800 rounded-full shadow-xl border border-slate-100 dark:border-gray-700 animate-bounce">
                    <x-heroicon-o-arrow-path class="animate-spin h-5 w-5 text-blue-600" />
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Updating...</span>
                </div>
            </div>

            {{-- DESKTOP: Table View --}}
            <div
                class="hidden md:block bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-gray-800">
                        <thead class="bg-slate-50 dark:bg-gray-950">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ __('admin.table_header_name') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ __('admin.form_label_role') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ __('admin.table_header_status') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ __('admin.table_header_action') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                            @forelse ($users as $user)
                                <tr wire:key="row-{{ $user->id }}"
                                    class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 relative">
                                                <img class="h-10 w-10 rounded-full object-cover ring-2 ring-white dark:ring-gray-800 shadow-sm"
                                                    src="{{ $user->profile_picture ? Storage::disk('s3')->temporaryUrl($user->profile_picture, now()->addMinutes(5)) : asset('images/default_profile.png') }}"
                                                    alt="{{ $user->name }}">
                                                @if (!$user->email_verified_at)
                                                    <div class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-amber-400 text-[10px] font-bold text-white ring-2 ring-white dark:ring-gray-900"
                                                        title="Email not verified">
                                                        !
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div
                                                    class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                    {{ $user->name }}
                                                </div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                                    {{ $user->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold capitalize border shadow-sm
                                            {{ match ($user->role) {
                                                'admin'
                                                    => 'bg-purple-50 text-purple-700 border-purple-100 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800',
                                                'doctor' => 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800',
                                                'nurse' => 'bg-pink-50 text-pink-700 border-pink-100 dark:bg-pink-900/30 dark:text-pink-300 dark:border-pink-800',
                                                default => 'bg-slate-50 text-slate-700 border-slate-100 dark:bg-gray-800 dark:text-slate-300 dark:border-gray-700',
                                            } }}">
                                            {{ str_replace('_', ' ', $user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($user->is_active && $user->email_verified_at)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-100 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-900">
                                                <span class="relative flex h-2 w-2">
                                                    <span
                                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                                    <span
                                                        class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                                </span>
                                                {{ __('admin.status_active') }}
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200 dark:bg-gray-800 dark:text-slate-400 dark:border-gray-700">
                                                <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                                                {{ __('admin.status_inactive') }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        {{-- FIX: Actions always visible for better accessibility --}}
                                        <div class="flex items-center justify-end gap-2">
                                            {{-- Resend Button Logic --}}
                                            @if (is_null($user->email_verified_at))
                                                <button wire:click="resendInvitation({{ $user->id }})"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-amber-50 text-amber-700 text-xs font-bold rounded-lg border border-amber-200 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-800 dark:hover:bg-amber-900/40 transition-colors shadow-sm mr-2"
                                                    title="User has not verified email. Click to resend invitation.">
                                                    <x-heroicon-m-paper-airplane class="w-3 h-3" />
                                                    Resend
                                                </button>
                                            @endif

                                            <button wire:click="editUser({{ $user->id }})"
                                                class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                title="{{ __('admin.action_edit') }}">
                                                <x-heroicon-s-pencil-square class="w-4 h-4" />
                                            </button>
                                            <button wire:click="viewDeleteUser({{ $user->id }})"
                                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500"
                                                title="{{ __('admin.action_delete') }}">
                                                <x-heroicon-s-trash class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div
                                            class="w-16 h-16 bg-slate-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-gray-700">
                                            <x-heroicon-o-users class="h-8 w-8 text-slate-400" />
                                        </div>
                                        <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                            {{ __('admin.no_users_found_title') }}</h3>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                            {{ __('admin.no_users_found_text') }}</p>
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
                    <div wire:key="mobile-card-{{ $user->id }}"
                        class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-slate-200 dark:border-gray-800 p-4 active:scale-[0.99] transition-transform">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <img class="h-10 w-10 rounded-full object-cover ring-2 ring-slate-100 dark:ring-gray-800"
                                    src="{{ $user->profile_picture ? Storage::disk('s3')->temporaryUrl($user->profile_picture, now()->addMinutes(5)) : asset('images/default_profile.png') }}"
                                    alt="{{ $user->name }}">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $user->name }}
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ $user->email }}</p>
                                    @if (is_null($user->email_verified_at))
                                        <span
                                            class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded mt-1 border border-amber-100">
                                            <x-heroicon-m-exclamation-circle class="w-3 h-3" /> Unverified
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold border capitalize
                                {{ match ($user->role) {
                                    'admin' => 'bg-purple-50 text-purple-700 border-purple-100',
                                    'doctor' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    'nurse' => 'bg-pink-50 text-pink-700 border-pink-100',
                                    default => 'bg-slate-50 text-slate-700 border-slate-100',
                                } }}">
                                {{ str_replace('_', ' ', $user->role) }}
                            </span>
                        </div>

                        <div
                            class="flex items-center justify-between border-t border-slate-100 dark:border-gray-800 pt-3 mt-2">
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold {{ $user->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-slate-100 text-slate-600 border border-slate-200' }}">
                                <span
                                    class="w-1.5 h-1.5 rounded-full {{ $user->is_active  ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ $user->is_active ? __('admin.status_active') : __('admin.status_inactive') }}
                            </span>

                            <div class="flex gap-1">
                                @if (is_null($user->email_verified_at))
                                    <button wire:click="resendInvitation({{ $user->id }})"
                                        class="p-2 text-amber-600 bg-amber-50 dark:bg-amber-900/30 rounded-lg hover:bg-amber-100 transition-colors border border-amber-100 dark:border-amber-800">
                                        <x-heroicon-o-paper-airplane class="w-4 h-4" />
                                    </button>
                                @endif
                                <button wire:click="editUser({{ $user->id }})"
                                    class="p-2 text-blue-600 bg-blue-50 dark:bg-blue-900/30 rounded-lg hover:bg-blue-100 transition-colors border border-blue-100 dark:border-blue-800">
                                    <x-heroicon-o-pencil-square class="w-4 h-4" />
                                </button>
                                <button wire:click="viewDeleteUser({{ $user->id }})"
                                    class="p-2 text-red-600 bg-red-50 dark:bg-red-900/30 rounded-lg hover:bg-red-100 transition-colors border border-red-100 dark:border-red-800">
                                    <x-heroicon-o-trash class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="text-center py-12 bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-slate-300 dark:border-gray-700">
                        <x-heroicon-o-users class="mx-auto h-12 w-12 text-slate-300" />
                        <h3 class="mt-2 text-sm font-bold text-slate-900 dark:text-white">
                            {{ __('admin.no_users_found_title') }}</h3>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($this->users->hasPages())
                <div class="mt-8">
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
                if (value) { document.body.style.overflow = 'hidden'; } else { document.body.style.overflow = ''; }
            });
        }
    }" x-on:open-edit-modal.window="showEditModal = true"
        x-on:close-edit-modal.window="showEditModal = false"
        x-on:user-updated.window="$dispatch('show-toast', { message: '{{ __('admin.toast_user_updated') }}' })">

        <template x-teleport="body">
            <div x-show="showEditModal" class="relative z-50" role="dialog" aria-modal="true">
                {{-- Backdrop with Blur --}}
                <div x-show="showEditModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity"></div>

                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div x-show="showEditModal" @click.away="$wire.call('closeEditModal')"
                            x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                            class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all w-full max-w-2xl border border-slate-100 dark:border-gray-800 my-8">

                            {{-- Modal Header --}}
                            <div
                                class="bg-white dark:bg-gray-900 px-6 py-4 border-b border-slate-100 dark:border-gray-800 flex items-center justify-between sticky top-0 z-10">
                                <div>
                                    <h3 class="text-lg font-bold leading-6 text-slate-900 dark:text-white">
                                        {{ __('admin.edit_user_modal_title') }}
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                        Editing: <span class="font-bold text-blue-600 dark:text-blue-400"
                                            x-text="$wire.name"></span>
                                    </p>
                                </div>
                                <button @click="$wire.call('closeEditModal')"
                                    class="rounded-lg bg-slate-50 dark:bg-gray-800 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400">
                                    <x-heroicon-o-x-mark class="h-5 w-5" />
                                </button>
                            </div>

                            <form wire:submit.prevent="updateUser">
                                {{-- Scrollable Content --}}
                                <div
                                    class="px-6 sm:px-8 py-6 max-h-[calc(100vh-200px)] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-700 scrollbar-track-transparent">
                                    {{-- Section: Personal Info --}}
                                    <div class="mb-8">
                                        <h4
                                            class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-5">
                                            {{ __('admin.section_personal_info') }}</h4>
                                        <div class="grid grid-cols-1 gap-y-5 gap-x-6 sm:grid-cols-6">

                                            <div class="sm:col-span-3">
                                                <label for="edit-name"
                                                    class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('admin.form_label_name') }}</label>
                                                <input type="text" id="edit-name" wire:model="name"
                                                    class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                                                @error('name')
                                                    <span
                                                        class="text-xs text-red-500 mt-1 font-medium block">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="edit-email"
                                                    class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('admin.form_label_email') }}</label>
                                                <input type="email" id="edit-email" wire:model="email"
                                                    class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                                                @error('email')
                                                    <span
                                                        class="text-xs text-red-500 mt-1 font-medium block">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="edit-role"
                                                    class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('admin.form_label_role') }}</label>
                                                <select id="edit-role" wire:model="role"
                                                    class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                                                    <option value="admin">{{ __('admin.role_admin') }}</option>
                                                    <option value="doctor">{{ __('admin.role_doctor') }}</option>
                                                    <option value="nurse">{{ __('admin.role_nurse') }}</option>
                                                    <option value="receptionist">{{ __('admin.role_receptionist') }}
                                                    </option>
                                                    <option value="lab-technician">
                                                        {{ __('admin.role_lab_technician') }}</option>
                                                    <option value="pharmacist">{{ __('admin.role_pharmacist') }}
                                                    </option>
                                                </select>
                                                @error('role')
                                                    <span
                                                        class="text-xs text-red-500 mt-1 font-medium block">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="sm:col-span-3">
                                                <label for="edit-phone"
                                                    class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">{{ __('admin.form_label_phone') }}</label>
                                                <input type="tel" id="edit-phone" wire:model="phone_number"
                                                    class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                                                @error('phone_number')
                                                    <span
                                                        class="text-xs text-red-500 mt-1 font-medium block">{{ $message }}</span>
                                                @enderror
                                            </div>

                                            <div class="sm:col-span-6">
                                                <div class="flex items-center justify-between bg-slate-50 dark:bg-gray-800/50 p-4 rounded-xl border border-slate-100 dark:border-gray-800"
                                                    x-data="{ isActive: @entangle('is_active') }">
                                                    <div class="flex flex-col">
                                                        <span
                                                            class="text-sm font-bold text-slate-900 dark:text-white">{{ __('admin.account_status') }}</span>
                                                        <span class="text-xs text-slate-500 dark:text-slate-400"
                                                            x-text="isActive ? '{{ __('admin.account_active_desc') }}' : '{{ __('admin.account_disabled_desc') }}'"></span>
                                                    </div>
                                                    <button type="button" @click="isActive = !isActive"
                                                        :class="isActive ? 'bg-blue-600' : 'bg-slate-200 dark:bg-gray-700'"
                                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
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
                                    <div class="border-t border-slate-100 dark:border-gray-800 pt-8">
                                        <h4
                                            class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-5">
                                            {{ __('admin.section_shift_management') }}</h4>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            {{-- Upcoming Shifts --}}
                                            <div>
                                                <label
                                                    class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('admin.assign_upcoming_shift_title') }}</label>
                                                <div class="relative rounded-xl shadow-sm mb-3">
                                                    <div
                                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                        <x-heroicon-o-magnifying-glass
                                                            class="h-4 w-4 text-slate-400" />
                                                    </div>
                                                    <input x-ref="shiftSearch" type="text"
                                                        class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 dark:text-white pl-10 focus:border-blue-500 focus:ring-blue-500 sm:text-xs py-2.5"
                                                        placeholder="{{ __('admin.search_shifts_placeholder') }}"
                                                        x-on:input="$refs.shiftList.querySelectorAll('li.shift-item').forEach(li => { li.style.display = li.textContent.toLowerCase().includes($event.target.value.toLowerCase()) ? '' : 'none'; });">
                                                </div>

                                                <div
                                                    class="h-48 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 border border-slate-200 dark:border-gray-800 rounded-xl bg-slate-50 dark:bg-gray-800/50">
                                                    <ul x-ref="shiftList"
                                                        class="divide-y divide-slate-100 dark:divide-gray-800">
                                                        <li class="shift-item">
                                                            <label
                                                                class="flex items-center px-4 py-3 hover:bg-white dark:hover:bg-gray-800/50 cursor-pointer transition-colors">
                                                                <input type="radio"
                                                                    wire:model.live="selected_shift_id" value=""
                                                                    class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500">
                                                                <span
                                                                    class="ml-3 block text-sm font-medium text-slate-500 italic">{{ __('admin.no_upcoming_shift') }}</span>
                                                            </label>
                                                        </li>
                                                        @foreach ($this->availableShifts as $shift)
                                                            <li wire:key="shift-option-{{ $shift->id }}"
                                                                class="shift-item">
                                                                <label
                                                                    class="flex items-center px-4 py-3 hover:bg-white dark:hover:bg-gray-800/50 cursor-pointer transition-colors">
                                                                    <input type="radio"
                                                                        wire:model.live="selected_shift_id"
                                                                        value="{{ $shift->id }}"
                                                                        class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500">
                                                                    <div class="ml-3">
                                                                        <span
                                                                            class="block text-sm font-bold text-slate-900 dark:text-white">{{ $shift->shift_date->format('D, M j') }}</span>
                                                                        <span
                                                                            class="block text-xs text-slate-500 dark:text-slate-400">{{ $shift->start_time->format('H:i') }}
                                                                            -
                                                                            {{ $shift->end_time->format('H:i') }}</span>
                                                                    </div>
                                                                </label>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>

                                            {{-- History --}}
                                            <div>
                                                <label
                                                    class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2">{{ __('admin.shift_history_title') }}</label>
                                                <div
                                                    class="h-[14rem] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 border border-slate-200 dark:border-gray-800 rounded-xl bg-slate-50 dark:bg-gray-800/50 p-2">
                                                    @if ($userShiftHistory->isNotEmpty())
                                                        <ul class="space-y-2">
                                                            @foreach ($userShiftHistory as $pastShift)
                                                                <li
                                                                    class="flex items-center justify-between px-3 py-2 bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-slate-100 dark:border-gray-800">
                                                                    <span
                                                                        class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $pastShift->shift_date->format('M j, Y') }}</span>
                                                                    <span
                                                                        class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ $pastShift->start_time->format('H:i') }}-{{ $pastShift->end_time->format('H:i') }}</span>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <div
                                                            class="flex h-full flex-col items-center justify-center text-center">
                                                            <x-heroicon-o-clock class="h-8 w-8 text-slate-300" />
                                                            <p class="mt-1 text-xs text-slate-500">
                                                                {{ __('admin.no_past_shifts_recorded') }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Footer Actions --}}
                                <div
                                    class="bg-slate-50 dark:bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 rounded-b-2xl border-t border-slate-100 dark:border-gray-800">
                                    <button type="submit" wire:loading.attr="disabled"
                                        class="inline-flex w-full sm:w-auto justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-700 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                        <span wire:loading.remove
                                            wire:target="updateUser">{{ __('admin.modal_button_save_changes') }}</span>
                                        <span wire:loading wire:target="updateUser" class="flex items-center gap-2">
                                            <x-heroicon-o-arrow-path class="animate-spin h-4 w-4" />
                                            {{ __('admin.modal_button_saving') }}
                                        </span>
                                    </button>
                                    <button type="button" @click="$wire.call('closeEditModal')"
                                        class="inline-flex w-full sm:w-auto justify-center rounded-xl bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-gray-700 hover:bg-slate-50 dark:hover:bg-gray-700 transition-all focus:outline-none focus:ring-2 focus:ring-slate-400">
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
