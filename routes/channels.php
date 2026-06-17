<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\VirtualClassroom;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
| Presence channel authorization for the custom WebRTC conferencing system.
| Returns user data that is shared with all other channel members.
*/

Broadcast::channel('meeting.{roomId}', function ($user, $roomId) {
    // Only allow authenticated users whose meeting is LIVE or UPCOMING
    $meeting = VirtualClassroom::where('meeting_id', $roomId)
        ->whereIn('status', ['LIVE', 'UPCOMING'])
        ->first();

    if (!$meeting) {
        return false;
    }

    // Return user data visible to other participants in the presence channel
    return [
        'id'   => $user->id,
        'name' => trim($user->first_name . ' ' . $user->last_name),
    ];
});
