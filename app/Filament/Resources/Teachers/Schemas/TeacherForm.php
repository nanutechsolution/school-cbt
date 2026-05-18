<?php

namespace App\Filament\Resources\Teachers\Schemas;

use App\Enums\GenderEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema; 

class TeacherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Guru')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required(),
                        TextInput::make('nip')
                            ->label('NIP / ID Guru')
                            ->required()
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create'),
                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options(collect(GenderEnum::cases())->mapWithKeys(fn($enum) => [$enum->value => $enum->label()]))
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
