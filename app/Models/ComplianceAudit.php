<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class ComplianceAudit extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'compliance_audits';

    protected $fillable = [
        'tenant_id',
        'framework_id',
        'audit_date',
        'auditor_name',
        'audit_type',
        'result',
        'findings',
        'report_path',
    ];

    protected $casts = [
        'audit_date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function framework(): BelongsTo
    {
        return $this->belongsTo(ComplianceFramework::class, 'framework_id');
    }
}
