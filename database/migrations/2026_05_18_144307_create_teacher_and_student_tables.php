<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Profil Guru
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            // Cascade delete: Jika user dihapus (force), profil ikut terhapus.
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); 
            $table->string('nip', 30)->nullable()->unique();
            $table->string('gender', 15)->nullable()->comment('Laki-laki / Perempuan');
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Tabel Profil Siswa
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            // Restrict on delete: Kelas tidak bisa dihapus jika masih ada siswanya
            $table->foreignId('classroom_id')->constrained('classrooms')->restrictOnDelete();
            
            // Null on delete: Sesi/Ruang bisa dihapus/dikosongkan tanpa menghapus data siswa
            $table->foreignId('exam_session_id')->nullable()->constrained('exam_sessions')->nullOnDelete();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            
            $table->string('nis', 20)->unique()->index();
            $table->string('nisn', 20)->nullable()->unique();
            $table->string('gender', 15)->nullable();
            $table->string('religion', 20)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
        Schema::dropIfExists('teachers');
    }
};