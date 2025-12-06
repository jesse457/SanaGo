<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        /* Smooth transition for the margin change */
        .main-transition { transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

{{-- 1. DEFINE STATE HERE (Globally) --}}
<body class="antialiased bg-gray-50 dark:bg-gray-900 h-full overflow-hidden"
      x-data="{
          sidebarExpanded: localStorage.getItem('sidebarExpanded') === 'true',
          mobileOpen: false,
          toggleSidebar() {
              this.sidebarExpanded = !this.sidebarExpanded;
              localStorage.setItem('sidebarExpanded', this.sidebarExpanded);
              // Trigger a resize event so Chart.js redraws immediately
              setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 320);
          }
      }"
      x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebarExpanded', value))">

    <div class="flex h-full">
        <!-- Mobile Overlay -->
        <div x-show="mobileOpen" x-transition.opacity @click="mobileOpen = false" class="fixed inset-0 bg-gray-900/80 z-40 lg:hidden" x-cloak></div>

        <!-- Sidebar Component -->
        <livewire:tenants.receptionist.components.sidebar />

        <!-- 2. DYNAMIC MARGIN HERE -->
        <!-- The class binding depends on the parent x-data 'sidebarExpanded' -->
        <div class="flex-1 flex flex-col h-full main-transition relative w-full"
             :class="sidebarExpanded ? 'lg:ml-64' : 'lg:ml-20'">

            <main class="flex-1 overflow-y-auto overflow-x-hidden bg-white dark:bg-gray-900">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
