<?php

namespace App\Enums;

enum RoleEnum: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN_SEKOLAH = 'admin_sekolah';
    case GURU = 'guru';
    case SISWA = 'siswa';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Administrator',
            self::ADMIN_SEKOLAH => 'Admin Sekolah',
            self::GURU => 'Guru',
            self::SISWA => 'Siswa',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}