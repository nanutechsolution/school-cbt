<?php

namespace App\Actions;

use App\DTOs\TeacherRegistrationDTO;
use App\Enums\RoleEnum;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterTeacherAction
{
    public function execute(TeacherRegistrationDTO $dto): Teacher
    {
        return DB::transaction(function () use ($dto) {
            $user = User::create([
                'name' => $dto->name,
                'username' => $dto->nip,
                'password' => Hash::make($dto->password),
                'is_active' => true,
            ]);

            $user->assignRole(RoleEnum::GURU->value);

            return Teacher::create([
                'user_id' => $user->id,
                'nip' => $dto->nip,
                'gender' => $dto->gender,
            ]);
        });
    }
}