<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Exam extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'question_bank_id',
        'name',
        'start_time',
        'end_time',
        'duration',
        'token',
        'show_result',
        'is_active',
        'max_cheating_limit',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'duration' => 'integer',
            'show_result' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Event yang dijalankan secara otomatis oleh model
     */
    protected static function booted(): void
    {
        // Otomatis generate token acak 6 digit saat membuat data baru
        static::creating(function ($exam) {
            if (empty($exam->token)) {
                $exam->token = strtoupper(Str::random(6));
            }
        });
    }

    public function questionBank(): BelongsTo
    {
        return $this->belongsTo(QuestionBank::class);
    }

    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class, 'classroom_exam');
    }
}