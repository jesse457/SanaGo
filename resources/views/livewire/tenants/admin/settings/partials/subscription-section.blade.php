@if(!$this->subscription)
    <div class="flex flex-col items-center justify-center py-20 px-4 text-center bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-slate-200/80 dark:border-gray-700">
        <div class="relative mb-6">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full blur-2xl opacity-20 animate-pulse"></div>
            <div class="relative p-5 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl text-white shadow-lg shadow-blue-500/30">
                <x-heroicon-o-rocket-launch class="w-10 h-10" />
            </div>
        </div>
        <h2 class="text-2xl font-bold text-slate-900 dark:text-white">Upgrade Your Plan</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-3 max-w-md">Unlock premium features and take your hospital management to the next level.</p>
        <button wire:click="$set('showUpgradeModal', true)" 
            class="mt-8 px-8 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-blue-500/30 transition-all hover:-translate-y-1 hover:shadow-xl flex items-center gap-2">
            <x-heroicon-o-sparkles class="w-5 h-5" />
            View Plans
        </button>
    </div>
@else
    <div class="space-y-8">
        {{-- Current Plan Hero --}}
        <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 dark:from-black dark:via-gray-900 dark:to-indigo-950 rounded-2xl p-8 text-white shadow-2xl">
            <div class="absolute top-0 right-0 w-96 h-96 bg-gradient-to-br from-blue-500/20 to-transparent rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-gradient-to-tr from-indigo-500/20 to-transparent rounded-full blur-3xl translate-y-1/2 -translate-x-1/2"></div>
            
            <div class="relative z-10 flex flex-col lg:flex-row justify-between lg:items-center gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-widest">Current Plan</span>
                        <span class="inline-flex items-center gap-1.5 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 px-3 py-1 rounded-full text-xs font-bold">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                            {{ $this->subscription->getStatusDisplayName() }}
                        </span>
                    </div>
                    <h2 class="text-4xl lg:text-5xl font-extrabold tracking-tight bg-gradient-to-r from-white to-blue-200 bg-clip-text text-transparent">
                        {{ $this->subscription->getPlanDisplayName() }}
                    </h2>
                    <p class="mt-3 text-slate-400 max-w-md">Your subscription renews automatically. Manage your billing and payment methods below.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button wire:click="$set('showUpgradeModal', true)" 
                        class="px-6 py-3 bg-white text-slate-900 rounded-xl font-bold hover:bg-slate-100 transition-all shadow-lg hover:-translate-y-0.5">
                        Change Plan
                    </button>
                    <button wire:click="$set('showCancelModal', true)" 
                        class="px-6 py-3 bg-white/10 text-white border border-white/20 rounded-xl font-medium hover:bg-white/20 transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        {{-- Usage Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([
                ['label' => 'Active Users', 'value' => '5 / 50', 'percent' => 10, 'icon' => 'users', 'color' => 'blue'],
                ['label' => 'Storage Used', 'value' => '1.5 GB / 5 GB', 'percent' => 30, 'icon' => 'server-stack', 'color' => 'violet'],
                ['label' => 'API Calls', 'value' => '2,450 / 10,000', 'percent' => 24.5, 'icon' => 'bolt', 'color' => 'amber'],
            ] as $stat)
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-slate-200/80 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-medium text-slate-500">{{ $stat['label'] }}</span>
                        <div class="p-2 rounded-lg bg-{{ $stat['color'] }}-50 dark:bg-{{ $stat['color'] }}-900/30 text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400">
                            @svg('heroicon-o-' . $stat['icon'], 'w-5 h-5')
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stat['value'] }}</p>
                    <div class="mt-3 h-2 bg-slate-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-{{ $stat['color'] }}-500 to-{{ $stat['color'] }}-600 rounded-full transition-all duration-500" style="width: {{ $stat['percent'] }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
