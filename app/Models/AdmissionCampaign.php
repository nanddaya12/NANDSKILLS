<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class AdmissionCampaign extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'admission_campaigns';

    protected $fillable = [
        'tenant_id',
        'cycle_id',
        'name',
        'budget',
        'target_enrollments',
        'status',
    ];

    protected $casts = [
        'budget' => 'decimal:2',
        'target_enrollments' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(AdmissionCycle::class, 'cycle_id');
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class, 'campaign_id');
    }
}
