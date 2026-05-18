<?php

namespace App\Filament\Resources\Subjects\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SubjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Mata Pelajaran')
                    ->schema([
                        TextInput::make('code')
                            ->label('Kode Mapel')
                            ->placeholder('Contoh: MTK')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                        TextInput::make('name')
                            ->label('Nama Mata Pelajaran')
                            ->placeholder('Contoh: Matematika Wajib')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),
            ]);
    }
}
