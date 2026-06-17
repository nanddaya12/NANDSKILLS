<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class FormBuilder extends Model
{
    use HasUuids, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'fields',
        'settings',
        'is_published',
    ];

    protected $casts = [
        'fields' => 'array',
        'settings' => 'array',
        'is_published' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(FormSubmission::class, 'form_id');
    }
}
