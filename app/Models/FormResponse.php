<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class FormResponse extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'form_responses';

    protected $fillable = [
        'tenant_id',
        'form_id',
        'user_id',
        'response_data',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(CustomForm::class, 'form_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
