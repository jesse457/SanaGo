<!-- Language Dropdown -->
<div class="relative z-50" x-data="{ langMenuOpen: false }">

    <!-- 1. THE TRIGGER BUTTON -->
    <button
        @click="langMenuOpen = !langMenuOpen"
        @keydown.escape.window="langMenuOpen = false"
        class="flex items-center justify-between gap-2 px-4 py-2 rounded-full
               bg-white dark:bg-gray-800
               border border-gray-200 dark:border-gray-700
               shadow-sm hover:shadow-md
               text-gray-700 dark:text-gray-200
               transition-all duration-200 ease-in-out
               focus:outline-none focus:ring-2 focus:ring-blue-500"
        type="button"
    >
        <div class="flex items-center gap-2">
            <!-- Language Icon (SVG) -->
            <div class="p-1 bg-blue-50 dark:bg-blue-900/50 rounded-full text-blue-600 dark:text-blue-400">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    <path d="M2 12h20"/>
                    <path d="M12 2v20"/>
                </svg>
            </div>

            <!-- Current Language Label -->
            <span class="text-sm font-bold tracking-wide">{{ strtoupper($currentLocale ?? 'EN') }}</span>
        </div>

        <!-- Chevron Icon (SVG) -->
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="w-4 h-4 text-gray-400 transition-transform duration-200"
             :class="{ 'rotate-180': langMenuOpen }">
            <path d="M6 9l6 6 6-6"/>
        </svg>
    </button>

    <!-- 2. THE DROPDOWN MENU -->
    <!-- Added 'x-cloak' to prevent flickering and explicit z-index -->
    <div x-show="langMenuOpen"
         x-cloak
         @click.away="langMenuOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="absolute right-0 mt-2 w-56 origin-top-right
                bg-white dark:bg-gray-800
                border border-gray-100 dark:border-gray-700
                rounded-xl shadow-xl z-50 overflow-hidden"
    >
        <div class="py-1">
            @foreach($supportedLocales as $locale)
                @php $isActive = ($locale === ($currentLocale ?? 'en')); @endphp

                <a href="{{ route('language.switch', $locale) }}"
                   class="flex items-center justify-between px-4 py-3 text-sm transition-colors
                          {{ $isActive
                             ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-semibold'
                             : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'
                          }}">

                    <div class="flex items-center gap-3">
                        <!-- Flag/Icon -->
                        <span class="text-base">
                            @switch($locale)
                                @case('en') 🇬🇧 @break
                                @case('fr') 🇫🇷 @break
                                @case('es') 🇪🇸 @break
                                @case('de') 🇩🇪 @break
                                @default 🌐
                            @endswitch
                        </span>
                        <!-- Text -->
                        <span>{{ __('languages.' . $locale) }}</span>
                    </div>

                    <!-- Active Checkmark (SVG) -->
                    @if($isActive)
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600 dark:text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>

<!-- Add this specific style once to your layout if icons flicker or x-show fails initially -->
<style>
    [x-cloak] { display: none !important; }
</style>
