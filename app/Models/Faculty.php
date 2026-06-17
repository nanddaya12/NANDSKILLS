<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class Faculty extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'faculties';

    protected $fillable = [
        'tenant_id',
        'head_user_id',
        'name',
        'code',
        'description',
        'status',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }
}
