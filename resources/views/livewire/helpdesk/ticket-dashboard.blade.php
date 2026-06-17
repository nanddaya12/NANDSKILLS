<div class="space-y-8 h-full flex flex-col">
    <!-- Header -->
    <div class="flex justify-between items-center bg-slate-950/40 p-6 rounded-2xl border border-white/5 shrink-0">
        <div>
            <h2 class="text-xl font-bold text-white">Helpdesk Support System</h2>
            <p class="text-slate-400 text-sm mt-1">Submit technical questions, report player errors, track resolving times, and chat with help agents.</p>
        </div>
        <button wire:click="$toggle('isCreating')" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 font-semibold text-sm transition-all shadow-md shadow-blue-600/10">
            {{ $isCreating ? 'View Tickets Board' : 'Submit Ticket Request' }}
        </button>
    </div>

    @if($isCreating)
        <!-- New Ticket Form -->
        <div class="p-8 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md max-w-2xl mx-auto space-y-6">
            <h3 class="text-lg font-bold text-white">Submit Support Ticket</h3>

            <form wire:submit.prevent="createTicket" class="space-y-5">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Subject</label>
                    <input type="text" wire:model.defer="subject" required
                           class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm"
                           placeholder="Trouble loading lesson video page...">
                    @error('subject') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Detailed Description</label>
                    <textarea wire:model.defer="description" rows="4" required
                              class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm"
                              placeholder="Please describe what steps led to the issue..."></textarea>
                    @error('description') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Support Category</label>
                        <select wire:model.defer="selectedCategory" required
                                class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                            <option value="">Choose Category...</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedCategory') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Priority</label>
                        <select wire:model.defer="priority" required
                                class="w-full bg-slate-900 border border-white/10 text-white rounded-xl py-3 px-4 focus:outline-none focus:border-blue-500 text-sm">
                            <option value="LOW">Low</option>
                            <option value="MEDIUM">Medium</option>
                            <option value="HIGH">High</option>
                            <option value="URGENT">Urgent</option>
                        </select>
                        @error('priority') <span class="text-xs text-rose-400 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="pt-4 flex gap-4">
                    <button type="submit" class="flex-1 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-semibold text-sm transition-colors">
                        Submit Ticket Request
                    </button>
                    <button type="button" wire:click="$set('isCreating', false)" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-semibold text-sm transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @else
        <!-- Split Dashboard: List on Left, Ticket Detail on Right -->
        <div class="flex-1 flex gap-8 min-h-0 overflow-hidden">
            <!-- Tickets Directory -->
            <div class="w-1/2 bg-white/5 border border-white/5 rounded-2xl flex flex-col overflow-hidden">
                <div class="p-4 border-b border-white/5 bg-slate-950/40">
                    <h3 class="font-bold text-white text-sm">Active Support Tickets</h3>
                </div>

                <div class="flex-1 overflow-y-auto divide-y divide-white/5">
                    @forelse($tickets as $ticket)
                        <button wire:click="viewTicket('{{ $ticket->id }}')"
                                class="w-full text-left p-4 hover:bg-white/5 transition-colors flex justify-between items-center gap-4 {{ $selectedTicket && $selectedTicket->id === $ticket->id ? 'bg-white/5' : '' }}">
                            <div class="space-y-1">
                                <h4 class="text-xs font-bold text-white">{{ $ticket->subject }}</h4>
                                <p class="text-[10px] text-slate-400">Requester: {{ $ticket->requester->first_name }} • Cat: {{ $ticket->category->name }}</p>
                            </div>
                            <div class="flex flex-col items-end gap-2 shrink-0">
                                @if($ticket->status === 'CLOSED')
                                    <span class="px-2 py-0.5 rounded bg-slate-800 text-[8px] font-bold text-slate-400 border border-white/5">CLOSED</span>
                                @else
                                    <span class="px-2 py-0.5 rounded bg-blue-500/10 text-blue-400 text-[8px] font-bold border border-blue-500/20">OPEN</span>
                                @endif
                                <span class="text-[8px] font-bold uppercase tracking-wider text-slate-500">{{ $ticket->priority }}</span>
                            </div>
                        </button>
                    @empty
                        <div class="py-12 text-center text-xs text-slate-500">No support tickets active.</div>
                    @endforelse
                </div>
            </div>

            <!-- Ticket Details/Timeline Chat -->
            <div class="w-1/2 bg-white/5 border border-white/5 rounded-2xl flex flex-col overflow-hidden">
                @if($selectedTicket)
                    <!-- Ticket Title Header -->
                    <div class="p-6 border-b border-white/5 bg-slate-950/40 flex justify-between items-center shrink-0">
                        <div>
                            <h3 class="font-bold text-white text-base leading-snug">{{ $selectedTicket->subject }}</h3>
                            <span class="text-[10px] text-slate-500 font-bold uppercase tracking-wider">Priority: {{ $selectedTicket->priority }}</span>
                        </div>
                        @if($selectedTicket->status !== 'CLOSED')
                            <button wire:click="closeTicket('{{ $selectedTicket->id }}')" class="px-3 py-1.5 rounded bg-rose-600 hover:bg-rose-500 text-[10px] font-bold text-white transition-all shadow-md shadow-rose-600/10">
                                Close Ticket
                            </button>
                        @endif
                    </div>

                    <!-- Chat Log / Thread -->
                    <div class="flex-1 overflow-y-auto p-6 space-y-4">
                        <!-- Initial Description Box -->
                        <div class="p-4 rounded-xl bg-slate-900 border border-white/5 space-y-2">
                            <p class="text-xs text-slate-300 font-medium leading-relaxed">{{ $selectedTicket->description }}</p>
                            <span class="text-[9px] text-slate-500 font-bold block uppercase">{{ $selectedTicket->created_at->diffForHumans() }}</span>
                        </div>

                        <!-- Ticket Comments Timeline -->
                        @foreach($selectedTicket->comments as $comment)
                            <div class="space-y-1">
                                <div class="flex justify-between text-[10px] text-slate-400 px-1 font-semibold">
                                    <span>{{ $comment->user->first_name }} {{ $comment->user->last_name }}</span>
                                    <span>{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="p-3.5 rounded-xl bg-white/5 border border-white/5 text-xs text-slate-300 leading-relaxed">
                                    {{ $comment->message }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Comment Input box -->
                    @if($selectedTicket->status !== 'CLOSED')
                        <div class="p-4 border-t border-white/5 bg-slate-950/40 flex gap-3 shrink-0">
                            <input type="text" wire:model.defer="commentMessage" placeholder="Type message to requester..."
                                   class="flex-1 bg-slate-900 border border-white/10 text-white rounded-xl py-2.5 px-4 focus:outline-none focus:border-blue-500 text-xs">
                            <button wire:click="addComment" class="px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-semibold text-white transition-all">
                                Send
                            </button>
                        </div>
                    @endif
                @else
                    <div class="flex-1 flex flex-col items-center justify-center text-slate-500 gap-3">
                        <svg class="h-12 w-12 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <span class="text-sm">Please select a support ticket to view details.</span>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
