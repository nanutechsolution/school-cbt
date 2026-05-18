<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Imports\StudentsImport;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Excel;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [

            Action::make('import')
                ->label('Import Excel')
                ->color('success')
                ->icon('heroicon-o-document-arrow-up')
                ->form([
                    FileUpload::make('file')
                        ->label('Upload File Excel')
                        ->disk('local') // Simpan di storage lokal internal
                        ->directory('imports')
                        ->visibility('private') // Jangan jadikan public
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            'application/vnd.ms-excel'
                        ])
                        ->required(),
                ])
                ->action(function (array $data) {
                    $filePath = $data['file'];

                    // Eksekusi Import (Akan otomatis masuk Queue karena ShouldQueue)
                    Excel::import(new StudentsImport, $filePath, 'local');

                    // Notifikasi sukses ke Admin
                    Notification::make()
                        ->title('Import Sedang Diproses')
                        ->body('Data sedang diproses di latar belakang. Silakan refresh halaman beberapa saat lagi.')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
