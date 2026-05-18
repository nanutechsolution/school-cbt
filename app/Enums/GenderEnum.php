<?php

namespace App\Enums;

enum GenderEnum: string
{
    case LAKI_LAKI = 'L';
    case PEREMPUAN = 'P';

    public function label(): string
    {
        return match ($this) {
            self::LAKI_LAKI => 'Laki-Laki',
            self::PEREMPUAN => 'Perempuan',
        };
    }
}