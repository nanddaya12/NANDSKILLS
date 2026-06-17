<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class AdmissionCycle extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'admission_cycles';

    protected $fillable = [
        'tenant_id',
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(AdmissionCampaign::class, 'cycle_id');
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class, 'cycle_id');
    }
}
