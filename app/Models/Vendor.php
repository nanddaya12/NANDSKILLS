<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class Vendor extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'vendors';

    protected $fillable = [
        'tenant_id',
        'name',
        'contact_name',
        'email',
        'phone',
        'address',
        'tax_number',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'vendor_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'vendor_id');
    }
}
