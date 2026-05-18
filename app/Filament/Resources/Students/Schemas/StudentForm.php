<?php

namespace App\Filament\Resources\Students\Schemas;

use App\Enums\GenderEnum;
use App\Enums\ReligionEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->schema([
                    Section::make('Informasi Pribadi & Akun')
                        ->schema([
                            // Field Name meminjam dari relasi User
                            TextInput::make('name')
                                ->label('Nama Lengkap')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('nis')
                                ->label('NIS (Nomor Induk Siswa)')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(20)
                                ->helperText('NIS juga akan digunakan sebagai Username untuk Login ujian.'),
                            TextInput::make('nisn')
                                ->label('NISN')
                                ->unique(ignoreRecord: true)
                                ->maxLength(20),
                            TextInput::make('password')
                                ->label('Password')
                                ->password()
                                ->revealable()
                                ->dehydrated(fn($state) => filled($state))
                                ->required(fn(string $context): bool => $context === 'create')
                                ->helperText('Kosongkan jika tidak ingin mengubah password (saat edit).'),
                        ])->columns(2),

                    Section::make('Detail Demografi')
                        ->schema([
                            Select::make('gender')
                                ->label('Jenis Kelamin')
                                ->options(collect(GenderEnum::cases())->mapWithKeys(fn($enum) => [$enum->value => $enum->label()]))
                                ->required(),
                            Select::make('religion')
                                ->label('Agama')
                                ->options(collect(ReligionEnum::cases())->mapWithKeys(fn($enum) => [$enum->value => $enum->label()]))
                                ->required(),
                        ])->columns(2),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Penempatan Ujian')
                        ->schema([
                            Select::make('classroom_id')
                                ->label('Kelas')
                                ->relationship('classroom', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('exam_session_id')
                                ->label('Sesi Ujian')
                                ->relationship('examSession', 'name')
                                ->searchable()
                                ->preload(),
                            Select::make('room_id')
                                ->label('Ruang Ujian')
                                ->relationship('room', 'name')
                                ->searchable()
                                ->preload(),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }
}
