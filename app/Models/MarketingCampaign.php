<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class MarketingCampaign extends Model
{
    use HasUuids, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'name',
        'type', // LANDING, POPUP, BANNER, NEWSLETTER
        'content',
        'settings',
        'is_published',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'content' => 'array',
        'settings' => 'array',
        'is_published' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
