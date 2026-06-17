<div class="space-y-6">

    {{-- ── PAGE HEADER ─────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 tracking-tight">🎥 Virtual Classrooms</h2>
            <p class="text-sm text-slate-500 mt-1">Schedule and conduct live video conferences — no external apps needed.</p>
        </div>
        @if(auth()->user()->hasRole(['Tenant Admin', 'Trainer']))
            <button wire:click="$toggle('isScheduling')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ $isScheduling ? 'Cancel' : 'Schedule Meeting' }}
            </button>
        @endif
    </div>

    @if (session()->has('status'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium rounded-xl flex items-center gap-3">
            <svg class="h-5 w-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('status') }}
        </div>
    @endif

    @php
        $showSidebar = auth()->user()->hasRole(['Tenant Admin', 'Trainer']);
    @endphp

    <div class="grid grid-cols-1 {{ $showSidebar ? 'lg:grid-cols-3' : 'lg:grid-cols-1' }} gap-6">

        {{-- Left / Main content: Meeting List --}}
        <div class="{{ $showSidebar ? 'lg:col-span-2' : 'lg:col-span-1' }} space-y-6">

            {{-- Live banner --}}
            @php $liveMeetings = collect($meetings)->filter(fn($m) => $m->status === 'LIVE'); @endphp
            @if($liveMeetings->isNotEmpty())
                <div class="bg-blue-600 text-white rounded-2xl shadow-sm p-6 relative overflow-hidden">
                    <span class="bg-white/20 text-white font-bold text-xs uppercase px-2.5 py-1 rounded-full tracking-wider">⬤ Live Now</span>
                    <h3 class="text-xl font-bold mt-2">{{ $liveMeetings->first()->topic }}</h3>
                    <p class="text-blue-100 text-sm max-w-md mt-1">A virtual classroom is currently live. Join now.</p>
                    <a href="{{ route('meeting.session', $liveMeetings->first()->id) }}" target="_blank"
                       class="mt-4 px-5 py-2.5 bg-white hover:bg-slate-100 text-blue-600 font-bold text-sm rounded-xl transition shadow-md inline-block">
                        Join Meeting →
                    </a>
                </div>
            @endif

            {{-- Sessions table --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 text-base">Scheduled Sessions</h3>
                    <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">{{ count($meetings) }} Total</span>
                </div>

                @if(count($meetings) === 0)
                    <div class="text-center py-14 px-4">
                        <div class="text-5xl mb-4">🎥</div>
                        <h4 class="font-semibold text-slate-700 text-sm">No sessions scheduled yet</h4>
                        <p class="text-xs text-slate-400 mt-1">Sessions created by instructors appear here.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-3">Topic & Course</th>
                                    <th class="px-6 py-3">Scheduled</th>
                                    <th class="px-6 py-3">Trainer</th>
                                    <th class="px-6 py-3 text-center">Status</th>
                                    <th class="px-6 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($meetings as $meeting)
                                    <tr class="hover:bg-blue-50/40 transition text-sm">
                                        <td class="px-6 py-4">
                                            <span class="font-semibold text-slate-800 block">{{ $meeting->topic }}</span>
                                            <span class="text-xs text-slate-400">{{ $meeting->course->title }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-slate-600">
                                            <span class="block font-medium">{{ $meeting->scheduled_at->format('M d, Y') }}</span>
                                            <span class="text-xs text-slate-400">{{ $meeting->scheduled_at->format('h:i A') }} · {{ $meeting->duration_minutes }}min</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <div class="h-6 w-6 rounded-full bg-blue-100 text-blue-600 border border-blue-200 flex items-center justify-center font-bold text-[9px] uppercase">
                                                    {{ substr($meeting->trainer->first_name ?? 'T', 0, 1) }}
                                                </div>
                                                <span class="text-slate-700 font-medium text-xs">{{ $meeting->trainer->first_name }} {{ $meeting->trainer->last_name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-nowrap">
                                            @if($meeting->status === 'UPCOMING')
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">Upcoming</span>
                                            @elseif($meeting->status === 'LIVE')
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 animate-pulse">⬤ LIVE</span>
                                            @else
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-400 line-through">Ended</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-2">
                                                @if(auth()->user()->id === $meeting->trainer_id || auth()->user()->hasRole('Tenant Admin'))
                                                    @if($meeting->status === 'UPCOMING')
                                                        <a href="{{ route('meeting.session', $meeting->id) }}" target="_blank"
                                                                class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition shadow-sm inline-block">
                                                            Start Class
                                                        </a>
                                                    @endif
                                                @endif
                                                @if($meeting->status === 'LIVE')
                                                    <a href="{{ route('meeting.session', $meeting->id) }}" target="_blank"
                                                            class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold rounded-lg transition shadow-sm inline-block">
                                                        Join Live
                                                    </a>
                                                @endif
                                                @if($meeting->status !== 'COMPLETED' && (auth()->user()->id === $meeting->trainer_id || auth()->user()->hasRole('Tenant Admin')))
                                                    <button wire:click="completeMeeting('{{ $meeting->id }}')"
                                                            onclick="confirm('End this session?') || event.stopImmediatePropagation()"
                                                            class="px-2 py-1.5 hover:bg-red-50 text-slate-400 hover:text-red-500 rounded-lg transition" title="Mark Completed">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right: Schedule form / info --}}
        @if($showSidebar)
            <div>
                @if($isScheduling)
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h3 class="font-bold text-slate-800 text-base">📅 Schedule New Class</h3>
                            <button wire:click="$set('isScheduling', false)" class="text-slate-400 hover:text-slate-600 transition">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <form wire:submit.prevent="scheduleMeeting" class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">Select Course</label>
                                <select wire:model="selectedCourse"
                                        class="w-full rounded-xl border border-slate-300 bg-white text-slate-800 text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    <option value="">-- Choose Course --</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                                @error('selectedCourse') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">Topic / Title</label>
                                <input type="text" wire:model="topic" placeholder="e.g. Chapter 3: Dynamic Routing"
                                       class="w-full rounded-xl border border-slate-300 bg-white text-slate-800 text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                @error('topic') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">Description</label>
                                <textarea wire:model="description" rows="3" placeholder="Brief session outline..."
                                          class="w-full rounded-xl border border-slate-300 bg-white text-slate-800 text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400"></textarea>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">Date & Time</label>
                                    <input type="datetime-local" wire:model="scheduledAt"
                                           class="w-full rounded-xl border border-slate-300 bg-white text-slate-800 text-xs px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    @error('scheduledAt') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">Duration (mins)</label>
                                    <input type="number" wire:model="duration"
                                           class="w-full rounded-xl border border-slate-300 bg-white text-slate-800 text-sm px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-400">
                                    @error('duration') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <button type="submit"
                                    class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl transition shadow-sm">
                                Save Schedule
                            </button>
                        </form>
                    </div>
                @else
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6 space-y-4">
                        <div class="h-12 w-12 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 text-2xl">🎥</div>
                        <h3 class="font-bold text-slate-800 text-base">Custom WebRTC Conferencing</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">NANDSKILLS uses a fully self-hosted, browser-native WebRTC engine — no external accounts, no Jitsi, no Zoom.</p>
                        <ul class="space-y-2 text-xs text-slate-500">
                            @foreach(['HD video & audio conferencing', 'Screen sharing & presentations', 'In-room live chat', 'Camera & microphone controls', 'Participant grid (up to 8 peers)', 'Host can end class for everyone'] as $feature)
                                <li class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ $feature }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
