<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['major_id', 'name', 'level'];

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class);
    }
    public function exams(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'classroom_exam');
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
}
