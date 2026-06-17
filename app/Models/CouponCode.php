<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class CouponCode extends Model
{
    use HasUuids;

    protected $table = 'coupon_codes';

    protected $fillable = [
        'code',
        'name',
        'discount_type',
        'discount_value',
        'max_uses',
        'used_count',
        'valid_from',
        'expires_at',
        'is_active',
        'applicable_plan_id',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'is_active'      => 'boolean',
        'valid_from'     => 'date',
        'expires_at'     => 'date',
    ];

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->valid_from && $this->valid_from->isFuture()) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;
        return true;
    }

    public function applyDiscount(float $amount): float
    {
        if ($this->discount_type === 'PERCENTAGE') {
            return round($amount * (1 - $this->discount_value / 100), 2);
        }
        return max(0, $amount - $this->discount_value);
    }
}
