<div class="space-y-8">
    <!-- Header -->
    <div class="flex justify-between items-center bg-slate-950/40 p-6 rounded-2xl border border-white/5">
        <div>
            <h2 class="text-xl font-bold text-white">Digital Document Center</h2>
            <p class="text-slate-400 text-sm mt-1">Upload records, track document revisions, organize category folders, audit access logs, and sign off approvals.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="$toggle('isFolderFormOpen')" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-750 font-semibold text-xs text-slate-300 transition-all border border-white/5">
                New Folder
            </button>
            <button wire:click="$toggle('isFormOpen')" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 font-semibold text-xs text-white transition-all shadow-md shadow-blue-600/10">
                Upload Document
            </button>
        </div>
    </div>

    <!-- Folder Creation form -->
    @if($isFolderFormOpen)
        <div class="p-6 rounded-2xl bg-white/5 border border-white/10 max-w-md mx-auto space-y-4">
            <h3 class="text-xs font-bold text-white uppercase tracking-wider">Create New Folder Category</h3>
            <form wire:submit.prevent="saveFolder" class="space-y-3">
                <div>
                    <label class="block text-[10px] text-slate-400 mb-1">Folder Name</label>
                    <input type="text" wire:model.defer="new_folder_name" required placeholder="e.g. Student Transcripts" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-[10px] text-slate-400 mb-1">Folder Icon / Emoji</label>
                    <input type="text" wire:model.defer="new_folder_icon" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs focus:border-blue-500">
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-xs">Create Folder</button>
                    <button type="button" wire:click="$set('isFolderFormOpen', false)" class="px-4 py-2 bg-slate-800 text-slate-350 rounded-xl text-xs">Cancel</button>
                </div>
            </form>
        </div>
    @endif

    <!-- Upload Document Form -->
    @if($isFormOpen)
        <div class="p-6 rounded-2xl bg-white/5 border border-white/10 max-w-xl mx-auto space-y-4">
            <h3 class="text-xs font-bold text-white uppercase tracking-wider">Upload Managed File</h3>
            <form wire:submit.prevent="saveDocument" class="space-y-4">
                <div>
                    <label class="block text-[10px] text-slate-400 mb-1">Folder Category</label>
                    <select wire:model.defer="selectedCategoryId" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs focus:border-blue-500">
                        <option value="">Select Folder...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->icon }} {{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1">Document Title</label>
                        <input type="text" wire:model.defer="title" required placeholder="e.g. CS Graduation requirements" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1">Owner Scope / Type</label>
                        <select wire:model.defer="owner_type" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-3 text-xs">
                            <option value="tenant">Tenant/Institution Wide</option>
                            <option value="student">Student Profiles Only</option>
                            <option value="staff">Staff Logs Only</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] text-slate-400 mb-1">Upload File (Max 10MB)</label>
                    <input type="file" wire:model="fileUpload" class="w-full bg-slate-900 border border-white/10 text-xs p-2 rounded-xl text-slate-350">
                    <div wire:loading wire:target="fileUpload" class="text-[9px] text-blue-400 mt-1">Uploading file...</div>
                    @error('fileUpload') <span class="text-xs text-rose-450 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] text-slate-400 mb-1">Expiry Date (Optional)</label>
                        <input type="date" wire:model.defer="expiry_date" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs">
                    </div>
                    <div class="flex items-center pt-5">
                        <input type="checkbox" wire:model.defer="is_public" id="is_public" class="rounded bg-slate-900 border-white/10 text-blue-600 mr-2">
                        <label for="is_public" class="text-xs text-slate-300">Available to public view</label>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] text-slate-400 mb-1">Description / Notes</label>
                    <textarea wire:model.defer="description" rows="3" class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-2 px-3 text-xs focus:border-blue-500"></textarea>
                </div>

                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-xl shadow-sm">Upload & Submit</button>
                    <button type="button" wire:click="$set('isFormOpen', false)" class="px-5 py-2.5 bg-slate-800 text-slate-300 rounded-xl text-xs">Cancel</button>
                </div>
            </form>
        </div>
    @endif

    <div class="grid grid-cols-3 gap-6">
        <!-- Folders and File Lists -->
        <div class="col-span-2 p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md space-y-4">
            <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Document Catalog</h3>
            
            <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2">
                @forelse($documents as $doc)
                    <div class="p-4 bg-slate-950/30 rounded-xl border border-white/5 hover:border-white/10 transition-colors flex justify-between items-center">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl pt-1">{{ $doc->category->icon ?? '📄' }}</span>
                            <div>
                                <h4 class="text-xs font-bold text-white">{{ $doc->title }}</h4>
                                <p class="text-[10px] text-slate-450 mt-0.5">{{ $doc->original_filename }} ({{ round($doc->file_size/1024, 1) }} KB) — Ver {{ $doc->version }}</p>
                                <div class="flex items-center gap-3 text-[9px] text-slate-500 mt-1 font-semibold uppercase">
                                    <span>Uploaded by: {{ $doc->uploader->first_name ?? 'Staff' }}</span>
                                    <span>Status: <span class="text-amber-400">{{ str_replace('_', ' ', $doc->status) }}</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button wire:click="selectDocument('{{ $doc->id }}')" class="text-xs bg-slate-800 hover:bg-slate-750 text-blue-450 px-3 py-1.5 rounded-lg font-semibold">Inspect</button>
                            @if($doc->status === 'PENDING_APPROVAL')
                                <button wire:click="approveDocument('{{ $doc->id }}')" class="text-xs bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-450 px-2 py-1.5 rounded font-bold">Approve</button>
                            @endif
                            <button onclick="confirm('Delete this file?') || event.stopImmediatePropagation()" wire:click="deleteDocument('{{ $doc->id }}')" class="text-xs text-rose-450 hover:text-rose-300 px-2 py-1.5">Delete</button>
                        </div>
                    </div>
                @empty
                    <div class="text-xs text-slate-500 py-12 text-center">No digital documents uploaded in workspace.</div>
                @endforelse
            </div>
        </div>

        <!-- Selected Document Audit and detail trail -->
        <div class="p-6 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-md h-fit space-y-5">
            <h3 class="text-sm font-bold text-white border-b border-white/5 pb-2">Document Audit & approvals</h3>
            
            @if($selectedDocument)
                <div class="space-y-4">
                    <div>
                        <div class="text-xs font-bold text-white">{{ $selectedDocument->title }}</div>
                        <p class="text-[10px] text-slate-500 mt-1">{{ $selectedDocument->description ?: 'No details provided.' }}</p>
                    </div>

                    <!-- Versions history list -->
                    <div class="space-y-2 border-t border-white/5 pt-4">
                        <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Version History</span>
                        @foreach($selectedDocument->versions as $ver)
                            <div class="flex justify-between items-center text-[10px] bg-slate-950/40 p-2 rounded border border-white/5">
                                <div>
                                    <span class="font-bold text-blue-400">Ver {{ $ver->version_no }}</span>
                                    <span class="text-slate-450 ml-2">({{ round($ver->file_size/1024, 1) }} KB)</span>
                                </div>
                                <span class="text-slate-500">{{ $ver->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Audit logs checklist -->
                    <div class="space-y-2 border-t border-white/5 pt-4">
                        <span class="text-[10px] uppercase tracking-wider text-slate-400 font-bold font-semibold">Access & Action Logs</span>
                        <div class="space-y-2 max-h-[160px] overflow-y-auto pr-1">
                            @foreach($selectedDocument->auditTrails as $trail)
                                <div class="p-2 rounded bg-slate-950/30 border border-white/5 text-[9px]">
                                    <div class="flex justify-between font-semibold text-slate-350">
                                        <span class="text-blue-300">{{ $trail->action }}</span>
                                        <span>{{ $trail->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="text-slate-500 mt-0.5">By: {{ $trail->user->first_name ?? 'System' }} (IP: {{ $trail->ip_address }})</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="text-xs text-slate-500 py-12 text-center italic">Inspect a document to view history, audit logs, and approval files.</div>
            @endif
        </div>
    </div>
</div>
