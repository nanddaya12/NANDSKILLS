<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class TenantInvoice extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'tenant_invoices';

    protected $fillable = [
        'tenant_id',
        'invoice_number',
        'amount',
        'status',
        'due_date',
        'paid_at',
        'stripe_payment_id',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

}
