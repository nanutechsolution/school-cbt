<?php

namespace App\Filament\Resources\Exams\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ExamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()->schema([
                    Section::make('Informasi Utama')
                        ->schema([
                            TextInput::make('name')
                                ->label('Nama Jadwal Ujian')
                                ->placeholder('Contoh: Pelaksanaan UTS MTK Kelas 12')
                                ->required()
                                ->maxLength(255)
                                ->columnSpanFull(),

                            Select::make('question_bank_id')
                                ->label('Paket Soal')
                                ->relationship('questionBank', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpanFull(),

                            // Fitur Multi-Select untuk menugaskan banyak kelas sekaligus!
                            Select::make('classrooms')
                                ->label('Tugaskan ke Kelas')
                                ->relationship('classrooms', 'name')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->required()
                                ->columnSpanFull(),
                        ]),
                ])->columnSpan(['lg' => 2]),

                Group::make()->schema([
                    Section::make('Waktu & Keamanan')
                        ->schema([
                            DateTimePicker::make('start_time')
                                ->label('Waktu Mulai')
                                ->required()
                                ->seconds(false),

                            DateTimePicker::make('end_time')
                                ->label('Waktu Selesai')
                                ->required()
                                ->seconds(false)
                                ->after('start_time'),

                            TextInput::make('duration')
                                ->label('Durasi Pengerjaan (Menit)')
                                ->numeric()
                                ->required()
                                ->default(90)
                                ->minValue(1),

                            TextInput::make('token')
                                ->label('Token Ujian')
                                ->disabled() // Tidak bisa diedit manual
                                ->dehydrated() // Tetap dikirim saat save meski disabled
                                ->helperText('Token akan otomatis dibuat saat disimpan.')
                                ->suffixAction( // Tombol Generate Ulang Token
                                    Action::make('generate_token')
                                        ->icon('heroicon-m-arrow-path')
                                        ->color('warning')
                                        ->action(function (Set $set) {
                                            $set('token', strtoupper(Str::random(6)));
                                        })
                                        ->visible(fn(string $context): bool => $context === 'edit')
                                        ->requiresConfirmation()
                                        ->modalHeading('Ganti Token?')
                                        ->modalDescription('Mengganti token saat ujian berlangsung akan memaksa siswa yang belum login untuk menggunakan token baru.')
                                ),

                            Toggle::make('show_result')
                                ->label('Tampilkan Nilai ke Siswa')
                                ->default(false),

                            Toggle::make('is_active')
                                ->label('Status Aktif')
                                ->default(true),
                        ]),
                ])->columnSpan(['lg' => 1]),
            ])->columns(3);
    }
}
