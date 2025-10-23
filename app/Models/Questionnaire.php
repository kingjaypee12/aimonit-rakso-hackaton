<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Questionnaire extends Model
{
    protected $fillable = [
        'code',
        'questions',
        'ai_prompt'
    ];

    protected $casts = [
        'questions' => 'array'
    ];

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
