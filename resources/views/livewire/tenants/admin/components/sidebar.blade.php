<aside x-cloak
    {{-- MODIFIED: Classes now ensure wide width on mobile and toggle only on desktop --}}
    :class="{
        '-translate-x-full': !mobileOpen,
        'translate-x-0': mobileOpen,
        'w-64': mobileOpen || sidebarExpanded, {{-- Always wide on mobile when open --}}
        'lg:w-64': sidebarExpanded,           {{-- Toggle width on desktop --}}
        'lg:w-[72px]': !sidebarExpanded
    }"
    class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 shadow-xl lg:shadow-sm lg:translate-x-0 transition-all duration-300 ease-in-out group"
    @keydown.escape.window="mobileOpen = false">

    {{-- 1. BRAND --}}
    <div class="h-16 flex items-center px-4 border-b border-gray-100 dark:border-gray-800 relative flex-shrink-0">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 overflow-hidden whitespace-nowrap">
            @if (tenant('logo'))
                <img src="{{ Storage::disk('s3')->temporaryUrl(tenant('logo'), now()->addMinutes(60)) }}" alt="Logo" class="h-10 rounded-2xl w-auto">
            @else
                <img src="{{ Storage::disk('central_public')->url('images/logo.png') }}" alt="Logo" class="h-8 w-auto">
            @endif

            {{-- MODIFIED: Text hidden logic only applies to desktop (lg:) --}}
            <div class="flex flex-col transition-opacity duration-300"
                 :class="sidebarExpanded ? 'opacity-100' : 'lg:opacity-0 lg:w-0 lg:hidden'">
                <span class="font-bold text-gray-900 dark:text-white leading-none tracking-tight text-base">Hospital</span>
                <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider mt-0.5">Admin</span>
            </div>
        </a>

        {{-- Desktop Toggle --}}
        <button @click="toggleSidebar()" class="hidden lg:flex absolute -right-3 top-1/2 -translate-y-1/2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-blue-600 rounded-full p-1 shadow-md z-50 transition-transform duration-300" :class="{ 'rotate-180': !sidebarExpanded }">
            <x-heroicon-s-chevron-left class="w-3 h-3" />
        </button>

        {{-- Mobile Close --}}
        <button @click="mobileOpen = false" class="lg:hidden ml-auto text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 p-1 rounded-md">
            <x-heroicon-m-x-mark class="w-6 h-6" />
        </button>
    </div>

    {{-- 2. NAV --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-6 px-3 space-y-1 custom-scrollbar">
        <div class="px-3 mb-2 transition-all duration-300"
             :class="sidebarExpanded ? 'opacity-100' : 'lg:opacity-0 lg:h-0 lg:overflow-hidden'">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest font-mono">Main Menu</p>
        </div>

        @php
            $menuItems = [
                ['route' => 'admin.dashboard', 'icon' => 'squares-2x2', 'label' => 'Dashboard'],
                ['route' => 'admin.user-shifts', 'icon' => 'calendar-days', 'label' => 'Shifts'],
                ['route' => 'admin.revenue-report', 'icon' => 'banknotes', 'label' => 'Revenue'],
                ['route' => 'admin.user-management', 'icon' => 'users', 'label' => 'Users'],
                ['route' => 'admin.user-activities', 'icon' => 'clock', 'label' => 'Activities'],
                ['route' => 'admin.feedback-history', 'icon' => 'document-text', 'label' => 'Feedback'],
                ['route' => 'admin.settings', 'icon' => 'cog-6-tooth', 'label' => 'Settings'],
            ];
        @endphp

        <ul class="space-y-1">
            @foreach($menuItems as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp
            <li x-data="{ tooltip: false }" @mouseleave="tooltip = false" class="relative">
                <a href="{{ route($item['route']) }}" wire:navigate @mouseenter="if(!sidebarExpanded) tooltip = true"
                   class="flex items-center rounded-lg px-3 py-2.5 transition-all duration-200 group relative {{ $isActive ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 font-semibold' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white' }}">
                    <div class="flex-shrink-0 transition-colors {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-gray-600' }}">
                        <x-dynamic-component :component="'heroicon-o-'.$item['icon']" class="w-5 h-5" />
                    </div>

                    {{-- MODIFIED: Label hidden only on desktop (lg:) when collapsed --}}
                    <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300 origin-left text-sm font-medium"
                          :class="sidebarExpanded ? 'w-auto opacity-100' : 'lg:w-0 lg:opacity-0 lg:hidden'">
                        {{ $item['label'] }}
                    </span>

                    {{-- Tooltip only for desktop --}}
                    <div x-show="!sidebarExpanded && tooltip" x-cloak class="hidden lg:block absolute left-14 z-50 px-2 py-1 bg-gray-900 text-white text-xs rounded shadow-lg whitespace-nowrap opacity-0 group-hover:opacity-100 pointer-events-none">{{ $item['label'] }}</div>
                </a>
            </li>
            @endforeach
        </ul>
    </nav>

    {{-- 3. PROFILE --}}
    <div class="p-4 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900">
        <div class="flex items-center gap-3 transition-all duration-300"
             :class="(mobileOpen || sidebarExpanded) ? 'justify-start' : 'lg:justify-center'">
            <div class="relative flex-shrink-0">
                @if(auth()->user()->profile_picture)
                    <img src="{{ Storage::disk('s3')->temporaryUrl(auth()->user()->profile_picture, now()->addMinutes(60)) }}" class="w-9 h-9 rounded-full object-cover border border-gray-200">
                @else
                    <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 flex items-center justify-center font-bold text-xs border border-blue-200">{{ substr(auth()->user()->name, 0, 1) }}</div>
                @endif
                <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></div>
            </div>

            {{-- MODIFIED: Profile info text visibility logic --}}
            <div class="flex-1 overflow-hidden transition-opacity duration-300"
                 :class="sidebarExpanded ? 'opacity-100' : 'lg:opacity-0 lg:w-0 lg:hidden'">
                <div class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</div>
                <div class="truncate text-xs text-gray-500">{{ tenant('hospital_name') }}</div>
            </div>

            <form method="POST" action="{{ route('auth.logout') }}"
                  :class="sidebarExpanded ? 'block' : 'lg:hidden'">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors p-1">
                    <x-heroicon-m-arrow-right-on-rectangle class="w-5 h-5" />
                </button>
            </form>
        </div>
    </div>
</aside>
