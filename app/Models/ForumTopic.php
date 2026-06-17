<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class ForumTopic extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'forum_topics';

    protected $fillable = [
        'tenant_id',
        'category_id',
        'user_id',
        'title',
        'content',
        'is_pinned',
        'is_locked',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ForumCategory::class, 'category_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
