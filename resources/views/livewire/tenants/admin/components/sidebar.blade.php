<aside
    x-cloak
    :class="{
        '-translate-x-full': !mobileOpen,
        'translate-x-0': mobileOpen,
        'w-64': sidebarExpanded,
        'w-20': !sidebarExpanded
    }"
    class="fixed inset-y-0 left-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 shadow-xl z-50 transition-all duration-300 ease-in-out flex flex-col lg:translate-x-0"
    @keydown.escape.window="mobileOpen = false"
>

    <!-- Toggle Button (Desktop Only) -->
    <!-- Calls the toggleSidebar() defined in layouts/app.blade.php -->
    <button @click="toggleSidebar()"
            class="hidden lg:flex absolute -right-3 top-12 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-full p-1 shadow-sm z-50 transition-transform duration-300 focus:outline-none items-center justify-center w-6 h-6"
            :class="{ 'rotate-180': !sidebarExpanded }">
        <x-heroicon-o-chevron-left class="w-3 h-3" />
    </button>

    <!-- Mobile Header (Close Button) -->
    <div class="flex items-center justify-between p-4 lg:hidden border-b border-gray-100 dark:border-gray-700">
        <h2 class="text-xl font-bold text-blue-700 dark:text-blue-400">Navigation</h2>
        <button @click="mobileOpen = false" class="p-2 rounded-full text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
            <x-heroicon-o-x-mark class="w-6 h-6" />
        </button>
    </div>

    <!-- User Info Section -->
    <div class="user-info-section py-6 border-b border-gray-100 dark:border-gray-700 transition-all duration-300 flex flex-col items-center">
        <!-- Avatar -->
        <div class="transition-all duration-300" :class="sidebarExpanded ? 'mb-3' : 'mb-0'">
            @if(auth()->user()->profile_picture)
                <img src="{{ Storage::disk('s3')->temporaryUrl(auth()->user()->profile_picture, now()->addMinutes(5)) }}"
                     alt="Avatar"
                     class="rounded-full object-cover border-2 border-blue-100 dark:border-blue-900 transition-all duration-300"
                     :class="sidebarExpanded ? 'w-16 h-16' : 'w-10 h-10'">
            @else
                <div class="bg-blue-50 dark:bg-blue-900/50 rounded-full flex items-center justify-center text-blue-600 dark:text-blue-400 font-bold border-2 border-blue-100 dark:border-blue-800 transition-all duration-300"
                     :class="sidebarExpanded ? 'w-16 h-16 text-2xl' : 'w-10 h-10 text-sm'">
                    {{ substr(auth()->user()->name ?? 'GU', 0, 1) }}{{ substr(auth()->user()->name ?? 'GU', strpos(auth()->user()->name ?? 'GU', ' ') + 1, 1) }}
                </div>
            @endif
        </div>

        <!-- Text Info (Hidden when collapsed) -->
        <div x-show="sidebarExpanded"
             x-transition:enter="transition ease-out duration-200 delay-100"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             class="text-center overflow-hidden whitespace-nowrap px-2 w-full">
            <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100 truncate max-w-[12rem] mx-auto">
                {{ auth()->user()->name }}
            </h3>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ ucfirst(auth()->user()->role ?? 'Admin') }}
            </p>
            <p class="text-xs text-blue-600 dark:text-blue-400 font-medium mt-1 truncate max-w-[12rem] mx-auto">
                {{ tenant('hospital_name') ?? 'Hospital Name' }}
            </p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden custom-scrollbar py-4 space-y-1">

        <!-- Section Title -->
        <div class="mb-2 px-4 transition-all duration-300" x-show="sidebarExpanded" x-transition.opacity>
            <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-bold tracking-wider">
                {{ __('admin.dashboard_title') ?? 'Main Menu' }}
            </h3>
        </div>

        <ul class="space-y-1 px-3">
            @php
                // Defined here to keep the HTML clean, you can also pass this from the component class
                $menuItems = [
                    ['route' => 'admin.dashboard', 'icon' => 'home', 'label' => __('admin.dashboard_title') ?? 'Dashboard'],
                    ['route' => 'admin.user-shifts', 'icon' => 'calendar-days', 'label' => __('admin.shifts_bar') ?? 'Shifts'],
                    ['route' => 'admin.revenue-report', 'icon' => 'banknotes', 'label' => __('admin.revenue_report_bar') ?? 'Revenue'],
                    ['route' => 'admin.user-management', 'icon' => 'users', 'label' => __('admin.user_management_bar') ?? 'Users'],
                    ['route' => 'admin.settings', 'icon' => 'cog-6-tooth', 'label' => __('admin.settings_bar') ?? 'Settings'],
                    ['route' => 'admin.user-activities', 'icon' => 'clock', 'label' => __('admin.user_activities_bar') ?? 'Activities'],
                    ['route' => 'admin.feedback-history', 'icon' => 'document-text', 'label' => __('admin.feedbacks_bar') ?? 'Feedback'],
                ];
            @endphp

            @foreach($menuItems as $item)
            <li x-data="{ tooltip: false }" @mouseleave="tooltip = false">
                <a href="{{ route($item['route']) }}"
                   wire:navigate
                   @mouseenter="if(!sidebarExpanded) tooltip = true"
                   class="flex items-center p-2.5 rounded-lg transition-all duration-200 group relative
                          {{ request()->routeIs($item['route'])
                             ? 'bg-blue-50 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 font-medium shadow-sm ring-1 ring-blue-100 dark:ring-blue-800'
                             : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-blue-600 dark:hover:text-blue-300' }}"
                   :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">

                    <!-- Icon Rendering -->
                    <div class="flex-shrink-0 transition-colors duration-200">
                        @if($item['icon'] == 'home')
                            <x-heroicon-o-home class="w-6 h-6" />
                        @elseif($item['icon'] == 'calendar-days')
                            <x-heroicon-o-calendar-days class="w-6 h-6" />
                        @elseif($item['icon'] == 'banknotes')
                            <x-heroicon-o-banknotes class="w-6 h-6" />
                        @elseif($item['icon'] == 'users')
                            <x-heroicon-o-users class="w-6 h-6" />
                        @elseif($item['icon'] == 'cog-6-tooth')
                            <x-heroicon-o-cog-6-tooth class="w-6 h-6" />
                        @elseif($item['icon'] == 'clock')
                            <x-heroicon-o-clock class="w-6 h-6" />
                        @elseif($item['icon'] == 'document-text')
                            <x-heroicon-o-document-text class="w-6 h-6" />
                        @endif
                    </div>

                    <!-- Label (Collapsible) -->
                    <span x-show="sidebarExpanded"
                          class="whitespace-nowrap overflow-hidden transition-all duration-300 origin-left"
                          x-transition:enter="transition ease-out duration-200"
                          x-transition:enter-start="opacity-0 translate-x-2"
                          x-transition:enter-end="opacity-100 translate-x-0">
                        {{ $item['label'] }}
                    </span>

                    <!-- Tooltip (Visible only when collapsed) -->
                    <div x-show="!sidebarExpanded && tooltip"
                         x-transition.opacity.duration.200ms
                         class="absolute left-full top-1/2 -translate-y-1/2 ml-2 bg-gray-900 text-white text-xs rounded px-2 py-1 z-50 whitespace-nowrap shadow-lg pointer-events-none"
                         x-cloak>
                        {{ $item['label'] }}
                    </div>
                </a>
            </li>
            @endforeach
        </ul>
    </nav>

    <!-- Footer / Logout -->
    <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit"
                x-data="{ tooltip: false }"
                @mouseenter="if(!sidebarExpanded) tooltip = true"
                @mouseleave="tooltip = false"
                class="flex items-center w-full p-2 rounded-lg text-gray-700 dark:text-gray-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200 group relative"
                :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">

                <x-heroicon-o-arrow-left-on-rectangle class="w-6 h-6 flex-shrink-0" />

                <span x-show="sidebarExpanded"
                      class="whitespace-nowrap transition-all duration-300">
                    {{ __('admin.logout') ?? 'Logout' }}
                </span>

                <!-- Tooltip for Logout -->
                <div x-show="!sidebarExpanded && tooltip"
                     x-transition.opacity.duration.200ms
                     class="absolute left-full top-1/2 -translate-y-1/2 ml-2 bg-gray-900 text-white text-xs rounded px-2 py-1 z-50 whitespace-nowrap shadow-lg pointer-events-none"
                     x-cloak>
                    {{ __('admin.logout') ?? 'Logout' }}
                </div>
            </button>
        </form>
    </div>
</aside>
