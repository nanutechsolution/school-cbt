<?php

namespace App\Models;

use App\Enums\QuestionTypeEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'question_bank_id',
        'type',
        'content',
        'audio_path',
        'image_path',
        'weight',
    ];

    protected function casts(): array
    {
        return [
            'type' => QuestionTypeEnum::class, // Auto casting string ke Enum di Laravel 11+
            'weight' => 'integer',
        ];
    }

    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }
}