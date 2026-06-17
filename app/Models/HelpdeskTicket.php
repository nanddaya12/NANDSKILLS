<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class HelpdeskTicket extends Model
{
    use HasUuids, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'requester_id',
        'assignee_id',
        'subject',
        'description',
        'status', // OPEN, IN_PROGRESS, RESOLVED, CLOSED
        'priority', // LOW, MEDIUM, HIGH, URGENT
        'category_id',
        'sla_deadline',
        'escalated',
    ];

    protected $casts = [
        'sla_deadline' => 'datetime',
        'escalated' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id')->orderBy('created_at', 'asc');
    }
}
