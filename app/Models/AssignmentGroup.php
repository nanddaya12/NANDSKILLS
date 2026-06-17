<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class AssignmentGroup extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'assignment_groups';

    protected $fillable = [
        'tenant_id',
        'course_id',
        'name',
        'weight',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

}
