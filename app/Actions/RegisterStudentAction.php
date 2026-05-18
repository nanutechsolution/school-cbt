<?php

namespace App\Actions;

use App\DTOs\StudentRegistrationDTO;
use App\Enums\RoleEnum;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;

class RegisterStudentAction
{
    /**
     * Mengeksekusi pendaftaran siswa
     * @throws Exception
     */
    public function execute(StudentRegistrationDTO $dto): Student
    {
        // Bungkus dalam database transaction
        return DB::transaction(function () use ($dto) {
            
            // 1. Buat User (Authentication)
            $user = User::create([
                'name' => $dto->name,
                'username' => $dto->nis, // NIS digunakan sebagai username login
                'password' => Hash::make($dto->password),
                'is_active' => true,
            ]);

            // 2. Berikan Role Siswa menggunakan enum (agar tidak hardcode)
            $user->assignRole(RoleEnum::SISWA->value);

            // 3. Buat Profil Student
            $student = Student::create([
                'user_id' => $user->id,
                'classroom_id' => $dto->classroomId,
                'exam_session_id' => $dto->examSessionId,
                'room_id' => $dto->roomId,
                'nis' => $dto->nis,
                'nisn' => $dto->nisn,
                'gender' => $dto->gender,
                'religion' => $dto->religion,
            ]);

            // Load relasi agar data lengkap saat direturn
            return $student->load(['user', 'classroom']);
        });
    }
}