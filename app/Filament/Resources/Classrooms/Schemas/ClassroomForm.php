<?php

namespace App\Filament\Resources\Classrooms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClassroomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Kelas')
                    ->schema([
                        Select::make('major_id')
                            ->label('Jurusan')
                            ->relationship('major', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('level')
                            ->label('Tingkat Kelas')
                            ->numeric()
                            ->placeholder('Contoh: 10, 11, atau 12')
                            ->required()
                            ->minValue(1)
                            ->maxValue(13),
                        TextInput::make('name')
                            ->label('Nama Kelas')
                            ->placeholder('Contoh: XII RPL 1')
                            ->required()
                            ->maxLength(50),
                    ])->columns(3),
            ]);
    }
}
