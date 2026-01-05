<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ClinicOS') }}</title>
    <link rel="icon" type="image/png" href="{{ Storage::disk('central_public')->url('images/logo.webp') }}">
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Global Dark Mode Logic -->
    <script>
        if (localStorage.getItem('darkMode') === 'true' ||
            (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .layout-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }

        /* Better scrollbar for dashboard */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #334155; }
    </style>
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-900 h-screen overflow-hidden text-sm"
    x-data="{
        sidebarExpanded: localStorage.getItem('sidebarExpanded') === 'true',
        mobileOpen: false,

        toggleSidebar() {
            this.sidebarExpanded = !this.sidebarExpanded;
            localStorage.setItem('sidebarExpanded', this.sidebarExpanded);
            setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 300);
        },

        notifications: [],
        showToast: false,
        toastMessage: '',
        toastDetails: '',

        init() {
            const stored = localStorage.getItem('doctor_notifications');
            if (stored) this.notifications = JSON.parse(stored);
            this.$watch('sidebarExpanded', val => localStorage.setItem('sidebarExpanded', val));

            if (typeof Echo !== 'undefined') {
                Echo.private('App.Models.User.{{ Auth::id() }}')
                    .notification((notification) => {
                        const newNotif = {
                            id: notification.id,
                            message: notification.data.message,
                            patient_name: notification.data.patient_name,
                            created_at: new Date().toISOString(),
                            read: false
                        };
                        this.notifications.unshift(newNotif);
                        this.saveToStorage();
                        this.toastMessage = newNotif.message;
                        this.toastDetails = newNotif.patient_name;
                        this.showToast = true;
                        setTimeout(() => { this.showToast = false; }, 5000);
                    });
            }
        },
        saveToStorage() { localStorage.setItem('doctor_notifications', JSON.stringify(this.notifications)); }
    }"
    {{-- Prevent background scroll when mobile menu is open --}}
    :class="{ 'overflow-hidden': mobileOpen }">

    <!-- OFFLINE INDICATOR -->
    <div wire:offline class="fixed top-0 w-full bg-red-600 text-white text-center py-1 z-[100] text-xs font-bold shadow-md">
        Offline Mode: Changes will not be saved.
    </div>

    <div class="flex h-full w-full overflow-hidden">

        <!-- 1. MOBILE BACKDROP -->
        <div x-show="mobileOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileOpen = false"
             class="fixed inset-0 z-40 bg-gray-900/60 backdrop-blur-sm lg:hidden">
        </div>

        <!-- 2. SIDEBAR -->
        <livewire:tenants.doctor.components.sidebar />

        <!-- 3. MAIN CONTENT WRAPPER -->
        <div class="flex-1 flex flex-col h-full relative layout-transition min-w-0"
            :class="sidebarExpanded ? 'lg:ml-64' : 'lg:ml-[72px]'">

            <!-- MOBILE TOP HEADER (Visible only on mobile/tablet) -->
            <header class="flex lg:hidden items-center justify-between h-16 px-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex-shrink-0 z-30">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center shadow-md text-white">
                        <x-heroicon-m-plus class="w-5 h-5" />
                    </div>
                    <span class="font-bold text-gray-900 dark:text-white tracking-tight uppercase text-xs">ClinicOS</span>
                </div>

                <div class="flex items-center gap-2">
                    {{-- Optional: Add notification bell here for mobile --}}
                    <button @click="mobileOpen = true" class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        <x-heroicon-o-bars-3-bottom-right class="w-7 h-7" />
                    </button>
                </div>
            </header>

            <!-- ACTUAL CONTENT AREA -->
            <main class="flex-1 overflow-y-auto overflow-x-hidden bg-gray-50 dark:bg-gray-900 custom-scrollbar">
                {{-- Responsive Padding --}}
                <div class=" mx-auto">
                    {{ $slot }}
                </div>
            </main>

        </div>
    </div>

    {{-- TOAST POPUP (Mobile Adjusted) --}}
    <div x-show="showToast" x-cloak
        class="fixed bottom-4 left-4 right-4 md:left-auto md:right-6 md:bottom-6 z-[100] w-auto md:max-w-sm bg-white dark:bg-gray-800 border-l-4 border-blue-500 shadow-2xl rounded-lg p-4 cursor-pointer"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-y-10 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-10 opacity-0"
        @click="showToast = false">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="h-9 w-9 rounded-full bg-blue-100 dark:bg-blue-900/40 flex items-center justify-center">
                    <x-heroicon-o-beaker class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
            <div class="ml-3 w-0 flex-1">
                <p class="text-sm font-bold text-gray-900 dark:text-white leading-none">New Lab Result</p>
                <p class="mt-1 text-xs text-gray-600 dark:text-gray-300 line-clamp-2" x-text="toastMessage"></p>
                <p class="mt-1 text-[10px] text-gray-400 font-medium" x-text="toastDetails"></p>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
                <button @click.stop="showToast = false" class="text-gray-400 hover:text-gray-500">
                    <x-heroicon-s-x-mark class="h-5 w-5" />
                </button>
            </div>
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
