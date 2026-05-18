<?php

namespace App\Filament\Resources\Teachers\Pages;

use App\Actions\RegisterTeacherAction;
use App\DTOs\TeacherRegistrationDTO;
use App\Filament\Resources\Teachers\TeacherResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTeacher extends CreateRecord
{
    protected static string $resource = TeacherResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $dto = TeacherRegistrationDTO::fromArray($data);
        return app(RegisterTeacherAction::class)->execute($dto);
    }
}
