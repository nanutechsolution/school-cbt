<?php

namespace App\Actions;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Exception;

class GenerateExamDocumentsAction
{
    /**
     * Men-generate PDF Kartu Peserta berdasarkan Kelas siswa
     */
    public function generateExamCards(int $classroomId): \Illuminate\Http\Response
    {
        $classroom = Classroom::with(['students.user', 'major'])->findOrFail($classroomId);
        $students = $classroom->students;

        if ($students->isEmpty()) {
            throw new Exception("Tidak ada data siswa di kelas ini untuk dicetak kartu pesertanya.");
        }

        // Render HTML ke PDF menggunakan layout kartu yang didesain khusus cetak
        $pdf = Pdf::loadView('exports.exam-cards', [
            'classroom' => $classroom,
            'students' => $students,
            'school_name' => 'SMK UNGGULAN INDONESIA',
            'year' => now()->format('Y'),
        ]);

        // Atur ukuran kertas ke A4 Portrait
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Kartu_Peserta_{$classroom->name}.pdf");
    }

    /**
     * Men-generate Berita Acara Resmi Pelaksanaan Ujian
     */
    public function generateBeritaAcara(int $examId): \Illuminate\Http\Response
    {
        $exam = Exam::with(['questionBank.subject', 'classrooms'])->findOrFail($examId);

        $totalStudents = Student::whereIn('classroom_id', $exam->classrooms->pluck('id'))->count();

        $pdf = Pdf::loadView('exports.berita-acara', [
            'exam' => $exam,
            'total_students' => $totalStudents,
            'school_name' => 'SMK UNGGULAN INDONESIA',
            'date_now' => now()->translatedFormat('l, d F Y'),
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Berita_Acara_{$exam->name}.pdf");
    }

    /**
     * Men-generate Daftar Hadir Peserta Ujian
     */
    public function generateDaftarHadir(int $examId): \Illuminate\Http\Response
    {
        $exam = Exam::with(['questionBank.subject', 'classrooms.students.user'])->findOrFail($examId);

        // Ambil semua siswa dari kelas-kelas yang ditugaskan di ujian ini
        $students = Student::whereIn('classroom_id', $exam->classrooms->pluck('id'))
            ->with(['user', 'classroom'])
            ->get()
            ->sortBy('user.name');

        $pdf = Pdf::loadView('exports.daftar-hadir', [
            'exam' => $exam,
            'students' => $students,
            'school_name' => 'SMK UNGGULAN INDONESIA',
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Daftar_Hadir_{$exam->name}.pdf");
    }

    /**
     * Men-generate PDF Daftar Nilai Resmi Hasil Ujian
     */
    public function generateExamResultsPdf(int $examId): \Illuminate\Http\Response
    {
        $exam = Exam::with(['questionBank.subject', 'classrooms'])->findOrFail($examId);

        // Tarik seluruh attempt siswa yang telah mengumpulkan (submitted) atau ditangguhkan (suspended)
        $attempts = ExamAttempt::where('exam_id', $examId)
            ->whereIn('status', ['submitted', 'suspended'])
            ->with(['student.user', 'student.classroom'])
            ->get();

        $results = $attempts->map(function ($attempt) {
            // 1. Ambil seluruh kunci jawaban asli dari bank soal
            $totalQuestions = $attempt->exam->questionBank->questions()->with('options')->get();

            $totalWeight = $totalQuestions->sum('weight');
            $earnedWeight = 0;

            $correctCount = 0;
            $incorrectCount = 0;

            // 2. Ambil seluruh jawaban yang disubmit siswa pada attempt ini
            $answers = ExamAnswer::where('exam_attempt_id', $attempt->id)->get()->keyBy('question_id');

            foreach ($totalQuestions as $q) {
                $studentAnswer = $answers->get($q->id);

                if ($q->type->value === 'multiple_choice' || $q->type->value === 'true_false') {
                    // Cek kebenaran Pilihan Ganda / Benar Salah
                    if ($studentAnswer && $studentAnswer->question_option_id) {
                        $selectedOption = $q->options->firstWhere('id', $studentAnswer->question_option_id);
                        if ($selectedOption && $selectedOption->is_correct) {
                            $earnedWeight += $q->weight;
                            $correctCount++;
                        } else {
                            $incorrectCount++;
                        }
                    } else {
                        $incorrectCount++;
                    }
                } elseif ($q->type->value === 'essay') {
                    // Nilai Essay diambil langsung dari kolom skor manual yang diberikan guru
                    if ($studentAnswer && $studentAnswer->score !== null) {
                        $earnedWeight += $studentAnswer->score;
                    }
                }
            }

            // Hitung nilai akhir dengan skala 100
            $finalScore = $totalWeight > 0 ? round(($earnedWeight / $totalWeight) * 100, 2) : 0;

            return [
                'name' => $attempt->student->user->name,
                'classroom' => $attempt->student->classroom->name,
                'correct' => $correctCount,
                'incorrect' => $incorrectCount,
                'score' => $finalScore,
                'status' => $attempt->status
            ];
        })->sortByDesc('score');

        $pdf = Pdf::loadView('exports.exam-results', [
            'exam' => $exam,
            'results' => $results,
            'school_name' => 'SMK UNGGULAN INDONESIA',
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->download("Daftar_Nilai_{$exam->name}.pdf");
    }
}
