<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class Rubric extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'rubrics';

    protected $fillable = [
        'tenant_id',
        'assignment_id',
        'criteria',
        'max_points',
        'description',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

}
