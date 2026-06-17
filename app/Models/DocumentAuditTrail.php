<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DocumentAuditTrail extends Model
{
    use HasUuids;

    protected $table = 'document_audit_trail';

    protected $fillable = [
        'document_id',
        'user_id',
        'action',
        'ip_address',
        'details',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(ManagedDocument::class, 'document_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
