<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Tahun Ajaran
        Schema::create('school_years', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique()->comment('Contoh: 2023/2024');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Tabel Jurusan
        Schema::create('majors', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Contoh: RPL');
            $table->string('name')->comment('Contoh: Rekayasa Perangkat Lunak');
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Tabel Kelas (Tergantung pada Jurusan)
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('major_id')->nullable()->constrained('majors')->nullOnDelete();
            $table->string('name', 50)->index()->comment('Contoh: XII RPL 1');
            $table->integer('level')->index()->comment('Tingkat kelas: 10, 11, 12');
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Tabel Mata Pelajaran
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
        });

        // 5. Tabel Ruang Ujian
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique()->comment('Contoh: Lab Komputer 1');
            $table->integer('capacity')->default(30);
            $table->timestamps();
            $table->softDeletes();
        });

        // 6. Tabel Sesi Ujian
       Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique()->comment('Contoh: Sesi 1');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('majors');
        Schema::dropIfExists('school_years');
    }
};