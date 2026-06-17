<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class Accreditation extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'accreditations';

    protected $fillable = [
        'tenant_id',
        'framework_name',
        'agency',
        'status',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

}
