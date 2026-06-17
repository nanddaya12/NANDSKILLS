<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class ExamResult extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'exam_results';

    protected $fillable = [
        'tenant_id',
        'exam_id',
        'user_id',
        'marks_obtained',
        'status',
        'grade',
        'gpa',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
