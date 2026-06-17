<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class ForumPost extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'forum_posts';

    protected $fillable = [
        'tenant_id',
        'topic_id',
        'user_id',
        'content',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ForumTopic::class, 'topic_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
