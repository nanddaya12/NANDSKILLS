<div class="space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">🛍️ Module Marketplace & Feature Control</h2>
            <p class="text-sm text-slate-500 mt-1">Enable or disable modular tools dynamically. Deactivating a module hides its navigation and restricts routes.</p>
        </div>
        <span class="px-3 py-1 bg-blue-50 border border-blue-100 text-blue-700 text-xs font-semibold rounded-full flex items-center gap-1.5 shadow-sm">
            🛡️ Dynamic Route Gates
        </span>
    </div>

    <!-- Status Alerts -->
    @if (session()->has('status'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium rounded-xl flex items-center gap-3 shadow-sm transition-all">
            <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    <!-- Marketplace grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($availableModules as $id => $module)
            @php
                $isActive = in_array($id, $activeModules);
            @endphp
            <div class="bg-white border {{ $isActive ? 'border-blue-200 ring-2 ring-blue-500/5' : 'border-slate-200' }} rounded-2xl p-6 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between h-full group">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="h-12 w-12 rounded-xl bg-slate-50 text-2xl flex items-center justify-center border border-slate-100 group-hover:scale-110 transition-transform duration-300">
                            {{ $module['icon'] }}
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold tracking-wider uppercase bg-slate-100 text-slate-500 border border-slate-200/50">
                            {{ $module['category'] }}
                        </span>
                    </div>

                    <div class="space-y-1">
                        <h3 class="font-bold text-slate-900 text-base flex items-center gap-2">
                            {{ $module['name'] }}
                        </h3>
                        <p class="text-xs text-slate-500 leading-relaxed font-medium">
                            {{ $module['desc'] }}
                        </p>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 mt-6 flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <span class="h-2.5 w-2.5 rounded-full {{ $isActive ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300' }}"></span>
                        <span class="text-[11px] font-bold uppercase tracking-wider {{ $isActive ? 'text-emerald-700' : 'text-slate-400' }}">
                            {{ $isActive ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>

                    <button wire:click="toggleModule('{{ $id }}')" class="px-4 py-2 text-xs font-bold rounded-xl transition-all shadow-sm {{ $isActive ? 'bg-slate-100 hover:bg-rose-50 hover:text-rose-700 hover:border-rose-200 text-slate-700 border border-slate-250' : 'bg-blue-600 hover:bg-blue-700 text-white border border-transparent' }}">
                        {{ $isActive ? 'Disable Module' : 'Enable Module' }}
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</div>
