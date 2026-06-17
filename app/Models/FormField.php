<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class FormField extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'form_fields';

    protected $fillable = [
        'tenant_id',
        'form_id',
        'label',
        'type',
        'name',
        'options',
        'is_required',
        'order_index',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class, 'form_id');
    }

}
