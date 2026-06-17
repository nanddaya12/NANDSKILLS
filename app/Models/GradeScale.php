<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class GradeScale extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'grade_scales';

    protected $fillable = [
        'tenant_id',
        'grade',
        'min_score',
        'max_score',
        'gpa',
        'description',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

}
