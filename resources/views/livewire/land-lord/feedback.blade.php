<div class="flex-1 p-4 md:p-6 lg:ml-64 bg-gray-50 dark:bg-gray-900 overflow-y-auto min-h-screen">
    {{-- Breadcrumbs & Header --}}
    <div class="mb-8">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('landlord.dashboard') }}" wire:navigate
                        class="inline-flex items-center text-sm font-medium text-slate-700 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-white">
                        <x-heroicon-s-home class="w-4 h-4 me-2.5" />
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <x-heroicon-s-chevron-right class="rtl:rotate-180 w-3 h-3 text-slate-400 mx-1" />
                        <span class="ms-1 text-sm font-medium text-slate-500 md:ms-2 dark:text-slate-400">Tenant
                            Complaints</span>
                    </div>
                </li>
            </ol>
        </nav>
        <h2 class="text-3xl font-bold text-slate-800 dark:text-white mt-4">Tenant Complaints & Feedback</h2>
        <p class="mt-2 text-lg text-gray-600 dark:text-gray-400">
            Review and manage feedback submitted by your tenants.
        </p>
    </div>

    {{-- Complaints Table --}}
    <section class="card">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Pending Feedback</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Complaint ID</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Tenant Name</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Subject</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Date</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    {{-- Loop through the dynamic feedback data --}}
                    @forelse ($feedbacks as $feedback)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">#{{ $feedback->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $feedback->tenant->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $feedback->subject }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ [
                                    'open' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300',
                                    'resolved' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                                    'closed' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
                                ][$feedback->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200' }}">
                                {{ ucfirst($feedback->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $feedback->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('landlord.respond-feedback', ['feedback' => $feedback->id]) }}" wire:navigate
                                    class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    Respond
                                </a>
                                <x-heroicon-o-arrow-right class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No feedbacks found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
