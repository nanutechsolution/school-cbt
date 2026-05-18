<?php

namespace App\Filament\Resources\Majors\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MajorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Jurusan')
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Jurusan')
                            ->placeholder('Contoh: RPL')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                        TextInput::make('name')
                            ->label('Nama Jurusan')
                            ->placeholder('Contoh: Rekayasa Perangkat Lunak')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }
}
