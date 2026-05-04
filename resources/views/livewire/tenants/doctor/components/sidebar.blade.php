
{{-- 1. MOBILE BACKDROP (Darkens the background when menu is open) --}}



{{-- 2. SIDEBAR --}}
<aside
    x-cloak
    :class="{
        '-translate-x-full': !mobileOpen,
        'translate-x-0': mobileOpen,
        'w-72 lg:w-64': sidebarExpanded,
        'w-72 lg:w-[72px]': !sidebarExpanded
    }"
    class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 shadow-xl lg:shadow-sm lg:translate-x-0 transition-all duration-300 ease-in-out group"
    @keydown.escape.window="mobileOpen = false"
>
    {{-- 1. BRAND HEADER --}}
    <div class="h-16 flex items-center px-4 border-b border-gray-100 dark:border-gray-800 relative flex-shrink-0">
        {{-- Logo --}}
        <a href="{{ route('doctor.dashboard') }}" class="flex items-center gap-3 overflow-hidden whitespace-nowrap">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center flex-shrink-0 shadow-lg shadow-blue-500/30 text-white">
                <x-heroicon-m-plus class="w-6 h-6 stroke-[3]" />
            </div>

            <div class="flex flex-col transition-opacity duration-300"
                 :class="(sidebarExpanded || mobileOpen) ? 'opacity-100' : 'lg:opacity-0 lg:w-0 lg:hidden'">
                <span class="font-bold text-gray-900 dark:text-white leading-none tracking-tight text-base uppercase">
                    {{ tenant('name') ?? 'Medical' }}
                </span>
                <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider mt-0.5">
                    Doctor Portal
                </span>
            </div>
        </a>

        {{-- Toggle Button (Desktop Only) --}}
        <button @click="toggleSidebar()"
                class="hidden lg:flex absolute -right-3 top-1/2 -translate-y-1/2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-500 hover:text-blue-600 rounded-full p-1 shadow-md z-50 hover:scale-110 transition-all"
                title="Toggle Sidebar">
            <x-heroicon-s-chevron-left class="w-3 h-3 transition-transform duration-300"
                x-bind:class="{ 'rotate-180': !sidebarExpanded }" />
        </button>

        {{-- Close Button (Mobile Only) --}}
        <button @click="mobileOpen = false" class="lg:hidden ml-auto p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg">
            <x-heroicon-m-x-mark class="w-6 h-6" />
        </button>
    </div>

    {{-- 2. NAVIGATION --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-6 px-3 space-y-1 custom-scrollbar">

        {{-- Section Label --}}
        <div class="px-3 mb-2 transition-all duration-300"
             :class="(sidebarExpanded || mobileOpen) ? 'opacity-100' : 'lg:opacity-0 lg:h-0 lg:overflow-hidden'">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest font-mono">Main Menu</p>
        </div>

        @php
            $navItems = [
                ['route' => 'doctor.dashboard', 'label' => 'Dashboard', 'icon' => 'squares-2x2'],
                ['route' => 'doctor.appointments', 'label' => 'Appointments', 'icon' => 'calendar-days'],
                ['route' => 'doctor.patients', 'label' => 'My Patients', 'icon' => 'users'],
                ['route' => 'doctor.medical-records', 'label' => 'Consultations', 'icon' => 'clipboard-document-list'],
                ['route' => 'doctor.lab-request', 'label' => 'Lab Requests', 'icon' => 'beaker'],
                ['route' => 'doctor.feedbacks', 'label' => 'Feedback', 'icon' => 'chat-bubble-left-right'],
            ];
        @endphp

        <ul class="space-y-1">
            @foreach($navItems as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp

            <li x-data="{ tooltip: false }" @mouseleave="tooltip = false" class="relative">
                <a href="{{ route($item['route']) }}"
                   wire:navigate
                   @mouseenter="if(!sidebarExpanded && !mobileOpen) tooltip = true"
                   class="flex items-center rounded-lg px-3 py-2.5 transition-all duration-200 group relative
                          {{ $isActive
                             ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 font-semibold'
                             : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-white'
                          }}">

                    {{-- Active Indicator Line (Mini mode desktop only) --}}
                    @if($isActive)
                        <div class="absolute left-0 top-1.5 bottom-1.5 w-1 bg-blue-600 rounded-r-full lg:block hidden"
                             x-show="!sidebarExpanded"></div>
                    @endif

                    <!-- Icon -->
                    <div class="flex-shrink-0 transition-colors {{ $isActive ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300' }}">
                        <x-dynamic-component :component="'heroicon-o-'.$item['icon']" class="w-5 h-5" />
                    </div>

                    <!-- Label: Visible if sidebar is expanded OR if on mobile -->
                    <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300 origin-left text-sm font-medium"
                          :class="(sidebarExpanded || mobileOpen) ? 'w-auto opacity-100' : 'lg:w-0 lg:opacity-0 lg:hidden'">
                        {{ $item['label'] }}
                    </span>

                    {{-- Tooltip: Only for Desktop Mini-mode --}}
                    <div x-show="!sidebarExpanded && !mobileOpen && tooltip" x-cloak
                         class="absolute left-14 z-50 px-2 py-1 bg-gray-900 text-white text-xs rounded shadow-lg whitespace-nowrap pointer-events-none">
                        {{ $item['label'] }}
                    </div>
                </a>
            </li>
            @endforeach
        </ul>
    </nav>

    {{-- 3. USER PROFILE FOOTER --}}
    <div class="p-4 border-t border-gray-100 dark:border-gray-800 bg-white dark:bg-gray-900">
        <div class="flex items-center gap-3 transition-all duration-300"
             :class="(sidebarExpanded || mobileOpen) ? 'justify-start' : 'lg:justify-center'">

            {{-- Avatar --}}
            <div class="relative flex-shrink-0 group cursor-pointer">
                @if(auth()->user()->profile_picture)
                    <img src="{{ Storage::disk('s3')->temporaryUrl(auth()->user()->profile_picture, now()->addMinutes(60)) }}"
                         class="w-9 h-9 rounded-full object-cover border border-gray-200 dark:border-gray-700 bg-gray-50"
                         alt="User">
                @else
                    <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300 flex items-center justify-center font-bold text-xs border border-blue-200">
                        {{ substr(auth()->user()->name, 0, 2) }}
                    </div>
                @endif
                <div class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white dark:border-gray-900 rounded-full"></div>
            </div>

            {{-- Text Info --}}
            <div class="flex-1 overflow-hidden" x-show="sidebarExpanded || mobileOpen" x-transition.opacity>
                <div class="truncate text-sm font-semibold text-gray-900 dark:text-white">
                    {{ auth()->user()->name }}
                </div>
                <div class="truncate text-xs text-gray-500 dark:text-gray-400">
                    {{ auth()->user()->email }}
                </div>
            </div>

            {{-- Logout Button --}}
            <form method="POST" action="{{ route('auth.logout') }}" x-show="sidebarExpanded || mobileOpen">
                @csrf
                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Logout">
                    <x-heroicon-m-arrow-right-on-rectangle class="w-5 h-5" />
                </button>
            </form>
        </div>
    </div>
</aside>
