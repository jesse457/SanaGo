<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ClinicOS Lab') }}</title>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        .main-transition { transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none;  scrollbar-width: none; }
    </style>
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-900 h-full overflow-hidden"
      x-data="{
          // 1. SIDEBAR STATE
          sidebarExpanded: localStorage.getItem('sidebarExpanded') === 'true',
          mobileOpen: false,
          toggleSidebar() {
              this.sidebarExpanded = !this.sidebarExpanded;
              localStorage.setItem('sidebarExpanded', this.sidebarExpanded);
              setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 320);
          },

          // 2. NOTIFICATION STATE
          notifications: [],
          showToast: false,
          toastData: {}, // Stores details for the popup

          get unreadCount() {
              return this.notifications.filter(n => !n.read).length;
          },

          init() {
              // A. Load Local Storage
              const stored = localStorage.getItem('lab_notifications');
              if (stored) this.notifications = JSON.parse(stored);

              this.$watch('sidebarExpanded', val => localStorage.setItem('sidebarExpanded', val));

              // B. Heartbeat & Missed Checks
              this.sendHeartbeat();
              setInterval(() => this.sendHeartbeat(), 30000);
              this.fetchMissedNotifications();

              // C. REVERB LISTENER
              if (typeof Echo !== 'undefined') {
                  Echo.private('App.Models.User.{{ Auth::id() }}')
                      .notification((notification) => {
                          this.handleIncoming(notification);
                      });
              }
          },

          // --- LOGIC ---

          sendHeartbeat() {
               axios.post('/user/heartbeat').catch(() => {});
          },

          fetchMissedNotifications() {
              axios.get('/user/notifications/missed').then(response => {
                  if (response.data.length > 0) {
                      response.data.forEach(n => {
                          // Standardize structure from DB
                          const data = {
                              id: n.id,
                              message: n.data.message,
                              patient_name: n.data.patient_name,
                              doctor_name: n.data.doctor_name,
                              urgency: n.data.urgency,
                              type: n.data.type,
                              created_at: n.created_at,
                              read: false
                          };
                          this.addNotificationSafe(data);
                      });
                  }
              });
          },

          handleIncoming(data) {
              const cleanData = {
                  id: data.id,
                  message: data.message,
                  patient_name: data.patient_name,
                  doctor_name: data.doctor_name,
                  urgency: data.urgency,
                  type: data.type,
                  created_at: new Date().toISOString(),
                  read: false
              };

              this.addNotificationSafe(cleanData);

              // Show Toast
              this.toastData = cleanData;
              this.showToast = true;

              // Auto hide toast after 6 seconds
              setTimeout(() => { this.showToast = false }, 6000);

              // Play Sound (Optional)
              // new Audio('/sounds/lab-alert.mp3').play().catch(() => {});
          },

          addNotificationSafe(item) {
              if (this.notifications.some(n => n.id === item.id)) return;
              this.notifications.unshift(item);
              if (this.notifications.length > 50) this.notifications = this.notifications.slice(0, 50);
              this.saveToStorage();
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
              localStorage.setItem('lab_notifications', JSON.stringify(this.notifications));
          }
      }"
      x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebarExpanded', value))">

    <div class="flex h-full">
        <!-- Mobile Overlay -->
        <div x-show="mobileOpen" x-transition.opacity @click="mobileOpen = false" class="fixed inset-0 bg-gray-900/80 z-40 lg:hidden" x-cloak></div>

        <!-- Sidebar Component -->
        <livewire:tenants.lab-technician.components.sidebar />

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col h-full main-transition relative w-full"
             :class="sidebarExpanded ? 'lg:ml-64' : 'lg:ml-20'">

            <!-- TOP NAVBAR (Injecting Bell Here) -->
            <header class="h-16 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-4 sm:px-6 z-20 sticky top-0">
                <!-- Toggle -->
                <button @click="mobileOpen = !mobileOpen" class="lg:hidden text-gray-500">
                     <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
                <button @click="toggleSidebar()" class="hidden lg:block text-gray-500">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" /></svg>
                </button>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-4">

                    <!-- NOTIFICATION BELL DROPDOWN -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="relative p-2 text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <!-- Badge -->
                            <span x-show="unreadCount > 0" x-transition.scale class="absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold h-4 w-4 rounded-full flex items-center justify-center border border-white dark:border-gray-800" x-text="unreadCount"></span>
                        </button>

                        <!-- Dropdown Panel -->
                        <div x-show="open" @click.outside="open = false" x-cloak
                             class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50">
                            <div class="p-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50">
                                <span class="font-semibold text-sm">Notifications</span>
                                <div class="flex gap-2 text-xs">
                                    <button @click="markAllRead()" class="text-indigo-600">Read All</button>
                                    <button @click="clearNotifications()" class="text-gray-400">Clear</button>
                                </div>
                            </div>
                            <div class="max-h-80 overflow-y-auto no-scrollbar">
                                <template x-for="note in notifications" :key="note.id">
                                    <div @click="markAsRead(note.id)" class="p-3 border-b border-gray-100 dark:border-gray-700 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700" :class="{'bg-indigo-50/50 dark:bg-indigo-900/20': !note.read}">
                                        <div class="flex justify-between">
                                            <p class="text-sm font-medium text-gray-800 dark:text-gray-200" x-text="note.message"></p>
                                            <span class="text-[10px] text-gray-400" x-text="new Date(note.created_at).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})"></span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span x-show="note.urgency === 'urgent'" class="text-[10px] px-1.5 py-0.5 rounded bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 font-bold">URGENT</span>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Dr. <span x-text="note.doctor_name"></span></p>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="notifications.length === 0" class="p-4 text-center text-gray-400 text-sm">
                                    No new orders.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto overflow-x-hidden bg-gray-50 dark:bg-gray-900">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- TOAST POPUP (Bottom Right) --}}
    <div x-show="showToast" x-cloak
         class="fixed bottom-6 right-6 z-[60] w-full max-w-sm bg-white dark:bg-gray-800 border-l-4 shadow-2xl rounded-lg p-4 cursor-pointer transform transition-all duration-300"
         :class="toastData.urgency === 'urgent' ? 'border-red-500' : 'border-indigo-500'"
         x-transition:enter="translate-y-4 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-4 opacity-0"
         @click="showToast = false">

        <div class="flex items-start">
            <div class="flex-shrink-0">
                <!-- Icon changes based on urgency -->
                <template x-if="toastData.urgency === 'urgent'">
                    <svg class="h-6 w-6 text-red-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </template>
                <template x-if="toastData.urgency !== 'urgent'">
                    <svg class="h-6 w-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </template>
            </div>
            <div class="ml-3 w-0 flex-1 pt-0.5">
                <p class="text-sm font-bold text-gray-900 dark:text-white" x-text="toastData.message"></p>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    <p>Patient: <span class="font-medium text-gray-700 dark:text-gray-300" x-text="toastData.patient_name"></span></p>
                    <p>Req by: <span x-text="toastData.doctor_name"></span></p>
                </div>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
                <button @click.stop="showToast = false" class="text-gray-400 hover:text-gray-500">
                    <span class="sr-only">Close</span>
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
