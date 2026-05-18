<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAttempt extends Model
{
    protected $fillable = [
        'student_id',
        'exam_id',
        'started_at',
        'end_time_at',
        'submitted_at',
        'status',
        'cheat_attempts_count',
        'ip_address',
        'user_agent',
        'final_score',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'end_time_at' => 'datetime',
            'submitted_at' => 'datetime',
            'cheat_attempts_count' => 'integer',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function answers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ExamAnswer::class);
    }
}