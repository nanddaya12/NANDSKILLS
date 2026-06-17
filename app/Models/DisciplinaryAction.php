<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

class DisciplinaryAction extends Model
{
    use HasUuids, HasTenantScope, Auditable;

    protected $table = 'disciplinary_actions';

    protected $fillable = [
        'tenant_id',
        'student_user_id',
        'issued_by',
        'action_type',
        'description',
        'issued_at',
        'expiry_date',
        'is_active',
        'resolution_notes',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expiry_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }
}
