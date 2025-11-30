<aside
    x-data="{ 
        sidebarExpanded: localStorage.getItem('sidebarExpanded') === 'true',
        mobileOpen: false,
        toggleSidebar() {
            this.sidebarExpanded = !this.sidebarExpanded;
            localStorage.setItem('sidebarExpanded', this.sidebarExpanded);
        }
    }"
    x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebarExpanded', value))"
    x-cloak
    :class="{
        '-translate-x-full': !mobileOpen,
        'translate-x-0': mobileOpen,
        'w-64': sidebarExpanded,
        'w-20': !sidebarExpanded
    }"
    class="fixed inset-y-0 left-0 bg-white border-r border-gray-200 shadow-xl z-30 transition-all duration-300 ease-in-out flex flex-col lg:translate-x-0"
    @keydown.escape.window="mobileOpen = false"
>

    <!-- Toggle Button (Desktop Only) -->
    <button @click="toggleSidebar()"
            class="hidden lg:flex absolute -right-3 top-12 bg-white border border-gray-200 text-gray-500 hover:text-blue-600 rounded-full p-1 shadow-sm z-40 transition-transform duration-300 focus:outline-none items-center justify-center w-6 h-6"
            :class="{ 'rotate-180': !sidebarExpanded }">
        <x-heroicon-o-chevron-left class="w-3 h-3" />
    </button>

    <!-- Mobile Header (Close Button) -->
    <div class="flex items-center justify-between p-4 lg:hidden">
        <h2 class="text-xl font-bold text-blue-700">Navigation</h2>
        <button @click="mobileOpen = false" class="p-2 rounded-full text-gray-700 hover:bg-gray-100">
            <x-heroicon-o-x-mark class="w-6 h-6" />
        </button>
    </div>

    <!-- User Info Section -->
    <div class="user-info-section py-6 border-b border-gray-100 transition-all duration-300 flex flex-col items-center">
        <!-- Avatar -->
        <div class="transition-all duration-300" :class="sidebarExpanded ? 'mb-3' : 'mb-0'">
            @if(auth()->user()->profile_picture)
                <img src="{{ Storage::disk('s3')->temporaryUrl(auth()->user()->profile_picture, now()->addMinutes(5)) }}"
                     alt="Avatar"
                     class="rounded-full object-cover border-2 border-blue-100 transition-all duration-300"
                     :class="sidebarExpanded ? 'w-16 h-16' : 'w-10 h-10'">
            @else
                <div class="bg-blue-50 rounded-full flex items-center justify-center text-blue-600 font-bold border-2 border-blue-100 transition-all duration-300"
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
             class="text-center overflow-hidden whitespace-nowrap px-2">
            <h3 class="text-base font-semibold text-gray-800 truncate max-w-[12rem] mx-auto">{{ auth()->user()->name }}</h3>
            <p class="text-xs text-gray-500">{{ ucfirst(auth()->user()->role) }}</p>
            <p class="text-xs text-blue-600 font-medium mt-1 truncate max-w-[12rem] mx-auto">{{ tenant('hospital_name') }}</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto custom-scrollbar py-4 space-y-1">

        <!-- Section Title -->
        <div class="mb-2 px-4 transition-all duration-300" x-show="sidebarExpanded" x-transition.opacity>
            <h3 class="text-xs uppercase text-gray-400 font-bold tracking-wider">{{ __('admin.dashboard_title') }}</h3>
        </div>

        <ul class="space-y-1 px-3">
            @php
                $menuItems = [
                    ['route' => 'admin.dashboard', 'icon' => 'home', 'label' => __('admin.dashboard_title')],
                    ['route' => 'admin.user-shifts', 'icon' => 'calendar-days', 'label' => __('admin.shifts_bar')],
                    ['route' => 'admin.revenue-report', 'icon' => 'banknotes', 'label' => __('admin.revenue_report_bar')],
                    ['route' => 'admin.user-management', 'icon' => 'users', 'label' => __('admin.user_management_bar')],
                    ['route' => 'admin.settings', 'icon' => 'cog-6-tooth', 'label' => __('admin.settings_bar')],
                    ['route' => 'admin.user-activities', 'icon' => 'clock', 'label' => __('admin.user_activities_bar')],
                    ['route' => 'admin.feedback-history', 'icon' => 'document-text', 'label' => __('admin.feedbacks_bar')],
                ];
            @endphp

            @foreach($menuItems as $item)
            <li x-data="{ tooltip: false }" @mouseleave="tooltip = false">
                <a href="{{ route($item['route']) }}"
                   wire:navigate
                   @mouseenter="if(!sidebarExpanded) tooltip = true"
                   class="flex items-center p-2.5 rounded-lg transition-all duration-200 group relative
                          {{ request()->routeIs($item['route'])
                             ? 'bg-blue-50 text-blue-700 font-medium shadow-sm ring-1 ring-blue-100'
                             : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600' }}"
                   :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">

                    <!-- Icon -->
                    <div class="flex-shrink-0 transition-colors duration-200">
                        @if($item['icon'] == 'home') <x-heroicon-o-home class="w-6 h-6" />
                        @elseif($item['icon'] == 'calendar-days') <x-heroicon-o-calendar-days class="w-6 h-6" />
                        @elseif($item['icon'] == 'banknotes') <x-heroicon-o-banknotes class="w-6 h-6" />
                        @elseif($item['icon'] == 'users') <x-heroicon-o-users class="w-6 h-6" />
                        @elseif($item['icon'] == 'cog-6-tooth') <x-heroicon-o-cog-6-tooth class="w-6 h-6" />
                        @elseif($item['icon'] == 'clock') <x-heroicon-o-clock class="w-6 h-6" />
                        @elseif($item['icon'] == 'document-text') <x-heroicon-o-document-text class="w-6 h-6" />
                        @endif
                    </div>

                    <!-- Text -->
                    <span x-show="sidebarExpanded"
                          class="whitespace-nowrap overflow-hidden transition-all duration-300 origin-left"
                          x-transition:enter="transition ease-out duration-200"
                          x-transition:enter-start="opacity-0 translate-x-2"
                          x-transition:enter-end="opacity-100 translate-x-0">
                        {{ $item['label'] }}
                    </span>

                    <!-- Tooltip -->
                    <div x-show="!sidebarExpanded && tooltip"
                         x-transition.opacity.duration.200ms
                         class="absolute left-full top-1/2 -translate-y-1/2 ml-2 bg-gray-900 text-white text-xs rounded px-2 py-1 z-50 whitespace-nowrap shadow-lg pointer-events-none"
                         style="display: none;">
                        {{ $item['label'] }}
                    </div>
                </a>
            </li>
            @endforeach
        </ul>
    </nav>

    <!-- Footer / Logout -->
    <div class="p-3 border-t border-gray-200 bg-gray-50">
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit"
                x-data="{ tooltip: false }"
                @mouseenter="if(!sidebarExpanded) tooltip = true"
                @mouseleave="tooltip = false"
                class="flex items-center w-full p-2 rounded-lg text-gray-700 hover:bg-red-50 hover:text-red-600 transition-colors duration-200 group relative"
                :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'">

                <x-heroicon-o-arrow-left-on-rectangle class="w-6 h-6 flex-shrink-0" />

                <span x-show="sidebarExpanded"
                      class="whitespace-nowrap transition-all duration-300">
                    {{ __('admin.logout') }}
                </span>

                <!-- Tooltip for Logout -->
                <div x-show="!sidebarExpanded && tooltip"
                     x-transition.opacity.duration.200ms
                     class="absolute left-full top-1/2 -translate-y-1/2 ml-2 bg-gray-900 text-white text-xs rounded px-2 py-1 z-50 whitespace-nowrap shadow-lg pointer-events-none"
                     style="display: none;">
                    {{ __('admin.logout') }}
                </div>
            </button>
        </form>
    </div>
</aside>