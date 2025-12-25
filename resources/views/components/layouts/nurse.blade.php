<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }

        /* Smooth transition for the margin change on Desktop */
        .main-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }

        /* Custom scrollbar for a modern look */
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
    </style>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-50 dark:bg-[#0f172a] h-full overflow-hidden"
      x-data="{
          sidebarExpanded: localStorage.getItem('sidebarExpanded') !== 'false',
          mobileOpen: false,
          toggleSidebar() {
              this.sidebarExpanded = !this.sidebarExpanded;
              localStorage.setItem('sidebarExpanded', this.sidebarExpanded);
              setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 320);
          }
      }"
      {{-- Prevents background scrolling when mobile menu is open --}}
      :class="{ 'overflow-hidden': mobileOpen }">

    <div class="flex h-full overflow-hidden">

        <!-- 1. SIDEBAR COMPONENT -->
        <livewire:tenants.nurse.components.sidebar />

        <!-- 2. MAIN CONTENT AREA -->
        <div class="flex-1 flex flex-col h-full min-w-0 main-transition relative"
             :class="sidebarExpanded ? 'lg:ml-64' : 'lg:ml-[72px]'">

            {{-- MOBILE TOPBAR (Only visible on Mobile/Tablet) --}}
            <header class="flex lg:hidden items-center justify-between h-16 px-4 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 flex-shrink-0 z-30">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-rose-500 to-pink-600 flex items-center justify-center shadow-md text-white">
                        <x-heroicon-m-heart class="w-5 h-5" />
                    </div>
                    <span class="font-bold text-gray-900 dark:text-white tracking-tight uppercase text-sm">Station</span>
                </div>

                <button @click="mobileOpen = true" class="p-2 rounded-md text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none">
                    <x-heroicon-o-bars-3-bottom-right class="w-7 h-7" />
                </button>
            </header>

            {{-- CONTENT WRAPPER --}}
            <main class="flex-1 overflow-y-auto overflow-x-hidden bg-gray-50 dark:bg-[#0f172a] custom-scrollbar">
                {{-- Add standard padding for dashboard consistency --}}
                <div class="p-4 md:p-6 lg:p-8 max-w-[1600px] mx-auto">
                    {{ $slot }}
                </div>
            </main>

        </div>
    </div>

    @livewireScripts
</body>
</html>
