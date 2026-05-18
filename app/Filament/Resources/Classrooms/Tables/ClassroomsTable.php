<?php

namespace App\Filament\Resources\Classrooms\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ClassroomsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Kelas')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('level')
                    ->label('Tingkat')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('major.code')
                    ->label('Jurusan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning'),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('major_id')
                    ->label('Filter Jurusan')
                    ->relationship('major', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('level')
                    ->label('Filter Tingkat')
                    ->options([
                        '10' => 'Tingkat 10',
                        '11' => 'Tingkat 11',
                        '12' => 'Tingkat 12',
                    ]),
            ])
            ->recordActions([
                Action::make('cetak_kartu')
                    ->label('Cetak Kartu')
                    ->icon('heroicon-o-identification')
                    ->color('warning')
                    ->action(function (\App\Models\Classroom $record) {
                        try {
                            $action = app(\App\Actions\GenerateExamDocumentsAction::class);

                            // Mendapatkan response biner PDF dari action class
                            $response = $action->generateExamCards($record->id);

                            // Mengembalikan streamed download untuk mencegah malformed UTF-8 JSON error
                            return response()->streamDownload(
                                fn() => print($response->getContent()),
                                "Kartu_Peserta_{$record->name}.pdf"
                            );
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Pencetakan Gagal')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
