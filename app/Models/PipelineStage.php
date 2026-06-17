<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class PipelineStage extends Model
{
    use HasUuids, HasTenantScope;

    protected $fillable = [
        'tenant_id',
        'name',
        'order_index',
        'color',
        'probability',
    ];

    protected $casts = [
        'order_index' => 'integer',
        'probability' => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'stage_id')->orderBy('created_at', 'desc');
    }
}
