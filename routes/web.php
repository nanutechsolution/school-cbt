<?php

use App\Livewire\Student\Login as StudentLogin;
use App\Livewire\Student\Dashboard as StudentDashboard;
use Illuminate\Support\Facades\Route;

// Rute Publik Siswa
Route::get('/siswa/login', StudentLogin::class)->name('student.login');

// Rute Terproteksi Siswa (Harus Login & Memiliki Role Siswa)
Route::middleware(['auth', 'role:siswa'])->group(function () {
    Route::get('/siswa/dashboard', StudentDashboard::class)->name('student.dashboard');
    Route::get('/siswa/ujian/{attemptId}', \App\Livewire\Student\ExamEngine::class)->name('student.exam');
    // Logout Siswa
    Route::post('/siswa/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('student.login');
    })->name('student.logout');
});

// Redirect halaman utama ke login siswa agar rapi
Route::get('/', function () {
    return redirect()->route('student.login');
});
