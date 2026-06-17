<?php

namespace App\Traits;

use App\Scopes\TenantScope;

trait HasTenantScope
{
    protected static function bootHasTenantScope(): void
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (app()->bound('currentTenant')) {
                $model->tenant_id = $model->tenant_id ?? app('currentTenant')->id;
            }
        });
    }
}
