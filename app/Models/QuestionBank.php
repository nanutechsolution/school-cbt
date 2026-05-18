<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionBank extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'name',
        'level',
        'is_active',
        'randomize_questions',
        'randomize_options',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'randomize_questions' => 'boolean',
            'randomize_options' => 'boolean',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
