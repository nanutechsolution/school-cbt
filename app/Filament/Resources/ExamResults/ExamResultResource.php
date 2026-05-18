<?php

namespace App\Filament\Resources\ExamResults;

use App\Actions\GenerateExamDocumentsAction;
use App\Filament\Resources\ExamResults\Pages\ManageExamResults;
use App\Models\ExamAnswer;
use App\Models\ExamAttempt;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ExamResultResource extends Resource
{
    protected static ?string $model = ExamAttempt::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;
    protected static ?string $recordTitleAttribute = 'name';

    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Ujian';

    protected static ?string $navigationLabel = 'Rekap & Koreksi Nilai';

    protected static ?string $modelLabel = 'Hasil Nilai Siswa';

    protected static ?int $navigationSort = 4;

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('status', ['submitted', 'suspended']))
            ->columns([
                TextColumn::make('student.user.name')
                    ->label('Nama Siswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('student.classroom.name')
                    ->label('Kelas')
                    ->sortable(),
                TextColumn::make('exam.name')
                    ->label('Jadwal Ujian')
                    ->searchable()
                    ->limit(25),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'submitted' => 'success',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),
                // Penghitungan Skor Akhir Dinamis (PG + Essay)
                TextColumn::make('final_score')
                    ->label('Nilai Akhir')
                    ->state(function (ExamAttempt $record): float {
                        $questions = $record->exam->questionBank->questions()->with('options')->get();
                        $totalWeight = $questions->sum('weight');
                        $earnedWeight = 0;

                        $answers = ExamAnswer::where('exam_attempt_id', $record->id)->get()->keyBy('question_id');

                        foreach ($questions as $q) {
                            $ans = $answers->get($q->id);
                            if ($q->type->value === 'multiple_choice' || $q->type->value === 'true_false') {
                                if ($ans && $ans->question_option_id) {
                                    $selected = $q->options->firstWhere('id', $ans->question_option_id);
                                    if ($selected && $selected->is_correct) {
                                        $earnedWeight += $q->weight;
                                    }
                                }
                            } elseif ($q->type->value === 'essay') {
                                if ($ans && $ans->score !== null) {
                                    $earnedWeight += $ans->score;
                                }
                            }
                        }

                        $score = $totalWeight > 0 ? round(($earnedWeight / $totalWeight) * 100, 2) : 0;

                        // Simpan/Cache ke kolom final_score agar performa database stabil
                        if ($record->final_score !== $score) {
                            $record->update(['final_score' => $score]);
                        }

                        return $score;
                    })
                    ->badge()
                    ->color('primary')
                    ->alignment('center'),
            ])
            ->filters([
                SelectFilter::make('exam_id')
                    ->label('Filter Sesi Ujian')
                    ->relationship('exam', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('koreksi_essay')
                        ->label('Koreksi Essay')
                        ->icon('heroicon-o-pencil-square')
                        ->color('warning')
                        ->slideOver()
                        ->form(function (ExamAttempt $record) {
                            // Cari seluruh soal essay dari ujian ini
                            $essayQuestions = $record->exam->questionBank->questions()
                                ->where('type', 'essay')
                                ->get();

                            if ($essayQuestions->isEmpty()) {
                                return [
                                    Placeholder::make('no_essay')
                                        ->label('Info Soal')
                                        ->content('Tidak ada tipe soal Essay/Uraian di dalam paket ujian ini. Seluruh nilai pilihan ganda sudah dikalkulasi otomatis oleh sistem.'),
                                ];
                            }

                            $formSchema = [];
                            foreach ($essayQuestions as $index => $q) {
                                // Ambil jawaban tertulis siswa untuk nomor ini
                                $ans = ExamAnswer::where('exam_attempt_id', $record->id)
                                    ->where('question_id', $q->id)
                                    ->first();

                                $formSchema[] = Section::make('Soal Nomor ' . ($index + 1))
                                    ->description('Maksimal Bobot Nilai Soal Ini: ' . $q->weight)
                                    ->schema([
                                        Placeholder::make('question_content_' . $q->id)
                                            ->label('Pertanyaan Soal')
                                            ->content(new \Illuminate\Support\HtmlString($q->content)),

                                        Placeholder::make('student_answer_' . $q->id)
                                            ->label('Jawaban Tertulis Siswa')
                                            ->content($ans ? ($ans->essay_answer ?? '(Tidak Dijawab)') : '(Tidak Dijawab)'),

                                        TextInput::make('scores.' . $q->id)
                                            ->label('Berikan Skor Nilai')
                                            ->numeric()
                                            ->required()
                                            ->minValue(0)
                                            ->maxValue($q->weight)
                                            ->default($ans ? $ans->score : 0)
                                            ->helperText('Masukkan angka skor antara 0 s.d ' . $q->weight),
                                    ]);
                            }

                            return $formSchema;
                        })
                        ->action(function (ExamAttempt $record, array $data) {
                            if (!isset($data['scores'])) {
                                return;
                            }

                            // Simpan skor essay satu per satu ke database
                            foreach ($data['scores'] as $questionId => $scoreValue) {
                                ExamAnswer::updateOrCreate(
                                    [
                                        'exam_attempt_id' => $record->id,
                                        'question_id' => $questionId,
                                    ],
                                    [
                                        'score' => $scoreValue,
                                    ]
                                );
                            }

                            // Trigger hitung ulang skor akhir setelah essay dikoreksi
                            $record->update(['final_score' => null]);

                            \Filament\Notifications\Notification::make()
                                ->title('Koreksi Tersimpan')
                                ->body('Hasil koreksi jawaban essay berhasil disimpan dan nilai akhir siswa telah diperbarui otomatis.')
                                ->success()
                                ->send();
                        }),

                    Action::make('cetak_pdf')
                        ->label('Cetak Nilai')
                        ->icon('heroicon-o-document-arrow-down')
                        ->color('success')
                        ->action(function (ExamAttempt $record) {
                            try {
                                $action = app(GenerateExamDocumentsAction::class);
                                $response = $action->generateExamResultsPdf($record->exam_id);

                                return response()->streamDownload(
                                    fn() => print($response->getContent()),
                                    "Rekap_Nilai_{$record->exam->name}.pdf"
                                );
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Pencetakan Gagal')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                ])
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageExamResults::route('/'),
        ];
    }
}
