<?php

namespace App\Filament\Resources\ExamSessions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExamSessionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Waktu Sesi')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Sesi')
                            ->placeholder('Contoh: Sesi 1')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->columnSpanFull(),
                        TimePicker::make('start_time')
                            ->label('Waktu Mulai')
                            ->required()
                            ->seconds(false),
                        TimePicker::make('end_time')
                            ->label('Waktu Selesai')
                            ->required()
                            ->seconds(false)
                            ->after('start_time'), // Validasi waktu selesai harus setelah waktu mulai
                    ])->columns(2),
            ]);
    }
}
