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
    <div x-data="{
            sidebarExpanded: localStorage.getItem('sidebarExpanded') === 'true',
            mobileOpen: false,
            isOffline: !navigator.onLine,
            toggleSidebar() {
                this.sidebarExpanded = !this.sidebarExpanded;
                localStorage.setItem('sidebarExpanded', this.sidebarExpanded);
            },
            init() {
                window.addEventListener('offline', () => this.isOffline = true);
                window.addEventListener('online', () => this.isOffline = false);
            }
         }"
         class="min-h-screen flex flex-col text-gray-900 dark:text-gray-100 font-sans">

        <!-- Offline Banner (Global) -->
        <div x-show="isOffline" x-cloak x-transition
             class="fixed top-0 inset-x-0 z-[100] bg-rose-600 text-white text-xs font-bold text-center py-2 shadow-md">
            <div class="flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"></path></svg>
                <span>YOU ARE OFFLINE. CHANGES MAY NOT SAVE.</span>
            </div>
        </div>

        <!-- Mobile Backdrop -->
        <div x-show="mobileOpen" x-cloak x-transition:opacity @click="mobileOpen = false"
             class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-40 lg:hidden"></div>

        <!-- Sidebar Component -->
        <livewire:land-lord.components.sidebar />

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300 ease-in-out"
             :class="sidebarExpanded ? 'lg:ml-64' : 'lg:ml-20'">

            <!-- Mobile Header (Only visible on small screens) -->
            <header class="lg:hidden flex items-center justify-between bg-white dark:bg-gray-800 px-4 h-16 shadow-sm sticky top-0 z-30">
                <button @click="mobileOpen = true" class="p-2 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <span class="font-bold text-indigo-600 dark:text-indigo-400">Landlord Portal</span>
                <div class="w-10"></div>
            </header>

            <!-- Page Content (Padding removed here to allow edge-to-edge UI) -->
            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>
    </div>
    @livewireScripts
</body>
</html>
