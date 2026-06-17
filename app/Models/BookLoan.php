<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class BookLoan extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'book_loans';

    protected $fillable = [
        'tenant_id',
        'book_id',
        'user_id',
        'loaned_at',
        'due_date',
        'returned_at',
        'fine_amount',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
