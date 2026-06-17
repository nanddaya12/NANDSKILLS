<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class JournalEntry extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'journal_entries';

    protected $fillable = [
        'tenant_id',
        'entry_date',
        'reference_number',
        'description',
        'posted_by_user_id',
        'status', // DRAFT, POSTED
    ];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'journal_entry_id');
    }

    public function postedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'posted_by_user_id');
    }

    /**
     * Helper to verify if the journal entry debits and credits balance.
     */
    public function isBalanced(): bool
    {
        $debits = $this->lines()->where('type', 'DEBIT')->sum('amount');
        $credits = $this->lines()->where('type', 'CREDIT')->sum('amount');

        return abs($debits - $credits) < 0.001;
    }
}
