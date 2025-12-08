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
        [x-cloak] { display: none !important; }
        .layout-transition { transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        /* Hide scrollbar for Chrome, Safari and Opera */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        /* Hide scrollbar for IE, Edge and Firefox */
        .no-scrollbar { -ms-overflow-style: none;  scrollbar-width: none; }
    </style>
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-900 h-screen overflow-hidden text-sm"
      x-data="{
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

          get unreadCount() {
              return this.notifications.filter(n => !n.read).length;
          },

          init() {
              const stored = localStorage.getItem('doctor_notifications');
              if (stored) this.notifications = JSON.parse(stored);

              this.$watch('sidebarExpanded', val => localStorage.setItem('sidebarExpanded', val));

              // Polling/Heartbeat logic here...
              // this.fetchMissedNotifications();
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
<div wire:offline class="fixed top-0 w-full bg-red-500 text-white text-center p-2 z-50">
    You are currently offline. Data will not be saved.
</div>
        <!-- SIDEBAR -->
        <livewire:tenants.doctor.components.sidebar />

        <!-- MAIN WRAPPER -->
        <div class="flex-1 flex flex-col h-full relative layout-transition min-w-0"
             :class="sidebarExpanded ? 'lg:ml-64' : 'lg:ml-[72px]'">

            <!-- CONTENT SLOT (This is where the Dashboard/Navbar injects) -->
            {{ $slot }}

        </div>
    </div>

    {{-- TOAST POPUP --}}
    <div x-show="showToast" x-cloak
         class="fixed bottom-4 right-4 z-[60] w-full max-w-sm bg-white dark:bg-gray-800 border-l-4 border-green-500 shadow-xl rounded-lg p-4 cursor-pointer"
         x-transition:enter="transform ease-out duration-300"
         x-transition:enter-start="translate-y-2 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="showToast = false">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <x-heroicon-o-check-circle class="h-6 w-6 text-green-400" />
            </div>
            <div class="ml-3 w-0 flex-1 pt-0.5">
                <p class="text-sm font-medium text-gray-900 dark:text-white">Notification</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400" x-text="toastMessage"></p>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
