<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class Attendance extends Model
{
    use HasUuids, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'course_id',
        'date',
        'status', // PRESENT, ABSENT, LATE, EXCUSED
        'user_id',
        'marked_by',
        'marked_by_id',
        'qr_code',
        'ip_address',
        'entry_mode',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function marker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by_id');
    }

    /**
     * Compute attendance percentage for a given user (PRESENT + LATE count as attended).
     */
    public static function studentAttendancePercent(?string $userId): float
    {
        if (!$userId) return 0.0;

        $total   = static::where('user_id', $userId)->count();
        $present = static::where('user_id', $userId)
            ->whereIn('status', ['PRESENT', 'LATE'])
            ->count();

        return $total > 0 ? round(($present / $total) * 100, 1) : 0.0;
    }
}
