<!-- ========================================== -->
<!-- OPTIMIZED NAVBAR COMPONENT -->
<!-- ========================================== -->

<!-- Add this style to your layout head if not present to prevent flashing -->


<header
    x-data="{
        mobileMenuOpen: false,
        isScrolled: false,
        darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),

     toggleDarkMode() {
    this.darkMode = !this.darkMode;
    localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
    this.applyTheme();
    // ADD THIS LINE: It tells the dashboard card to swap images immediately
    window.dispatchEvent(new CustomEvent('theme-changed', { detail: this.darkMode }));
},

        applyTheme() {
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },

        handleScroll() {
            this.isScrolled = window.scrollY > 20;
        },

        init() {
            this.applyTheme();
            this.handleScroll();
        }
    }"
    @scroll.window="handleScroll()"
    @keydown.escape.window="mobileMenuOpen = false"
    class="fixed inset-x-0 top-0 z-50 transition-all duration-300 border-b"
    :class="isScrolled
        ? 'bg-white/80 dark:bg-[#0B1120]/90 backdrop-blur-lg border-slate-200 dark:border-slate-800 shadow-sm'
        : 'bg-transparent border-transparent'"
>

    <nav class="max-w-7xl mx-auto px-6 h-[72px] flex justify-between items-center">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-3 group z-50 relative">
            <!-- Ensure logo path is correct -->
            <img class="h-8 w-auto" src="{{ asset('images/logo.webp') }}" alt="Sana Go Logo">
            <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                SanaGo
            </span>
        </a>

        <!-- PHP Configuration for Links (Cleaner Code) -->
        @php
            $navLinks = [
                ['route' => 'home',     'label' => __('navbar.home')],
                ['route' => 'features', 'label' => __('navbar.features')],
                ['route' => 'pricing',  'label' => __('navbar.pricing')],
                ['route' => 'about',    'label' => __('navbar.about')],
                ['route' => 'career',   'label' => __('navbar.careers')],
                ['route' => 'blog',     'label' => __('navbar.blog'), 'active' => 'blog*'],
            ];

            // Styles
            $baseLink = "text-sm font-medium transition-all duration-200 relative py-1 hover:text-blue-600 dark:hover:text-blue-400";
            $activeLink = "text-blue-600 dark:text-blue-400 after:content-[''] after:absolute after:bottom-[-2px] after:left-0 after:w-full after:h-0.5 after:bg-blue-600 after:rounded-full";
            $inactiveLink = "text-slate-600 dark:text-slate-300";
        @endphp

        <!-- Desktop Navigation -->
        <div class="hidden lg:flex items-center gap-8">
            @foreach($navLinks as $link)
                <a wire:navigate
                   href="{{ route($link['route']) }}"
                   class="{{ $baseLink }} {{ request()->routeIs($link['active'] ?? $link['route']) ? $activeLink : $inactiveLink }}">
                   {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        <!-- Right Side Actions (Desktop) -->
        <div class="hidden lg:flex items-center gap-4">
            <!-- Dark Mode Toggle -->
            <button @click="toggleDarkMode()"
                    class="p-2 rounded-full text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                <!-- Sun Icon -->
                <svg x-show="!darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <!-- Moon Icon -->
                <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            </button>

            <!-- CTA Button -->
            <a href="{{ route('book-demo') }}" class="group relative inline-flex h-10 overflow-hidden rounded-full p-[1px] focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2 focus:ring-offset-slate-50">
                <span class="absolute inset-[-1000%] animate-[spin_2s_linear_infinite] bg-[conic-gradient(from_90deg_at_50%_50%,#E2CBFF_0%,#393BB2_50%,#E2CBFF_100%)]"></span>
                <span class="inline-flex h-full w-full cursor-pointer items-center justify-center rounded-full bg-white dark:bg-slate-950 px-5 py-1 text-sm font-bold text-slate-900 dark:text-white backdrop-blur-3xl transition-colors group-hover:bg-slate-50 dark:group-hover:bg-slate-900">
                    {{ __('navbar.book_now') }}
                </span>
            </a>
        </div>

        <!-- Mobile Menu Button -->
        <div class="lg:hidden flex items-center gap-4">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-600 dark:text-slate-300 p-2 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                 <span class="sr-only">Open menu</span>
                 <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                 <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu Dropdown -->
    <div x-show="mobileMenuOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         @click.outside="mobileMenuOpen = false"
         x-cloak
         class="lg:hidden absolute top-[72px] left-0 w-full bg-white/95 dark:bg-[#0B1120]/95 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800 shadow-xl">

        <div class="px-6 py-6 space-y-2 max-h-[calc(100vh-80px)] overflow-y-auto">
            @php
                $mobileBase = "block px-4 py-3 rounded-xl text-lg font-semibold transition-colors";
                $mobileActive = "bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400";
                $mobileInactive = "text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800";
            @endphp

            @foreach($navLinks as $link)
                <a wire:navigate
                   href="{{ route($link['route']) }}"
                   @click="mobileMenuOpen = false"
                   class="{{ $mobileBase }} {{ request()->routeIs($link['active'] ?? $link['route']) ? $mobileActive : $mobileInactive }}">
                    {{ $link['label'] }}
                </a>
            @endforeach

            <!-- Mobile Settings Area -->
            <div class="pt-6 mt-4 border-t border-slate-200 dark:border-slate-800 flex flex-col gap-4">
                <!-- Mobile Dark Mode -->
                <button @click="toggleDarkMode()" class="w-full flex items-center justify-between px-4 py-3 rounded-xl bg-slate-100 dark:bg-slate-800/50 text-slate-900 dark:text-white font-medium hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors">
                    <span x-text="darkMode ? '{{ __('navbar.light_mode') }}' : '{{ __('navbar.dark_mode') }}'"></span>
                    <div class="p-1 rounded-full bg-white dark:bg-slate-700 shadow-sm">
                        <svg x-show="!darkMode" class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        <svg x-show="darkMode" class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    </div>
                </button>

                <!-- Mobile CTA -->
                <a href="{{ route('book-demo') }}" class="w-full py-3 rounded-xl bg-blue-600 text-white font-bold text-center hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/20">
                    {{ __('navbar.book_now') }}
                </a>
            </div>
        </div>
    </div>
    <style>[x-cloak] { display: none !important; }</style>
</header>
