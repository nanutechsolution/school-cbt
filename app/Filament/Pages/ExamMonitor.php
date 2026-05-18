<?php

namespace App\Filament\Pages;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use UnitEnum;

class ExamMonitor extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Ujian';

    protected static ?string $navigationLabel = 'Live Monitoring';

    protected static ?string $title = 'Monitoring Real-time CBT';

    protected static ?int $navigationSort = 3;

    protected  string $view = 'filament.pages.exam-monitor';

    public ?int $selectedExamId = null;
    public array $participants = [];

    public function mount()
    {
        // Cari ujian pertama yang aktif jika ada untuk default view
        $activeExam = Exam::where('is_active', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->first();

        if ($activeExam) {
            $this->selectedExamId = $activeExam->id;
        }

        $this->loadParticipants();
    }

    /**
     * Memuat daftar siswa beserta progres & kecurangan mereka saat ini
     */
    public function loadParticipants()
    {
        if (!$this->selectedExamId) {
            $this->participants = [];
            return;
        }

        $attempts = ExamAttempt::where('exam_id', $this->selectedExamId)
            ->with(['student.user', 'student.classroom'])
            ->get();

        $exam = Exam::find($this->selectedExamId);
        $totalQuestions = $exam ? $exam->questionBank->questions()->count() : 0;

        $this->participants = $attempts->map(function ($attempt) use ($totalQuestions) {
            $answered = ExamAnswer::where('exam_attempt_id', $attempt->id)->count();
            return [
                'attempt_id' => $attempt->id,
                'name' => $attempt->student->user->name,
                'classroom' => $attempt->student->classroom->name,
                'progress' => $totalQuestions > 0 ? round(($answered / $totalQuestions) * 100) : 0,
                'answered_count' => $answered,
                'total_questions' => $totalQuestions,
                'cheat_count' => $attempt->cheat_attempts_count,
                'status' => $attempt->status,
                'ip_address' => $attempt->ip_address,
                'updated_at' => $attempt->updated_at->format('H:i:s'),
            ];
        })->toArray();
    }

    /**
     * Reset Status Ujian Siswa (Fitur penyelamat jika siswa butuh login ulang)
     */
    public function resetAttempt($attemptId)
    {
        $attempt = ExamAttempt::find($attemptId);
        if ($attempt) {
            // Hapus log pengerjaan agar siswa bisa login dari perangkat lain/mulai ulang
            $attempt->delete();
            $this->loadParticipants();

            $this->dispatch('notify', [
                'status' => 'success',
                'message' => 'Status ujian peserta berhasil di-reset!'
            ]);
        }
    }

    /**
     * Ambil daftar seluruh ujian untuk komponen dropdown pemilih
     */
    protected function getExams(): Collection
    {
        return Exam::where('is_active', true)->pluck('name', 'id');
    }

    protected function getViewData(): array
    {
        return [
            'exams' => $this->getExams(),
        ];
    }
}
