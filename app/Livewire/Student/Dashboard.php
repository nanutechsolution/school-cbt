<?php

namespace App\Livewire\Student;

use App\Models\Exam;
use App\Models\ExamAttempt;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Dashboard extends Component
{
    public string $tokenInput = '';
    public ?int $selectedExamId = null;

    protected $rules = [
        'tokenInput' => 'required|string|size:6',
    ];

    /**
     * Memproses inisialisasi / klik tombol Mulai Ujian oleh Siswa
     */
    public function startExam($examId)
    {
        $this->selectedExamId = $examId;
        $this->validate();

        $user = Auth::user();
        $student = $user->student;
        $exam = Exam::where('id', $examId)->where('is_active', true)->firstOrFail();

        // 1. Validasi Waktu: Pastikan ujian masih bisa diakses
        $now = now();
        if ($now->lt($exam->start_time) || $now->gt($exam->end_time)) {
            $this->addError('token', 'Waktu pelaksanaan ujian ini tidak sesuai atau telah berakhir.');
            return;
        }

        // 2. Validasi Token: Harus match dengan token yang di-generate Admin/Guru
        if (strtoupper($this->tokenInput) !== strtoupper($exam->token)) {
            $this->addError('token', 'Token ujian yang Anda masukkan salah. Silakan minta ke pengawas.');
            return;
        }

        // 3. Cek Status Attempt Sebelumnya
        $existingAttempt = ExamAttempt::where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->first();

        if ($existingAttempt) {
            if ($existingAttempt->status !== 'processing') {
                $this->addError('token', 'Anda sudah menyelesaikan ujian ini sebelumnya.');
                return;
            }
            // Jika statusnya masih processing (misal karena logout tak sengaja), arahkan kembali
            return redirect()->route('student.exam', ['attemptId' => $existingAttempt->id]);
        }

        // 4. Jika Valid, Buat Sesi Attempt Baru (Stateless State Tracking)
        $durationInSeconds = $exam->duration * 60;
        $endTimeAt = now()->addSeconds($durationInSeconds);

        // Pengaman: Jangan biarkan end_time_at melebihi batas end_time jadwal ujian mutlak
        if ($endTimeAt->gt($exam->end_time)) {
            $endTimeAt = $exam->end_time;
        }

        $attempt = ExamAttempt::create([
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'started_at' => now(),
            'end_time_at' => $endTimeAt,
            'status' => 'processing',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Lempar siswa ke halaman pengerjaan soal (Halaman ini akan dibuat di tahap berikutnya)
        return redirect()->route('student.exam', ['attemptId' => $attempt->id]);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $user = Auth::user();
        // Ambil profil siswa beserta kelasnya
        $student = $user->student()->with('classroom')->first();

        $activeExams = collect();

        if ($student && $student->classroom) {
            // Query ujian yang aktif untuk kelas siswa saat ini
            $activeExams = Exam::whereHas('classrooms', function ($query) use ($student) {
                $query->where('classrooms.id', $student->classroom_id);
            })
                ->where('is_active', true)
                ->where('start_time', '<=', now())
                ->where('end_time', '>=', now())
                ->with(['questionBank.subject'])
                ->get();
        }

        // Ambil riwayat attempt ujian siswa untuk mengecek status pengerjaan
        $attempts = ExamAttempt::where('student_id', $student->id)
            ->get()
            ->keyBy('exam_id');

        return view('livewire.student.dashboard', [
            'student' => $student,
            'activeExams' => $activeExams,
            'attempts' => $attempts
        ]);
    }
}
