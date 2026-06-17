/**
 * NANDSKILLS Custom WebRTC Conference Room Engine
 * No external APIs — pure browser RTCPeerConnection + Laravel Echo signaling
 * Supports: camera, mic, screen share, in-room chat, up to 8 peers (mesh)
 */

const ICE_SERVERS = [
    { urls: 'stun:stun.l.google.com:19302' },
    { urls: 'stun:stun1.l.google.com:19302' },
    { urls: 'stun:stun2.l.google.com:19302' },
];

class NandskillsRoom {
    constructor({ roomId, meetingId, userId, userName, isHost, csrfToken, signalUrl }) {
        this.roomId      = roomId;
        this.meetingId   = meetingId;
        this.userId      = String(userId);
        this.userName    = userName;
        this.isHost      = isHost;
        this.csrfToken   = csrfToken;
        this.signalUrl   = signalUrl; // /meeting/{roomId}/signal

        this.localStream    = null;
        this.screenStream   = null;
        this.peers          = {};        // peerId => RTCPeerConnection
        this.peerStreams     = {};        // peerId => MediaStream
        this.participants    = {};        // peerId => { name, muted, videoOff }

        this.micEnabled     = true;
        this.cameraEnabled  = true;
        this.screenSharing  = false;

        this.onParticipantUpdate = null; // callback(participants)
        this.onChatMessage       = null; // callback({from, name, text, time})
        this.onRoomEnded         = null; // callback()

        this._channel = null;
    }

    /* ─── PUBLIC API ──────────────────────────────────── */

    async join() {
        // 1. Get local media
        try {
            this.localStream = await navigator.mediaDevices.getUserMedia({
                audio: true,
                video: { width: { ideal: 1280 }, height: { ideal: 720 }, facingMode: 'user' }
            });
        } catch (e) {
            console.warn('[NANDSKILLS] Camera/mic denied, joining audio-only:', e);
            this.localStream = await navigator.mediaDevices.getUserMedia({ audio: true, video: false });
            this.cameraEnabled = false;
        }

        // Attach local preview
        const localVideo = document.getElementById('nandskills-local-video');
        if (localVideo) {
            localVideo.srcObject = this.localStream;
            localVideo.muted = true;
        }

        // 2. Subscribe to presence channel for signaling
        this._channel = window.Echo.join(`meeting.${this.roomId}`)
            .here((users) => {
                users.forEach(u => {
                    if (String(u.id) !== this.userId) {
                        this._initPeer(String(u.id), u.name, true);
                    }
                });
                this._notifyParticipants();
            })
            .joining((user) => {
                const pid = String(user.id);
                if (pid !== this.userId) {
                    this.participants[pid] = { name: user.name, muted: false, videoOff: false };
                    this._initPeer(pid, user.name, false);
                    this._notifyParticipants();
                }
            })
            .leaving((user) => {
                const pid = String(user.id);
                this._removePeer(pid);
                delete this.participants[pid];
                this._notifyParticipants();
                if (this.onChatMessage) {
                    this.onChatMessage({ from: pid, name: user.name, text: '👋 left the room.', time: new Date().toLocaleTimeString(), system: true });
                }
            })
            .listenForWhisper('signal', (data) => this._handleSignal(data))
            .listen('.signal', (data) => this._handleSignal(data));

        // Announce join
        await this._sendSignal('joined', { name: this.userName });

        this._notifyParticipants();
    }

    toggleMic() {
        this.micEnabled = !this.micEnabled;
        this.localStream.getAudioTracks().forEach(t => t.enabled = this.micEnabled);
        this._sendSignal('mute-state', { muted: !this.micEnabled });
        return this.micEnabled;
    }

    toggleCamera() {
        this.cameraEnabled = !this.cameraEnabled;
        this.localStream.getVideoTracks().forEach(t => t.enabled = this.cameraEnabled);
        this._sendSignal('video-state', { videoOff: !this.cameraEnabled });
        return this.cameraEnabled;
    }

    async startScreenShare() {
        if (this.screenSharing) return;
        try {
            this.screenStream = await navigator.mediaDevices.getDisplayMedia({ video: true, audio: true });
            const screenTrack = this.screenStream.getVideoTracks()[0];

            // Replace video track in all peer connections
            Object.values(this.peers).forEach(pc => {
                const sender = pc.getSenders().find(s => s.track && s.track.kind === 'video');
                if (sender) sender.replaceTrack(screenTrack);
            });

            const localVideo = document.getElementById('nandskills-local-video');
            if (localVideo) localVideo.srcObject = this.screenStream;

            this.screenSharing = true;
            screenTrack.onended = () => this.stopScreenShare();
        } catch (e) {
            console.warn('[NANDSKILLS] Screen share cancelled:', e);
        }
    }

    async stopScreenShare() {
        if (!this.screenSharing) return;
        this.screenSharing = false;
        const camTrack = this.localStream.getVideoTracks()[0];

        Object.values(this.peers).forEach(pc => {
            const sender = pc.getSenders().find(s => s.track && s.track.kind === 'video');
            if (sender && camTrack) sender.replaceTrack(camTrack);
        });

        if (this.screenStream) {
            this.screenStream.getTracks().forEach(t => t.stop());
            this.screenStream = null;
        }
        const localVideo = document.getElementById('nandskills-local-video');
        if (localVideo) localVideo.srcObject = this.localStream;
    }

    sendChat(text) {
        if (!text.trim()) return;
        this._sendSignal('chat', { text: text.trim(), name: this.userName, time: new Date().toLocaleTimeString() });
        if (this.onChatMessage) {
            this.onChatMessage({ from: this.userId, name: 'You', text: text.trim(), time: new Date().toLocaleTimeString(), self: true });
        }
    }

    async leave() {
        await this._sendSignal('left', { name: this.userName });
        this._cleanup();
    }

    async endRoom() {
        await this._sendSignal('room-ended', {});
        this._cleanup();
        if (this.onRoomEnded) this.onRoomEnded();
    }

    /* ─── PEER CONNECTION ─────────────────────────────── */

    _initPeer(peerId, peerName, isInitiator) {
        if (this.peers[peerId]) return;

        this.participants[peerId] = this.participants[peerId] || { name: peerName, muted: false, videoOff: false };

        const pc = new RTCPeerConnection({ iceServers: ICE_SERVERS });
        this.peers[peerId] = pc;

        // Add local tracks
        this.localStream.getTracks().forEach(track => {
            pc.addTrack(track, this.localStream);
        });

        // Receive remote stream
        this.peerStreams[peerId] = new MediaStream();
        pc.ontrack = (event) => {
            event.streams[0].getTracks().forEach(track => {
                this.peerStreams[peerId].addTrack(track);
            });
            const vid = document.getElementById(`nandskills-peer-${peerId}`);
            if (vid) vid.srcObject = this.peerStreams[peerId];
        };

        // ICE candidates
        pc.onicecandidate = (event) => {
            if (event.candidate) {
                this._sendSignal('ice-candidate', { candidate: event.candidate }, peerId);
            }
        };

        pc.onconnectionstatechange = () => {
            if (['disconnected', 'failed', 'closed'].includes(pc.connectionState)) {
                this._removePeer(peerId);
            }
        };

        if (isInitiator) {
            pc.onnegotiationneeded = async () => {
                try {
                    const offer = await pc.createOffer();
                    await pc.setLocalDescription(offer);
                    this._sendSignal('offer', { sdp: pc.localDescription }, peerId);
                } catch (e) {
                    console.error('[NANDSKILLS] Offer error:', e);
                }
            };
        }
    }

    async _handleSignal(data) {
        const { type, from, to, payload } = data;

        // Ignore own signals and signals not addressed to us (if targeted)
        if (String(from) === this.userId) return;
        if (to && String(to) !== this.userId) return;

        switch (type) {
            case 'offer':
                await this._handleOffer(String(from), payload.sdp);
                break;
            case 'answer':
                await this._handleAnswer(String(from), payload.sdp);
                break;
            case 'ice-candidate':
                await this._handleIce(String(from), payload.candidate);
                break;
            case 'chat':
                if (this.onChatMessage) {
                    this.onChatMessage({ from, name: payload.name, text: payload.text, time: payload.time });
                }
                break;
            case 'mute-state':
                if (this.participants[String(from)]) {
                    this.participants[String(from)].muted = payload.muted;
                    this._notifyParticipants();
                }
                break;
            case 'video-state':
                if (this.participants[String(from)]) {
                    this.participants[String(from)].videoOff = payload.videoOff;
                    this._notifyParticipants();
                }
                break;
            case 'room-ended':
                this._cleanup();
                if (this.onRoomEnded) this.onRoomEnded();
                break;
            case 'joined':
                if (!this.participants[String(from)]) {
                    this.participants[String(from)] = { name: payload.name, muted: false, videoOff: false };
                    this._initPeer(String(from), payload.name, false);
                    this._notifyParticipants();
                }
                break;
            default:
                if (this.onCustomSignal) {
                    this.onCustomSignal(type, payload, from);
                }
                break;
        }
    }

    async _handleOffer(peerId, sdp) {
        if (!this.peers[peerId]) this._initPeer(peerId, this.participants[peerId]?.name || 'Participant', false);
        const pc = this.peers[peerId];
        await pc.setRemoteDescription(new RTCSessionDescription(sdp));
        const answer = await pc.createAnswer();
        await pc.setLocalDescription(answer);
        this._sendSignal('answer', { sdp: pc.localDescription }, peerId);
    }

    async _handleAnswer(peerId, sdp) {
        const pc = this.peers[peerId];
        if (pc) await pc.setRemoteDescription(new RTCSessionDescription(sdp));
    }

    async _handleIce(peerId, candidate) {
        const pc = this.peers[peerId];
        if (pc && candidate) {
            try { await pc.addIceCandidate(new RTCIceCandidate(candidate)); } catch (e) {}
        }
    }

    _removePeer(peerId) {
        if (this.peers[peerId]) {
            this.peers[peerId].close();
            delete this.peers[peerId];
        }
        delete this.peerStreams[peerId];
        const vid = document.getElementById(`nandskills-peer-${peerId}`);
        if (vid) vid.srcObject = null;
        this._notifyParticipants();
    }

    /* ─── SIGNALING ───────────────────────────────────── */

    async _sendSignal(type, payload, toUserId = null) {
        try {
            await fetch(this.signalUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ type, payload, to: toUserId }),
            });
        } catch (e) {
            console.warn('[NANDSKILLS] Signal send failed:', e);
        }
    }

    /* ─── HELPERS ─────────────────────────────────────── */

    _notifyParticipants() {
        if (this.onParticipantUpdate) this.onParticipantUpdate({ ...this.participants });
    }

    _cleanup() {
        Object.keys(this.peers).forEach(pid => this._removePeer(pid));
        if (this.localStream) this.localStream.getTracks().forEach(t => t.stop());
        if (this.screenStream) this.screenStream.getTracks().forEach(t => t.stop());
        if (this._channel) window.Echo.leave(`meeting.${this.roomId}`);
        this.localStream = null;
        this.screenStream = null;
        this._channel = null;
    }
}

window.NandskillsRoom = NandskillsRoom;
export default NandskillsRoom;
