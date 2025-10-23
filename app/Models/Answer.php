<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    protected $fillable = [
        'questionnaire_id',
        'answers',
        'answered_at',
        'submitted_at',
        'score'
    ];

    protected $casts = [
        'answers' => 'array',
        'answered_at' => 'datetime',
        'submitted_at' => 'datetime'
    ];

    public function questionnaire(): BelongsTo
    {
        return $this->belongsTo(Questionnaire::class);
    }
}
