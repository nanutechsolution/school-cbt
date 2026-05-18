<?php

namespace App\Events;

use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StudentExamStateChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $data;

    /**
     * Inisialisasi event dengan mengirimkan data real-time siswa
     */
    public function __construct(ExamAttempt $attempt)
    {
        $student = $attempt->student;
        $totalQuestions = $attempt->exam->questionBank->questions()->count();
        $answeredQuestions = ExamAnswer::where('exam_attempt_id', $attempt->id)->count();
        
        $this->data = [
            'attempt_id' => $attempt->id,
            'exam_id' => $attempt->exam_id,
            'student_id' => $student->id,
            'student_name' => $student->user->name,
            'classroom_name' => $student->classroom->name,
            'progress' => $totalQuestions > 0 ? round(($answeredQuestions / $totalQuestions) * 100) : 0,
            'answered_count' => $answeredQuestions,
            'total_questions' => $totalQuestions,
            'cheat_count' => $attempt->cheat_attempts_count,
            'status' => $attempt->status, // processing, submitted, suspended
            'last_update' => now()->format('H:i:s'),
        ];
    }

    /**
     * Tentukan Channel penyiaran (Spesifik per Sesi Ujian agar tidak bentrok)
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('exam-monitoring.' . $this->data['exam_id']),
        ];
    }

    /**
     * Nama event yang akan ditangkap di sisi klien
     */
    public function broadcastAs(): string
    {
        return 'StateChanged';
    }
}