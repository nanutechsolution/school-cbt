<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Utama Pelaksanaan Ujian
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            
            // Mengambil soal dari QuestionBank
            $table->foreignId('question_bank_id')->constrained('question_banks')->restrictOnDelete();
            
            $table->string('name')->comment('Contoh: Pelaksanaan UTS Matematika Kelas XII');
            
            // Pengaturan Waktu
            $table->dateTime('start_time')->comment('Waktu ujian mulai bisa diakses');
            $table->dateTime('end_time')->comment('Waktu ujian otomatis tertutup');
            $table->integer('duration')->comment('Durasi pengerjaan dalam menit');
            
            // Keamanan & Pengaturan
            $table->string('token', 10)->unique()->index();
            $table->boolean('show_result')->default(false)->comment('Apakah siswa bisa melihat nilai setelah selesai');
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Tabel Pivot: Menugaskan Ujian ke Kelas tertentu
        Schema::create('classroom_exam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classroom_exam');
        Schema::dropIfExists('exams');
    }
};