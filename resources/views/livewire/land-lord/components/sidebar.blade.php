<aside x-cloak
    :class="{
        '-translate-x-full': !mobileOpen,
        'translate-x-0': mobileOpen,
        'lg:w-64': sidebarExpanded,
        'lg:w-20': !sidebarExpanded,
        'w-64': true {{-- Always wide on mobile when visible --}}
    }"
    class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 shadow-xl lg:shadow-sm lg:translate-x-0 transition-all duration-300 ease-in-out"
    @keydown.escape.window="mobileOpen = false">

    {{-- 1. BRAND --}}
    <div class="h-16 flex items-center px-4 border-b border-gray-100 dark:border-gray-800 relative flex-shrink-0">
        <a href="{{ route('landlord.dashboard') }}" class="flex items-center gap-3 overflow-hidden">
            <div class="w-10 h-10 flex items-center justify-center flex-shrink-0">
                <img class="h-8 w-auto" src="{{ asset('images/logo.webp') }}" alt="Logo">
            </div>
            <div class="flex flex-col transition-all duration-300"
                 :class="sidebarExpanded ? 'opacity-100' : 'lg:opacity-0 lg:w-0'">
                <span class="font-bold text-gray-900 dark:text-white leading-none text-base">SanaGo</span>
                <span class="text-[10px] font-semibold text-gray-500 uppercase mt-0.5">Super Admin</span>
            </div>
        </a>

        <!-- Desktop Toggle Button -->
        <button @click="toggleSidebar()"
            class="hidden lg:flex absolute -right-3 top-1/2 -translate-y-1/2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-indigo-600 rounded-full p-1 shadow-md z-50 transition-transform duration-300"
            :class="{ 'rotate-180': !sidebarExpanded }">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>

        <!-- Mobile Close Button -->
        <button @click="mobileOpen = false" class="lg:hidden ml-auto p-2 text-gray-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    {{-- 2. NAV --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
        @php
            $menuItems = [
                ['route' => 'landlord.dashboard', 'icon' => 'squares-2x2', 'label' => 'Dashboard'],
                ['route' => 'landlord.manage-tenants', 'icon' => 'users', 'label' => 'Tenants'],
                ['route' => 'landlord.feedbacks', 'icon' => 'chat-bubble-left-right', 'label' => 'Complaints'],
                ['route' => 'landlord.settings', 'icon' => 'cog-6-tooth', 'label' => 'Settings'],
            ];
        @endphp

        <ul class="space-y-1">
            @foreach($menuItems as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp
            <li x-data="{ tooltip: false }" class="relative">
                <a href="{{ route($item['route']) }}" wire:navigate
                   @mouseenter="if(window.innerWidth > 1024 && !sidebarExpanded) tooltip = true"
                   @mouseleave="tooltip = false"
                   class="flex items-center rounded-lg px-3 py-2.5 transition-all duration-200 {{ $isActive ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800' }}">

                    <div class="flex-shrink-0 {{ $isActive ? 'text-indigo-600' : 'text-gray-400' }}">
                        <x-dynamic-component :component="'heroicon-o-'.$item['icon']" class="w-6 h-6" />
                    </div>

                    <span class="ml-3 text-sm font-medium transition-all duration-300"
                          :class="sidebarExpanded ? 'opacity-100' : 'lg:opacity-0 lg:w-0 overflow-hidden'">
                        {{ $item['label'] }}
                    </span>

                    <!-- Desktop Tooltip (only when collapsed) -->
                    <div x-show="tooltip" x-cloak
                         class="fixed left-20 px-2 py-1 bg-gray-900 text-white text-xs rounded shadow-lg z-[60]">
                        {{ $item['label'] }}
                    </div>
                </a>
            </li>
            @endforeach
        </ul>
    </nav>

    {{-- 3. PROFILE / LOGOUT --}}
    <div class="p-4 border-t border-gray-100 dark:border-gray-800">
        <div class="flex items-center gap-3" :class="sidebarExpanded ? 'justify-start' : 'lg:justify-center'">
            <div class="w-9 h-9 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold flex-shrink-0">
                {{ substr(auth()->user()->name, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0" x-show="sidebarExpanded || (window.innerWidth < 1024)">
                <p class="text-sm font-semibold truncate">{{ auth()->user()->name }}</p>
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit" class="text-xs text-red-500 hover:underline">Logout</button>
                </form>
            </div>
        </div>
    </div>
</aside>
