<div class="w-full min-h-screen bg-slate-50 dark:bg-gray-950 font-sans text-slate-600 dark:text-slate-300">
    <div class="max-w-7xl mx-auto">

        {{-- STICKY HEADER --}}
        <header class="sticky top-0 flex-shrink-0 bg-white/90 dark:bg-gray-800/95 backdrop-blur-md border-b border-gray-200 dark:border-gray-700 shadow-sm z-30 transition-all duration-200">
            <div class="px-4 sm:px-6 py-4 md:flex md:items-center md:justify-between space-y-3 md:space-y-0">
                <div class="flex-1 min-w-0">
                    <nav class="flex text-xs font-medium text-gray-500 dark:text-gray-400 mb-1" aria-label="Breadcrumb">
                        <ol class="inline-flex items-center space-x-1 md:space-x-2">
                            <li class="inline-flex items-center">
                                <a href="#" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors flex items-center"><x-heroicon-s-home class="w-3 h-3 mr-1.5" /> Home</a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <a href="{{ route('landlord.manage-demo') }}" wire:navigate class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Demo Requests</a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <x-heroicon-s-chevron-right class="w-3 h-3 text-gray-300 mx-1" />
                                    <span class="text-gray-900 dark:text-white">{{ $demoRequest->full_name }}</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight leading-7">Request Details</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Viewing details for {{ $demoRequest->facility_name }}</p>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-2">
                    <a href="mailto:{{ $demoRequest->email }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 rounded-xl text-sm font-bold text-slate-700 hover:bg-slate-50 shadow-sm transition-all dark:bg-gray-800 dark:border-gray-700 dark:text-slate-300">
                        <x-heroicon-m-envelope class="w-4 h-4"/> Send Email
                    </a>
                </div>
            </div>
        </header>

        <div class="p-4 sm:p-6 space-y-6">

            {{-- Top Stats Row --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                {{-- Status --}}
                <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 flex flex-col justify-between h-full">
                    <span class="text-xs font-bold uppercase text-slate-500 tracking-wider">Status</span>
                    <div class="mt-2 flex items-center gap-2">
                        @if ($demoRequest->status === 'new')
                            <span class="relative flex h-3 w-3"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-emerald-500"></span></span>
                            <span class="text-xl font-bold text-emerald-700 dark:text-emerald-400">New</span>
                        @elseif($demoRequest->status === 'contacted')
                            <span class="h-3 w-3 rounded-full bg-blue-500"></span>
                            <span class="text-xl font-bold text-blue-700 dark:text-blue-400">Contacted</span>
                        @else
                            <span class="h-3 w-3 rounded-full bg-purple-500"></span>
                            <span class="text-xl font-bold text-purple-700 dark:text-purple-400">{{ ucfirst($demoRequest->status) }}</span>
                        @endif
                    </div>
                </div>

                {{-- Region --}}
                <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 flex flex-col justify-between h-full">
                    <span class="text-xs font-bold uppercase text-slate-500 tracking-wider">Region</span>
                    <div class="mt-2 flex items-baseline gap-1">
                        <span class="text-2xl font-bold text-slate-900 dark:text-white">{{ $demoRequest->region ?? 'N/A' }}</span>
                    </div>
                </div>

                {{-- Date --}}
                <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 flex flex-col justify-between h-full">
                    <span class="text-xs font-bold uppercase text-slate-500 tracking-wider">Requested On</span>
                    <div class="mt-2 text-xl font-bold text-slate-900 dark:text-white">{{ $demoRequest->created_at->format('M d, Y') }}</div>
                </div>

                {{-- Contact Method --}}
                <div class="bg-white dark:bg-gray-900 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 flex flex-col justify-between h-full">
                    <span class="text-xs font-bold uppercase text-slate-500 tracking-wider">WhatsApp</span>
                    <div class="mt-2 flex items-center gap-2">
                        @if ($demoRequest->has_whatsapp)
                            <x-heroicon-s-check-circle class="w-6 h-6 text-emerald-500" />
                            <span class="text-sm font-bold text-slate-900 dark:text-white">Available</span>
                        @else
                            <x-heroicon-s-x-circle class="w-6 h-6 text-slate-400" />
                            <span class="text-sm font-bold text-slate-500">Not Available</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left Column: Details --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-gray-800 flex justify-between items-center bg-slate-50/50 dark:bg-gray-800/50">
                            <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <x-heroicon-s-user class="w-5 h-5 text-indigo-500" /> Applicant Information
                            </h3>
                        </div>

                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                            {{-- Applicant Details --}}
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3">Personal Details</p>
                                <h2 class="text-3xl font-bold text-slate-900 dark:text-white mb-2">{{ $demoRequest->full_name }}</h2>
                                <p class="text-indigo-600 dark:text-indigo-400 font-bold mb-6">{{ $demoRequest->job_title ?? 'No Job Title' }}</p>

                                <div class="space-y-4">
                                    <div class="flex justify-between text-sm pb-3 border-b border-slate-50 dark:border-gray-800">
                                        <span class="text-slate-500 font-medium">Email</span>
                                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $demoRequest->email }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm pb-3 border-b border-slate-50 dark:border-gray-800">
                                        <span class="text-slate-500 font-medium">Phone</span>
                                        <span class="font-bold text-slate-700 dark:text-slate-300">{{ $demoRequest->phone_number }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Facility Details --}}
                            <div class="bg-slate-50 dark:bg-gray-800/30 p-5 rounded-xl border border-slate-100 dark:border-gray-800">
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-3">Facility Details</p>
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white mb-4">{{ $demoRequest->facility_name }}</h3>

                                <div class="space-y-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 flex items-center justify-center text-slate-500">
                                            <x-heroicon-s-building-office class="w-4 h-4"/>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500 uppercase font-bold">Type</p>
                                            <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $demoRequest->facility_type }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 flex items-center justify-center text-slate-500">
                                            <x-heroicon-s-map-pin class="w-4 h-4"/>
                                        </div>
                                        <div>
                                            <p class="text-xs text-slate-500 uppercase font-bold">Region</p>
                                            <p class="text-sm font-bold text-slate-900 dark:text-white">{{ $demoRequest->region }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Actions --}}
                <div class="space-y-6">
                    {{-- Admin Notes --}}
                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 p-6">
                        <h3 class="font-bold text-slate-900 dark:text-white mb-4 text-sm uppercase tracking-wide">Internal Notes</h3>
                        <textarea wire:model="notes" class="w-full rounded-xl border-slate-200 dark:border-gray-700 bg-slate-50 dark:bg-gray-800 text-sm p-3 focus:ring-indigo-500 focus:border-indigo-500 min-h-[120px]" placeholder="Add notes about the call..."></textarea>
                        <button wire:click="saveNotes" class="mt-3 w-full bg-slate-900 dark:bg-slate-700 text-white py-2 rounded-xl text-sm font-bold hover:bg-slate-800 transition shadow-sm">Save Note</button>
                    </div>

                    {{-- Actions Zone --}}
                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-800 overflow-hidden">
                        <div class="px-6 py-4 bg-slate-50 dark:bg-gray-800/50 border-b border-slate-100 dark:border-gray-800">
                            <h3 class="font-bold text-slate-700 dark:text-slate-300 flex items-center gap-2 text-sm">
                                <x-heroicon-s-cog-6-tooth class="w-4 h-4" /> Workflow Actions
                            </h3>
                        </div>
                        <div class="p-6 space-y-3">
                            @if($demoRequest->status !== 'contacted')
                                <button wire:click="updateStatus('contacted')" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl text-sm font-bold transition-colors shadow-sm">
                                    Mark as Contacted
                                </button>
                            @endif

                            @if($demoRequest->status !== 'converted')
                                <button wire:click="updateStatus('converted')" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2.5 rounded-xl text-sm font-bold transition-colors shadow-sm">
                                    Convert to Tenant
                                </button>
                            @endif

                            <div class="pt-4 mt-4 border-t border-slate-100 dark:border-gray-800">
                                <button wire:click="delete" wire:confirm="Are you sure you want to delete this request?" class="w-full border border-rose-200 text-rose-600 hover:bg-rose-50 dark:border-rose-900 dark:text-rose-400 dark:hover:bg-rose-900/20 py-2.5 rounded-xl text-sm font-bold transition-colors">
                                    Delete Request
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
