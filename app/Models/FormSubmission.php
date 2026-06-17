<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class FormSubmission extends Model
{
    use HasUuids;

    protected $fillable = [
        'form_id',
        'data',
        'ip_address',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(FormBuilder::class, 'form_id');
    }
}
