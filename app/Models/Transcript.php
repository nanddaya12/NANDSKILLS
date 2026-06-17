<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class Transcript extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'transcripts';

    protected $fillable = [
        'tenant_id',
        'user_id',
        'cgpa',
        'compiled_at',
        'file_url',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
