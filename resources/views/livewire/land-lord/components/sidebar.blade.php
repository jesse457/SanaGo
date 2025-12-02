<aside
    x-cloak
    :class="{
        '-translate-x-full': !mobileOpen,
        'translate-x-0': mobileOpen,
        'w-64': sidebarExpanded,
        'w-20': !sidebarExpanded
    }"
    class="fixed inset-y-0 left-0 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 shadow-xl z-30 transition-all duration-300 ease-in-out flex flex-col lg:translate-x-0"
    @keydown.escape.window="mobileOpen = false"
>

    <!-- Desktop Toggle Button -->
    <button @click="toggleSidebar()"
            class="hidden lg:flex absolute -right-3 top-10 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-full p-1 shadow-sm z-40 transition-transform duration-300 focus:outline-none items-center justify-center w-6 h-6"
            :class="{ 'rotate-180': !sidebarExpanded }">
        <x-heroicon-o-chevron-left class="w-3 h-3" />
    </button>

    <!-- Mobile Header (Close Button) -->
    <div class="flex items-center justify-between p-4 lg:hidden border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-xl font-bold text-indigo-600 dark:text-indigo-400">Navigation</h2>
        <button @click="mobileOpen = false" class="p-2 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
            <x-heroicon-o-x-mark class="w-6 h-6" />
        </button>
    </div>

    <!-- User Profile Section -->
    <div class="user-info-section py-6 border-b border-gray-200 dark:border-gray-700 transition-all duration-300 flex flex-col items-center overflow-hidden">

        <!-- Animated Avatar -->
        <div class="transition-all duration-300 ease-in-out flex items-center justify-center"
             :class="sidebarExpanded ? 'mb-3 w-16 h-16' : 'mb-0 w-10 h-10'">
            <div class="bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center text-indigo-700 dark:text-indigo-300 font-bold border-2 border-indigo-50 dark:border-indigo-800 transition-all duration-300 h-full w-full"
                 :class="sidebarExpanded ? 'text-2xl' : 'text-sm'">
                L
            </div>
        </div>

        <!-- Text Info (Fade in/out) -->
        <div x-show="sidebarExpanded"
             x-transition:enter="transition ease-out duration-200 delay-75"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             class="text-center whitespace-nowrap px-2 w-full">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white truncate">Landlord User</h3>
            <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium mt-1">Central Dashboard</p>
        </div>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 overflow-y-auto custom-scrollbar py-4 space-y-1">

        <!-- Section Header -->
        <div class="mb-2 px-4 transition-all duration-300" x-show="sidebarExpanded" x-transition.opacity>
            <h3 class="text-xs uppercase text-gray-400 dark:text-gray-500 font-bold tracking-wider">Management</h3>
        </div>

        <ul class="space-y-1 px-3">
            @php
                $menuItems = [
                    [
                        'route' => 'landlord.dashboard',
                        'icon' => 'home',
                        'label' => 'Dashboard'
                    ],
                    [
                        'route' => 'landlord.manage-tenants',
                        'icon' => 'users',
                        'label' => 'Manage Tenants'
                    ],
                    [
                        'route' => 'landlord.feedbacks',
                        'icon' => 'chat-bubble-left-right',
                        'label' => 'Tenant Complaints'
                    ],
                    [
                        'route' => 'landlord.settings',
                        'icon' => 'cog-6-tooth',
                        'label' => 'Settings'
                    ],
                ];
            @endphp

            @foreach($menuItems as $item)
            <li x-data="{ tooltip: false }" @mouseleave="tooltip = false" class="relative">
                <a href="{{ route($item['route']) }}"
                   wire:navigate
                   @mouseenter="if(!sidebarExpanded) tooltip = true"
                   class="flex items-center p-2.5 rounded-lg transition-all duration-200 group
                          {{ request()->routeIs($item['route'])
                             ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 font-semibold shadow-sm ring-1 ring-indigo-100 dark:ring-indigo-700'
                             : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700/50 hover:text-indigo-600 dark:hover:text-indigo-300' }}"
                   :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">

                    <!-- Icon -->
                    <div class="flex-shrink-0 transition-colors duration-200">
                        <x-dynamic-component :component="'heroicon-o-'.$item['icon']" class="w-6 h-6" />
                    </div>

                    <!-- Label -->
                    <span x-show="sidebarExpanded"
                          class="whitespace-nowrap overflow-hidden transition-all duration-300 origin-left"
                          x-transition:enter="transition ease-out duration-200"
                          x-transition:enter-start="opacity-0 translate-x-2"
                          x-transition:enter-end="opacity-100 translate-x-0">
                        {{ $item['label'] }}
                    </span>
                </a>

                <!-- Floating Tooltip (Collapsed State) -->
                <div x-show="!sidebarExpanded && tooltip"
                     x-transition.opacity.duration.200ms
                     class="absolute left-full top-1/2 -translate-y-1/2 ml-2 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-md shadow-lg whitespace-nowrap z-50 pointer-events-none"
                     style="display: none;">
                    {{ $item['label'] }}
                    <!-- Little arrow -->
                    <div class="absolute top-1/2 right-full -translate-y-1/2 -mr-1 border-4 border-transparent border-r-gray-900 dark:border-r-gray-700"></div>
                </div>
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
                class="flex items-center w-full p-2.5 rounded-lg text-gray-700 dark:text-gray-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200 group relative"
                :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">

                <x-heroicon-o-arrow-left-on-rectangle class="w-6 h-6 flex-shrink-0" />

                <span x-show="sidebarExpanded"
                      class="whitespace-nowrap transition-all duration-300 font-medium">
                    {{ __('admin.logout') }}
                </span>

                <!-- Tooltip -->
                <div x-show="!sidebarExpanded && tooltip"
                     x-transition.opacity.duration.200ms
                     class="absolute left-full top-1/2 -translate-y-1/2 ml-2 px-2 py-1 bg-gray-900 dark:bg-gray-700 text-white text-xs rounded-md shadow-lg whitespace-nowrap z-50 pointer-events-none"
                     style="display: none;">
                    {{ __('admin.logout') }}
                </div>
            </button>
        </form>
    </div>
</aside>
