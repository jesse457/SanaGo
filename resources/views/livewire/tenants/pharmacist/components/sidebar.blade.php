<aside id="side-nav" x-bind:class="{ '-translate-x-full': !open, 'translate-x-0': open }"
    class="fixed inset-y-0 left-0 bg-white shadow-lg w-64 p-4 z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col rounded-r-lg">
    <div class="flex items-center justify-between mb-8 lg:hidden">
        <h2 class="text-xl font-bold text-blue-700">Navigation</h2>
        <button @click="open = false"
            class="inline-flex items-center justify-center p-2 rounded-full text-gray-700 hover:bg-gray-100 transition-colors duration-200">
            <x-heroicon-o-x-mark class="w-6 h-6" />
        </button>
    </div>

    <div class="user-info-section mb-6 pb-4 border-b border-gray-200 text-center">
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
        <h3 class="text-lg font-semibold text-gray-800 mb-1">John Doe</h3>
        <p class="text-sm text-gray-600">Pharmacist</p> {{-- Corrected role to Pharmacist --}}
        <p class="text-sm text-blue-600 font-medium mt-2">{{ tenant('hospital_name') }}</p>
    </div>

    <nav class="flex-1 space-y-2 overflow-y-auto custom-scrollbar">
        <div class="pharmacist-section mb-6 "> {{-- Changed section class and heading to Pharmacist --}}
            <h3 class="text-xs uppercase text-gray-500 font-bold mb-3 px-3">Pharmacist Section</h3>
            <ul>
                <li class="mb-2">
                    <a href="{{ route('pharmacist.dashboard') }}" wire:navigate
                        class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('pharmacist.dashboard') ? 'active bg-blue-50 text-blue-700 font-semibold' : '' }}">
                        <x-heroicon-o-home class="w-5 h-5" />
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="mb-2">
                    <a href="{{ route('pharmacist.medications') }}" wire:navigate {{-- Route for dispensing meds --}}
                        class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('pharmacist.medications') ? 'active bg-blue-50 text-blue-700 font-semibold' : '' }}">
                        <x-heroicon-o-clipboard-document-check class="w-5 h-5" /> {{-- Icon for dispensing/prescriptions --}}
                        <span>Dispense Medications</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('pharmacist.feedbacks') }}" wire:navigate {{-- Route for sales reports --}}
                        class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('pharmacist.feedbacks') ? 'active bg-blue-50 text-blue-700 font-semibold' : '' }}">
                        <x-heroicon-o-chat-bubble-left-right class="w-5 h-5" />

                        <span>FeedBack</span>
                    </a>
                </li>
                <li class="mb-2">
                    <a href="{{ route('pharmacist.manage-drugs') }}" wire:navigate {{-- Route for sales reports --}}
                        class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('pharmacist.manage-drugs') ? 'active bg-blue-50 text-blue-700 font-semibold' : '' }}">
                        <x-heroicon-o-rectangle-stack class="w-6 h-6" />

                        <span>Manage Drugs</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

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
