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
            class="hidden lg:flex absolute -right-3 top-12 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-full p-1 shadow-sm z-40 transition-transform duration-300 focus:outline-none items-center justify-center w-6 h-6"
            :class="{ 'rotate-180': !sidebarExpanded }">
        <x-heroicon-o-chevron-left class="w-3 h-3" />
    </button>

    <!-- Mobile Header (Close Button) -->
    <div class="flex items-center justify-between p-4 border-b border-gray-100 dark:border-gray-700 lg:hidden">
        <span class="font-bold text-lg text-blue-600 dark:text-blue-400">Menu</span>
        <button @click="mobileOpen = false" class="p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700">
            <x-heroicon-o-x-mark class="w-6 h-6" />
        </button>
    </div>

    <!-- User Profile Section -->
    <div class="py-6 border-b border-gray-100 dark:border-gray-700 bg-gradient-to-b from-blue-50/50 to-transparent dark:from-gray-800 dark:to-gray-800 transition-all duration-300 flex flex-col items-center">
        
        <!-- Avatar -->
        <div class="relative inline-block transition-all duration-300 group" :class="sidebarExpanded ? 'mb-3' : 'mb-0'">
            @if(auth()->user()->profile_picture)
                <img src="{{ Storage::disk('s3')->temporaryUrl(auth()->user()->profile_picture, now()->addMinutes(60)) }}"
                     alt="{{ auth()->user()->name }}"
                     class="rounded-full object-cover border-4 border-white dark:border-gray-700 shadow-md transition-all duration-300"
                     :class="sidebarExpanded ? 'w-20 h-20' : 'w-10 h-10 border-2'">
            @else
                <div class="rounded-full bg-blue-600 text-white flex items-center justify-center font-bold border-4 border-white dark:border-gray-700 shadow-md transition-all duration-300"
                     :class="sidebarExpanded ? 'w-20 h-20 text-2xl' : 'w-10 h-10 text-sm border-2'">
                    {{ substr(auth()->user()->name ?? 'User', 0, 1) }}
                </div>
            @endif
            
            <!-- Active Status Dot -->
            <span class="absolute bottom-0 right-0 bg-green-500 border-2 border-white dark:border-gray-800 rounded-full transition-all duration-300"
                  :class="sidebarExpanded ? 'w-4 h-4 right-1 bottom-1' : 'w-2.5 h-2.5 right-0 bottom-0'"></span>
        </div>

        <!-- Text Info (Hidden when collapsed) -->
        <div x-show="sidebarExpanded"
             x-transition:enter="transition ease-out duration-200 delay-100"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             class="text-center overflow-hidden whitespace-nowrap px-2 w-full">
             
            <h3 class="font-bold text-gray-900 dark:text-white truncate max-w-[12rem] mx-auto">{{ auth()->user()->name ?? 'Guest' }}</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-semibold mt-1">
                {{ ucfirst(auth()->user()->role ?? 'Guest') }}
            </p>
            <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 truncate max-w-[10rem]">
                {{ tenant('name') ?? 'Main Clinic' }}
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 space-y-1 custom-scrollbar">

        <!-- Section Label -->
        <div class="mb-2 px-4 transition-all duration-300" x-show="sidebarExpanded" x-transition.opacity>
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">{{ __('doctor.menu_main') }}</p>
        </div>

        <ul class="space-y-1 px-3">
            @php
                $navItems = [
                    [
                        'route' => 'doctor.dashboard',
                        'label' => __('doctor.dashboard'),
                        'icon' => 'home'
                    ],
                    [
                        'route' => 'doctor.patients',
                        'label' => __('doctor.patients'),
                        'icon' => 'users'
                    ],
                    [
                        'route' => 'doctor.appointments',
                        'label' => __('doctor.appointments'),
                        'icon' => 'calendar'
                    ],
                    [
                        'route' => 'doctor.medical-records',
                        'label' => __('doctor.consultations'),
                        'icon' => 'clipboard'
                    ],
                    [
                        'route' => 'doctor.lab-request',
                        'label' => __('doctor.lab_requests'),
                        'icon' => 'beaker'
                    ],
                    [
                        'route' => 'doctor.feedbacks',
                        'label' => __('doctor.feedback'),
                        'icon' => 'chat'
                    ],
                ];
            @endphp

            @foreach($navItems as $item)
            <li x-data="{ tooltip: false }" @mouseleave="tooltip = false">
                <a href="{{ route($item['route']) }}"
                   wire:navigate
                   @mouseenter="if(!sidebarExpanded) tooltip = true"
                   class="flex items-center p-2.5 rounded-lg transition-all duration-200 group relative
                          {{ request()->routeIs($item['route'])
                             ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 border-l-4 border-blue-600 dark:border-blue-400 shadow-sm'
                             : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white' }}"
                   :class="sidebarExpanded ? 'justify-start gap-3 pl-2' : 'justify-center pl-2.5 border-l-0'">

                    <!-- Icon -->
                    <div class="flex-shrink-0 transition-colors duration-200">
                        @if($item['icon'] == 'home') <x-heroicon-o-home class="w-6 h-6" />
                        @elseif($item['icon'] == 'users') <x-heroicon-o-users class="w-6 h-6" />
                        @elseif($item['icon'] == 'calendar') <x-heroicon-o-calendar-days class="w-6 h-6" />
                        @elseif($item['icon'] == 'clipboard') <x-heroicon-o-clipboard-document-list class="w-6 h-6" />
                        @elseif($item['icon'] == 'beaker') <x-heroicon-o-beaker class="w-6 h-6" />
                        @elseif($item['icon'] == 'chat') <x-heroicon-o-chat-bubble-bottom-center-text class="w-6 h-6" />
                        @endif
                    </div>

                    <!-- Text -->
                    <span x-show="sidebarExpanded"
                          class="whitespace-nowrap overflow-hidden transition-all duration-300 origin-left text-sm font-medium"
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

    <!-- Logout Footer -->
    <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit"
                x-data="{ tooltip: false }"
                @mouseenter="if(!sidebarExpanded) tooltip = true"
                @mouseleave="tooltip = false"
                class="flex items-center w-full p-2 rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200 group relative"
                :class="sidebarExpanded ? 'justify-start gap-3' : 'justify-center'"
                title="{{ __('doctor.logout') }}">

                <x-heroicon-o-arrow-left-on-rectangle class="w-6 h-6 flex-shrink-0 group-hover:scale-110 transition-transform" />

                <span x-show="sidebarExpanded"
                      class="whitespace-nowrap transition-all duration-300 text-sm font-medium">
                    {{ __('doctor.logout') }}
                </span>

                <!-- Tooltip for Logout -->
                <div x-show="!sidebarExpanded && tooltip"
                     x-transition.opacity.duration.200ms
                     class="absolute left-full top-1/2 -translate-y-1/2 ml-2 bg-gray-900 text-white text-xs rounded px-2 py-1 z-50 whitespace-nowrap shadow-lg pointer-events-none"
                     style="display: none;">
                    {{ __('doctor.logout') }}
                </div>
            </button>
        </form>
    </div>
</aside>