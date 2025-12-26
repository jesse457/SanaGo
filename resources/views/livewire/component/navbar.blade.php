<!-- ========================================== -->
<!-- LIVESTYLE NAVBAR COMPONENT -->
<!-- ========================================== -->
<header x-data="{
    mobileMenuOpen: false,
    darkMode: localStorage.getItem('darkMode') === 'true',

    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        this.applyTheme();
    },

    applyTheme() {
        if (this.darkMode) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },

    init() {
        // This ensures the Alpine state matches the actual HTML class on load
        this.applyTheme();
    }
}"
class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
:class="(window.pageYOffset > 20) ? 'bg-white/80 dark:bg-[#0B1120]/80 backdrop-blur-lg border-b border-slate-200/50 dark:border-slate-800/50 shadow-sm' : 'bg-transparent'"
@scroll.window="window.pageYOffset > 20">

    <nav class="max-w-7xl mx-auto px-6 h-[72px] flex justify-between items-center">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
            <img class="h-6 w-8" src="{{ asset('images/logo.png') }}" alt="Sana Go Health System Logo">
            <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">SanaGo</span>
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden lg:flex items-center gap-8">
            @php
                // Helper to define link styles
                $linkClasses = "text-sm font-medium transition-all duration-200 relative py-1 hover:text-blue-600 dark:hover:text-blue-400";
                $activeClasses = "text-blue-600 dark:text-blue-400 after:content-[''] after:absolute after:bottom-[-2px] after:left-0 after:w-full after:h-0.5 after:bg-blue-600 after:rounded-full";
                $inactiveClasses = "text-slate-600 dark:text-slate-300";
            @endphp

            <a wire:navigate href="{{ route('home') }}"
               class="{{ $linkClasses }} {{ request()->routeIs('home') ? $activeClasses : $inactiveClasses }}">
               {{ __('navbar.home') }}
            </a>

            <a wire:navigate href="{{ route('features') }}"
               class="{{ $linkClasses }} {{ request()->routeIs('features') ? $activeClasses : $inactiveClasses }}">
               {{ __('navbar.features') }}
            </a>

            <a wire:navigate href="{{ route('pricing') }}"
               class="{{ $linkClasses }} {{ request()->routeIs('pricing') ? $activeClasses : $inactiveClasses }}">
               {{ __('navbar.pricing') }}
            </a>

            <a wire:navigate href="{{ route('about') }}"
               class="{{ $linkClasses }} {{ request()->routeIs('about') ? $activeClasses : $inactiveClasses }}">
               {{ __('navbar.about') }}
            </a>

            <a wire:navigate href="{{ route('career') }}"
               class="{{ $linkClasses }} {{ request()->routeIs('career') ? $activeClasses : $inactiveClasses }}">
               {{ __('navbar.careers') }}
            </a>

            <a wire:navigate href="{{ route('blog') }}"
               class="{{ $linkClasses }} {{ request()->routeIs('blog*') ? $activeClasses : $inactiveClasses }}">
               {{ __('navbar.blog') }}
            </a>
        </div>

        <!-- Right Side Actions -->
        <div class="hidden lg:flex items-center gap-4">
            <!-- Global Dark Mode Toggle -->
            <button @click="toggleDarkMode()" class="p-2 rounded-full text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <svg x-show="!darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            </button>

            <a href="{{ route('book-demo') }}" class="relative inline-flex h-10 overflow-hidden rounded-full p-[1px] focus:outline-none">
                <span class="absolute inset-[-1000%] animate-[spin_2s_linear_infinite] bg-[conic-gradient(from_90deg_at_50%_50%,#E2CBFF_0%,#393BB2_50%,#E2CBFF_100%)]"></span>
                <span class="inline-flex h-full w-full cursor-pointer items-center justify-center rounded-full bg-white dark:bg-slate-950 px-5 py-1 text-sm font-bold text-slate-900 dark:text-white backdrop-blur-3xl hover:bg-slate-50 dark:hover:bg-slate-900 transition-colors">
                {{ __('navbar.book_now') }}
                </span>
            </a>
        </div>

        <!-- Mobile Menu Button -->
        <div class="lg:hidden flex items-center gap-4">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-600 dark:text-slate-300 p-2">
                 <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                 <svg x-show="mobileMenuOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div x-show="mobileMenuOpen"
         x-collapse
         x-cloak
         class="lg:hidden bg-white/95 dark:bg-[#0B1120]/95 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800">
        <div class="px-6 py-6 space-y-2">
            @php
                $mobileBase = "block px-4 py-3 rounded-xl text-lg font-semibold transition-colors";
                $mobileActive = "bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400";
                $mobileInactive = "text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800";
            @endphp

            <a wire:navigate href="{{ route('home') }}" class="{{ $mobileBase }} {{ request()->routeIs('home') ? $mobileActive : $mobileInactive }}">
                {{ __('navbar.home') }}
            </a>
            <a wire:navigate href="{{ route('features') }}" class="{{ $mobileBase }} {{ request()->routeIs('features') ? $mobileActive : $mobileInactive }}">
                {{ __('navbar.features') }}
            </a>
            <a wire:navigate href="{{ route('pricing') }}" class="{{ $mobileBase }} {{ request()->routeIs('pricing') ? $mobileActive : $mobileInactive }}">
                {{ __('navbar.pricing') }}
            </a>
            <a wire:navigate href="{{ route('about') }}" class="{{ $mobileBase }} {{ request()->routeIs('about') ? $mobileActive : $mobileInactive }}">
                {{ __('navbar.about') }}
            </a>
            <a wire:navigate href="{{ route('career') }}" class="{{ $mobileBase }} {{ request()->routeIs('career') ? $mobileActive : $mobileInactive }}">
                {{ __('navbar.careers') }}
            </a>
            <a wire:navigate href="{{ route('blog') }}" class="{{ $mobileBase }} {{ request()->routeIs('blog*') ? $mobileActive : $mobileInactive }}">
                {{ __('navbar.blog') }}
            </a>

            <div class="pt-6 mt-4 border-t border-slate-200 dark:border-slate-800">
                <button @click="toggleDarkMode()" class="w-full flex items-center justify-between px-4 py-3 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white font-medium">
                    <span x-text="darkMode ? '{{ __('navbar.light_mode') }}' : '{{ __('navbar.dark_mode') }}'"></span>
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>
            </div>
        </div>
    </div>
</header>
