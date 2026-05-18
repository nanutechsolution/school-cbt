<?php

namespace App\Filament\Pages;

use App\Settings\SchoolSettings;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use UnitEnum;

class ManageSchoolSettings extends Page implements HasSchemas, HasActions
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = SchoolSettings::class;

    protected static string|UnitEnum|null $navigationGroup = 'Konfigurasi';

    protected static ?string $navigationLabel = 'Pengaturan Sekolah';

    protected static ?string $title = 'Pengaturan Profil Sekolah';
    protected  string $view = 'filament.pages.manage-school-settings';

    use InteractsWithSchemas,   InteractsWithActions;

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                Section::make('Informasi Kop Surat Dokumen Resmi')
                    ->description('Sesuaikan identitas sekolah untuk kebutuhan kop surat PDF otomatis.')
                    ->schema([
                        TextInput::make('foundation_name')
                            ->label('Nama Yayasan / Instansi Induk')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('school_name')
                            ->label('Nama Lembaga / Sekolah')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('address')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Nomor Telepon / Kontak Resmi')
                            ->required()
                            ->maxLength(50),
                    ])->columns(2),
            ]);
    }

    /**
     * Praktik Terbaik Filament: 
     * Mendefinisikan tombol submit melalui method getFormActions
     */
    public function saveAction(): Action
    {
        return Action::make('save')
            ->label('Simpan Pengaturan')
            ->submit('save')
            ->icon('heroicon-m-check-circle')
            ->color('primary')
            ->size('lg')
            ->keyBindings(['mod+s']);
    }

    public function save(SchoolSettings $settings): void
    {
        $data = $this->form->getState();

        $settings->fill($data);
        $settings->save();

        Notification::make()
            ->title('Pengaturan berhasil disimpan!')
            ->success()
            ->send();
    }
}
