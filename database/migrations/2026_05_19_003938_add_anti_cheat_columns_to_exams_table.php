<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->integer('max_cheating_limit')->default(3)->comment('Batas maksimal toleransi siswa keluar layar/pindah tab sebelum diblokir');
        });
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn('max_cheating_limit');
        });
    }
};