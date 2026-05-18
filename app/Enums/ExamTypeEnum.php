<?php

namespace App\Enums;

enum ExamTypeEnum: string
{
    case UH = 'uh';
    case UTS = 'uts';
    case UAS = 'uas';
    case TRYOUT = 'tryout';

    public function label(): string
    {
        return match ($this) {
            self::UH => 'Ulangan Harian',
            self::UTS => 'Ujian Tengah Semester',
            self::UAS => 'Ujian Akhir Semester',
            self::TRYOUT => 'Try Out',
        };
    }
}