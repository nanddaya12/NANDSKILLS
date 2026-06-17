<div class="space-y-6 h-[calc(100vh-140px)] flex flex-col">
    <!-- Navigation Tabs & Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center bg-slate-900/60 p-4 rounded-2xl border border-white/5 backdrop-blur-md shrink-0 gap-4">
        <div>
            <h2 class="text-xl font-black text-white tracking-tight">Enterprise Communication Hub</h2>
            <p class="text-slate-400 text-xs mt-0.5">Secure direct messaging channels and official announcement broadcasts.</p>
        </div>
        <div class="flex bg-slate-950 p-1 rounded-xl border border-white/5">
            <button wire:click="$set('activeTab', 'messages')"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $activeTab === 'messages' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white' }}">
                💬 Direct Messages
            </button>
            <button wire:click="$set('activeTab', 'announcements')"
                    class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $activeTab === 'announcements' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white' }}">
                📢 Bulletins
            </button>
            @if(auth()->user()->hasRole(['Super Admin', 'Tenant Admin', 'Trainer']))
                <button wire:click="$set('activeTab', 'broadcast')"
                        class="px-4 py-2 rounded-lg text-xs font-bold transition-all {{ $activeTab === 'broadcast' ? 'bg-blue-600 text-white' : 'text-slate-400 hover:text-white' }}">
                    ⚡ Broadcast Announcement
                </button>
            @endif
        </div>
    </div>

    <!-- Main Workspace -->
    <div class="flex-1 min-h-0 flex gap-6">

        @if($activeTab === 'messages')
            <!-- CONTACTS DIRECTORY -->
            <div class="w-80 bg-slate-900/40 border border-white/5 rounded-2xl flex flex-col overflow-hidden backdrop-blur-md">
                <div class="p-4 border-b border-white/5">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Directory Contacts</h3>
                </div>
                <div class="flex-1 overflow-y-auto divide-y divide-white/5">
                    @forelse($contacts as $contact)
                        <button wire:click="selectContact('{{ $contact['id'] }}')"
                                class="w-full text-left p-4 flex items-center gap-3 transition-colors {{ $selectedContactId === $contact['id'] ? 'bg-blue-600/10 border-l-4 border-blue-500' : 'hover:bg-white/5' }}">
                            <div class="h-10 w-10 shrink-0 rounded-full bg-slate-800 flex items-center justify-center font-bold text-slate-300 border border-white/10 uppercase">
                                {{ substr($contact['name'], 0, 1) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex justify-between items-baseline">
                                    <h4 class="text-sm font-bold text-white truncate">{{ $contact['name'] }}</h4>
                                </div>
                                <div class="flex items-center justify-between text-xs text-slate-400 mt-1">
                                    <span class="truncate">{{ $contact['email'] }}</span>
                                    <span class="px-1.5 py-0.5 rounded bg-white/5 text-[9px] font-bold uppercase text-slate-300 border border-white/5">{{ $contact['role'] }}</span>
                                </div>
                            </div>
                        </button>
                    @empty
                        <div class="p-8 text-center text-slate-500 text-sm">No contacts matching your role rules.</div>
                    @endforelse
                </div>
            </div>

            <!-- MESSAGING CONTAINER -->
            <div class="flex-1 bg-slate-900/40 border border-white/5 rounded-2xl flex flex-col overflow-hidden backdrop-blur-md">
                @if($selectedContactId)
                    @php
                        $activeContact = collect($contacts)->firstWhere('id', $selectedContactId);
                    @endphp
                    <!-- Active Contact Header -->
                    <div class="p-4 bg-slate-950/40 border-b border-white/5 flex items-center justify-between shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-full bg-blue-600/10 text-blue-400 border border-blue-500/20 flex items-center justify-center font-bold uppercase">
                                {{ substr($activeContact['name'] ?? 'C', 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-white">{{ $activeContact['name'] ?? 'Chat' }}</h3>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $activeContact['email'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Threads -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-4 flex flex-col justify-end" id="chat-scroller">
                        <div class="space-y-4 overflow-y-auto max-h-full">
                            @forelse($messages as $msg)
                                <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                    <div class="max-w-[70%] rounded-2xl px-4 py-3 text-sm {{ $msg->sender_id === auth()->id() ? 'bg-blue-600 text-white rounded-br-none shadow-md shadow-blue-600/10' : 'bg-slate-800 text-slate-100 rounded-bl-none border border-white/5' }}">
                                        <p class="leading-relaxed whitespace-pre-line">{{ $msg->content }}</p>
                                        <div class="text-[9px] mt-1.5 text-right {{ $msg->sender_id === auth()->id() ? 'text-blue-200' : 'text-slate-400' }} font-semibold">
                                            {{ $msg->created_at->format('g:i A') }}
                                            @if($msg->sender_id === auth()->id())
                                                <span class="ml-1">{{ $msg->is_read ? '✓✓' : '✓' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-20 text-slate-500 text-xs">No message history. Send a message to start the channel.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Input message block -->
                    <div class="p-4 border-t border-white/5 bg-slate-950/20 shrink-0">
                        <form wire:submit.prevent="sendMessage" class="flex gap-3">
                            <input type="text" wire:model.defer="messageText" placeholder="Type your message here..." required
                                   class="flex-1 bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                            <button type="submit" class="px-6 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-sm tracking-wide transition-colors">
                                Send
                            </button>
                        </form>
                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center text-slate-500 p-8">
                        <span class="text-4xl mb-4">💬</span>
                        <p class="text-sm font-bold text-slate-400">Select a contact to begin secure communication.</p>
                    </div>
                @endif
            </div>

        @elseif($activeTab === 'announcements')
            <!-- ANNOUNCEMENTS TIMELINE -->
            <div class="flex-1 bg-slate-900/40 border border-white/5 rounded-2xl flex flex-col overflow-hidden backdrop-blur-md p-6">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-6">Academy Bulletin Board</h3>
                <div class="flex-1 overflow-y-auto space-y-6">
                    @forelse($announcements as $announcement)
                        <div class="p-6 rounded-2xl bg-white/5 border border-white/5 space-y-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="text-base font-extrabold text-white">{{ $announcement->title }}</h4>
                                    <p class="text-xs text-slate-400 mt-1">
                                        Posted by <strong class="text-slate-300">{{ $announcement->creator->first_name }} {{ $announcement->creator->last_name }}</strong> 
                                        on {{ $announcement->created_at->format('M d, Y at g:i A') }}
                                    </p>
                                </div>
                                <div class="flex gap-2">
                                    @foreach($announcement->target_roles as $role)
                                        <span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-400 text-[9px] font-black uppercase tracking-wider border border-blue-500/20">{{ $role }}</span>
                                    @endforeach
                                </div>
                            </div>
                            <p class="text-sm text-slate-300 leading-relaxed whitespace-pre-line">{{ $announcement->content }}</p>
                        </div>
                    @empty
                        <div class="text-center py-20 text-slate-500 text-sm">No bulletins published to your roles.</div>
                    @endforelse
                </div>
            </div>

        @elseif($activeTab === 'broadcast' && auth()->user()->hasRole(['Super Admin', 'Tenant Admin', 'Trainer']))
            <!-- BROADCAST BUILDER FORM -->
            <div class="flex-1 bg-slate-900/40 border border-white/5 rounded-2xl flex flex-col overflow-hidden backdrop-blur-md p-8 max-w-3xl mx-auto space-y-6">
                <div>
                    <h3 class="text-lg font-black text-white">Broadcast New Bulletin Announcement</h3>
                    <p class="text-xs text-slate-400 mt-1">Publish notifications that appear instantly in targeting dashboards.</p>
                </div>

                <form wire:submit.prevent="broadcastAnnouncement" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Bulletin Title</label>
                        <input type="text" wire:model.defer="announcementTitle" required
                               class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm"
                               placeholder="Emergency closing, Class scheduling update...">
                        @error('announcementTitle') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Target Roles (Multiple)</label>
                        <div class="flex flex-wrap gap-4 bg-slate-950 p-4 rounded-xl border border-white/5">
                            <label class="flex items-center text-sm text-slate-300 gap-2 cursor-pointer">
                                <input type="checkbox" value="Student" wire:model="targetRoles" class="rounded bg-slate-900 border-white/10 text-blue-600 focus:ring-0">
                                <span>Students</span>
                            </label>
                            <label class="flex items-center text-sm text-slate-300 gap-2 cursor-pointer">
                                <input type="checkbox" value="Trainer" wire:model="targetRoles" class="rounded bg-slate-900 border-white/10 text-blue-600 focus:ring-0">
                                <span>Trainers</span>
                            </label>
                            <label class="flex items-center text-sm text-slate-300 gap-2 cursor-pointer">
                                <input type="checkbox" value="Parent" wire:model="targetRoles" class="rounded bg-slate-900 border-white/10 text-blue-600 focus:ring-0">
                                <span>Parents</span>
                            </label>
                            <label class="flex items-center text-sm text-slate-300 gap-2 cursor-pointer">
                                <input type="checkbox" value="Tenant Admin" wire:model="targetRoles" class="rounded bg-slate-900 border-white/10 text-blue-600 focus:ring-0">
                                <span>Admins</span>
                            </label>
                        </div>
                        @error('targetRoles') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Scope Branch (Optional)</label>
                            <select wire:model.defer="selectedBranch"
                                    class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                                <option value="">Global / All Branches</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-2">Announcement Content</label>
                        <textarea wire:model.defer="announcementContent" rows="6" required
                                  class="w-full bg-slate-950 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm"
                                  placeholder="Write the detailed bulletin message content here..."></textarea>
                        @error('announcementContent') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-sm tracking-wide transition-all shadow-md shadow-blue-600/10">
                        Publish Broadcast Announcement
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>
