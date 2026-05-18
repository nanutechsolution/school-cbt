<?php

namespace App\Filament\Resources\ExamSessions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ExamSessionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Sesi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('start_time')
                    ->label('Waktu Mulai')
                    ->time('H:i')
                    ->sortable()
                    ->badge()
                    ->color('success'),
                TextColumn::make('end_time')
                    ->label('Waktu Selesai')
                    ->time('H:i')
                    ->sortable()
                    ->badge()
                    ->color('danger'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
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
