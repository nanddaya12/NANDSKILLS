<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class ExamSchedule extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'exam_schedules';

    protected $fillable = [
        'tenant_id',
        'exam_id',
        'room_id',
        'date_time',
        'duration_minutes',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

}
