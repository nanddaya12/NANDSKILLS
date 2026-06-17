<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class AdmissionTest extends Model
{
    use HasUuids;

    protected $fillable = [
        'admission_id',
        'test_name',
        'score',
        'max_score',
        'passing_score',
        'taken_at',
        'result',
        'notes',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'passing_score' => 'decimal:2',
        'taken_at' => 'date',
    ];

    public function admission(): BelongsTo
    {
        return $this->belongsTo(Admission::class);
    }
}
