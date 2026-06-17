<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TimetableEntry extends Model
{
    use HasUuids;

    protected $table = 'timetable_entries';

    protected $fillable = [
        'timetable_id',
        'slot_id',
        'subject_id',
        'room_id',
        'teacher_user_id',
        'day_of_week',
        'entry_date_override',
        'entry_type',
        'notes',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'entry_date_override' => 'date',
    ];

    public function timetable(): BelongsTo
    {
        return $this->belongsTo(Timetable::class);
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(TimetableSlot::class, 'slot_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_user_id');
    }
}
