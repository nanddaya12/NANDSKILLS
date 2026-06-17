<div
    x-data="nandskillsMeetingManager({
        roomId:     '{{ $meeting->meeting_id }}',
        meetingId:  '{{ $meeting->id }}',
        userId:     '{{ auth()->id() }}',
        userName:   '{{ addslashes(trim(auth()->user()->first_name . ' ' . auth()->user()->last_name)) }}',
        isHost:     {{ $isHost ? 'true' : 'false' }},
        csrfToken:  '{{ csrf_token() }}',
        signalUrl:  '{{ route('meeting.signal', $meeting->meeting_id) }}'
    })"
    x-init="initRoom()"
    class="flex flex-col h-screen bg-slate-950 text-slate-100 overflow-hidden"
>
    {{-- ── 1. HEADER BAR ─────────────────────────────────── --}}
    <header class="h-16 px-6 bg-slate-900 border-b border-slate-800 flex items-center justify-between shrink-0 z-20">
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-500/10 text-red-400 border border-red-500/20 animate-pulse">⬤ LIVE</span>
            <div>
                <h1 class="font-bold text-slate-200 text-base leading-none">{{ $meeting->topic }}</h1>
                <span class="text-xs text-slate-500 mt-1 block">{{ $meeting->course->title }}</span>
            </div>
        </div>
        
        <div class="flex items-center gap-4">
            <span x-text="duration" class="text-sm font-mono text-slate-400 bg-slate-800/80 px-3 py-1 rounded-full border border-slate-700">00:00</span>
            <div class="h-8 w-px bg-slate-800"></div>
            <div class="flex items-center gap-2">
                <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center font-bold text-xs">
                    {{ substr(auth()->user()->first_name, 0, 1) }}
                </div>
                <span class="text-xs font-semibold text-slate-300">{{ auth()->user()->first_name }} @if($isHost) (Host/Trainer) @else (Student) @endif</span>
            </div>
        </div>
    </header>

    {{-- ── 2. MAIN WORKSPACE ─────────────────────────────── --}}
    <div class="flex-1 flex overflow-hidden relative">
        
        {{-- Video Gallery Area --}}
        <div id="nandskills-video-area" class="flex-1 p-4 flex flex-col gap-4 overflow-hidden relative bg-slate-950 transition-all duration-300">
            
            {{-- Peer video tiles --}}
            <div id="nandskills-video-container" class="flex-1 grid gap-4 items-center justify-center"
                 :class="peerCount === 0 ? 'grid-cols-1' : peerCount <= 1 ? 'grid-cols-2' : peerCount <= 3 ? 'grid-cols-2' : 'grid-cols-3'">
                
                {{-- Waiting state when alone --}}
                <div x-show="peerCount === 0" class="flex flex-col items-center justify-center text-slate-400 p-8 text-center">
                    <div class="h-16 w-16 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center mb-4 text-2xl text-blue-500 animate-pulse">
                        👥
                    </div>
                    <h3 class="font-bold text-slate-300 text-sm">Waiting for other participants…</h3>
                    <p class="text-xs text-slate-500 mt-1">Students can join using the meeting link in their classrooms.</p>
                </div>

                {{-- Peer streams --}}
                <template x-for="(peer, peerId) in participants" :key="peerId">
                    <div class="relative rounded-2xl overflow-hidden bg-slate-900 border border-slate-800 aspect-video shadow-lg group">
                        <video :id="'nandskills-peer-' + peerId"
                               autoplay playsinline
                               class="w-full h-full object-cover"
                               :class="peer.videoOff ? 'hidden' : ''">
                        </video>
                        {{-- Camera off placeholder --}}
                        <div x-show="peer.videoOff"
                             class="absolute inset-0 flex items-center justify-center bg-slate-900">
                            <div class="h-20 w-20 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-slate-300 font-bold text-3xl uppercase"
                                 x-text="peer.name ? peer.name[0] : '?'">
                            </div>
                        </div>
                        {{-- Hover controls --}}
                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                            <button @click="triggerPip('nandskills-peer-' + peerId)" 
                                    class="p-2 bg-slate-800/90 hover:bg-slate-700 text-white rounded-xl shadow-md transition text-xs flex items-center gap-1">
                                📺 Pop out
                            </button>
                        </div>
                        {{-- Tag --}}
                        <div class="absolute bottom-3 left-3 flex items-center gap-2">
                            <span class="bg-black/60 backdrop-blur-md text-white text-xs font-semibold px-3 py-1 rounded-full border border-white/10"
                                  x-text="peer.name || 'Participant'"></span>
                            <span x-show="peer.muted" class="bg-red-500/80 backdrop-blur-md text-white rounded-full p-1 border border-red-500/20">
                                🔇
                            </span>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Local Preview (PiP styled bottom right corner) --}}
            <div class="absolute bottom-6 right-6 w-48 rounded-2xl overflow-hidden shadow-2xl border-2 border-blue-500 bg-slate-900 aspect-video z-10 group">
                <video id="nandskills-local-video" 
                       autoplay muted playsinline 
                       class="w-full h-full object-cover transition-all"
                       :style="{ filter: selectedFilter }">
                </video>
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <button @click="triggerPip('nandskills-local-video')" 
                            class="p-1.5 bg-slate-800/90 text-white text-[10px] rounded-lg shadow-md transition">
                        📺 Pop out
                    </button>
                </div>
                <div class="absolute bottom-2 left-2">
                    <span class="bg-blue-600/90 backdrop-blur-sm text-white text-[10px] font-bold px-2 py-0.5 rounded-full border border-blue-500/30">You</span>
                </div>
            </div>

        </div>

        {{-- ── 3. RIGHT HAND SIDEBAR (TABS PANEL) ──────────────── --}}
        <aside class="w-96 bg-slate-900 border-l border-slate-800 flex flex-col shrink-0 z-10">
            {{-- Tabs Selection header --}}
            <div class="grid grid-cols-4 border-b border-slate-800 text-xs font-bold text-slate-400 uppercase tracking-wider text-center shrink-0">
                <button @click="activeTab = 'chat'" 
                        :class="activeTab === 'chat' ? 'bg-slate-800 text-blue-400 border-b-2 border-blue-500' : 'hover:bg-slate-850 hover:text-slate-200'"
                        class="py-4 transition">
                    💬 Chat
                </button>
                <button @click="activeTab = 'whiteboard'" 
                        :class="activeTab === 'whiteboard' ? 'bg-slate-800 text-blue-400 border-b-2 border-blue-500' : 'hover:bg-slate-850 hover:text-slate-200'"
                        class="py-4 transition">
                    🎨 Board
                </button>
                <button @click="activeTab = 'notes'" 
                        :class="activeTab === 'notes' ? 'bg-slate-800 text-blue-400 border-b-2 border-blue-500' : 'hover:bg-slate-850 hover:text-slate-200'"
                        class="py-4 transition">
                    📝 Notes
                </button>
                <button @click="activeTab = 'users'" 
                        :class="activeTab === 'users' ? 'bg-slate-800 text-blue-400 border-b-2 border-blue-500' : 'hover:bg-slate-850 hover:text-slate-200'"
                        class="py-4 transition">
                    👥 Users
                </button>
            </div>

            {{-- Tab Contents --}}
            <div class="flex-1 flex flex-col overflow-hidden">
                
                {{-- TAB 1: LIVE CHAT --}}
                <div x-show="activeTab === 'chat'" class="flex-1 flex flex-col overflow-hidden">
                    <div id="nandskills-chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4">
                        <div class="text-center text-slate-600 text-xs my-2">Messages are private & local to this classroom.</div>
                        <template x-for="(msg, i) in chatMessages" :key="i">
                            <div class="flex flex-col" :class="msg.self ? 'items-end' : 'items-start'">
                                <div class="rounded-2xl px-3 py-2 max-w-[85%] text-xs leading-relaxed"
                                     :class="msg.system ? 'bg-slate-800/40 text-slate-500 text-center italic w-full border border-slate-800' : (msg.self ? 'bg-blue-600 text-white rounded-br-none' : 'bg-slate-800 text-slate-300 rounded-bl-none')">
                                    <span x-show="!msg.self && !msg.system" class="block font-bold text-[10px] text-blue-400 mb-1" x-text="msg.name"></span>
                                    <span x-text="msg.text"></span>
                                </div>
                                <span class="text-[9px] text-slate-500 mt-1 px-1 font-mono" x-text="msg.time"></span>
                            </div>
                        </template>
                    </div>
                    <div class="p-3 border-t border-slate-800 flex gap-2 bg-slate-900/60 shrink-0">
                        <input type="text" x-model="chatInput" @keydown.enter="sendChat()"
                               placeholder="Type a message…"
                               class="flex-1 bg-slate-950 border border-slate-800 rounded-xl text-xs px-3.5 py-2.5 text-slate-200 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                        <button @click="sendChat()" 
                                class="px-4 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl transition">
                            Send
                        </button>
                    </div>
                </div>

                {{-- TAB 2: WHITEBOARD --}}
                <div x-show="activeTab === 'whiteboard'" class="flex-1 flex flex-col overflow-hidden p-4 space-y-4">
                    <div class="flex items-center justify-between shrink-0">
                        <h3 class="text-sm font-bold text-slate-300">Shared Whiteboard</h3>
                        <span class="text-[10px] px-2 py-0.5 rounded bg-slate-800 text-slate-400" 
                              x-text="config.isHost ? '🔧 Drawing Mode' : '👁️ View Only'"></span>
                    </div>

                    {{-- Canvas Container --}}
                    <div class="flex-1 bg-white rounded-xl overflow-hidden border border-slate-800 relative cursor-crosshair" style="min-height: 250px;">
                        <canvas id="whiteboard-canvas" 
                                @mousedown="startDrawing($event)" 
                                @mousemove="draw($event)" 
                                @mouseup="stopDrawing()" 
                                @mouseleave="stopDrawing()"
                                class="w-full h-full block">
                        </canvas>
                    </div>

                    {{-- Whiteboard tools (host only) --}}
                    <div x-show="config.isHost" class="p-3 bg-slate-950 rounded-xl border border-slate-800 flex items-center justify-between gap-3 shrink-0">
                        <div class="flex items-center gap-2">
                            {{-- Color pickers --}}
                            <template x-for="c in ['#000000', '#EF4444', '#3B82F6', '#10B981', '#F59E0B']">
                                <button @click="brushColor = c" 
                                        :style="{ backgroundColor: c }"
                                        :class="brushColor === c ? 'ring-2 ring-white scale-110' : ''"
                                        class="h-5 w-5 rounded-full border border-slate-800 transition"></button>
                            </template>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <label class="text-[10px] text-slate-500">Width:</label>
                            <select x-model="brushSize" class="bg-slate-900 border border-slate-800 text-xs rounded px-1.5 py-1 text-slate-300 focus:outline-none">
                                <option value="2">Thin</option>
                                <option value="5">Medium</option>
                                <option value="8">Thick</option>
                            </select>
                            
                            <button @click="clearWhiteboard()" class="px-2.5 py-1 bg-red-950 hover:bg-red-900 border border-red-800 text-red-300 text-[10px] rounded transition font-semibold">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>

                {{-- TAB 3: PERSONAL NOTEBOOK --}}
                <div x-show="activeTab === 'notes'" class="flex-1 flex flex-col overflow-hidden p-4 space-y-4">
                    <div class="flex items-center justify-between shrink-0">
                        <div>
                            <h3 class="text-sm font-bold text-slate-300">My Meeting Notes</h3>
                            <p class="text-[10px] text-slate-500">Saved automatically to your browser.</p>
                        </div>
                        <button @click="downloadNotes()" class="px-3 py-1.5 bg-blue-950 hover:bg-blue-900 border border-blue-800 text-blue-300 text-[10px] rounded-lg transition font-semibold flex items-center gap-1">
                            💾 Download
                        </button>
                    </div>
                    <textarea x-model="notesText" @input="saveNotes()"
                              placeholder="Write notes or class reminders here..."
                              class="flex-1 w-full bg-slate-950 border border-slate-800 rounded-xl text-xs p-3.5 focus:outline-none focus:ring-1 focus:ring-blue-500 text-slate-300 resize-none font-sans leading-relaxed"></textarea>
                </div>

                {{-- TAB 4: PARTICIPANTS --}}
                <div x-show="activeTab === 'users'" class="flex-1 overflow-y-auto p-4 space-y-3">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">
                        Online Now (<span x-text="peerCount + 1"></span>)
                    </h3>
                    
                    {{-- Local --}}
                    <div class="flex items-center justify-between py-2 border-b border-slate-800/40">
                        <div class="flex items-center gap-2.5">
                            <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center font-semibold text-xs text-white">
                                {{ substr(auth()->user()->first_name, 0, 1) }}
                            </div>
                            <div>
                                <span class="text-xs font-bold text-slate-200 block">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</span>
                                <span class="text-[10px] text-slate-500">@if($isHost) Trainer Profile @else Student Profile @endif</span>
                            </div>
                        </div>
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    </div>

                    {{-- Peers --}}
                    <template x-for="(peer, peerId) in participants" :key="peerId">
                        <div class="flex items-center justify-between py-2 border-b border-slate-800/40">
                            <div class="flex items-center gap-2.5">
                                <div class="h-8 w-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-xs uppercase"
                                     x-text="peer.name ? peer.name[0] : '?'">
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-slate-300 block" x-text="peer.name"></span>
                                    <span class="text-[9px] text-slate-500" x-text="peer.muted ? '🔇 Muted' : '🔊 Speaking'"></span>
                                </div>
                            </div>
                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                        </div>
                    </template>
                </div>

            </div>
        </aside>
    </div>

    {{-- ── 4. VIDEO CONTROL BAR ────────────────────────── --}}
    <footer class="h-20 bg-slate-900 border-t border-slate-800 px-6 flex items-center justify-between shrink-0 z-20">
        
        {{-- Left: Filter Selector --}}
        <div class="flex items-center gap-3">
            <label class="text-xs text-slate-400 font-semibold">Video Filter:</label>
            <select x-model="selectedFilter" 
                    class="bg-slate-950 border border-slate-800 text-xs rounded-xl px-3 py-2 text-slate-300 focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option value="none">Normal</option>
                <option value="blur(6px)">Blur Background</option>
                <option value="grayscale(100%)">Grayscale</option>
                <option value="sepia(80%)">Sepia / Retro</option>
                <option value="sepia(30%) saturate(140%)">Warm / Sunlight</option>
                <option value="hue-rotate(180deg) saturate(120%)">Cool / Night</option>
                <option value="saturate(180%) contrast(110%)">Vivid / HDR</option>
            </select>
        </div>

        {{-- Center: Controls --}}
        <div class="flex items-center gap-3.5">
            {{-- Mic --}}
            <button @click="toggleMic()" 
                    :class="micOn ? 'bg-slate-800 hover:bg-slate-700 text-slate-300 border-slate-700' : 'bg-red-500/20 hover:bg-red-500/30 text-red-400 border-red-500/30'"
                    class="h-11 w-11 rounded-xl flex items-center justify-center transition border shadow-sm"
                    title="Mute/Unmute Mic">
                <span x-text="micOn ? '🎙️' : '🔇'"></span>
            </button>

            {{-- Camera --}}
            <button @click="toggleCamera()" 
                    :class="cameraOn ? 'bg-slate-800 hover:bg-slate-700 text-slate-300 border-slate-700' : 'bg-red-500/20 hover:bg-red-500/30 text-red-400 border-red-500/30'"
                    class="h-11 w-11 rounded-xl flex items-center justify-center transition border shadow-sm"
                    title="Toggle Camera">
                <span x-text="cameraOn ? '📹' : '❌'"></span>
            </button>

            {{-- Screen Share --}}
            <button @click="toggleScreen()" 
                    :class="screenSharing ? 'bg-blue-600 text-white border-blue-500' : 'bg-slate-800 hover:bg-slate-700 text-slate-300 border-slate-700'"
                    class="h-11 w-11 rounded-xl flex items-center justify-center transition border shadow-sm"
                    title="Share Screen">
                💻
            </button>

            <div class="h-6 w-px bg-slate-850"></div>

            {{-- Pop Window --}}
            <button @click="openPopup()" 
                    class="h-11 px-4 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 rounded-xl transition shadow-sm text-xs font-semibold flex items-center gap-1.5"
                    title="Popout to small floating window">
                🪟 Pop out window
            </button>

            {{-- Fullscreen --}}
            <button @click="toggleFullscreen()" 
                    class="h-11 w-11 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 rounded-xl transition shadow-sm flex items-center justify-center"
                    title="Toggle Fullscreen">
                🖥️
            </button>
        </div>

        {{-- Right: Leave / End --}}
        <div>
            @if($isHost)
                <button @click="endRoom()" 
                        class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                    End Session for All
                </button>
            @else
                <button @click="leaveRoom()" 
                        class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 rounded-xl text-xs font-bold transition shadow-sm">
                    Leave Class
                </button>
            @endif
        </div>

    </footer>

    {{-- ── Alpine / WebRTC Shared Logic ─────────────────────── --}}
    <script>
    function nandskillsMeetingManager(config) {
        return {
            config: config,
            room: null,
            participants: {},
            peerCount: 0,
            micOn: true,
            cameraOn: true,
            screenSharing: false,
            chatMessages: [],
            chatInput: '',
            duration: '00:00',
            _timer: null,
            _elapsed: 0,

            // Sidebar and filters
            activeTab: 'chat',
            selectedFilter: 'none',
            notesText: '',

            // Whiteboard Canvas states
            canvas: null,
            ctx: null,
            drawing: false,
            brushColor: '#000000',
            brushSize: 5,
            lastX: 0,
            lastY: 0,

            initRoom() {
                const self = this;
                
                // Initialize notes
                this.notesText = localStorage.getItem('nandskills_notes_' + config.roomId) || '';

                // Initialize NandskillsRoom object
                this.room = new window.NandskillsRoom(config);

                this.room.onParticipantUpdate = (peers) => {
                    self.participants = peers;
                    self.peerCount = Object.keys(peers).length;
                };

                this.room.onChatMessage = (msg) => {
                    self.chatMessages.push(msg);
                    self.$nextTick(() => {
                        const el = document.getElementById('nandskills-chat-messages');
                        if (el) el.scrollTop = el.scrollHeight;
                    });
                };

                this.room.onRoomEnded = () => {
                    alert('Instructor has completed the virtual session.');
                    self.leaveRoom();
                };

                // Listen for custom signals: Whiteboard Drawing
                this.room.onCustomSignal = (type, payload, from) => {
                    if (type === 'whiteboard-draw') {
                        self.drawRemoteLine(payload);
                    } else if (type === 'whiteboard-clear') {
                        self.clearLocalCanvas();
                    }
                };

                this.room.join();

                // Start timer
                this._timer = setInterval(() => {
                    self._elapsed++;
                    const m = String(Math.floor(self._elapsed / 60)).padStart(2, '0');
                    const s = String(self._elapsed % 60).padStart(2, '0');
                    self.duration = m + ':' + s;
                }, 1000);

                // Init canvas drawing contexts
                this.$nextTick(() => {
                    self.initWhiteboard();
                });
            },

            // ── WebRTC Controls ──
            toggleMic() {
                this.micOn = this.room.toggleMic();
            },

            toggleCamera() {
                this.cameraEnabled = this.room.toggleCamera();
                this.cameraOn = this.cameraEnabled;
            },

            toggleScreen() {
                if (this.screenSharing) {
                    this.room.stopScreenShare();
                    this.screenSharing = false;
                } else {
                    this.room.startScreenShare().then(() => {
                        this.screenSharing = true;
                    });
                }
            },

            sendChat() {
                if (!this.chatInput.trim()) return;
                this.room.sendChat(this.chatInput.trim());
                this.chatInput = '';
            },

            leaveRoom() {
                if (this.room) this.room.leave();
                clearInterval(this._timer);
                @this.leaveMeeting();
            },

            endRoom() {
                if (confirm('End this classroom session for everyone?')) {
                    if (this.room) this.room.endRoom();
                    clearInterval(this._timer);
                    @this.endMeeting();
                }
            },

            // ── Video Controls ──
            triggerPip(elementId) {
                const vid = document.getElementById(elementId);
                if (vid && document.pictureInPictureEnabled) {
                    if (document.pictureInPictureElement) {
                        document.exitPictureInPicture();
                    } else {
                        vid.requestPictureInPicture().catch(e => console.warn(e));
                    }
                } else {
                    alert('Picture in Picture is not supported on this browser or stream.');
                }
            },

            toggleFullscreen() {
                const container = document.getElementById('nandskills-video-area');
                if (container) {
                    if (!document.fullscreenElement) {
                        container.requestFullscreen().catch(e => console.warn(e));
                    } else {
                        document.exitFullscreen();
                    }
                }
            },

            openPopup() {
                window.open(window.location.href, '_blank', 'width=1000,height=700,status=no,menubar=no,toolbar=no,resizable=yes');
            },

            // ── Notebook Notes ──
            saveNotes() {
                localStorage.setItem('nandskills_notes_' + config.roomId, this.notesText);
            },

            downloadNotes() {
                const blob = new Blob([this.notesText], { type: 'text/plain;charset=utf-8' });
                const link = document.createElement('a');
                link.href = URL.createObjectURL(blob);
                link.download = `notes_${config.roomId}_${new Date().toISOString().slice(0,10)}.txt`;
                link.click();
            },

            // ── Whiteboard drawing ──
            initWhiteboard() {
                this.canvas = document.getElementById('whiteboard-canvas');
                if (this.canvas) {
                    this.ctx = this.canvas.getContext('2d');
                    // Resize canvas to match bounds
                    this.canvas.width = this.canvas.parentElement.clientWidth;
                    this.canvas.height = this.canvas.parentElement.clientHeight;
                    
                    // Clear state
                    this.ctx.fillStyle = "#ffffff";
                    this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
                }
            },

            startDrawing(e) {
                if (!config.isHost) return; // Only teacher can draw
                this.drawing = true;
                const rect = this.canvas.getBoundingClientRect();
                this.lastX = e.clientX - rect.left;
                this.lastY = e.clientY - rect.top;
            },

            draw(e) {
                if (!this.drawing || !config.isHost) return;
                
                const rect = this.canvas.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                // Draw local
                this.ctx.beginPath();
                this.ctx.moveTo(this.lastX, this.lastY);
                this.ctx.lineTo(x, y);
                this.ctx.strokeStyle = this.brushColor;
                this.ctx.lineWidth = this.brushSize;
                this.ctx.lineCap = 'round';
                this.ctx.stroke();

                // Send drawing coordinates normalized as percentages so screen sizes don't distort drawing
                const pctPayload = {
                    x: x / this.canvas.width,
                    y: y / this.canvas.height,
                    lastX: this.lastX / this.canvas.width,
                    lastY: this.lastY / this.canvas.height,
                    color: this.brushColor,
                    size: this.brushSize
                };

                this.room._sendSignal('whiteboard-draw', pctPayload);

                this.lastX = x;
                this.lastY = y;
            },

            stopDrawing() {
                this.drawing = false;
            },

            drawRemoteLine(payload) {
                if (!this.canvas || !this.ctx) return;
                
                // Map percentages back to local scale
                const x = payload.x * this.canvas.width;
                const y = payload.y * this.canvas.height;
                const lx = payload.lastX * this.canvas.width;
                const ly = payload.lastY * this.canvas.height;

                this.ctx.beginPath();
                this.ctx.moveTo(lx, ly);
                this.ctx.lineTo(x, y);
                this.ctx.strokeStyle = payload.color;
                this.ctx.lineWidth = payload.size;
                this.ctx.lineCap = 'round';
                this.ctx.stroke();
            },

            clearWhiteboard() {
                if (!config.isHost) return;
                this.clearLocalCanvas();
                this.room._sendSignal('whiteboard-clear', {});
            },

            clearLocalCanvas() {
                if (this.canvas && this.ctx) {
                    this.ctx.fillStyle = "#ffffff";
                    this.ctx.fillRect(0, 0, this.canvas.width, this.canvas.height);
                }
            }
        };
    }
    </script>
</div>
