<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class Branch extends Model
{
    use HasUuids, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'name',
        'address',
        'contact_email',
        'contact_phone',
        'status',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'branch_admins', 'branch_id', 'user_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
