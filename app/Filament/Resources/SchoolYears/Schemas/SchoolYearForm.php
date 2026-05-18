<?php

namespace App\Filament\Resources\SchoolYears\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SchoolYearForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Tahun Ajaran')
                    ->schema([
                        TextInput::make('name')
                            ->label('Tahun Ajaran')
                            ->placeholder('Contoh: 2023/2024')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText('Hanya satu tahun ajaran yang boleh aktif dalam satu waktu.')
                            ->default(false),
                    ])->columns(1),
            ]);
    }
}
