<div class="flex-1 p-4 md:p-6 lg:ml-64 bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">
    <div
        class="sticky z-10 top-0 mb-4
               bg-white/80 dark:bg-gray-900/80 backdrop-blur-md
               border-b border-gray-200/50 dark:border-gray-700/50
               px-4 py-3 shadow-sm rounded-b-lg">
        <div class="flex items-center justify-between">

            {{-- Left: title --}}
            <div class="flex items-center space-x-2">
                <x-heroicon-s-clipboard-document-list class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white">
                        Dashboard
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 hidden md:block">
                        Welcome back, <span
                            class="font-medium text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</span>!
                    </p>
                </div>
            </div>

            {{-- Right: icons + dropdown --}}
            <div class="flex items-center space-x-3 md:space-x-4">

                {{-- Profile dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                        class="flex items-center space-x-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 p-1 pr-2 transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=3b82f6&color=fff"
                            alt="avatar"
                            class="w-8 h-8 rounded-full object-cover border border-gray-200 dark:border-gray-600">
                        {{-- ✅ FIXED LINE --}}
                        <x-heroicon-s-chevron-down class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform"
                            x-bind:class="open ? 'rotate-180' : ''" />
                    </button>

                    {{-- Dropdown panel --}}
                    <div x-show="open" x-transition x-cloak @click.outside="open = false"
                        class="absolute right-0 mt-2 w-48 py-2
                           bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 z-20">
                        <a href="{{ route('lab-technician.profile') }}" wire:navigate
                            class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                            <x-heroicon-o-user class="w-4 h-4 mr-2 text-gray-500 dark:text-gray-400" />Profile
                        </a>

                        <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
                        <form method="POST" action="{{ route('auth.logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150 ease-in-out">
                                <x-heroicon-o-arrow-left-on-rectangle
                                    class="w-4 h-4 mr-2 text-gray-500 dark:text-gray-400" />Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Dashboard Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        {{-- Total Patients --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 flex flex-col items-center justify-center text-center hover:shadow-lg transition-shadow duration-200 cursor-pointer">
            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full mb-3">
                <x-heroicon-o-clipboard-document-check class="w-8 h-8 text-blue-600 dark:text-blue-400" />
            </div>
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Test Completed </h3>
            <p class="text-4xl font-bold text-blue-600 dark:text-blue-400"> {{ $completedTestsToday->count()}}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">lab test completed today</p>
        </div>

        {{-- Appointments Today --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 flex flex-col items-center justify-center text-center hover:shadow-lg transition-shadow duration-200 cursor-pointer">
            <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full mb-3">
                <x-heroicon-o-exclamation-triangle class="w-8 h-8 text-green-600 dark:text-green-400" />
            </div>
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Request In Progress</h3>
            <p class="text-4xl font-bold text-green-600 dark:text-green-400"> {{ $inProgessTest->count() }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">lab test in progress</p>
        </div>



        {{-- Placeholder Card --}}
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 flex flex-col items-center justify-center text-center hover:shadow-lg transition-shadow duration-200 cursor-pointer">
            <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-full mb-3">
                <x-heroicon-o-clock class="w-8 h-8 text-indigo-600 dark:text-indigo-400" />
            </div>
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2"> Pending Request</h3>
            <p class="text-4xl font-bold text-indigo-600 dark:text-indigo-400">{{ $pendingLabRequests->count() }}</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">lab test request pending</p>
        </div>
    </div>

    <div
        class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4 flex items-center justify-between border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pending Lab Requests</h3>
            <a href="#"
                class="text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                View All →
            </a>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Patient Name
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Test Type
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Requesting Doctor
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Request Date
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Urgency
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($pendingLabRequests as $request)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <td
                                class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-gray-200">
                                {{ $request->patient?->first_name }} {{ $request->patient?->last_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $request->testDefinition?->test_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ $request->doctor?->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                {{ \Carbon\Carbon::parse($request->request_date)->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span @class([
                                    'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium',
                                    'bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300' =>
                                        $request->urgency_level == 'Urgent',
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300' =>
                                        $request->urgency_level == 'High',
                                    'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' =>
                                        $request->urgency_level == 'Normal',
                                    'bg-gray-100 text-gray-800 dark:bg-gray-900/40 dark:text-gray-300' =>
                                        $request->urgency_level == 'Low',
                                ])>
                                    {{ $request->urgency_level }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div
                                    class="flex flex-col items-center justify-center text-gray-500 dark:text-gray-400">
                                    <x-heroicon-o-check-badge class="w-12 h-12 mb-4 text-green-400" />
                                    <p class="text-lg font-medium text-gray-700 dark:text-gray-300 mb-1">All
                                        Caught Up!</p>
                                    <p class="text-sm">There are no pending lab requests at the moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
