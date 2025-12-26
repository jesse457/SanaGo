<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Landlord Portal') }}</title>

    <!-- Global Dark Mode Logic (Prevents Flash) -->
    <script>
        if (localStorage.getItem('darkMode') === 'true' ||
            (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <link rel="icon" type="image/png" href="{{ Storage::disk('central_public')->url('images/logo.png') }}">

    <style>
        [x-cloak] { display: none !important; }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #a0aec0; }
        .dark ::-webkit-scrollbar-thumb { background: #4b5563; }
    </style>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-300">

    <!-- Offline Notification -->
    <div x-data="{ isOffline: !navigator.onLine }"
         x-init="window.addEventListener('offline', () => isOffline = true); window.addEventListener('online', () => isOffline = false)"
         x-show="isOffline"
         x-transition
         class="fixed top-0 left-0 right-0 z-[100] bg-red-600 text-white text-center py-1 text-sm font-medium"
         x-cloak>
        You are currently offline. Some features may not work.
    </div>

    <div x-data="{
            sidebarExpanded: $persist(true).as('sidebarExpanded'),
            mobileOpen: false,
            toggleSidebar() { this.sidebarExpanded = !this.sidebarExpanded }
         }"
         class="min-h-screen flex">

        <!-- Sidebar Component -->
        <!-- Pass mobileOpen state to the sidebar component if needed -->
        <livewire:land-lord.components.sidebar />

        <!-- Mobile Sidebar Overlay -->
        <div x-show="mobileOpen"
             @click="mobileOpen = false"
             class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm lg:hidden"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak></div>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300 ease-in-out"
             :class="sidebarExpanded ? 'lg:ml-64' : 'lg:ml-20'">

            <!-- Header -->
            <header class="flex items-center justify-between bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-4 lg:px-8 h-16 sticky top-0 z-30 md:hidden">

                <div class="flex items-center gap-4">
                    <!-- Mobile Menu Toggle -->
                    <button @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>

                    <h1 class="font-bold text-xl tracking-tight text-indigo-600 dark:text-indigo-400">
                        {{ config('app.name', 'Landlord Portal') }}
                    </h1>
                </div>

             
            </header>

            <main class="">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                on: localStorage.getItem('darkMode') === 'true',
                toggle() {
                    this.on = !this.on;
                    localStorage.setItem('darkMode', this.on);
                    this.updateView();
                },
                updateView() {
                    if (this.on) {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            })
        });

        // Handle Livewire Navigation Persistence
        document.addEventListener('livewire:navigated', () => {
            if (localStorage.getItem('darkMode') === 'true') {
                document.documentElement.classList.add('dark');
            }
        });
    </script>
</body>
</html>
