<?php

namespace App\Enums;

enum ReligionEnum: string
{
    case ISLAM = 'islam';
    case KRISTEN = 'kristen';
    case KATOLIK = 'katolik';
    case HINDU = 'hindu';
    case BUDDHA = 'buddha';
    case KONGHUCU = 'konghucu';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}