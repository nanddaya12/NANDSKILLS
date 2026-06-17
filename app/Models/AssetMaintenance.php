<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class AssetMaintenance extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'asset_maintenance';

    protected $fillable = [
        'tenant_id',
        'asset_id',
        'description',
        'cost',
        'status',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

}
