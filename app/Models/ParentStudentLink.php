<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class ParentStudentLink extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'parent_student_links';

    protected $fillable = [
        'tenant_id',
        'parent_user_id',
        'student_user_id',
        'relationship',
        'is_primary',
        'can_view_fees',
        'can_view_attendance',
        'can_view_grades',
        'can_message_teachers',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'can_view_fees' => 'boolean',
        'can_view_attendance' => 'boolean',
        'can_view_grades' => 'boolean',
        'can_message_teachers' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }
}
