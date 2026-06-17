<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class AdmissionOffer extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'admission_offers';

    protected $fillable = [
        'tenant_id',
        'admission_id',
        'offer_letter_text',
        'sent_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }
}
