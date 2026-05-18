<?php

namespace App\Actions;

use App\Enums\QuestionTypeEnum;
use App\Models\Question;
use App\Models\QuestionBank;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;
use Exception;

class ImportQuestionsFromDocxAction
{
    /**
     * Mengekstrak teks dari DOCX dan menyimpannya sebagai soal
     */
    public function execute(int $questionBankId, string $filePath): int
    {
        $importedCount = 0;

        try {
            // 1. Ekstrak Teks dari DOCX menggunakan PHPWord
            $phpWord = IOFactory::load($filePath);
            $fullText = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if ($element instanceof TextRun) {
                        foreach ($element->getElements() as $textElement) {
                            if ($textElement instanceof Text) {
                                $fullText .= $textElement->getText();
                            }
                        }
                        $fullText .= "\n";
                    } elseif (method_exists($element, 'getText')) {
                        $fullText .= $element->getText() . "\n";
                    }
                }
            }

            // 2. Parsing Text Menggunakan Aiken Format Logic
            $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $fullText));

            $currentQuestion = '';
            $currentOptions = [];
            $parsedQuestions = [];

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue; // Skip baris kosong
                }

                // Cek apakah baris adalah kunci jawaban (Contoh: "ANSWER: B")
                if (preg_match('/^ANSWER:\s*([A-E])$/i', $line, $matches)) {
                    $correctLetter = strtoupper($matches[1]);

                    // Simpan soal yang sudah lengkap di-parsing ke array
                    if (!empty($currentQuestion) && count($currentOptions) > 0) {
                        $parsedQuestions[] = [
                            'question' => $currentQuestion,
                            'options' => $currentOptions,
                            'answer' => $correctLetter
                        ];
                    }

                    // Reset untuk soal berikutnya
                    $currentQuestion = '';
                    $currentOptions = [];
                }
                // Cek apakah baris adalah pilihan jawaban (Contoh: "A. Jawaban" atau "A) Jawaban")
                elseif (preg_match('/^([A-E])[.)]\s+(.+)$/i', $line, $matches)) {
                    $letter = strtoupper($matches[1]);
                    $optionText = trim($matches[2]);
                    $currentOptions[$letter] = $optionText;
                }
                // Jika bukan jawaban dan bukan opsi, maka ini adalah teks pertanyaan
                else {
                    // Gabungkan jika pertanyaan lebih dari 1 baris
                    $currentQuestion .= ($currentQuestion === '' ? '' : '<br>') . htmlspecialchars($line);
                }
            }

            // 3. Masukkan ke Database dengan DB Transaction
            // 3. Masukkan ke Database dengan DB Transaction
            DB::transaction(function () use ($questionBankId, $parsedQuestions, &$importedCount) {
                foreach ($parsedQuestions as $item) {
                    // Buat Soal (Bungkus dengan tag <p>)
                    $question = Question::create([
                        'question_bank_id' => $questionBankId,
                        'type' => QuestionTypeEnum::MULTIPLE_CHOICE->value,
                        'content' => '<p>' . $item['question'] . '</p>',
                        'weight' => 1,
                    ]);

                    // Buat Pilihan Jawaban (Bungkus dengan tag <p> dan aman dari tag HTML nyasar)
                    foreach ($item['options'] as $letter => $optionText) {
                        $question->options()->create([
                            'content' => '<p>' . htmlspecialchars($optionText) . '</p>',
                            'is_correct' => ($letter === $item['answer']),
                        ]);
                    }
                    $importedCount++;
                }
            });
        } catch (Exception $e) {
            Log::error("Gagal parsing DOCX: " . $e->getMessage());
            throw new Exception("Format file Word tidak valid atau terjadi kesalahan sistem.");
        }

        return $importedCount;
    }
}
