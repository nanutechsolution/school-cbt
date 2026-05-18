<?php

namespace App\Filament\Resources\Students\Pages;

use App\Actions\RegisterStudentAction;
use App\DTOs\StudentRegistrationDTO;
use App\Filament\Resources\Students\StudentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    /**
     * Override method penyimpanan untuk menggunakan Action & DTO
     */
    protected function handleRecordCreation(array $data): Model
    {
        // 1. Bungkus data dari form ke dalam DTO
        $dto = StudentRegistrationDTO::fromArray($data);

        // 2. Panggil Action Class untuk mengeksekusi logic (Create User + Student + Role)
        $action = app(RegisterStudentAction::class);

        return $action->execute($dto);
    }
}
