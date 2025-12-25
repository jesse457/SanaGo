<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ClinicOS') }}</title>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }

        .layout-transition {
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-900 h-screen overflow-hidden text-sm" x-data="{
    // 1. SIDEBAR STATE
    sidebarExpanded: localStorage.getItem('sidebarExpanded') === 'true',
    mobileOpen: false,

    toggleSidebar() {
        this.sidebarExpanded = !this.sidebarExpanded;
        localStorage.setItem('sidebarExpanded', this.sidebarExpanded);
        setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 300);
    },

    // 2. NOTIFICATION STATE
    notifications: [],
    showToast: false,
    toastMessage: '',
    toastDetails: '',

    get unreadCount() {
        return this.notifications.filter(n => !n.read).length;
    },

    init() {
        // Load from LocalStorage
        const stored = localStorage.getItem('doctor_notifications');
        if (stored) this.notifications = JSON.parse(stored);

        // Watch for Sidebar changes
        this.$watch('sidebarExpanded', val => localStorage.setItem('sidebarExpanded', val));

        // --- REVERB LISTENER ---
        if (typeof Echo !== 'undefined') {
            Echo.private('App.Models.User.{{ Auth::id() }}')
                .notification((notification) => {
                    console.log('Notification Received:', notification);

                    // Format incoming data
                    const newNotif = {
                        id: notification.id,
                        message: notification.data.message,
                        patient_name: notification.data.patient_name,
                        test_name: notification.data.test_name,
                        created_at: new Date().toISOString(),
                        read: false
                    };

                    // Add to array
                    this.notifications.unshift(newNotif);
                    this.saveToStorage();

                    // Show Toast
                    this.toastMessage = newNotif.message;
                    this.toastDetails = newNotif.patient_name;
                    this.showToast = true;

                    // Play Sound
                    // new Audio('/sounds/notification.mp3').play().catch(e => console.log('Audio blocked'));

                    setTimeout(() => { this.showToast = false; }, 5000);
                });
        }
    },

    markAsRead(id) {
        const notif = this.notifications.find(n => n.id === id);
        if (notif) {
            notif.read = true;
            this.saveToStorage();
        }
    },

    markAllRead() {
        this.notifications.forEach(n => n.read = true);
        this.saveToStorage();
    },

    clearNotifications() {
        this.notifications = [];
        this.saveToStorage();
    },

    saveToStorage() {
        localStorage.setItem('doctor_notifications', JSON.stringify(this.notifications));
    }
}">

    <div class="flex h-full w-full">
        <!-- MOBILE BACKDROP -->
        <div x-show="mobileOpen" x-transition.opacity @click="mobileOpen = false"
            class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 lg:hidden" x-cloak>
        </div>

        <div wire:offline class="fixed top-0 w-full bg-red-500 text-white text-center p-2 z-[60]">
            You are currently offline. Data will not be saved.
        </div>
        <div x-show="mobileOpen" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="mobileOpen = false"
            class="fixed inset-0 z-40 bg-gray-900/60 backdrop-blur-sm lg:hidden">
        </div>
        <!-- SIDEBAR -->
        <livewire:tenants.doctor.components.sidebar />

        <!-- MAIN WRAPPER -->
        <div class="flex-1 flex flex-col h-full relative layout-transition min-w-0"
            :class="sidebarExpanded ? 'lg:ml-64' : 'lg:ml-[72px]'">

            <!-- CONTENT SLOT -->
            {{ $slot }}

        </div>
    </div>

    {{-- TOAST POPUP --}}
    <div x-show="showToast" x-cloak
        class="fixed bottom-6 right-6 z-[70] w-full max-w-sm bg-white dark:bg-gray-800 border-l-4 border-blue-500 shadow-2xl rounded-r-lg p-4 cursor-pointer transform transition-all duration-300 hover:scale-105"
        x-transition:enter="translate-y-10 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="translate-y-0 opacity-0" @click="showToast = false">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <x-heroicon-o-beaker class="h-5 w-5 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
            <div class="ml-3 w-0 flex-1 pt-0.5">
                <p class="text-sm font-bold text-gray-900 dark:text-white">New Lab Result</p>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300" x-text="toastMessage"></p>
                <p class="mt-0.5 text-xs text-gray-400" x-text="toastDetails"></p>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
                <button @click.stop="showToast = false" class="text-gray-400 hover:text-gray-500">
                    <x-heroicon-s-x-mark class="h-5 w-5" />
                </button>
            </div>
        </div>
    </div>

    @livewireScripts
</body>

</html>
