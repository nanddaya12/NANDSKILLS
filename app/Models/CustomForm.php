<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class CustomForm extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'custom_forms';

    protected $fillable = [
        'tenant_id',
        'title',
        'description',
        'is_public',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

}
