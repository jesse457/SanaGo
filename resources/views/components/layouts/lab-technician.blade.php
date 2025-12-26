<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'ClinicOS Lab') }}</title>
    <link rel="icon" type="image/png" href="{{ Storage::disk('central_public')->url('images/logo.png') }}">
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
          toastData: {},

          get unreadCount() {
              return this.notifications.filter(n => !n.read).length;
          },

          init() {
              // A. Load Local Storage
              const stored = localStorage.getItem('lab_notifications');
              if (stored) this.notifications = JSON.parse(stored);

              this.$watch('sidebarExpanded', val => localStorage.setItem('sidebarExpanded', val));
              this.$watch('notifications', val => this.saveToStorage());

              // B. ECHO LISTENER (LAB CHANNEL)
              if (typeof Echo !== 'undefined') {
                  // Listen to the Department Channel
                  Echo.private('lab.requests')
                      .listen('.new.request', (e) => {
                          this.handleIncoming({
                              message: 'New Lab Request',
                              patient_name: e.patient_name,
                              doctor_name: e.doctor_name,
                              urgency: e.urgency, // 'normal', 'urgent'
                              id: e.consultation_id + '-' + Date.now(),
                              type: 'lab'
                          });

                          // Optional: Refresh Livewire Table
                          Livewire.dispatch('refresh-lab-requests');
                      });
              }
          },

          handleIncoming(data) {
              const cleanData = {
                  id: data.id,
                  message: data.message,
                  patient_name: data.patient_name,
                  doctor_name: data.doctor_name,
                  urgency: data.urgency || 'normal',
                  type: data.type,
                  created_at: new Date().toISOString(),
                  read: false
              };

              this.addNotificationSafe(cleanData);

              // Show Toast
              this.toastData = cleanData;
              this.showToast = true;

              // Play Sound
              // new Audio('/sounds/bell.mp3').play().catch(() => {});

              // Auto hide toast after 8 seconds
              setTimeout(() => { this.showToast = false }, 8000);
          },

          addNotificationSafe(item) {
              // Avoid duplicates if rapid firing
              if (this.notifications.some(n => n.id === item.id)) return;
              this.notifications.unshift(item);
              // Keep list clean (max 50)
              if (this.notifications.length > 50) this.notifications = this.notifications.slice(0, 50);
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

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-full main-transition relative w-full"
             :class="sidebarExpanded ? 'lg:ml-64' : 'lg:ml-20'">
            <main class="flex-1 overflow-y-auto overflow-x-hidden bg-gray-50 dark:bg-gray-900">
                {{ $slot }}
            </main>
        </div>
    </div>

    {{-- TOAST POPUP --}}
    <div x-show="showToast" x-cloak
         class="fixed bottom-6 right-6 z-[100] w-full max-w-sm bg-white/95 dark:bg-gray-800/95 backdrop-blur-md border-l-4 shadow-2xl rounded-lg p-4 cursor-pointer transform transition-all duration-300 ring-1 ring-black/5"
         :class="toastData.urgency === 'urgent' ? 'border-red-500' : 'border-indigo-500'"
         x-transition:enter="translate-y-12 opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-12 opacity-0"
         @click="showToast = false">

        <div class="flex items-start">
            <div class="flex-shrink-0">
                <template x-if="toastData.urgency === 'urgent'">
                    <div class="h-10 w-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center animate-pulse">
                        <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </template>
                <template x-if="toastData.urgency !== 'urgent'">
                    <div class="h-10 w-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                    </div>
                </template>
            </div>
            <div class="ml-3 w-0 flex-1">
                <p class="text-sm font-bold text-gray-900 dark:text-white" x-text="toastData.message"></p>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    <p>Patient: <span class="font-medium text-gray-700 dark:text-gray-200" x-text="toastData.patient_name"></span></p>
                    <p class="mt-0.5">Req By: <span x-text="toastData.doctor_name"></span></p>
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
