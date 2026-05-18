<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_attempt_id')->constrained('exam_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            
            // Menyimpan pilihan jawaban (untuk PG)
            $table->foreignId('question_option_id')->nullable()->constrained('question_options')->cascadeOnDelete();
            
            // Menyimpan teks jawaban (untuk Essay)
            $table->text('essay_answer')->nullable();
            
            // Status Ragu-Ragu
            $table->boolean('is_doubtful')->default(false);
            
            $table->timestamps();

            // Index unik agar 1 nomor soal dalam 1 attempt tidak memiliki baris ganda
            $table->unique(['exam_attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};