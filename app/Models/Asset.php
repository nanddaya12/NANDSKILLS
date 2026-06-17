<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class Asset extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'assets';

    protected $fillable = [
        'tenant_id',
        'name',
        'serial_number',
        'status',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
