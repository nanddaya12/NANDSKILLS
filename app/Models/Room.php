<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class Room extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'rooms';

    protected $fillable = [
        'tenant_id',
        'building_id',
        'name',
        'capacity',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

}
