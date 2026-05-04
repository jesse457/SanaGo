<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header
            class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4 md:flex md:items-center md:justify-between space-y-3 md:space-y-0">
                {{-- Title & Breadcrumbs --}}
                <div class="flex-1 min-w-0">
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-1" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="{{ route('receptionist.dashboard') }}" wire:navigate
                                    class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center">
                                    <x-heroicon-s-home class="w-3 h-3 mr-1.5" />
                                   {{ __('Home') }}
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">{{ __('Patient Management') }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                        {{ __('Patient Management') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('View, search, and manage patient records and history.') }}
                    </p>
                </div>

                {{-- Action Toolbar --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('receptionist.register-patient') }}" wire:navigate
                        class="group relative inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-tr from-pink-500 to-rose-500 text-white text-sm font-semibold rounded-xl shadow-md hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all overflow-hidden dark:focus:ring-offset-gray-900">
                        <div
                            class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000">
                        </div>
                        <x-heroicon-o-user-plus class="w-5 h-5" />
                        <span>{{ __('New Patient') }}</span>
                    </a>
                </div>
            </div>

            {{-- Filters Bar --}}
            <div class="px-4 sm:px-6 py-3 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row gap-3 items-center justify-between">
                    {{-- Search --}}
                    <div class="relative w-full md:max-w-md group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <x-heroicon-m-magnifying-glass
                                class="h-4 w-4 text-gray-400 group-focus-within:text-blue-500 transition-colors" />
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            class="block w-full pl-9 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm transition duration-150 ease-in-out"
                            placeholder="{{ __('Search by name, ID, or phone number...') }}">
                        <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <x-heroicon-o-arrow-path class="animate-spin h-4 w-4 text-blue-500" />
                        </div>
                    </div>

                    {{-- Active Filters --}}
                    @if ($search)
                        <div class="flex items-center justify-end w-full md:w-auto">
                            <button wire:click="$set('search', '')"
                                class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium hover:underline transition-colors flex items-center gap-1">
                                <x-heroicon-m-trash class="w-3 h-3" /> {{ __('Clear Search') }}
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <div class="relative min-h-[400px] p-4 sm:p-6 pb-20">

            {{-- Loading Overlay --}}
            <div wire:loading.flex wire:target="search, openEditModal"
                class="absolute inset-0 bg-white/60 dark:bg-gray-900/60 backdrop-blur-[2px] z-20 flex items-start justify-center rounded-2xl pt-20 transition-all">
                <div
                    class="flex items-center gap-3 px-6 py-3 bg-white dark:bg-gray-800 rounded-full shadow-xl border border-slate-100 dark:border-gray-700 animate-bounce">
                    <x-heroicon-o-arrow-path class="animate-spin h-5 w-5 text-blue-600" />
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ __('Updating...') }}</span>
                </div>
            </div>

            {{-- DESKTOP: Table View --}}
            <div
                class="hidden md:block bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 dark:divide-gray-800">
                        <thead class="bg-slate-50 dark:bg-gray-950">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Patient Details') }}</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Contact') }}</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Demographics') }}</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Last Visit') }}</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                            @forelse ($patients as $patient)
                                <tr wire:key="patient-{{ $patient->id }}"
                                    class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-4">
                                            <div class="h-10 w-10 flex-shrink-0 rounded-full bg-pink-100 dark:bg-indigo-900/50 flex items-center justify-center text-pink-700 dark:text-indigo-300 font-bold text-xs ring-2 ring-white dark:ring-gray-800">
                                                {{ substr($patient->first_name, 0, 1) }}{{ substr($patient->last_name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-slate-900 dark:text-white group-hover:text-pink-600 dark:group-hover:text-pink-400 transition-colors">
                                                    {{ $patient->first_name }} {{ $patient->last_name }}
                                                </div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400 font-mono bg-slate-100 dark:bg-slate-800/50 px-1.5 py-0.5 rounded w-fit mt-0.5">
                                                    {{ $patient->patient_uid }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-slate-700 dark:text-slate-300">
                                            {{ $patient->phone ?? '--' }}
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                            {{ $patient->email ?? '--' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                                {{ $patient->age }} Yrs
                                            </span>
                                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">
                                                {{ substr($patient->gender, 0, 1) ?? '-' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php $lastVisit = $patient->appointments()->orderByDesc('appointment_date')->first(); @endphp
                                        @if ($lastVisit)
                                            <div class="flex items-center gap-1.5 text-sm font-medium text-slate-600 dark:text-slate-300">
                                                <x-heroicon-m-calendar-days class="w-4 h-4 text-slate-400" />
                                                {{ \Illuminate\Support\Carbon::parse($lastVisit->appointment_date)->format('M d, Y') }}
                                            </div>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs font-bold bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-100 dark:border-amber-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> New
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="openEditModal({{ $patient->id }})"
                                            class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            title="{{ __('Edit Patient') }}">
                                            <x-heroicon-s-pencil-square class="w-4 h-4" />
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <div class="w-16 h-16 bg-slate-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-gray-700">
                                            <x-heroicon-o-user-minus class="h-8 w-8 text-slate-400" />
                                        </div>
                                        <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ __('No patients found') }}</h3>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                            {{ __('We couldn\'t find any patients matching your search.') }}
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MOBILE: Card View (Hidden on Desktop) --}}
            <div class="md:hidden space-y-4">
                @forelse ($patients as $patient)
                    <div wire:key="mobile-patient-{{ $patient->id }}"
                        class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-slate-200 dark:border-gray-800 p-4 active:scale-[0.99] transition-transform">

                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold text-sm">
                                    {{ substr($patient->first_name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ $patient->first_name }} {{ $patient->last_name }}
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-mono">{{ $patient->patient_uid }}</p>
                                </div>
                            </div>
                            <button wire:click="openEditModal({{ $patient->id }})" class="p-2 text-blue-600 bg-blue-50 rounded-lg dark:bg-blue-900/30 dark:text-blue-400">
                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                            </button>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                            <div class="bg-slate-50 dark:bg-gray-800/50 p-2 rounded-lg">
                                <span class="block text-slate-400 uppercase text-[10px] font-bold">{{ __('Age / Sex') }}</span>
                                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $patient->age }} Yrs • {{ ucfirst($patient->gender) }}</span>
                            </div>
                            <div class="bg-slate-50 dark:bg-gray-800/50 p-2 rounded-lg">
                                <span class="block text-slate-400 uppercase text-[10px] font-bold">{{ __('Phone') }}</span>
                                <span class="font-medium text-slate-700 dark:text-slate-300">{{ $patient->phone ?? '--' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-t border-slate-100 dark:border-gray-800 pt-3">
                            @php $lastVisit = $patient->appointments()->orderByDesc('appointment_date')->first(); @endphp
                            <div class="text-xs text-slate-500">
                                @if ($lastVisit)
                                    {{ __('Last Visit') }}: <span class="font-bold text-slate-700 dark:text-slate-300">{{ \Illuminate\Support\Carbon::parse($lastVisit->appointment_date)->format('M d, Y') }}</span>
                                @else
                                    <span class="text-amber-600 font-bold">{{ __('New Patient') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-slate-300 dark:border-gray-700">
                        <x-heroicon-o-user-minus class="mx-auto h-12 w-12 text-slate-300" />
                        <h3 class="mt-2 text-sm font-bold text-slate-900 dark:text-white">{{ __('No patients found') }}</h3>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($patients->hasPages())
                <div class="mt-8">
                    {{ $patients->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div x-data="{ open: @entangle('showEditModal') }"
         x-init="$watch('open', value => { if (value) { document.body.style.overflow = 'hidden'; } else { document.body.style.overflow = ''; } })"
         style="display: none;"
         x-show="open">

        <template x-teleport="body">
            <div x-show="open" class="relative z-50" role="dialog" aria-modal="true">
                {{-- Backdrop --}}
                <div x-show="open"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity"
                     @click="open = false; $wire.cancelEdit()"></div>

                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                        <div x-show="open"
                             x-transition:enter="ease-out duration-300"
                             x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="ease-in duration-200"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                             class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all sm:w-full sm:max-w-2xl border border-slate-100 dark:border-gray-800">

                            {{-- Modal Header --}}
                            <div class="bg-white dark:bg-gray-900 px-6 py-5 border-b border-slate-100 dark:border-gray-800 flex items-center justify-between sticky top-0 z-10">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                                    {{ __('Edit Patient Information') }}
                                </h3>
                                <button @click="open = false; $wire.cancelEdit()"
                                    class="rounded-xl bg-slate-50 dark:bg-gray-800 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400">
                                    <x-heroicon-o-x-mark class="h-6 w-6" />
                                </button>
                            </div>

                            <div class="px-6 py-6 bg-slate-50/50 dark:bg-gray-800/50">
                                <form wire:submit="savePatient" class="space-y-6">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        {{-- UID --}}
                                        <div class="space-y-1.5">
                                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('Patient UID') }}</label>
                                            <input type="text" wire:model="patient_uid" readonly
                                                class="block w-full rounded-xl border-slate-200 bg-slate-100 dark:bg-gray-700 dark:border-gray-600 text-slate-500 dark:text-gray-400 cursor-not-allowed shadow-sm sm:text-sm py-2.5" />
                                        </div>

                                        {{-- Gender --}}
                                        <div class="space-y-1.5">
                                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('Gender') }}</label>
                                            <select wire:model="gender"
                                                class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5">
                                                <option value="">{{ __('Select Gender') }}</option>
                                                <option value="male">{{ __('Male') }}</option>
                                                <option value="female">{{ __('Female') }}</option>
                                                <option value="other">{{ __('Other') }}</option>
                                            </select>
                                            @error('gender') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- First Name --}}
                                        <div class="space-y-1.5">
                                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('First Name') }}</label>
                                            <input type="text" wire:model="first_name"
                                                class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5" />
                                            @error('first_name') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Last Name --}}
                                        <div class="space-y-1.5">
                                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('Last Name') }}</label>
                                            <input type="text" wire:model="last_name"
                                                class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5" />
                                            @error('last_name') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Age --}}
                                        <div class="space-y-1.5">
                                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('Age') }}</label>
                                            <input type="number" wire:model="age"
                                                class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5" />
                                            @error('age') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Phone --}}
                                        <div class="space-y-1.5">
                                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('Phone Number') }}</label>
                                            <input type="text" wire:model="phone"
                                                class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5" />
                                            @error('phone') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Email --}}
                                        <div class="md:col-span-2 space-y-1.5">
                                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('Email Address') }}</label>
                                            <input type="email" wire:model="email"
                                                class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5" />
                                            @error('email') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                        </div>

                                        {{-- Address --}}
                                        <div class="md:col-span-2 space-y-1.5">
                                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300">{{ __('Address') }}</label>
                                            <textarea wire:model="address" rows="3"
                                                class="block w-full rounded-xl border-slate-200 dark:border-gray-700 dark:bg-gray-800 text-slate-900 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-2.5"></textarea>
                                            @error('address') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    {{-- Modal Footer Actions --}}
                                    <div class="flex flex-row-reverse gap-3 pt-6 border-t border-slate-200 dark:border-gray-700">
                                        <button type="submit" wire:loading.attr="disabled"
                                            class="inline-flex justify-center w-full sm:w-auto rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-700 disabled:opacity-50 transition-all">
                                            <span wire:loading.remove wire:target="savePatient">{{ __('Save Changes') }}</span>
                                            <span wire:loading wire:target="savePatient" class="flex items-center gap-2">
                                                <x-heroicon-o-arrow-path class="animate-spin h-4 w-4" /> {{ __('Saving...') }}
                                            </span>
                                        </button>
                                        <button type="button" @click="open = false; $wire.cancelEdit()"
                                            class="inline-flex justify-center w-full sm:w-auto rounded-xl bg-white dark:bg-gray-800 px-5 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-300 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-gray-600 hover:bg-slate-50 dark:hover:bg-gray-700 transition-colors">
                                            {{ __('Cancel') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</main>
