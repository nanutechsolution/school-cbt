<?php

namespace App\Livewire\Student;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

class Login extends Component
{
    public string $username = '';
    public string $password = '';

    protected array $rules = [
        'username' => 'required|string',
        'password' => 'required|string',
    ];

    public function login()
    {
        $this->validate();

        // Cari user & pastikan dia aktif serta merupakan siswa
        if (Auth::attempt(['username' => $this->username, 'password' => $this->password, 'is_active' => true])) {
            $user = Auth::user();
            
            if ($user->hasRole('siswa')) {
                // Catat data login terakhir untuk audit trail
                $user->update([
                    'last_login_at' => now(),
                    'last_login_ip' => request()->ip(),
                ]);

                session()->regenerate();
                return redirect()->route('student.dashboard');
            }

            // Jika bukan siswa tapi mencoba login lewat sini, paksa logout
            Auth::logout();
        }

        $this->addError('username', 'Kredensial yang Anda masukkan salah atau akun dinonaktifkan.');
    }

    #[Layout('layouts.app')] // Menggunakan layout app standar laravel
    public function render()
    {
        return view('livewire.student.login');
    }
}