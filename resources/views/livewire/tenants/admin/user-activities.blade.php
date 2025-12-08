<main class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- 1. HEADER SECTION (Sticky) --}}
        <header class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4 md:flex md:items-center md:justify-between space-y-3 md:space-y-0">

                {{-- Title & Breadcrumbs --}}
                <div class="flex-1 min-w-0">
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-1" aria-label="Breadcrumb">
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
                                    <span class="text-gray-900 dark:text-white">{{ __('admin.user_activities_bar') }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">
                        {{ __('admin.user_activities_bar') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('admin.activities_page_description') }}
                    </p>
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
                            placeholder="{{ __('admin.activities_search_placeholder') }}">
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        {{-- Date Filter --}}
                        <input type="date" wire:model.live="dateFilter"
                            class="block w-full sm:w-40 border border-gray-300 dark:border-gray-600 rounded-lg py-2 px-3 leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm" />

                        {{-- Type Filter --}}
                        <select wire:model.live="filterType"
                            class="block w-full sm:w-40 border border-gray-300 dark:border-gray-600 rounded-lg py-2 pl-3 pr-8 leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            <option value="">{{ __('admin.activities_filter_all_types') }}</option>
                            @foreach ($activityTypes as $type)
                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Active Filters Badges --}}
                @if ($search || $filterType || $dateFilter)
                    <div class="flex items-center justify-end w-full md:w-auto">
                        <button wire:click="clearFilters"
                            class="text-xs text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 font-medium hover:underline transition-colors flex items-center gap-1">
                            <x-heroicon-m-trash class="w-3 h-3" /> {{ __('admin.activities_reset_filters') }}
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
                    <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ __('admin.activities_loading_text') }}</span>
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
                                    {{ __('admin.activities_table_user') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ __('admin.activities_table_type') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ __('admin.activities_table_description') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ __('admin.activities_table_time') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                                    {{ __('admin.activities_table_actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-gray-800 bg-white dark:bg-gray-900">
                            @forelse ($activities as $activity)
                                <tr wire:key="activity-{{ $activity->id }}"
                                    class="group hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 relative">
                                                <img class="h-10 w-10 rounded-full object-cover ring-2 ring-white dark:ring-gray-800 shadow-sm"
                                                    src="{{ $activity->user && $activity->user->profile_picture ? Storage::disk('s3')->temporaryUrl($activity->user->profile_picture, now()->addMinutes(5)) : asset('images/default_profile.png') }}"
                                                    alt="{{ $activity->user->name ?? 'User' }}">
                                            </div>
                                            <div class="ml-4">
                                                <div
                                                    class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                    {{ $activity->user->name ?? __('admin.activities_unknown_user') }}
                                                </div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                                    {{ $activity->user->email ?? '' }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $type = $activity->activity_type ?? 'other';
                                            $map = [
                                                'login' => 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800',
                                                'logout' => 'bg-slate-50 text-slate-700 border-slate-100 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700',
                                                'created' => 'bg-blue-50 text-blue-700 border-blue-100 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800',
                                                'updated' => 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800',
                                                'deleted' => 'bg-red-50 text-red-700 border-red-100 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800',
                                                'other' => 'bg-purple-50 text-purple-700 border-purple-100 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800',
                                            ];
                                            $cls = $map[$type] ?? $map['other'];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold capitalize border shadow-sm {{ $cls }}">
                                            {{ ucfirst($type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                        <div class="max-w-xs truncate" title="{{ $activity->description }}">
                                            {{ $activity->description }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">
                                        <div class="flex flex-col">
                                            <span class="font-medium">{{ $activity->created_at->format('M j, Y') }}</span>
                                            <span class="text-xs">{{ $activity->created_at->format('h:i A') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="showDetails({{ $activity->id }})"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-white text-slate-600 text-xs font-bold rounded-lg border border-slate-200 hover:bg-slate-50 hover:text-blue-600 dark:bg-gray-800 dark:text-slate-300 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:text-blue-400 transition-colors shadow-sm"
                                            title="{{ __('admin.activities_view_details') }}">
                                            <x-heroicon-o-eye class="w-4 h-4" />
                                            {{ __('admin.activities_view_details') }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="w-16 h-16 bg-slate-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100 dark:border-gray-700">
                                            <x-heroicon-o-inbox-stack class="h-8 w-8 text-slate-400" />
                                        </div>
                                        <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                            {{ __('admin.activities_no_activities_found') }}
                                        </h3>
                                        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                            {{ __('admin.try_adjusting_filters') }}
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MOBILE: Card View --}}
            <div class="md:hidden space-y-4">
                @forelse ($activities as $activity)
                    <div wire:key="mobile-card-{{ $activity->id }}"
                        class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-slate-200 dark:border-gray-800 p-4 active:scale-[0.99] transition-transform">
                        {{-- Top: User & Date --}}
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-3">
                                <img class="h-10 w-10 rounded-full object-cover ring-2 ring-slate-100 dark:ring-gray-800"
                                    src="{{ $activity->user && $activity->user->profile_picture ? Storage::disk('s3')->temporaryUrl($activity->user->profile_picture, now()->addMinutes(5)) : asset('images/default_profile.png') }}"
                                    alt="{{ $activity->user->name ?? 'User' }}">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                        {{ $activity->user->name ?? __('admin.activities_unknown_user') }}
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                            @php
                                $type = $activity->activity_type ?? 'other';
                                $map = [
                                    'login' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    'logout' => 'bg-slate-50 text-slate-700 border-slate-100',
                                    'created' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    'updated' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    'deleted' => 'bg-red-50 text-red-700 border-red-100',
                                    'other' => 'bg-purple-50 text-purple-700 border-purple-100',
                                ];
                                $cls = $map[$type] ?? $map['other'];
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold border capitalize {{ $cls }}">
                                {{ ucfirst($type) }}
                            </span>
                        </div>

                        {{-- Middle: Description --}}
                        <div class="text-sm text-slate-600 dark:text-slate-300 mb-3 line-clamp-2">
                            {{ $activity->description }}
                        </div>

                        {{-- Bottom: Action --}}
                        <div class="flex items-center justify-end border-t border-slate-100 dark:border-gray-800 pt-3">
                            <button wire:click="showDetails({{ $activity->id }})"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                {{ __('admin.activities_view_details') }} <x-heroicon-m-arrow-right class="w-3 h-3" />
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-white dark:bg-gray-900 rounded-2xl border border-dashed border-slate-300 dark:border-gray-700">
                        <x-heroicon-o-inbox-stack class="mx-auto h-12 w-12 text-slate-300" />
                        <h3 class="mt-2 text-sm font-bold text-slate-900 dark:text-white">
                            {{ __('admin.activities_no_activities_found') }}
                        </h3>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if ($activities->hasPages())
                <div class="mt-8">
                    {{ $activities->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- DETAILS MODAL --}}
    <div x-data="{ open: false, activity: null }"
        x-on:open-activity-details.window="open = true; activity = $event.detail.activity"
        x-on:keydown.escape.window="open = false"
        x-init="$watch('open', value => { if (value) { document.body.style.overflow = 'hidden'; } else { document.body.style.overflow = ''; } })">

        <template x-teleport="body">
            <div x-show="open" class="relative z-50" role="dialog" aria-modal="true">
                {{-- Backdrop --}}
                <div x-show="open" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity"></div>

                <div class="fixed inset-0 z-10 overflow-y-auto">
                    <div class="flex min-h-full items-center justify-center p-4 text-center">
                        <div x-show="open" @click.away="open = false"
                            x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                            class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-900 text-left shadow-2xl transition-all sm:w-full sm:max-w-lg border border-slate-100 dark:border-gray-800">

                            {{-- Modal Header --}}
                            <div class="bg-white dark:bg-gray-900 px-6 py-4 border-b border-slate-100 dark:border-gray-800 flex items-center justify-between sticky top-0 z-10">
                                <div>
                                    <h3 class="text-lg font-bold leading-6 text-slate-900 dark:text-white">
                                        {{ __('admin.modal_activity_details_title') }}
                                    </h3>
                                </div>
                                <button @click="open = false"
                                    class="rounded-lg bg-slate-50 dark:bg-gray-800 p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-slate-400">
                                    <x-heroicon-o-x-mark class="h-5 w-5" />
                                </button>
                            </div>

                            <div class="px-6 py-6" x-if="activity">
                                <div class="space-y-5">
                                    {{-- User Info --}}
                                    <div class="flex items-center gap-4 bg-slate-50 dark:bg-gray-800/50 p-4 rounded-xl border border-slate-100 dark:border-gray-800">
                                        <div class="h-12 w-12 rounded-full bg-slate-200 dark:bg-gray-700 flex items-center justify-center text-xl font-bold text-slate-500 overflow-hidden">
                                             {{-- Note: In Alpine x-html or specific rendering logic might be needed for images if not pre-loaded, simpler to just show initials or generic icon here if simpler --}}
                                            <x-heroicon-s-user class="w-6 h-6" />
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 dark:text-white"
                                                x-text="activity?.user?.name ?? '{{ __('admin.activities_unknown_user') }}'"></p>
                                            <p class="text-xs text-slate-500 dark:text-slate-400" x-text="activity?.user?.email ?? ''"></p>
                                        </div>
                                    </div>

                                    {{-- Details List --}}
                                    <dl class="space-y-4">
                                        <div>
                                            <dt class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">
                                                {{ __('admin.modal_detail_type') }}
                                            </dt>
                                            <dd class="text-sm font-medium text-slate-800 dark:text-slate-200 capitalize bg-slate-50 dark:bg-gray-800 px-3 py-2 rounded-lg inline-block border border-slate-100 dark:border-gray-700"
                                                x-text="activity.activity_type">
                                            </dd>
                                        </div>

                                        <div>
                                            <dt class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">
                                                {{ __('admin.modal_detail_timestamp') }}
                                            </dt>
                                            <dd class="text-sm text-slate-700 dark:text-slate-300 flex items-center gap-2">
                                                <x-heroicon-m-calendar class="w-4 h-4 text-slate-400" />
                                                <span x-text="new Date(activity.created_at).toLocaleString()"></span>
                                            </dd>
                                        </div>

                                        <div>
                                            <dt class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">
                                                {{ __('admin.modal_detail_description') }}
                                            </dt>
                                            <dd class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed bg-slate-50 dark:bg-gray-800/50 p-3 rounded-xl border border-slate-100 dark:border-gray-800"
                                                x-text="activity.description">
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>

                            <div class="bg-slate-50 dark:bg-gray-900/50 px-6 py-4 flex flex-row-reverse gap-3 border-t border-slate-100 dark:border-gray-800">
                                <button type="button" @click="open = false"
                                    class="inline-flex w-full sm:w-auto justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-md hover:bg-blue-700 hover:shadow-lg transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900">
                                    {{ __('admin.modal_button_close') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</main>
