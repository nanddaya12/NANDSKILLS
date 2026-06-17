<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class DocumentCategory extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'document_categories';

    protected $fillable = [
        'tenant_id',
        'parent_id',
        'name',
        'icon',
        'description',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(DocumentCategory::class, 'parent_id')->orderBy('sort_order');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ManagedDocument::class, 'category_id');
    }
}
