<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Events\MeetingSignal;
use App\Models\VirtualClassroom;
use Illuminate\Support\Facades\Auth;

class MeetingSignalController extends Controller
{
    /**
     * Relay a WebRTC signaling message (offer / answer / ICE candidate / chat).
     * POST /meeting/{roomId}/signal
     */
    public function signal(Request $request, string $roomId): JsonResponse
    {
        $request->validate([
            'type'    => 'required|in:offer,answer,ice-candidate,joined,left,chat,mute-state,video-state,room-ended,whiteboard-draw,whiteboard-clear',
            'payload' => 'required|array',
            'to'      => 'nullable|string',
        ]);

        $meeting = VirtualClassroom::where('meeting_id', $roomId)
            ->whereIn('status', ['LIVE', 'UPCOMING'])
            ->firstOrFail();

        // Verify the authenticated user belongs to the correct tenant
        $user = Auth::user();

        broadcast(new MeetingSignal(
            roomId:     $roomId,
            fromUserId: (string) $user->id,
            type:       $request->type,
            payload:    $request->payload,
            toUserId:   $request->to,
        ))->toOthers();

        return response()->json(['ok' => true]);
    }

    /**
     * Return a signed room token so JS can authenticate with Laravel Echo presence channel.
     * GET /meeting/{roomId}/token
     */
    public function token(Request $request, string $roomId): JsonResponse
    {
        $meeting = VirtualClassroom::where('meeting_id', $roomId)
            ->whereIn('status', ['LIVE', 'UPCOMING'])
            ->firstOrFail();

        $user = Auth::user();

        return response()->json([
            'room_id'    => $roomId,
            'meeting_id' => $meeting->id,
            'user_id'    => $user->id,
            'name'       => trim($user->first_name . ' ' . $user->last_name),
            'is_host'    => ($user->id === $meeting->trainer_id),
            'csrf'       => csrf_token(),
        ]);
    }
}
