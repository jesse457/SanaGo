<aside id="side-nav" x-bind:class="{ '-translate-x-full': !open, 'translate-x-0': open }"
    class="fixed inset-y-0 left-0 bg-white dark:bg-gray-800 shadow-xl w-64 p-4 z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col rounded-r-2xl border-r border-gray-200 dark:border-gray-700">

    <div class="flex items-center justify-between mb-8 lg:hidden">
        <h2 class="text-xl font-bold text-blue-700 dark:text-blue-500">Navigation</h2>
        <button @click="open = false"
            class="inline-flex items-center justify-center p-2 rounded-full text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
            <x-heroicon-o-x-mark class="w-6 h-6" />
        </button>
    </div>

    <div class="user-info-section mb-6 pb-4 border-b border-gray-200 dark:border-gray-700 text-center">
        @auth
 {{-- Check if user has an avatar/image --}}
            @if(auth()->user()->profile_picture)
                {{-- Display user avatar --}}
                  <div class="flex items-center justify-center mb-3">
                <img src="{{ Storage::disk('s3')->temporaryUrl(auth()->user()->profile_picture,now()->addMinutes(5)) }}"
                     alt="{{ auth()->user()->name }}"
                     class="w-16 h-16 rounded-full object-cover border-2 border-blue-300">
                        </div>
            @else
                {{-- Dynamic user initials --}}

                <div
                    class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 text-3xl font-bold border-2 border-blue-300">
                    {{ substr(auth()->user()->name ?? 'GU', 0, 1) }}{{ substr(auth()->user()->name ?? 'GU', strpos(auth()->user()->name ?? 'GU', ' ') + 1, 1) }}
                </div>

            @endif
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ Auth::user()->name }}</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst(Auth::user()->role) }}</p>
            <p class="text-sm text-blue-600 dark:text-blue-400 font-medium mt-2">
                {{ tenant('name') ?? 'Hospital Name' }}
            </p>
        @else
            <div class="flex items-center justify-center mb-3">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 text-3xl font-bold">
                    ?
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Guest User</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Please log in</p>
            <p class="text-sm text-blue-600 dark:text-blue-400 font-medium mt-2">No Tenant</p>
        @endauth
    </div>

    <nav class="flex-1 space-y-2 overflow-y-auto custom-scrollbar">
        <h3 class="text-xs uppercase text-gray-500 dark:text-gray-400 font-bold mb-3 px-3">Lab Technician</h3>
        <ul>
            <li class="mb-2">
                <a href="{{ route('lab-technician.dashboard') }}" wire:navigate
                    class="nav-link flex items-center gap-3 p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors duration-200 {{ request()->routeIs('lab-technician.dashboard') ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-semibold' : '' }}">
                    <x-heroicon-o-home class="w-5 h-5" />
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('lab-technician.test-requests') }}" wire:navigate
                    class="nav-link flex items-center gap-3 p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors duration-200 {{ request()->routeIs('lab-technician.test-requests') ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-semibold' : '' }}">
                    <x-heroicon-o-clipboard-document-list class="w-5 h-5" />
                    <span>Test Requests</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('lab-technician.manage-lab-definitions') }}" wire:navigate
                    class="nav-link flex items-center gap-3 p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors duration-200 {{ request()->routeIs('lab-technician.manage-lab-definitions') ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-semibold' : '' }}">
                    <x-heroicon-o-beaker class="w-5 h-5" />
                    <span>Manage Lab Tests</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="{{ route('lab-technician.lab-results') }}" wire:navigate
                    class="nav-link flex items-center gap-3 p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors duration-200 {{ request()->routeIs('lab-technician.lab-results') ? 'active bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-semibold' : '' }}">
                    <x-heroicon-o-clipboard-document-check class="w-5 h-5" />
                    <span>Lab Results</span>
                </a>
            </li>
              <li class="mb-2">
                    <a wire:navigate href="{{ route('lab-technician.feedbacks') }}"
                        class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-200 {{ request()->routeIs('lab-technician.feedbacks') ? 'active bg-blue-50 text-blue-700 font-semibold' : '' }}">
                        <x-heroicon-o-document-text class="w-5 h-5" />
                        <span>Feedback History</span>
                    </a>
                </li>
        </ul>
    </nav>

    <div class="mt-auto pt-4 border-t border-gray-200 dark:border-gray-700">
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit" class="flex items-center gap-3 p-3 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors duration-200 w-full text-left">
                <x-heroicon-s-arrow-left-on-rectangle class="w-5 h-5" />
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
