<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class Book extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'books';

    protected $fillable = [
        'tenant_id',
        'title',
        'author',
        'isbn',
        'total_copies',
        'available_copies',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

}
