{{-- Enhanced UI for "Create Tenant" Page --}}

<div class="flex-1 p-4 md:p-8 lg:ml-64 bg-slate-50 dark:bg-gray-900 min-h-screen">
    {{-- Breadcrumbs & Header --}}
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('landlord.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-slate-700 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-white">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" />
                        {{ __('ui.home') }}
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-slate-400 mx-1" />
                        <a href="{{ route('landlord.manage-tenants') }}" wire:navigate
                            class="ms-1 text-sm font-medium text-slate-700 hover:text-indigo-600 md:ms-2 dark:text-slate-400 dark:hover:text-white">{{ __('ui.manage_tenants') }}</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-slate-400 mx-1" />
                        <span class="ms-1 text-sm font-medium text-slate-400 md:ms-2 dark:text-slate-400">{{ __('ui.create_new_tenant') }}</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h2 class="text-3xl font-bold text-slate-800 dark:text-white mt-4">{{ __('ui.create_tenant_account') }}</h2>
    </div>

    {{-- Main Form Card --}}
    <div id="createTenantFormContainer" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg">
        <form wire:submit.prevent="createTenant" class="p-6 sm:p-8">

            @if (session()->has('success'))
                <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms
                    class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-100 dark:bg-green-900/50 dark:text-green-400"
                    role="alert" x-init="setTimeout(() => show = false, 3000)">
                    <span class="font-medium">{{ __('ui.success') }}!</span> {{ session('success') }}
                </div>
            @endif

            {{-- Section 1: Tenant Information --}}
            <div class="pb-8">
                <h3 class="text-lg font-semibold leading-7 text-slate-900 dark:text-white flex items-center gap-3 mb-6">
                    <x-heroicon-o-building-office class="w-6 h-6 text-indigo-600" />
                    {{ __('ui.tenant_info') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    {{-- Tenant Name --}}
                    <div>
                        <label for="tenantName"
                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('ui.tenant_name_label') }}</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-o-building-office class="h-5 w-5 text-slate-400" />
                            </div>
                            <input type="text" id="tenantName" wire:model.live.debounce.300ms="tenantName"
                                placeholder="{{ __('ui.tenant_name_placeholder') }}"
                                class="border block w-full rounded-lg border-slate-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white py-2.5 pl-10 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200">
                        </div>
                        @error('tenantName')
                            <p class="text-red-600 dark:text-red-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone Number --}}
                    <div>
                        <label for="phoneNumber"
                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('ui.phone_number') }}</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-o-phone class="h-5 w-5 text-slate-400" />
                            </div>
                            <input type="tel" id="phoneNumber" wire:model="phoneNumber"
                                placeholder="{{ __('ui.phone_number_placeholder') }}"
                                class="border block w-full rounded-lg border-slate-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white py-2.5 pl-10 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200">
                        </div>
                        @error('phoneNumber')
                            <p class="text-red-600 dark:text-red-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div class="md:col-span-2">
                        <label for="address"
                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('ui.address') }}</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-o-map-pin class="h-5 w-5 text-slate-400" />
                            </div>
                            <input type="text" id="address" wire:model="address"
                                placeholder="{{ __('ui.address_placeholder') }}"
                                class="border block w-full rounded-lg border-slate-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white py-2.5 pl-10 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200">
                        </div>
                        @error('address')
                            <p class="text-red-600 dark:text-red-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>


            {{-- Section 2: Administrator Account (NEW) --}}
            <div class="border-t border-slate-200 dark:border-gray-700 pt-8 pb-8">
                <h3 class="text-lg font-semibold leading-7 text-slate-900 dark:text-white flex items-center gap-3 mb-6">
                    <x-heroicon-o-user-circle class="w-6 h-6 text-indigo-600" />
                    {{ __('ui.admin_account') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    {{-- Admin Name --}}
                    <div>
                        <label for="adminName"
                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('ui.admin_name') }}</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-o-user class="h-5 w-5 text-slate-400" />
                            </div>
                            <input type="text" id="adminName" wire:model="adminName"
                                placeholder="{{ __('ui.admin_name_placeholder') }}"
                                class="border block w-full rounded-lg border-slate-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white py-2.5 pl-10 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200">
                        </div>
                        @error('adminName')
                            <p class="text-red-600 dark:text-red-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Admin Email --}}
                    <div>
                        <label for="adminEmail"
                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('ui.contact_email') }}</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-o-envelope class="h-5 w-5 text-slate-400" />
                            </div>
                            <input type="email" id="adminEmail" wire:model="adminEmail"
                                placeholder="{{ __('ui.admin_email_placeholder') }}"
                                class="border block w-full rounded-lg border-slate-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white py-2.5 pl-10 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200">
                        </div>
                        @error('adminEmail')
                            <p class="text-red-600 dark:text-red-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            {{-- Section 3: Subscription & Domain --}}
            <div class="border-t border-slate-200 dark:border-gray-700 pt-8">
                <h3
                    class="text-lg font-semibold leading-7 text-slate-900 dark:text-white flex items-center gap-3 mb-6">
                    <x-heroicon-o-credit-card class="w-6 h-6 text-indigo-600" />
                    {{ __('ui.subscription_domain') }}
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                    {{-- Subscription Tier --}}
                    <div>
                        <label for="subscriptionTier" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('ui.subscription_tier') }}</label>
                        <select id="subscriptionTier" wire:model="subscriptionTier"
                            class="border block w-full rounded-lg border-slate-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white py-2.5 px-3 shadow-sm focus:border-indigo-600 focus:ring-indigo-600 sm:text-sm transition-all duration-200">
                            {{-- Loop through plans passed from component --}}
                            @foreach($availablePlans as $plan)
                                <option value="{{ $plan }}">{{ __("ui.$plan") }}</option>
                            @endforeach
                        </select>
                        @error('subscriptionTier')
                            <p class="text-red-600 dark:text-red-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Generated Domain (Read-Only) --}}
                    <div>
                        <label for="generatedDomain"
                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('ui.generated_domain') }}</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <x-heroicon-o-globe-alt class="h-5 w-5 text-slate-400" />
                            </div>
                            <input type="text" id="generatedDomain" wire:model="generatedDomain" readonly
                                class="border block w-full rounded-lg border-slate-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white py-2.5 pl-10 shadow-sm bg-slate-100 dark:bg-gray-700 cursor-not-allowed sm:text-sm transition-all duration-200">
                        </div>
                        @error('generatedDomain')
                            <p class="text-red-600 dark:text-red-400 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            {{-- Form Actions / Footer --}}
            <div class="flex items-center justify-end gap-4 mt-10 pt-6 border-t border-slate-200 dark:border-gray-700">
                <button type="button" wire:navigate href="{{ route('landlord.manage-tenants') }}"
                    class="rounded-lg bg-white dark:bg-gray-700 px-6 py-2.5 text-sm font-semibold text-slate-900 dark:text-white shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-gray-600 hover:bg-slate-50 dark:hover:bg-gray-600 transition-all duration-200 flex items-center gap-2">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                    {{ __('ui.cancel') }}
                </button>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2.5 rounded-lg bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all duration-200 transform hover:-translate-y-0.5"
                    wire:loading.attr="disabled" wire:loading.class="opacity-75 cursor-not-allowed">
                    <div wire:loading wire:target="createTenant">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </d>
                        </svg>
                        <span>{{ __('ui.creating') }}</span>
                    </div>
                    <span wire:loading.remove wire:target="createTenant" class="flex items-center gap-2">
                        <x-heroicon-o-check class="w-5 h-5" />
                        {{ __('ui.create_tenant_button') }}
                    </span>
                </button>
            </div>



        </form>
    </div>
</div>
