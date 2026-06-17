<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class StudentProfile extends Model
{
    use HasUuids, HasTenantScope;

    protected $fillable = [
        'user_id',
        'tenant_id',
        'roll_number',
        'admission_date',
        'academic_record',
        'status',
    ];

    protected $casts = [
        'academic_record' => 'array',
        'admission_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(ParentProfile::class, 'student_parents', 'student_id', 'parent_id');
    }

    public function studentFees(): HasMany
    {
        return $this->hasMany(StudentFee::class, 'student_id');
    }
}
