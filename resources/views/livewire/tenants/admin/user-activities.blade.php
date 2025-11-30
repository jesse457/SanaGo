{{-- resources/views/livewire/tenants/admin/user-activities.blade.php --}}
<main class="flex-1 p-4  bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">
    <button @click="open = true"
        class="lg:hidden p-2 rounded-md text-gray-700 bg-white shadow-sm hover:bg-gray-100 transition-colors mb-6">
        <x-heroicon-o-bars-3 class="w-6 h-6" />
    </button>

    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-gray-600 dark:text-gray-400 dark:hover:text-gray-300">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" />
                        {{ __('admin.home') }}
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" />
                        <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">{{ __('admin.user_activities_bar') }}
                        </span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="space-y-6">
        <header class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-1 flex items-center gap-3">
                    <x-heroicon-s-clock class="w-8 h-8 text-indigo-600" />
                    {{ __('admin.user_activities_bar') }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400">{{ __('admin.activities_page_description') }}</p>
            </div>
        </header>

        {{-- Search & Filters --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                {{-- Search --}}
                <div class="relative flex-1 w-full md:w-auto mt-0 md:mt-6">
                    <label for="search" class="sr-only">{{ __('admin.search') }}</label>

                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <x-heroicon-o-magnifying-glass class="h-5 w-5 text-gray-400" />
                    </div>

                    <input id="search" type="text" wire:model.live.debounce.300ms="search"
                        placeholder="{{ __('admin.activities_search_placeholder') }}"
                        class="w-full pl-10 pr-10 py-2 rounded-md border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                        aria-label="{{ __('admin.search') }}" />

                    @if ($search)
                        <button type="button" wire:click="$set('search','')"
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded"
                            aria-label="{{ __('admin.action_clear_search') }}">
                            <x-heroicon-o-x-mark class="w-5 h-5" />
                        </button>
                    @endif
                </div>

                {{-- Filters: responsive grid that collapses to a single column on small screens --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 flex-1 w-full md:w-auto">
                    <div>
                        <label for="dateFilter"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.activities_filter_date') }}</label>
                        <input type="date" id="dateFilter" wire:model.live="dateFilter"
                            class="mt-1 block w-full rounded-md border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-300 focus:border-indigo-500 px-3 py-2" />
                    </div>

                    <div>
                        <label for="filterType"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('admin.activities_filter_type') }}</label>
                        <select id="filterType" wire:model.live="filterType"
                            class="h-10 mt-1 block w-full rounded-md border border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 px-3">
                            <option value="">{{ __('admin.activities_filter_all_types') }}</option>
                            @foreach ($activityTypes as $type)
                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="hidden md:block"></div>
                </div>

                <div class="w-full md:w-auto">
                    <button type="button" wire:click="clearFilters"
                        class="w-full md:w-auto inline-flex items-center gap-2 px-3 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        title="{{ __('admin.activities_clear_filters_title') }}" aria-label="{{ __('admin.activities_clear_filters_title') }}">
                        <x-heroicon-o-arrow-path class="w-5 h-5" />
                        <span>{{ __('admin.activities_reset_filters') }}</span>
                    </button>
                </div>
            </div>

            {{-- Active filters badges --}}
            @if ($search || $filterType || $filterUser || $dateFilter)
                <div class="mt-4 flex flex-wrap items-center gap-2 text-sm">
                    <span class="text-gray-500 dark:text-gray-400">{{ __('admin.active_filters_label') }}:</span>
                    @if ($search)
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 dark:bg-indigo-600/20 dark:text-indigo-300">
                            {{ __('admin.active_filter_search') }}: “{{ $search }}”
                            <button class="ml-1" wire:click="$set('search','')" aria-label="{{ __('admin.action_remove_search') }}">
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                            </button>
                        </span>
                    @endif
                    @if ($filterType)
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-blue-100 text-blue-800 dark:bg-blue-600/20 dark:text-blue-300">
                            {{ __('admin.activities_filter_type') }}: {{ ucfirst($filterType) }}
                            <button class="ml-1" wire:click="$set('filterType','')" aria-label="{{ __('admin.action_remove_type') }}">
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                            </button>
                        </span>
                    @endif
                    @if ($filterUser)
                        @php $u = $users->firstWhere('id', (int) $filterUser); @endphp
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-600/20 dark:text-emerald-300">
                            {{ __('admin.activities_filter_user') }}: {{ $u?->name ?? __('admin.activities_unknown_user') }}
                            <button class="ml-1" wire:click="$set('filterUser','')" aria-label="{{ __('admin.action_remove_user') }}">
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                            </button>
                        </span>
                    @endif
                    @if ($dateFilter)
                        <span
                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-100 text-amber-800 dark:bg-amber-600/20 dark:text-amber-300">
                            {{ __('admin.activities_filter_date') }}: {{ $dateFilter }}
                            <button class="ml-1" wire:click="$set('dateFilter', null)" aria-label="{{ __('admin.action_remove_date') }}">
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                            </button>
                        </span>
                    @endif
                </div>
            @endif
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow overflow-hidden relative">
            <div wire:loading.flex
                class="absolute inset-0 backdrop-blur-sm bg-white/60 dark:bg-gray-900/40 flex items-center justify-center z-10">
                <div class="flex items-center gap-3 text-indigo-600 dark:text-indigo-400">
                    <x-heroicon-o-arrow-path class="animate-spin h-6 w-6" />
                    <span>{{ __('admin.activities_loading_text') }}</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('admin.activities_table_user') }}</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('admin.activities_table_type') }}</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('admin.activities_table_description') }}</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('admin.activities_table_time') }}</th>
                            <th
                                class="px-6 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('admin.activities_table_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($activities as $activity)
                            <tr wire:key="activity-{{ $activity->id }}"
                                class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <img class="h-8 w-8 rounded-full object-cover"   src="{{ $activity->user->profile_picture ? Storage::disk('s3')->temporaryUrl($activity->user->profile_picture, now()->addMinutes(5)) : asset('images/default_profile.png') }}"
                                            alt="{{ $activity->user->name ?? 'Unknown User' }}">
                                        <div class="min-w-0">
                                            <span
                                                class="block text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $activity->user->name ?? __('admin.activities_unknown_user') }}
                                            </span>
                                            @if ($activity->user)
                                                <span class="block text-xs text-gray-500 dark:text-gray-400 truncate">
                                                    {{ $activity->user->email }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $type = $activity->activity_type ?? 'other';
                                        $map = [
                                            'login' =>
                                                'bg-green-100 text-green-800 dark:bg-green-600/20 dark:text-green-400',
                                            'logout' =>
                                                'bg-gray-100 text-gray-800 dark:bg-gray-600/20 dark:text-gray-300',
                                            'created' =>
                                                'bg-blue-100 text-blue-800 dark:bg-blue-600/20 dark:text-blue-400',
                                            'updated' =>
                                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-600/20 dark:text-yellow-300',
                                            'deleted' => 'bg-red-100 text-red-800 dark:bg-red-600/20 dark:text-red-400',
                                            'other' =>
                                                'bg-purple-100 text-purple-800 dark:bg-purple-600/20 dark:text-purple-300',
                                        ];
                                        $cls = $map[$type] ?? $map['other'];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cls }}">
                                        {{ ucfirst($type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-200">
                                    <div class="max-w-md">
                                        <span
                                            title="{{ $activity->description }}">{{ \Illuminate\Support\Str::limit($activity->description, 120) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    <span title="{{ $activity->created_at->toDayDateTimeString() }}">
                                        {{ $activity->created_at->diffForHumans() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button wire:click="showDetails({{ $activity->id }})"
                                        class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors"
                                        title="{{ __('admin.activities_view_details') }}">
                                        <x-heroicon-o-eye class="w-5 h-5" />
                                        <span class="sr-only">{{ __('admin.activities_view_details') }}</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12">
                                    <div
                                        class="flex flex-col items-center justify-center text-center text-gray-500 dark:text-gray-400">
                                        <x-heroicon-o-inbox-stack class="w-14 h-14 mb-3 text-gray-400" />
                                        <p class="text-base mb-2">{{ __('admin.activities_no_activities_found') }}</p>
                                        @if ($search || $filterType || $filterUser || $dateFilter)
                                            <button wire:click="clearFilters"
                                                class="inline-flex items-center gap-2 px-3 py-2 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700 dark:bg-gray-700 dark:hover:bg-gray-600 dark:text-gray-200">
                                                <x-heroicon-o-arrow-path class="w-5 h-5" />
                                                {{ __('admin.activities_reset_filters_button') }}
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($activities->hasPages())
                <div
                    class="px-6 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 flex flex-col sm:flex-row gap-2 sm:gap-4 sm:items-center sm:justify-between text-sm text-gray-600 dark:text-gray-300">
                    <span>
                        {{ __('admin.pagination_showing') }} {{ $activities->firstItem() }} {{ __('admin.pagination_to') }} {{ $activities->lastItem() }} {{ __('admin.pagination_of') }}
                        {{ $activities->total() }} {{ __('admin.pagination_results') }}
                    </span>
                    <div>
                        {{ $activities->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Activity Details Modal (Alpine) --}}
    <div x-data="{ open: false, activity: null }" x-on:open-activity-details.window="open = true; activity = $event.detail.activity"
        x-on:keydown.escape.window="open = false" x-show="open" x-transition
        class="fixed inset-0 bg-gray-900/50 flex items-center justify-center p-4 z-50" aria-modal="true"
        role="dialog">
        <div @click.away="open = false"
            class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-lg w-full p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ __('admin.modal_activity_details_title') }}</h2>
                <button @click="open = false"
                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                    aria-label="{{ __('admin.modal_close_modal') }}">
                    <x-heroicon-o-x-mark class="w-6 h-6" />
                </button>
            </div>

            <template x-if="activity">
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('admin.modal_detail_user') }}</span>
                        <span class="text-gray-900 dark:text-white"
                            x-text="activity?.user?.name ?? '{{ __('admin.activities_unknown_user') }}'"></span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('admin.modal_detail_email') }}</span>
                        <span class="text-gray-900 dark:text-white" x-text="activity?.user?.email ?? '—'"></span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('admin.modal_detail_type') }}</span>
                        <span class="capitalize" x-text="activity.activity_type ?? '—'"></span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('admin.modal_detail_description') }}</span>
                        <p class="mt-1 text-gray-700 dark:text-gray-200 break-words"
                            x-text="activity.description ?? '—'"></p>
                    </div>

                    <div class="flex justify-between gap-4">
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ __('admin.modal_detail_timestamp') }}</span>
                        <span class="text-gray-900 dark:text-white"
                            x-text="activity.created_at ? new Date(activity.created_at).toLocaleString() : '—'"></span>
                    </div>
                </div>
            </template>

            <div class="flex justify-end pt-4">
                <button @click="open = false"
                    class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                    {{ __('admin.modal_button_close') }}
                </button>
            </div>
        </div>
    </div>
</main>

@push('scripts')
    <script>
        // Optional: simple toast hook if you use it elsewhere
        window.addEventListener('notify', (e) => {
            // Example: you could integrate with your toast system here
            // console.log(e.detail);
        });
    </script>
@endpush
