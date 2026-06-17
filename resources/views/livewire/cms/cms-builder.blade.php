<div class="space-y-8 text-slate-800 font-sans" xmlns:wire="http://www.w3.org/1999/xhtml">
    <!-- Header control bar -->
    <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-slate-800">CMS Web Builder</h2>
            <p class="text-slate-500 text-sm mt-1">Design and arrange the blocks of your academy landing portal page.</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="savePage" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-sm font-semibold text-white transition-all shadow-lg shadow-blue-500/20">
                Save & Publish
            </button>
        </div>
    </div>

    @if ($statusMessage)
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm flex items-center gap-3">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ $statusMessage }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Visual section listing -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-base font-bold text-slate-850">Website Section Blocks</h3>
                <div class="flex gap-2">
                    <button wire:click="addSection('notice_bar')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-[10px] font-bold tracking-wider uppercase text-slate-600">
                        + Alert Bar
                    </button>
                    <button wire:click="addSection('hero')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-[10px] font-bold tracking-wider uppercase text-slate-600">
                        + Hero
                    </button>
                    <button wire:click="addSection('about')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-[10px] font-bold tracking-wider uppercase text-slate-600">
                        + About
                    </button>
                    <button wire:click="addSection('contact')" class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-[10px] font-bold tracking-wider uppercase text-slate-600">
                        + Contact
                    </button>
                </div>
            </div>

            <div class="space-y-4">
                @forelse($sections as $index => $section)
                    <div class="p-4 rounded-xl border {{ $editingSectionIndex === (string)$index ? 'border-blue-500 bg-blue-50/50' : 'border-slate-200 bg-slate-50' }} flex items-center justify-between hover:border-slate-300 transition-all">
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-blue-600">{{ $section['type'] }}</span>
                            <h4 class="text-sm font-bold text-slate-800">
                                @if($section['type'] === 'notice_bar')
                                    {{ $section['settings']['text'] ?? 'Notice Bar block' }}
                                @elseif($section['type'] === 'hero')
                                    {{ $section['settings']['title'] ?? 'Hero block' }}
                                @elseif($section['type'] === 'stats')
                                    Metric Counter Stats block
                                @elseif($section['type'] === 'about')
                                    {{ $section['settings']['title'] ?? 'About info block' }}
                                @elseif($section['type'] === 'contact')
                                    {{ $section['settings']['title'] ?? 'Contact detail block' }}
                                @else
                                    Custom Page block
                                @endif
                            </h4>
                        </div>
                        <div class="flex items-center gap-2">
                            <button wire:click="moveUp({{ $index }})" class="p-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-600" title="Move Up">
                                ▲
                            </button>
                            <button wire:click="moveDown({{ $index }})" class="p-2 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-600" title="Move Down">
                                ▼
                            </button>
                            <button wire:click="selectSection({{ $index }})" class="px-3 py-1.5 rounded-lg bg-blue-50 border border-blue-200 text-blue-600 hover:bg-blue-600 hover:text-white text-xs font-semibold transition-all">
                                Edit Settings
                            </button>
                            <button wire:click="deleteSection({{ $index }})" class="p-2 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-600 hover:text-white" title="Delete">
                                🗑
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 border border-dashed border-slate-200 rounded-xl bg-slate-50">
                        No section blocks added yet. Click one of the buttons above to build your page structure.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Section configuration panel -->
        <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-6">
            <h3 class="text-base font-bold text-slate-800 border-b border-slate-100 pb-4">Block Settings Properties</h3>

            @if ($editingSectionIndex !== null)
                <form wire:submit.prevent="updateSectionSettings" class="space-y-5">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-blue-600 block mb-2">
                        Configure Block: {{ $sections[(int)$editingSectionIndex]['type'] }}
                    </span>

                    @if ($sections[(int)$editingSectionIndex]['type'] === 'notice_bar')
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-2">Notice Text</label>
                            <input type="text" wire:model.defer="editingSectionSettings.text" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-2">CTA Link Text</label>
                            <input type="text" wire:model.defer="editingSectionSettings.cta_text" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-2">CTA Destination URL</label>
                            <input type="text" wire:model.defer="editingSectionSettings.cta_url" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                        </div>
                    @endif

                    @if ($sections[(int)$editingSectionIndex]['type'] === 'hero')
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-2">Hero Header Title</label>
                            <input type="text" wire:model.defer="editingSectionSettings.title" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-2">Subtitle Message</label>
                            <textarea wire:model.defer="editingSectionSettings.subtitle" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-2">CTA Link Text</label>
                            <input type="text" wire:model.defer="editingSectionSettings.cta_text" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-2">CTA Destination URL</label>
                            <input type="text" wire:model.defer="editingSectionSettings.cta_url" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-2">Illustration Image URL</label>
                            <input type="text" wire:model.defer="editingSectionSettings.image_url" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                        </div>
                    @endif

                    @if ($sections[(int)$editingSectionIndex]['type'] === 'about')
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-2">About Header Title</label>
                            <input type="text" wire:model.defer="editingSectionSettings.title" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-2">Company Description</label>
                            <textarea wire:model.defer="editingSectionSettings.description" rows="4" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-2">Mission Statement</label>
                            <textarea wire:model.defer="editingSectionSettings.mission" rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500"></textarea>
                        </div>
                    @endif

                    @if ($sections[(int)$editingSectionIndex]['type'] === 'contact')
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-2">Contact Title</label>
                            <input type="text" wire:model.defer="editingSectionSettings.title" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-2">Office Phone</label>
                            <input type="text" wire:model.defer="editingSectionSettings.phone" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-2">Contact Email</label>
                            <input type="email" wire:model.defer="editingSectionSettings.email" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-2">Academy Street Address</label>
                            <input type="text" wire:model.defer="editingSectionSettings.address" class="w-full bg-slate-50 border border-slate-200 rounded-xl py-2.5 px-4 text-sm text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500">
                        </div>
                    @endif

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="flex-1 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-550 text-xs font-bold text-white transition-all">
                            Apply Changes
                        </button>
                        <button type="button" wire:click="$set('editingSectionIndex', null)" class="flex-1 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-xs font-semibold transition-all text-slate-500">
                            Cancel
                        </button>
                    </div>
                </form>
            @else
                <div class="text-center py-12 text-slate-400 text-xs border border-dashed border-slate-200 rounded-xl bg-slate-50">
                    Select a section from the list to modify its content properties.
                </div>
            @endif
        </div>
    </div>
</div>
