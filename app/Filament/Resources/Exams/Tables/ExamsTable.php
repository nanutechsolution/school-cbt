<?php

namespace App\Filament\Resources\Exams\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ExamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Jadwal')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('questionBank.name')
                    ->label('Paket Soal')
                    ->limit(30)
                    ->sortable(),
                TextColumn::make('start_time')
                    ->label('Mulai')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('duration')
                    ->label('Durasi')
                    ->formatStateUsing(fn($state) => $state . ' Menit')
                    ->badge()
                    ->color('info'),
                TextColumn::make('token')
                    ->label('Token')
                    ->badge()
                    ->color('danger')
                    ->copyable() // Bisa di-copy dengan 1 kali klik oleh Admin/Guru
                    ->copyMessage('Token disalin!'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                TrashedFilter::make(),
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
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
