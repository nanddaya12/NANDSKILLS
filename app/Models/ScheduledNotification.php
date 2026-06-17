<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class ScheduledNotification extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'scheduled_notifications';

    protected $fillable = [
        'tenant_id',
        'template_id',
        'subject',
        'body',
        'channel',
        'audience',
        'audience_filter',
        'scheduled_at',
        'is_recurring',
        'cron_expression',
        'status',
        'created_by',
    ];

    protected $casts = [
        'audience_filter' => 'array',
        'scheduled_at' => 'datetime',
        'is_recurring' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class, 'template_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
