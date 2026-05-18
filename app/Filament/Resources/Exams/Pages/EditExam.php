<?php

namespace App\Filament\Resources\Exams\Pages;

use App\Actions\GenerateExamDocumentsAction;
use App\Filament\Resources\Exams\ExamResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditExam extends EditRecord
{
    protected static string $resource = ExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cetak_berita_acara')
                ->label('Cetak Berita Acara')
                ->icon('heroicon-o-document-text')
                ->color('info')
                ->action(function () {
                    $action = app(GenerateExamDocumentsAction::class);
                    return $action->generateBeritaAcara($this->record->id);
                }),

            // 2. Tombol Cetak Daftar Hadir
            Action::make('cetak_daftar_hadir')
                ->label('Cetak Daftar Hadir')
                ->icon('heroicon-o-document-check')
                ->color('success')
                ->action(function () {
                    $action = app(GenerateExamDocumentsAction::class);
                    return $action->generateDaftarHadir($this->record->id);
                }),

            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
