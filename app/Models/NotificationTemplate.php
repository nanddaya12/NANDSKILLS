<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class NotificationTemplate extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'notification_templates';

    protected $fillable = [
        'tenant_id',
        'category_id',
        'name',
        'subject',
        'body',
        'body_html',
        'body_sms',
        'channel',
        'audience',
        'audience_filter',
        'is_active',
    ];

    protected $casts = [
        'audience_filter' => 'array',
        'is_active' => 'boolean',
    ];


    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

}
