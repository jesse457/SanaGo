<aside x-cloak
    :class="{
        '-translate-x-full': !mobileOpen,
        'translate-x-0': mobileOpen,
        'w-64': sidebarExpanded,
        'w-[72px]': !sidebarExpanded
    }"
    class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 shadow-sm lg:translate-x-0 transition-all duration-300 ease-in-out group"
    @keydown.escape.window="mobileOpen = false">

    {{-- 1. BRAND --}}
    <div class="h-16 flex items-center px-4 border-b border-gray-100 dark:border-gray-800 relative flex-shrink-0">
        <a href="{{ route('landlord.dashboard') }}" class="flex items-center gap-3 overflow-hidden whitespace-nowrap">
            <div class="w-10 h-10 flex items-center justify-center flex-shrink-0  text-white">
                     <img class="h-6 w-8" src="{{ asset('images/logo.png') }}" alt="Sana Go Health System Logo">
            </div>
            <div class="flex flex-col transition-opacity duration-300"
                 :class="sidebarExpanded ? 'opacity-100' : 'opacity-0 w-0 hidden'">
                <span class="font-bold text-gray-900 dark:text-white leading-none tracking-tight text-base">SanaGo</span>
                <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider mt-0.5">Super Admin</span>
            </div>
        </a>
        <button @click="toggleSidebar()" class="hidden lg:flex absolute -right-3 top-1/2 -translate-y-1/2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-indigo-600 rounded-full p-1 shadow-md z-50 transition-transform duration-300" :class="{ 'rotate-180': !sidebarExpanded }">
            <x-heroicon-s-chevron-left class="w-3 h-3" />
        </button>
        <button @click="mobileOpen = false" class="lg:hidden ml-auto text-gray-500"><x-heroicon-m-x-mark class="w-6 h-6" /></button>
    </div>

    {{-- 2. NAV --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-6 px-3 space-y-1 custom-scrollbar">
        <div class="px-3 mb-2 transition-all duration-300" :class="sidebarExpanded ? 'opacity-100' : 'opacity-0 h-0 overflow-hidden'">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest font-mono">Main Menu</p>
        </div>

        @php
            $menuItems = [
                ['route' => 'landlord.dashboard', 'icon' => 'squares-2x2', 'label' => 'Dashboard'],
                ['route' => 'landlord.manage-tenants', 'icon' => 'users', 'label' => 'Manage Tenants'],
                ['route' => 'landlord.feedbacks', 'icon' => 'chat-bubble-left-right', 'label' => 'Complaints'],
                ['route' => 'landlord.settings', 'icon' => 'cog-6-tooth', 'label' => 'Settings'],
            ];
        @endphp

        <ul class="space-y-1">
            @foreach($menuItems as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp
            <li x-data="{ tooltip: false }" @mouseleave="tooltip = false" class="relative">
                <a href="{{ route($item['route']) }}" wire:navigate @mouseenter="if(!sidebarExpanded) tooltip = true"
                   class="flex items-center rounded-lg px-3 py-2.5 transition-all duration-200 group relative {{ $isActive ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}">
                    <div class="flex-shrink-0 transition-colors {{ $isActive ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-600' }}">
                        <x-dynamic-component :component="'heroicon-o-'.$item['icon']" class="w-5 h-5" />
                    </div>
                    <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300 origin-left text-sm font-medium" :class="sidebarExpanded ? 'w-auto opacity-100' : 'w-0 opacity-0 hidden'">{{ $item['label'] }}</span>
                    <div x-show="!sidebarExpanded && tooltip" x-cloak class="absolute left-14 z-50 px-2 py-1 bg-gray-900 text-white text-xs rounded shadow-lg whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none">{{ $item['label'] }}</div>
                </a>
            </li>
            @endforeach
        </ul>
    </nav>

    {{-- 3. PROFILE --}}
    <div class="p-4 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900">
        <div class="flex items-center gap-3 transition-all duration-300" :class="sidebarExpanded ? 'justify-start' : 'justify-center'">
            <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold text-sm border border-indigo-200 flex-shrink-0">{{ substr(auth()->user()->name, 0, 1) }}</div>
            <div class="flex-1 overflow-hidden" x-show="sidebarExpanded" x-transition.opacity>
                <div class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</div>
                <div class="truncate text-xs text-gray-500">Super Admin</div>
            </div>
            <form method="POST" action="{{ route('auth.logout') }}" x-show="sidebarExpanded">@csrf<button type="submit" class="text-gray-400 hover:text-red-600 transition-colors p-1"><x-heroicon-m-arrow-right-on-rectangle class="w-5 h-5" /></button></form>
        </div>
    </div>
</aside>
