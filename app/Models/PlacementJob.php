<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class PlacementJob extends Model
{
    use HasUuids, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'employer_profile_id',
        'title',
        'description',
        'requirements',
        'location',
        'type',
        'salary_range',
        'status',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employer(): BelongsTo
    {
        return $this->belongsTo(EmployerProfile::class, 'employer_profile_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(PlacementApplication::class);
    }
}
