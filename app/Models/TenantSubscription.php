<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class TenantSubscription extends Model
{
    use HasUuids;

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'status',
        'start_date',
        'end_date',
        'stripe_subscription_id',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(TenantPlan::class);
    }
}
