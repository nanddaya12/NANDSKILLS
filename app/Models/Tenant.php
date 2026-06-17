<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Tenant extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'subdomain',
        'custom_domain',
        'status',
        'logo_url',
        'favicon_url',
        'primary_color',
        'secondary_color',
        'typography',
        'dashboard_theme',
        'email_template',
        'website_theme',
        'plan_id',
        'active_modules',
    ];

    protected $casts = [
        'active_modules' => 'array',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(TenantPlan::class, 'plan_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(TenantSubscription::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(Role::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function websitePages(): HasMany
    {
        return $this->hasMany(WebsitePage::class);
    }

    public function blogPosts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }
}
