<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ClinicOS Pharmacy') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] { display: none !important; }
        .main-transition { transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 4px; }
    </style>
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-900 h-full overflow-hidden"
      x-data="{
          // 1. STATE
          sidebarExpanded: localStorage.getItem('sidebarExpanded') === 'true',
          mobileOpen: false,

          // 2. NOTIFICATIONS
          notifications: [],
          showToast: false,
          toastData: {},

          // 3. COMPUTED PROPERTIES (Getters)
          get unreadCount() {
              return this.notifications.filter(n => !n.read).length;
          },

          toggleSidebar() {
              this.sidebarExpanded = !this.sidebarExpanded;
              localStorage.setItem('sidebarExpanded', this.sidebarExpanded);
              setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 320);
          },

          // 4. ACTIONS
          markAsRead(id) {
              const notif = this.notifications.find(n => n.id === id);
              if (notif) notif.read = true;
              // Saving is handled by $watch below
          },

          clearNotifications() {
              this.notifications = [];
          },

          init() {
              // Load saved
              const stored = localStorage.getItem('pharma_notifications');
              if (stored) this.notifications = JSON.parse(stored);

              this.$watch('sidebarExpanded', val => localStorage.setItem('sidebarExpanded', val));
              this.$watch('notifications', val => localStorage.setItem('pharma_notifications', JSON.stringify(val)));

              // 3. ECHO LISTENER (PHARMACY CHANNEL)
              if (typeof Echo !== 'undefined') {
                  // Note: Event name often has a dot prefix if using broadcastAs()
                  Echo.private('pharmacy.orders')
                      .listen('.new.order', (e) => {
                          this.handleIncoming({
                              message: 'New Prescription Order',
                              patient_name: e.patient_name,
                              doctor_name: e.doctor_name,
                              id: (e.id || Date.now()) + '-' + Math.random().toString(36).substr(2, 9),
                              type: 'rx'
                          });

                          // Refresh Dashboard
                          Livewire.dispatch('refresh-prescription-list');
                      });
              }
          },

          handleIncoming(data) {
              const cleanData = {
                  id: data.id,
                  message: data.message,
                  patient_name: data.patient_name,
                  doctor_name: data.doctor_name,
                  type: 'rx',
                  created_at: new Date().toISOString(),
                  read: false
              };

              // Add to top list
              this.notifications.unshift(cleanData);
              if (this.notifications.length > 50) this.notifications.pop();

              // Trigger Toast
              this.toastData = cleanData;
              this.showToast = true;

              // Auto hide
              setTimeout(() => { this.showToast = false }, 8000);
          }
      }"
      x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebarExpanded', value))">

    <div class="flex h-full">
        <!-- Mobile Overlay -->
        <div x-show="mobileOpen" x-transition.opacity @click="mobileOpen = false" class="fixed inset-0 bg-gray-900/80 z-40 lg:hidden" x-cloak></div>

        <!-- Sidebar Component -->
        <livewire:tenants.pharmacist.components.sidebar />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-full main-transition relative w-full"
             :class="sidebarExpanded ? 'lg:ml-64' : 'lg:ml-20'">
            <main class="flex-1 overflow-y-auto overflow-x-hidden bg-gray-50 dark:bg-gray-900">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- PHARMACY TOAST (Green Theme) --}}
    <div x-show="showToast" x-cloak
         class="fixed bottom-6 right-6 z-[100] w-full max-w-sm bg-white/95 dark:bg-gray-800/95 backdrop-blur-md border-l-4 border-emerald-500 shadow-2xl rounded-lg p-4 cursor-pointer transform transition-all duration-300 ring-1 ring-black/5"
         x-transition:enter="translate-y-12 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-12 opacity-0"
         @click="showToast = false">

        <div class="flex items-start">
            <div class="flex-shrink-0">
                <div class="h-10 w-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <svg class="h-6 w-6 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="ml-3 w-0 flex-1">
                <p class="text-sm font-bold text-gray-900 dark:text-white" x-text="toastData.message"></p>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    <p>Patient: <span class="font-medium text-gray-700 dark:text-gray-200" x-text="toastData.patient_name"></span></p>
                    <p class="mt-0.5">Dr: <span x-text="toastData.doctor_name"></span></p>
                </div>
            </div>
            <div class="ml-4 flex-shrink-0 flex">
                <button @click.stop="showToast = false" class="text-gray-400 hover:text-gray-500">
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
