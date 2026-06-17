<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ManagedDocumentApproval extends Model
{
    use HasUuids;

    protected $table = 'managed_document_approvals';

    protected $fillable = [
        'document_id',
        'approver_user_id',
        'status',
        'notes',
        'actioned_at',
    ];

    protected $casts = [
        'actioned_at' => 'datetime',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(ManagedDocument::class, 'document_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_user_id');
    }
}
