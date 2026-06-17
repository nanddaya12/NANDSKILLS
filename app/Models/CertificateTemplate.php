<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class CertificateTemplate extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'certificate_templates';

    protected $fillable = [
        'tenant_id',
        'name',
        'content_html',
        'is_active',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

}
