<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class AuditRecord extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'audit_records';

    protected $fillable = [
        'tenant_id',
        'accreditation_id',
        'auditor_name',
        'audit_date',
        'status',
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
