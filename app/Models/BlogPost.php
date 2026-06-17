<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class BlogPost extends Model
{
    use HasUuids, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'title',
        'slug',
        'content',
        'summary',
        'featured_image',
        'status', // DRAFT, PUBLISHED, ARCHIVED
        'published_at',
        'author_id',
        'view_count',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'view_count' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(BlogCategory::class, 'blog_post_categories', 'blog_post_id', 'blog_category_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tags', 'blog_post_id', 'blog_tag_id');
    }
}
