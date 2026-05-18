<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan nilai skor manual pada tabel lembar jawaban (untuk penilaian essay)
        Schema::table('exam_answers', function (Blueprint $table) {
            $table->decimal('score', 8, 2)->nullable()->comment('Nilai manual guru untuk soal essay');
        });

        // 2. Tambahkan cache nilai akhir di tabel attempt ujian
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->decimal('final_score', 8, 2)->nullable()->comment('Nilai akhir ter-cache skala 100');
        });
    }

    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropColumn('final_score');
        });

        Schema::table('exam_answers', function (Blueprint $table) {
            $table->dropColumn('score');
        });
    }
};