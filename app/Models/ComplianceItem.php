<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class ComplianceItem extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'compliance_items';

    protected $fillable = [
        'tenant_id',
        'accreditation_id',
        'name',
        'description',
        'is_met',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function accreditation(): BelongsTo
    {
        return $this->belongsTo(Accreditation::class);
    }

}
