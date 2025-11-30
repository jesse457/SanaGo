<!-- ========================================== -->
<!-- LIVESTYLE NAVBAR COMPONENT -->
<!-- ========================================== -->
<header x-data="{
    mobileMenuOpen: false,
    darkMode: localStorage.getItem('darkMode') === 'true',
    init() {
        // Set initial dark mode state on page load
        if (this.darkMode || (!this.darkMode && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    },
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
        this.darkMode ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark');
    }
}"
class="fixed inset-x-0 top-0 z-50 transition-all duration-300 'bg-white/80 dark:bg-[#0B1120]/80 backdrop-blur-lg border-b border-slate-200/50 dark:border-slate-800/50 shadow-sm bg-white/80 dark:bg-[#0B1120]/80 backdrop-blur-lg border-b border-slate-200/50 dark:border-slate-800/50 shadow-sm"
:class="(window.pageYOffset > 20) ? 'bg-white/80 dark:bg-[#0B1120]/80 backdrop-blur-lg border-b border-slate-200/50 dark:border-slate-800/50 shadow-sm' : 'bg-transparent'"
@scroll.window="window.pageYOffset > 20">

    <nav class="max-w-7xl mx-auto px-6 h-[72px] flex justify-between items-center">
        <!-- Logo -->
        <a href="#" class="flex items-center gap-3 group">
            <!-- This image will sit on top of the gradient -->
            <img class="h-6 w-8" src="{{ asset('images/logo.png') }}" alt="Sana Go Health System Logo">
            <span class="text-xl font-bold tracking-tight text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">SanaGo</span>
        </a>

        <!-- Desktop Navigation -->
        <div class="hidden lg:flex items-center gap-8">
            <a wire:navigate href="{{ route('home') }}" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Home</a>
            <a wire:navigate href="{{ route('features') }}" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Features</a>
            <a wire:navigate href="{{ route('pricing') }}" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Pricing</a>
            <a wire:navigate href="{{ route('about') }}" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">About</a>
            <a wire:navigate href="{{ route('career') }}"class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Careers</a>
            <a wire:navigate href="{{ route('blog') }}" class="text-sm font-medium text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Blog</a>
        </div>

        <!-- Right Side Actions -->
        <div class="hidden lg:flex items-center gap-4">
            <!-- Dark Mode Toggle -->
            <button @click="toggleDarkMode()" class="p-2 rounded-full text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                <!-- Sun Icon (for light mode) -->
                <svg x-show="!darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <!-- Moon Icon (for dark mode) -->
                <svg x-show="darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            </button>
            <!-- Login Button -->
            <a href="{{ route('book-demo') }}" class="relative inline-flex h-10 overflow-hidden rounded-full p-[1px] focus:outline-none">
                <span class="absolute inset-[-1000%] animate-[spin_2s_linear_infinite] bg-[conic-gradient(from_90deg_at_50%_50%,#E2CBFF_0%,#393BB2_50%,#E2CBFF_100%)]"></span>
                <span class="inline-flex h-full w-full cursor-pointer items-center justify-center rounded-full bg-white dark:bg-slate-950 px-5 py-1 text-sm font-bold text-slate-900 dark:text-white backdrop-blur-3xl hover:bg-slate-50 dark:hover:bg-slate-900 transition-colors">
                Book Now
                </span>
            </a>
        </div>

        <!-- Mobile Menu Button -->
        <div class="lg:hidden flex items-center gap-4">
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-600 dark:text-slate-300 p-2">
                 <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu (collapsible) -->
    <div x-show="mobileMenuOpen"
         x-collapse
         x-cloak
         class="lg:hidden bg-white/95 dark:bg-[#0B1120]/95 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800">
        <div class="px-6 py-6 space-y-4">
            <a wire:navigate href="{{ route('home') }}" class="block text-lg font-semibold text-slate-900 dark:text-white">Home</a>
            <a wire:navigate href="{{ route('features') }}" class="block text-lg font-semibold text-slate-600 dark:text-slate-300">Features</a>
            <a wire:navigate href="{{ route('pricing') }}" class="block text-lg font-semibold text-slate-600 dark:text-slate-300">Pricing</a>
            <a wire:navigate href="{{ route('about') }}" class="block text-lg font-semibold text-slate-600 dark:text-slate-300">About</a>
            <a wire:navigate href="{{ route('career') }}"class="block text-lg font-semibold text-slate-600 dark:text-slate-300">Careers</a>
            <a wire:navigate href="{{ route('blog') }}" class="block text-lg font-semibold text-slate-600 dark:text-slate-300">Blog</a>
            <div class="pt-6 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <button @click="toggleDarkMode()" class="text-slate-600 dark:text-slate-300 flex items-center gap-2 font-medium">
                    <span x-text="darkMode ? 'Light Mode' : 'Dark Mode'"></span>
                </button>
            </div>
        </div>
    </div>
</header>
