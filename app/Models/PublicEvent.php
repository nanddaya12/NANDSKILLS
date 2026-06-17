<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PublicEvent extends Model
{
    use HasUuids;

    protected $table = 'public_events';

    protected $fillable = [
        'tenant_id',
        'title',
        'description',
        'event_type',
        'start_datetime',
        'end_datetime',
        'venue',
        'featured_image',
        'is_public',
        'status',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime'   => 'datetime',
        'is_public'      => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
