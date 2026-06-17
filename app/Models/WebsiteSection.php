<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class WebsiteSection extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'website_sections';

    protected $fillable = [
        'tenant_id',
        'page_id',
        'section_type',
        'content_data',
        'order_index',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(WebsitePage::class, 'page_id');
    }

}
