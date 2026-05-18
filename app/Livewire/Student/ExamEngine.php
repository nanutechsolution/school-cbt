<?php

namespace App\Livewire\Student;

use App\Models\ExamAttempt;
use App\Models\ExamAnswer;
use App\Events\StudentExamStateChanged;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

class ExamEngine extends Component
{
    public int $attemptId;
    public int $currentQuestionIndex = 0;

    // State Lembar Jawaban saat ini
    public ?int $selectedOptionId = null;
    public string $essayAnswerText = '';
    public bool $isDoubtfulCheck = false;

    // Properti sisa waktu (detik)
    public int $timeLeftSeconds = 0;

    public function mount($attemptId)
    {
        $this->attemptId = $attemptId;

        $attempt = ExamAttempt::where('id', $attemptId)
            ->where('student_id', Auth::user()->student->id)
            ->where('status', 'processing')
            ->firstOrFail();

        $this->timeLeftSeconds = now()->diffInSeconds($attempt->end_time_at, false);

        if ($this->timeLeftSeconds <= 0) {
            $this->submitExam();
            return;
        }

        $this->loadQuestionState();

        // Broadcast pertama kali saat siswa mulai membuka lembar ujian
        $this->dispatchBroadcast($attempt);
    }

    public function loadQuestionState()
    {
        $questions = $this->getQuestionsProperty();
        $currentQuestion = $questions[$this->currentQuestionIndex];

        $savedAnswer = ExamAnswer::where('exam_attempt_id', $this->attemptId)
            ->where('question_id', $currentQuestion->id)
            ->first();

        if ($savedAnswer) {
            $this->selectedOptionId = $savedAnswer->question_option_id;
            $this->essayAnswerText = $savedAnswer->essay_answer ?? '';
            $this->isDoubtfulCheck = $savedAnswer->is_doubtful;
        } else {
            $this->selectedOptionId = null;
            $this->essayAnswerText = '';
            $this->isDoubtfulCheck = false;
        }
    }

    public function getQuestionsProperty()
    {
        $attempt = ExamAttempt::find($this->attemptId);
        $bank = $attempt->exam->questionBank;

        $query = $bank->questions()->with('options');

        if ($bank->randomize_questions) {
            $query->inRandomOrder($this->attemptId);
        }

        return $query->get();
    }

    public function changeQuestion($index)
    {
        $this->saveCurrentAnswer();
        $this->currentQuestionIndex = $index;
        $this->loadQuestionState();
    }

    public function saveCurrentAnswer()
    {
        $questions = $this->getQuestionsProperty();
        $currentQuestion = $questions[$this->currentQuestionIndex];

        if ($this->selectedOptionId !== null || !empty($this->essayAnswerText) || $this->isDoubtfulCheck) {
            $answer = ExamAnswer::updateOrCreate(
                [
                    'exam_attempt_id' => $this->attemptId,
                    'question_id' => $currentQuestion->id,
                ],
                [
                    'question_option_id' => $this->selectedOptionId,
                    'essay_answer' => $this->essayAnswerText ? $this->essayAnswerText : null,
                    'is_doubtful' => $this->isDoubtfulCheck,
                ]
            );

            // Trigger broadcast progres baru ke monitor
            $this->dispatchBroadcast(ExamAttempt::find($this->attemptId));
        }
    }

    public function toggleDoubtful()
    {
        $this->isDoubtfulCheck = !$this->isDoubtfulCheck;
        $this->saveCurrentAnswer();
    }

    public function logViolation()
    {
        $attempt = ExamAttempt::where('id', $this->attemptId)
            ->where('student_id', Auth::user()->student->id)
            ->where('status', 'processing')
            ->first();

        if (!$attempt) {
            return;
        }

        $attempt->increment('cheat_attempts_count');
        $maxLimit = $attempt->exam->max_cheating_limit;

        if ($attempt->cheat_attempts_count >= $maxLimit) {
            $attempt->update([
                'status' => 'suspended',
                'submitted_at' => now()
            ]);

            $this->dispatchBroadcast($attempt);

            session()->flash('cheat_error', 'Akun Anda ditangguhkan otomatis oleh sistem karena terdeteksi keluar dari layar ujian melebihi batas toleransi. Silakan lapor ke pengawas ruangan.');
            return redirect()->route('student.dashboard');
        }

        // Broadcast insiden kecurangan
        $this->dispatchBroadcast($attempt);

        $this->dispatch('show-cheat-warning', [
            'count' => $attempt->cheat_attempts_count,
            'max' => $maxLimit
        ]);
    }

    public function submitExam()
    {
        $this->saveCurrentAnswer();

        $attempt = ExamAttempt::find($this->attemptId);
        $attempt->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $this->dispatchBroadcast($attempt);

        return redirect()->route('student.dashboard');
    }

    /**
     * Helper asinkron untuk penyiaran event websockets
     */
    protected function dispatchBroadcast(ExamAttempt $attempt)
    {
        broadcast(new StudentExamStateChanged($attempt))->toOthers();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $questions = $this->getQuestionsProperty();
        $currentQuestion = $questions[$this->currentQuestionIndex];

        $savedAnswers = ExamAnswer::where('exam_attempt_id', $this->attemptId)
            ->get()
            ->keyBy('question_id');

        return view('livewire.student.exam-engine', [
            'question' => $currentQuestion,
            'questions' => $questions,
            'savedAnswers' => $savedAnswers
        ]);
    }
}
