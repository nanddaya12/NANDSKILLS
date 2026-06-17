<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class Account extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'accounts';

    protected $fillable = [
        'tenant_id',
        'code',
        'name',
        'type', // ASSET, LIABILITY, EQUITY, REVENUE, EXPENSE
        'description',
        'parent_id',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'account_id');
    }

    /**
     * Compute current balance dynamically based on ledger entries
     */
    public function getBalanceAttribute(): float
    {
        $lines = $this->journalEntryLines()
            ->whereHas('journalEntry', function ($q) {
                $q->where('status', 'POSTED');
            })->get();

        $balance = 0.00;
        foreach ($lines as $line) {
            if ($line->type === 'DEBIT') {
                $balance += (float)$line->amount;
            } else {
                $balance -= (float)$line->amount;
            }
        }

        // For Liabilities, Equity, and Revenue, positive balance is normal credit balance
        if (in_array($this->type, ['LIABILITY', 'EQUITY', 'REVENUE'])) {
            $balance = -$balance;
        }

        return $balance;
    }
}
