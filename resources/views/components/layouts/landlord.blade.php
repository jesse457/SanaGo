<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Landlord Portal') }}</title>

    <!-- Fonts & Icons -->
    <link rel="icon" type="image/png" href="{{ Storage::disk('central_public')->url('images/logo.png') }}">
    <style>

        [x-cloak] { display: none !important; }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #a0aec0; }
        .dark ::-webkit-scrollbar-thumb { background: #4b5563; }
    </style>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-100 dark:bg-gray-900">
    <!--
        Root State Management
        - sidebarExpanded: Persists state across page loads
        - mobileOpen: Toggles mobile menu
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
         class="min-h-screen flex text-gray-900 dark:text-gray-100 font-sans">

        <!-- Mobile Backdrop Overlay -->
        <div x-show="mobileOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileOpen = false"
             class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm z-20 lg:hidden"
             x-cloak>
        </div>

        <!-- Sidebar Component -->
        <livewire:land-lord.components.sidebar />

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300 ease-in-out"
             :class="sidebarExpanded ? 'lg:ml-64' : 'lg:ml-20'">

            <!-- Mobile Header (Hamburger) -->
            <div class="lg:hidden flex items-center justify-between bg-white dark:bg-gray-800 p-4 shadow-sm sticky top-0 z-10">
                <button @click="mobileOpen = true" class="text-gray-600 dark:text-gray-300 focus:outline-none p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <span class="font-bold text-lg text-indigo-600 dark:text-indigo-400">Landlord Portal</span>
                <div class="w-6"></div> <!-- Spacer -->
            </div>

            <main class="flex-1 overflow-y-auto overflow-x-hidden  dark:bg-gray-900 bg-white">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
