<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class ReportTemplate extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'report_templates';

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'configuration',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

}
