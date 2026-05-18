<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SchoolSettings extends Settings
{
    public string $foundation_name;
    public string $school_name;
    public string $address;
    public string $phone;

    public static function group(): string
    {
        return 'school';
    }
}