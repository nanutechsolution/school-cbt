<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    /**
     * Memasukkan data Name dari tabel users ke dalam form Filament
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['name'] = $this->record->user->name;
        return $data;
    }

    /**
     * Mengatur logic penyimpanan untuk 2 tabel berbeda secara bersamaan
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        DB::transaction(function () use ($record, $data) {
            $user = $record->user;

            // 1. Update Data User (Authentication)
            $user->name = $data['name'];
            $user->username = $data['nis']; // Username selalu sama dengan NIS

            if (filled($data['password'] ?? null)) {
                $user->password = Hash::make($data['password']);
            }
            $user->save();

            // 2. Buang field user agar tidak menyebabkan error "Column not found" di tabel students
            unset($data['name'], $data['password']);

            // 3. Update Data Student (Profil)
            $record->update($data);
        });

        return $record;
    }
}
