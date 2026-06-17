<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class Payroll extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'payrolls';

    protected $fillable = [
        'tenant_id',
        'employee_id',
        'amount',
        'bonus',
        'deductions',
        'status',
        'paid_at',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

}
