<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TenantPlan extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'price',
        'billing_interval',
        'max_users',
        'max_courses',
        'max_storage_bytes',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
    ];

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'plan_id');
    }
}
