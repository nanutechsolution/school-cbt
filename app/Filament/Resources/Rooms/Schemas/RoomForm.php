<?php

namespace App\Filament\Resources\Rooms\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Ruangan')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Ruangan')
                            ->placeholder('Contoh: Lab Komputer 1')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        TextInput::make('capacity')
                            ->label('Kapasitas Peserta')
                            ->numeric()
                            ->required()
                            ->default(30)
                            ->minValue(1),
                    ])->columns(2),
            ]);
    }
}
