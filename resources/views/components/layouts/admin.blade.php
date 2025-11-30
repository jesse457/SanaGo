<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

  
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #a0aec0; }
    </style>
    @livewireStyles
     @googlefonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-100">
    <!--
      State Definition:
      sidebarExpanded: Controls desktop width (true = 64, false = 20)
      mobileOpen: Controls mobile off-canvas visibility
    -->
    <div x-data="{
            sidebarExpanded: localStorage.getItem('sidebarExpanded') === 'true',
            mobileOpen: false,
            toggleSidebar() {
                this.sidebarExpanded = !this.sidebarExpanded;
                localStorage.setItem('sidebarExpanded', this.sidebarExpanded);
            }
         }"
         x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebarExpanded', value))"
         class="min-h-screen bg-gray-50 flex">

        <!-- Mobile Overlay -->
        <div x-show="mobileOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileOpen = false"
             class="fixed inset-0 bg-gray-900/80 z-20 lg:hidden"
             x-cloak>
        </div>

        <!-- Sidebar Component -->
        <livewire:tenants.admin.components.sidebar />

        <!-- Main Content Area -->
        <!-- We use :class to dynamically adjust the left margin based on sidebar width -->
        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300 ease-in-out"
             :class="sidebarExpanded ? 'lg:ml-64' : 'lg:ml-20'">

            <!-- Mobile Header (Optional: To show hamburger menu on mobile) -->
            <div class="lg:hidden flex items-center justify-between bg-white p-4 shadow-sm sticky top-0 z-10">
                <button @click="mobileOpen = true" class="text-gray-600 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <span class="font-bold text-lg text-gray-800">{{ config('app.name') }}</span>
                <div class="w-6"></div> <!-- Spacer for center alignment -->
            </div>

            <main class="flex-1 p-4 ">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
