<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class CertificateVerification extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'certificate_verifications';

    protected $fillable = [
        'tenant_id',
        'certificate_id',
        'verified_at',
        'ip_address',
        'browser',
        'status',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function certificate(): BelongsTo
    {
        return $this->belongsTo(Certificate::class);
    }

}
