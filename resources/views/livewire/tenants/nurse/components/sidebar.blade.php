<aside id="side-nav" x-bind:class="{ '-translate-x-full': !open, 'translate-x-0': open }"
    class="fixed inset-y-0 left-0 bg-white shadow-lg w-64 p-4 z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col rounded-r-lg">

    <div class="flex items-center justify-between mb-8 lg:hidden">
        <h2 class="text-xl font-bold text-blue-700">Navigation</h2>
        <button @click="open = false"
            class="inline-flex items-center justify-center p-2 rounded-full text-gray-700 hover:bg-gray-100 transition-colors duration-200">
            <x-heroicon-o-x-mark class="w-6 h-6" />
        </button>
    </div>

    <!-- User and Hospital Information Section -->
    <div class="user-info-section mb-6 pb-4 border-b border-gray-200 text-center">
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
            <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ Auth::user()->name }}</h3>
            <p class="text-sm text-gray-600">{{ ucfirst(Auth::user()->role) }}</p>
            <p class="text-sm text-blue-600 font-medium mt-2">
                {{ tenant()->name ?? 'Hospital Name' }} {{-- Accessing tenant name from 'data' JSON --}}
            </p>
        @else
            <div class="flex items-center justify-center mb-3">
                <div
                    class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 text-3xl font-bold">
                    ?
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Guest User</h3>
            <p class="text-sm text-gray-600">Please log in</p>
            <p class="text-sm text-blue-600 font-medium mt-2">No Tenant</p>
        @endauth
    </div>

    <!-- Navigation -->
    <nav class="flex-1">
        <div class="nurse-section mb-6">
            <h3 class="text-xs uppercase text-gray-500 font-bold mb-3 px-3">Nurse Section</h3>
            <ul>
                <li class="mb-2">
                    <a href="{{ route('nurse.dashboard') }}" wire:navigate
                        class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('nurse.dashboard') ? 'active bg-blue-50 text-blue-700 font-semibold' : '' }}">
                        <x-heroicon-o-home class="w-5 h-5" />
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('nurse.record-vitals') }}" wire:navigate
                        class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('nurse.record-vitals') ? 'active bg-blue-50 text-blue-700 font-semibold' : '' }}">
                        <x-heroicon-o-clipboard-document-check class="w-5 h-5" />
                        <span>Record Vitals</span>
                    </a>
                </li>

                <li class="mb-2">
                    <a href="{{ route('nurse.medical-usage') }}" wire:navigate
                        class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('nurse.medical-usage') ? 'active bg-blue-50 text-blue-700 font-semibold' : '' }}">
                        <x-heroicon-o-archive-box class="w-5 h-5" />
                        <span>Supply Usage</span>
                    </a>
                </li>
                    <li class="mb-2">
                    <a wire:navigate href="{{ route('nurse.feedbacks') }}"
                        class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors duration-200 {{ request()->routeIs('admin.feedback-history') ? 'active bg-blue-50 text-blue-700 font-semibold' : '' }}">
                        <x-heroicon-o-document-text class="w-5 h-5" />
                        <span>Feedback History</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Logout -->
    <div class="mt-auto pt-4 border-t border-gray-200">
        <form method="POST" action="{{ route('auth.logout') }}">
            @csrf
            <button type="submit"
                class="flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 w-full text-left">
                <x-heroicon-o-arrow-left-on-rectangle class="w-5 h-5" />
                <span>Logout</span>
            </button>
        </form>
    </div>
</aside>
