<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class Exam extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'exams';

    protected $fillable = [
        'tenant_id',
        'exam_session_id',
        'course_id',
        'title',
        'type',
        'total_marks',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function examSession(): BelongsTo
    {
        return $this->belongsTo(ExamSession::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

}
