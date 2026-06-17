<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class Badge extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'badges';

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'icon_url',
        'xp_required',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

}
