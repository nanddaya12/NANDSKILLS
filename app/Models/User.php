<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;
use App\Traits\Auditable;

use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasUuids, HasTenantScope, Auditable, Notifiable, HasApiTokens;

    protected $fillable = [
        'tenant_id',
        'email',
        'password_hash',
        'first_name',
        'last_name',
        'phone',
        'is_email_verified',
        'mfa_enabled',
        'mfa_secret',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password_hash',
        'mfa_secret',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_email_verified' => 'boolean',
            'mfa_enabled' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    // Map Laravel default Auth password check to our password_hash column
    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles', 'user_id', 'role_id')
            ->using(UserRole::class);
    }

    public function studentProfile(): HasOne
    {
        return $this->hasOne(StudentProfile::class);
    }

    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentProfile::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // RBAC Helpers
    public function hasRole(string|array $roleNames): bool
    {
        if (is_array($roleNames)) {
            return $this->roles()->whereIn('name', $roleNames)->exists();
        }
        return $this->roles()->where('name', $roleNames)->exists();
    }

    public function hasPermission(string $permissionName): bool
    {
        // Check if user has any role that possesses the given permission name
        return $this->roles()
            ->whereHas('permissions', function ($query) use ($permissionName) {
                $query->where('name', $permissionName);
            })->exists();
    }
}
