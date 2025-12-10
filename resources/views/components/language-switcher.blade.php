<!-- Language Dropdown -->
<div class="relative z-50" x-data="{ langMenuOpen: false }">

    <!-- 1. THE TRIGGER BUTTON -->
    <button
        @click="langMenuOpen = !langMenuOpen"
        @keydown.escape.window="langMenuOpen = false"
        class="group flex items-center justify-center p-2 rounded-lg transition-all duration-200 ease-in-out
               bg-white dark:bg-gray-800
               border border-gray-200 dark:border-gray-700
               hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600
               focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:ring-offset-1 dark:focus:ring-offset-gray-900"
        type="button"
        aria-label="Change Language"
    >
        <div class="flex items-center gap-2">
            <!-- Heroicon: Language (Modern & Semantic) -->
            <x-heroicon-m-language class="w-5 h-5 text-gray-500 group-hover:text-indigo-600 dark:text-gray-400 dark:group-hover:text-indigo-400 transition-colors" />

            <!-- Current Language Label (Hidden on mobile for space, visible on tablet+) -->
            <span class="hidden sm:block text-sm font-semibold text-gray-700 dark:text-gray-200 group-hover:text-gray-900 dark:group-hover:text-white uppercase">
                {{ $currentLocale ?? 'EN' }}
            </span>

            <!-- Chevron -->
            <x-heroicon-m-chevron-down
                class="w-3 h-3 text-gray-400 group-hover:text-gray-600 dark:text-gray-500 transition-transform duration-200"
                x-bind:class="langMenuOpen ? 'rotate-180' : ''"
            />
        </div>
    </button>

    <!-- 2. THE DROPDOWN MENU -->
    <div x-show="langMenuOpen"
         x-cloak
         @click.outside="langMenuOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
         class="absolute right-0 mt-2 w-48 origin-top-right
                bg-white dark:bg-gray-800
                border border-gray-100 dark:border-gray-700
                rounded-xl shadow-lg ring-1 ring-black/5
                divide-y divide-gray-100 dark:divide-gray-700 overflow-hidden"
    >
        <div class="px-3 py-2 bg-gray-50/50 dark:bg-gray-700/50">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Select Language
            </p>
        </div>

        <div class="p-1">
            @foreach($supportedLocales as $locale)
                @php $isActive = ($locale === ($currentLocale ?? 'en')); @endphp

                <a href="{{ route('language.switch', $locale) }}"
                   class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg transition-colors group
                          {{ $isActive
                             ? 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300'
                             : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/80'
                          }}">

                    <div class="flex items-center gap-3">
                        <!-- Flag Emoji (or use svg flags if you prefer) -->
                        <span class="text-lg leading-none filter drop-shadow-sm">
                            @switch($locale)
                                @case('en') 🇺🇸 @break
                                @case('fr') 🇫🇷 @break
                                @case('es') 🇪🇸 @break
                                @case('de') 🇩🇪 @break
                                @default 🏳️
                            @endswitch
                        </span>

                        <span class="font-medium {{ $isActive ? 'font-semibold' : '' }}">
                            {{ ucfirst(__('languages.' . $locale)) }}
                        </span>
                    </div>

                    <!-- Active Checkmark -->
                    @if($isActive)
                        <x-heroicon-m-check class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
