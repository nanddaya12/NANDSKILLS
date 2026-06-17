<div class="space-y-8">
    <!-- Top Header -->
    <div class="flex justify-between items-center bg-slate-900/60 p-6 rounded-2xl border border-white/5 backdrop-blur-md">
        <div>
            <h2 class="text-2xl font-black text-white tracking-tight">Academic Certificate Studio</h2>
            <p class="text-slate-400 text-sm mt-1">Design credentials, insert dynamic parameters, and manage secure verification signatures.</p>
        </div>
        <button wire:click="createTemplate" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 font-bold text-xs tracking-wider transition-colors text-white rounded-xl uppercase">
            + Create Template
        </button>
    </div>

    @if (session()->has('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-sm font-semibold">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Templates & Editor Panel -->
        <div class="lg:col-span-5 p-6 rounded-2xl bg-slate-900/40 border border-white/5 backdrop-blur-md space-y-6">
            <!-- Selector -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Select Active Template</label>
                <select wire:model="selectedTemplateId" wire:change="loadTemplates"
                        class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm font-bold">
                    @foreach($templates as $tpl)
                        <option value="{{ $tpl->id }}">{{ $tpl->name }} {{ $tpl->is_active ? '(Active)' : '' }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Form Details -->
            <form wire:submit.prevent="saveTemplate" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Template Display Name</label>
                    <input type="text" wire:model.defer="templateName" required
                           class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                    @error('templateName') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Status</label>
                    <select wire:model.defer="isActive"
                            class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                        <option value="1">Active / Issuing</option>
                        <option value="0">Draft / Inactive</option>
                    </select>
                    @error('isActive') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Design HTML Layout</label>
                        <span class="text-[10px] text-slate-500 font-bold uppercase">Inline style styles allowed</span>
                    </div>
                    <textarea wire:model="htmlContent" rows="12" required
                              class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-xs font-mono"></textarea>
                    @error('htmlContent') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-sm tracking-wide transition-all shadow-md shadow-blue-600/10">
                    Save Template Layout
                </button>
            </form>

            <!-- Variables Chip helper -->
            <div class="bg-slate-950/40 p-4 rounded-xl border border-white/5 space-y-3">
                <h4 class="text-xs font-bold text-white uppercase tracking-wider">Dynamic Layout Placeholders</h4>
                <p class="text-[10px] text-slate-500 leading-normal">Double curly braces placeholders will render dynamically during issuance:</p>
                <div class="flex flex-wrap gap-2 pt-1">
                    <span class="px-2 py-1 bg-white/5 rounded text-[10px] text-slate-300 font-mono select-all">{{ '{{student_name}}' }}</span>
                    <span class="px-2 py-1 bg-white/5 rounded text-[10px] text-slate-300 font-mono select-all">{{ '{{course_title}}' }}</span>
                    <span class="px-2 py-1 bg-white/5 rounded text-[10px] text-slate-300 font-mono select-all">{{ '{{certificate_code}}' }}</span>
                    <span class="px-2 py-1 bg-white/5 rounded text-[10px] text-slate-300 font-mono select-all">{{ '{{issue_date}}' }}</span>
                </div>
            </div>
        </div>

        <!-- Live Visual Preview Panel -->
        <div class="lg:col-span-7 flex flex-col space-y-4">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Live Document Preview</h3>
            
            <div class="w-full bg-slate-950 p-4 rounded-2xl border border-white/5 flex-1 min-h-[500px] flex items-center justify-center overflow-auto">
                <div class="w-full max-w-2xl bg-white shadow-2xl rounded-xl p-4 overflow-hidden transform scale-90 origin-center text-slate-900">
                    <!-- Parse placeholders for preview -->
                    @php
                        $previewHtml = str_replace(
                            ['{{student_name}}', '{{course_title}}', '{{certificate_code}}', '{{issue_date}}'],
                            ['Dayanand Nand', 'Mastery in Advanced Software Engineering', 'CERT-2026-NAND88', date('Y-m-d')],
                            $htmlContent
                        );
                    @endphp
                    {!! $previewHtml !!}
                </div>
            </div>
        </div>
    </div>
</div>
