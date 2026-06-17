<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class ComplianceFramework extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'compliance_frameworks';

    protected $fillable = [
        'tenant_id',
        'name',
        'agency',
        'standard_version',
        'description',
        'status',
        'valid_from',
        'valid_until',
        'completion_percentage',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'completion_percentage' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(ComplianceRequirement::class, 'framework_id');
    }
}
