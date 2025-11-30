 <!-- Sidebar Navigation -->
    <aside id="side-nav" x-bind:class="{ '-translate-x-full': !open, 'translate-x-0': open }"
           class="fixed inset-y-0 left-0 bg-white shadow-lg w-64 p-4 z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col rounded-r-lg dark:bg-gray-800 dark:shadow-xl">
        <div class="flex items-center justify-between mb-8 lg:hidden">
            <h2 class="text-xl font-bold text-indigo-600 dark:text-indigo-400">Navigation</h2>
            <button @click="open = false"
                    class="inline-flex items-center justify-center p-2 rounded-full text-gray-700 hover:bg-gray-100 transition-colors duration-200 dark:text-gray-300 dark:hover:bg-gray-700">
                <x-heroicon-o-x-mark class="w-6 h-6" />
            </button>
        </div>
        <div class="user-info-section mb-6 pb-4 border-b border-gray-200 dark:border-gray-700 text-center">
            <div class="flex items-center justify-center mb-3">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 text-3xl font-bold dark:bg-indigo-900/50 dark:text-indigo-300">
                    L
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-1">Landlord User</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">Landlord Role</p>
            <p class="text-sm text-indigo-600 dark:text-indigo-400 font-medium mt-2">
                Central Dashboard
            </p>
        </div>
        <nav class="flex-1">
            <div class="landlord-section mb-6">
                <h3 class="text-xs uppercase text-gray-500 font-bold mb-3 px-3">Landlord</h3>
                <ul>
                    <li class="mb-2">
                        <a href="{{ route('landlord.dashboard') }}" wire:navigate
                           class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors {{ request()->routeIs('landlord.dashboard') ? 'active bg-blue-50 text-blue-700 font-semibold' : '' }} duration-200 dark:text-gray-300 dark:hover:bg-gray-700">
                            <x-heroicon-o-home class="w-5 h-5" />
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('landlord.manage-tenants') }}" wire:navigate
                           class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors {{ request()->routeIs('landlord.manage-tenants') ? 'active bg-blue-50 text-blue-700 font-semibold' : '' }} duration-200 dark:text-gray-300 dark:hover:bg-gray-700">
                            <x-heroicon-o-users class="w-5 h-5" />
                            <span>Manage Tenants</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('landlord.feedbacks') }}" wire:navigate
                           class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 {{ request()->routeIs('landlord.feedbacks') ? 'active bg-blue-50 text-blue-700 font-semibold' : '' }} transition-colors duration-200 dark:text-white dark:bg-indigo-900/50">
                            <x-heroicon-o-chat-bubble-left-right class="w-5 h-5" />
                            <span>Tenant Complaints</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('landlord.settings') }}" wire:navigate
                           class="nav-link flex items-center gap-3 p-3 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors {{ request()->routeIs('landlord.settings') ? 'active bg-blue-50 text-blue-700 font-semibold' : '' }} duration-200 dark:text-gray-300 dark:hover:bg-gray-700">
                             <x-heroicon-o-cog-6-tooth class="w-5 h-5" />
                            <span>Settings</span>
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
                <span>{{ __('admin.logout') }}</span>
            </button>
        </form>
    </div>
    </aside>
