<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\HasTenantScope;

class PeerReview extends Model
{
    use HasUuids, HasTenantScope;

    protected $table = 'peer_reviews';

    protected $fillable = [
        'tenant_id',
        'submission_id',
        'reviewer_id',
        'score',
        'comments',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(AssignmentSubmission::class, 'submission_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

}
