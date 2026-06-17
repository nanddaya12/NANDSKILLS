<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class CgpaRule extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'cgpa_rules';

    protected $fillable = [
        'tenant_id',
        'program_id',
        'standing_name',
        'min_cgpa',
        'max_cgpa',
        'status_tag',
        'description',
    ];

    protected $casts = [
        'min_cgpa' => 'decimal:2',
        'max_cgpa' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }
}
