<?php

namespace App\Filament\Resources\QuestionBanks\Schemas;

use App\Enums\QuestionTypeEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class QuestionBankForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->schema([
                    Section::make('Informasi Paket Soal')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama Paket Soal')
                                ->placeholder('Contoh: UTS Matematika Ganjil')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),
                            Select::make('subject_id')
                                ->label('Mata Pelajaran')
                                ->relationship('subject', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            Select::make('level')
                                ->label('Tingkat Kelas')
                                ->options([
                                    10 => 'Tingkat 10',
                                    11 => 'Tingkat 11',
                                    12 => 'Tingkat 12',
                                ])
                                ->required(),
                            Select::make('teacher_id')
                                ->label('Guru Pembuat')
                                ->options(
                                    \App\Models\Teacher::with('user')->get()->pluck('user.name', 'id')
                                )
                                ->searchable()
                                ->preload()
                                ->required()
                                ->default(fn() => auth()->user()->teacher?->id)
                                ->helperText('Penanggung jawab paket soal ini.'),
                        ])->columns(2),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Pengaturan Ujian')
                        ->schema([
                            Toggle::make('is_active')
                                ->label('Status Aktif')
                                ->default(true)
                                ->helperText('Jika nonaktif, tidak bisa digunakan di ujian.'),
                            Toggle::make('randomize_questions')
                                ->label('Acak Urutan Soal')
                                ->default(true),
                            Toggle::make('randomize_options')
                                ->label('Acak Pilihan Jawaban (PG)')
                                ->default(true),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }
}
