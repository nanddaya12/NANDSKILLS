<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Role extends Model
{
    use HasUuids;

    protected $fillable = [
        'tenant_id',
        'name',
        'description',
        'is_system',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('tenant_or_system', new class implements Scope {
            public function apply(Builder $builder, Model $model): void
            {
                if (app()->bound('currentTenant')) {
                    $builder->where(function ($query) use ($model) {
                        $query->whereNull($model->getTable() . '.tenant_id')
                              ->orWhere($model->getTable() . '.tenant_id', app('currentTenant')->id);
                    });
                } else {
                    $builder->whereNull($model->getTable() . '.tenant_id');
                }
            }
        });

        static::creating(function ($model) {
            if (app()->bound('currentTenant') && !$model->is_system) {
                $model->tenant_id = $model->tenant_id ?? app('currentTenant')->id;
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles', 'role_id', 'user_id')
            ->using(UserRole::class);
    }
}
