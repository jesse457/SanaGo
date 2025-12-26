<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ Storage::disk('central_public')->url('images/logo.png') }}">
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
        /* Smooth transition for the margin change */
        .main-transition { transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased bg-gray-50 dark:bg-gray-900 h-full overflow-hidden"
      x-data="{
          sidebarExpanded: localStorage.getItem('sidebarExpanded') === 'true',
          mobileOpen: false,
          toggleSidebar() {
              this.sidebarExpanded = !this.sidebarExpanded;
              localStorage.setItem('sidebarExpanded', this.sidebarExpanded);
              setTimeout(() => { window.dispatchEvent(new Event('resize')); }, 320);
          }
      }"
      x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebarExpanded', value))">

    <div class="flex h-full">
        <!-- Mobile Overlay (Backdrop) -->
        <div x-show="mobileOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileOpen = false"
             class="fixed inset-0 bg-gray-900/80 z-40 lg:hidden"
             x-cloak>
        </div>
{{-- 1. MOBILE BACKDROP --}}
<div
    x-show="mobileOpen"
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

        <!-- Sidebar Component -->
        <livewire:tenants.receptionist.components.sidebar />

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col h-full main-transition relative w-full"
             :class="sidebarExpanded ? 'lg:ml-64' : 'lg:ml-[72px]'">

            <!-- MOBILE HEADER: Visible only on small screens to open sidebar -->
            <header class="flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 lg:hidden">
                <div class="flex items-center gap-3">
                    <button @click="mobileOpen = true" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <span class="font-bold text-gray-900 dark:text-white">{{ config('app.name') }}</span>
                </div>
                <!-- Optional: User avatar for mobile header -->
                <div class="w-8 h-8 rounded-full bg-pink-100 flex items-center justify-center text-pink-700 font-bold text-xs">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </header>

            <main class="flex-1 overflow-y-auto overflow-x-hidden bg-gray-50 dark:bg-gray-900">
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
