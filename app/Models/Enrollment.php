<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class Enrollment extends Model
{
    use HasUuids, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'course_id',
        'progress_percent',
        'status', // ACTIVE, COMPLETED, DROPPED
        'enrolled_at',
        'completed_at',
    ];

    protected $casts = [
        'progress_percent' => 'integer',
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public $timestamps = false; // Custom dates are handled manually

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}
