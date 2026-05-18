<?php

namespace App\Filament\Resources\QuestionBanks\RelationManagers;

use App\Enums\QuestionTypeEnum;
use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';
    protected static ?string $title = 'Daftar Soal';
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Soal')
                    ->schema([
                        Select::make('type')
                            ->label('Tipe Soal')
                            ->options(collect(QuestionTypeEnum::cases())->mapWithKeys(fn($enum) => [$enum->value => $enum->label()]))
                            ->required()
                            ->live() // Memicu re-render UI saat tipe diubah
                            ->default(QuestionTypeEnum::MULTIPLE_CHOICE->value),

                        TextInput::make('weight')
                            ->label('Bobot Nilai')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->minValue(1),

                        RichEditor::make('content')
                            ->label('Teks Soal')
                            ->required()
                            ->fileAttachmentsDisk('local')
                            ->fileAttachmentsDirectory('questions')
                            ->fileAttachmentsVisibility('private')
                            ->toolbarButtons([
                                'attachFiles',
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'subscript',
                                'superscript',
                                'bulletList',
                                'orderedList',
                                'table',
                            ])
                            ->columnSpanFull(),

                        FileUpload::make('audio_path')
                            ->label('Audio Listening (Opsional)')
                            ->disk('local')
                            ->directory('questions/audio')
                            ->visibility('private')
                            ->acceptedFileTypes(['audio/mpeg', 'audio/wav']),

                        FileUpload::make('image_path')
                            ->label('Gambar Tambahan (Opsional)')
                            ->disk('local')
                            ->directory('questions/images')
                            ->visibility('private')
                            ->image(),
                    ])->columns(2),

                Section::make('Pilihan Jawaban')
                    ->description('Tambahkan opsi jawaban dan tandai mana yang merupakan Kunci Jawaban yang benar.')
                    ->schema([
                        Repeater::make('options') // Relasi ke tabel question_options
                            ->relationship()
                            ->label('Daftar Pilihan')
                            ->schema([
                                RichEditor::make('content')
                                    ->label('Teks Pilihan')
                                    ->required()
                                    ->toolbarButtons([
                                        'bold',
                                        'italic',
                                        'underline',
                                        'strike',
                                        'subscript',
                                        'superscript',
                                    ])
                                    ->columnSpanFull()
                                    ->disableToolbarButtons(['attachFiles']),
                                FileUpload::make('image_path')
                                    ->label('Gambar (Opsional)')
                                    ->disk('local')
                                    ->directory('options/images')
                                    ->visibility('private')
                                    ->image(),
                                Toggle::make('is_correct')
                                    ->label('Kunci Jawaban Benar')
                                    ->onColor('success')
                                    ->offColor('danger')
                                    ->default(false),
                            ])
                            ->columns(2)
                            ->itemLabel(fn(array $state): ?string => $state['is_correct'] ? 'Kunci Jawaban (Benar)' : 'Opsi (Salah)')
                            ->visible(fn(Get $get) => in_array($get('type'), [
                                QuestionTypeEnum::MULTIPLE_CHOICE->value,
                                QuestionTypeEnum::TRUE_FALSE->value
                            ])),
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                TextColumn::make('type')
                    ->label('Tipe Soal')
                    ->badge()
                    ->color(fn(QuestionTypeEnum $state): string => match ($state) {
                        QuestionTypeEnum::MULTIPLE_CHOICE => 'primary',
                        QuestionTypeEnum::ESSAY => 'warning',
                        QuestionTypeEnum::TRUE_FALSE => 'success',
                    })
                    ->formatStateUsing(fn(QuestionTypeEnum $state): string => $state->label()),
                TextColumn::make('content')
                    ->label('Potongan Soal')
                    ->limit(50)
                    ->html(), // Karena menggunakan RichEditor, tampilkan sebagai HTML
                TextColumn::make('weight')
                    ->label('Bobot')
                    ->sortable()
                    ->badge(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->relationship('subject', 'name')
                    ->searchable(),
                TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->headerActions([
                Action::make('import_docx')
                    ->label('Import dari Word')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->schema([
                        FileUpload::make('file')
                            ->label('Upload File .docx')
                            ->disk('local')
                            ->directory('imports/docx')
                            ->visibility('private')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' // MIME khusus DOCX
                            ])
                            ->required()
                            ->helperText('Pastikan format file Word Anda mengikuti standar Aiken Format (Contoh: Soal -> A. Jawaban -> ANSWER: A).'),
                    ])
                    ->action(function (array $data, RelationManager $livewire) {
                        try {
                            $filePath = storage_path('app/private/' . $data['file']);
                            $questionBankId = $livewire->getOwnerRecord()->id; // Dapatkan ID paket soal saat ini

                            $action = app(\App\Actions\ImportQuestionsFromDocxAction::class);
                            $count = $action->execute($questionBankId, $filePath);

                            \Filament\Notifications\Notification::make()
                                ->title('Import Berhasil')
                                ->body("Berhasil mengimpor {$count} soal dari file Word.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->title('Import Gagal')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                CreateAction::make()
                    ->label('Tambah Soal')
                    ->icon('heroicon-o-plus-circle')
                    ->slideOver(),
                // AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make()
                    ->slideOver(),
                // DissociateAction::make(),
                DeleteAction::make(),
                ForceDeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]));
    }
}
