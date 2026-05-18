<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained('exams')->cascadeOnDelete();
            
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('end_time_at')->comment('Waktu mutlak siswa harus selesai (started_at + durasi ujian)');
            $table->timestamp('submitted_at')->nullable()->comment('Kapan siswa mengklik selesai');
            
            // Status Ujian menggunakan string status statis
            $table->string('status', 20)->default('processing')->comment('processing, submitted, suspended');
            
            // Untuk logging Anti-Cheat dasar
            $table->integer('cheat_attempts_count')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamps();
            
            // Index komposit untuk mempercepat pencarian status ujian siswa
            $table->index(['student_id', 'exam_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};