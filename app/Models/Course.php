<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class Course extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $fillable = [
        'tenant_id',
        'title',
        'slug',
        'description',
        'short_description',
        'cover_image_url',
        'status',
        'language',
        'price',
        'version',
        'parent_id',
        'is_bundle',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_bundle' => 'boolean',
        'version' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'parent_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(Course::class, 'parent_id');
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class)->orderBy('order_index');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'course_categories', 'course_id', 'category_id');
    }

    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_prerequisites', 'course_id', 'prerequisite_id');
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
}
