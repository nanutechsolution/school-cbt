<?php

namespace App\Enums;

enum QuestionTypeEnum: string
{
    case MULTIPLE_CHOICE = 'multiple_choice';
    case ESSAY = 'essay';
    case TRUE_FALSE = 'true_false';

    public function label(): string
    {
        return match ($this) {
            self::MULTIPLE_CHOICE => 'Pilihan Ganda',
            self::ESSAY => 'Uraian / Essay',
            self::TRUE_FALSE => 'Benar / Salah',
        };
    }
}