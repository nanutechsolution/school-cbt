<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('school.foundation_name', 'YAYASAN PENDIDIKAN NASIONAL');
        $this->migrator->add('school.school_name', 'SMK UNGGULAN INDONESIA');
        $this->migrator->add('school.address', 'Jl. Raya Pendidikan No. 45, Jakarta Selatan');
        $this->migrator->add('school.phone', '(021) 777-8888');
    }
};
