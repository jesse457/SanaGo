
{{-- 2. SIDEBAR --}}
<aside x-cloak
    :class="{
        'translate-x-0': mobileOpen,
        '-translate-x-full': !mobileOpen,
        'w-72 lg:w-64': sidebarExpanded,
        'w-72 lg:w-[72px]': !sidebarExpanded
    }"
    class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 shadow-xl lg:shadow-sm lg:translate-x-0 transition-all duration-300 ease-in-out group"
    @keydown.escape.window="mobileOpen = false">

    <!-- Header / Logo Area -->
    <div class="h-16 flex items-center px-4 border-b border-gray-100 dark:border-gray-800 relative flex-shrink-0">

        <a href="{{ route('receptionist.dashboard') }}" class="flex items-center gap-3 overflow-hidden whitespace-nowrap">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-pink-500 to-rose-500 flex items-center justify-center flex-shrink-0 shadow-lg shadow-pink-500/30 text-white">
                <x-heroicon-m-users class="w-6 h-6" />
            </div>

            <div class="flex flex-col transition-opacity duration-300"
                 :class="(sidebarExpanded || mobileOpen) ? 'opacity-100' : 'lg:opacity-0 lg:w-0 lg:hidden'">
                <span class="font-bold text-gray-900 dark:text-white leading-none tracking-tight text-base">{{ __('Front Desk') }}</span>
                <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider mt-0.5">{{ __('Reception') }}</span>
            </div>
        </a>

        <!-- Desktop Toggle Button (Hidden on Mobile) -->
        <button @click="toggleSidebar()"
                class="hidden lg:flex absolute -right-3 top-1/2 -translate-y-1/2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-pink-600 rounded-full p-1 shadow-md z-50 transition-transform duration-300"
                :class="{ 'rotate-180': !sidebarExpanded }">
            <x-heroicon-s-chevron-left class="w-3 h-3" />
        </button>

        <!-- Mobile Close Button (Hidden on Desktop) -->
        <button @click="mobileOpen = false" class="lg:hidden ml-auto p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
            <x-heroicon-m-x-mark class="w-6 h-6" />
        </button>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-6 px-3 space-y-1 custom-scrollbar">
        <div class="px-3 mb-2 transition-all duration-300"
             :class="(sidebarExpanded || mobileOpen) ? 'opacity-100' : 'lg:opacity-0 lg:h-0 lg:overflow-hidden'">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest font-mono">{{ __('Main Menu') }}</p>
        </div>

        @php
            $menuItems = [
                ['route' => 'receptionist.dashboard', 'icon' => 'squares-2x2', 'label' => __('Dashboard')],
                ['route' => 'receptionist.appointments', 'icon' => 'calendar-days', 'label' => __('Appointments')],
                ['route' => 'receptionist.patients', 'icon' => 'users', 'label' => __('Patients')],
                ['route' => 'receptionist.checkin', 'icon' => 'user-plus', 'label' => __('Check-in/Admit')],
                ['route' => 'receptionist.feedback-history', 'icon' => 'chat-bubble-bottom-center-text', 'label' => __('Feedbacks')],
            ];
        @endphp

        <ul class="space-y-1">
            @foreach($menuItems as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp
            <li x-data="{ tooltip: false }" @mouseleave="tooltip = false" class="relative">
                <a href="{{ route($item['route']) }}"
                   wire:navigate
                   @mouseenter="if(!sidebarExpanded && !mobileOpen) tooltip = true"
                   class="flex items-center rounded-lg px-3 py-2.5 transition-all duration-200 group relative
                          {{ $isActive
                             ? 'bg-pink-50 dark:bg-pink-900/20 text-pink-700 dark:text-pink-400 font-semibold'
                             : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white'
                          }}">

                    <div class="flex-shrink-0 transition-colors {{ $isActive ? 'text-pink-600 dark:text-pink-400' : 'text-gray-400 group-hover:text-gray-600' }}">
                        <x-dynamic-component :component="'heroicon-o-'.$item['icon']" class="w-5 h-5" />
                    </div>

                    <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300 origin-left text-sm font-medium"
                          :class="(sidebarExpanded || mobileOpen) ? 'w-auto opacity-100' : 'lg:w-0 lg:opacity-0 lg:hidden'">
                        {{ $item['label'] }}
                    </span>

                    <!-- Tooltip for collapsed state desktop only -->
                    <div x-show="!sidebarExpanded && !mobileOpen && tooltip" x-cloak class="absolute left-14 z-50 px-2 py-1 bg-gray-900 text-white text-xs rounded shadow-lg whitespace-nowrap pointer-events-none">
                        {{ $item['label'] }}
                    </div>
                </a>
            </li>
            @endforeach
        </ul>
    </nav>

    <!-- User Footer -->
    <div class="p-4 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900">
        <div class="flex items-center gap-3 transition-all duration-300" :class="(sidebarExpanded || mobileOpen) ? 'justify-start' : 'lg:justify-center'">
            <div class="relative flex-shrink-0">
                @if(auth()->user()->profile_picture)
                    <img src="{{ Storage::disk('s3')->temporaryUrl(auth()->user()->profile_picture, now()->addMinutes(60)) }}" class="w-9 h-9 rounded-full object-cover border border-gray-200">
                @else
                    <div class="w-9 h-9 rounded-full bg-pink-100 dark:bg-pink-900 text-pink-700 dark:text-pink-300 flex items-center justify-center font-bold text-xs border border-pink-200">{{ substr(auth()->user()->name, 0, 1) }}</div>
                @endif
                <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white dark:border-gray-900 rounded-full"></div>
            </div>

            <div class="flex-1 overflow-hidden" x-show="sidebarExpanded || mobileOpen" x-transition.opacity>
                <div class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</div>
                <div class="truncate text-xs text-gray-500">{{ __('Receptionist') }}</div>
            </div>

            <form method="POST" action="{{ route('auth.logout') }}" x-show="sidebarExpanded || mobileOpen">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors p-1" title="{{ __('Logout') }}">
                    <x-heroicon-m-arrow-right-on-rectangle class="w-5 h-5" />
                </button>
            </form>
        </div>
    </div>
</aside>
