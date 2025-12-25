<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Landlord Portal') }}</title>

    <!-- Fonts & Icons -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <style>
        [x-cloak] { display: none !important; }
        /* Custom scrollbar for sidebar */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
    </style>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Chart.js is required for the dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-900 h-full font-sans text-gray-900 dark:text-gray-100">
    <div x-data="{
            sidebarExpanded: localStorage.getItem('sidebarExpanded') === 'true',
            mobileOpen: false,
            isOffline: !navigator.onLine,
            toggleSidebar() {
                this.sidebarExpanded = !this.sidebarExpanded;
                localStorage.setItem('sidebarExpanded', this.sidebarExpanded);
                // Help Charts redraw after sidebar resize
                setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 320);
            }
         }"
         x-init="window.addEventListener('offline', () => isOffline = true);
                 window.addEventListener('online', () => isOffline = false);"
         class="min-h-screen flex flex-col">

        <!-- Offline Alert -->
        <div x-show="isOffline" x-cloak x-transition class="fixed top-0 inset-x-0 z-[100] bg-rose-600 text-white text-[10px] font-bold text-center py-1.5 uppercase tracking-widest shadow-lg">
            Connection Lost - Working Offline
        </div>

        <!-- Mobile Backdrop -->
        <div x-show="mobileOpen" x-cloak x-transition:opacity @click="mobileOpen = false"
             class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-40 lg:hidden"></div>

        <!-- Sidebar Component -->
        <livewire:tenants.admin.components.sidebar />

        <!-- Content Wrapper -->
        <div class="flex-1 flex flex-col transition-all duration-300 ease-in-out"
             :class="sidebarExpanded ? 'lg:ml-64' : 'lg:ml-20'">

            <!-- Mobile Navbar Header -->
            <header class="lg:hidden flex items-center justify-between bg-white dark:bg-gray-800 px-4 h-16 shadow-sm sticky top-0 z-30 border-b dark:border-gray-700">
                <button @click="mobileOpen = true" class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <x-heroicon-o-bars-3-bottom-left class="w-6 h-6" />
                </button>
                <div class="flex items-center gap-2">
                    <img class="h-6 w-auto" src="{{ asset('images/logo.png') }}" alt="Logo">
                    <span class="font-bold text-indigo-600 text-sm">Portal</span>
                </div>
                <div class="w-10"></div>
            </header>

            <!-- Main Page Slot -->
            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
