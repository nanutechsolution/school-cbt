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
                    try {
                        $action = app(GenerateExamDocumentsAction::class);
                        $response = $action->generateBeritaAcara($this->record->id);

                        // Alirkan biner PDF menggunakan response stream untuk menghindari error JSON UTF-8
                        return response()->streamDownload(
                            fn() => print($response->getContent()),
                            "Berita_Acara_{$this->record->name}.pdf"
                        );
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Pencetakan Gagal')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            // 2. Tombol Cetak Daftar Hadir
            Action::make('cetak_daftar_hadir')
                ->label('Cetak Daftar Hadir')
                ->icon('heroicon-o-document-check')
                ->color('success')
                ->action(function () {
                    try {
                        $action = app(GenerateExamDocumentsAction::class);
                        $response = $action->generateDaftarHadir($this->record->id);

                        // Alirkan biner PDF menggunakan response stream untuk menghindari error JSON UTF-8
                        return response()->streamDownload(
                            fn() => print($response->getContent()),
                            "Daftar_Hadir_{$this->record->name}.pdf"
                        );
                    } catch (\Exception $e) {
                        \Filament\Notifications\Notification::make()
                            ->title('Pencetakan Gagal')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
