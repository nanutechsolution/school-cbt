<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Paket Soal (Question Bank)
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            // Relasi ke Guru Pembuat Soal
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            // Relasi ke Mata Pelajaran
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            
            $table->string('name')->comment('Contoh: UTS Matematika Ganjil 2024');
            $table->integer('level')->index()->comment('Tingkat Kelas: 10, 11, 12');
            
            // Pengaturan opsi default paket
            $table->boolean('is_active')->default(true);
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('randomize_options')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Tabel Soal (Questions)
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')->constrained('question_banks')->cascadeOnDelete();
            
            $table->string('type', 30)->comment('Berasal dari QuestionTypeEnum');
            
            // Konten soal bisa berupa teks panjang atau HTML dari Rich Text Editor
            $table->longText('content');
            
            // Mendukung soal dengan media (Listening Bahasa Inggris / Gambar)
            $table->string('audio_path')->nullable();
            $table->string('image_path')->nullable();
            
            // Pembobotan nilai
            $table->integer('weight')->default(1)->comment('Bobot skor soal ini');
            
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Tabel Pilihan Jawaban (Untuk Pilihan Ganda / Benar Salah)
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            
            $table->longText('content');
            $table->string('image_path')->nullable(); // Terkadang pilihan jawaban berupa gambar
            
            // Indikator kunci jawaban
            $table->boolean('is_correct')->default(false);
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('question_banks');
    }
};