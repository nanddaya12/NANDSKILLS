<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class AdmissionForm extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $fillable = [
        'tenant_id',
        'program_id',
        'title',
        'description',
        'fields_config',
        'is_active',
        'application_open_date',
        'application_close_date',
        'application_fee',
    ];

    protected $casts = [
        'fields_config' => 'array',
        'is_active' => 'boolean',
        'application_open_date' => 'date',
        'application_close_date' => 'date',
        'application_fee' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function admissions(): HasMany
    {
        return $this->hasMany(Admission::class);
    }
}
