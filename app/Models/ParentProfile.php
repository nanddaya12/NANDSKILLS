<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class ParentProfile extends Model
{
    use HasUuids, HasTenantScope;

    protected $fillable = [
        'user_id',
        'tenant_id',
        'relationship',
        'occupation',
        'address',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(StudentProfile::class, 'student_parents', 'parent_id', 'student_id');
    }
}
