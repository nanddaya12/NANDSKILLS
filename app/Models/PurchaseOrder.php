<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class PurchaseOrder extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'tenant_id',
        'vendor_id',
        'order_date',
        'due_date',
        'status', // DRAFT, SENT, RECEIVED, CANCELLED
        'total_amount',
        'terms',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PurchaseOrderLine::class, 'purchase_order_id');
    }
}
