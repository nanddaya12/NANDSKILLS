<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class NotificationCategory extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'notification_categories';

    protected $fillable = [
        'tenant_id',
        'name',
        'icon',
        'color',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function templates(): HasMany
    {
        return $this->hasMany(NotificationTemplate::class, 'category_id');
    }
}
