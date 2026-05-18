<?php

namespace App\Imports;

use App\Actions\RegisterStudentAction;
use App\DTOs\StudentRegistrationDTO;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Exception;

class StudentsImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue
{
    /**
     * Memproses koleksi baris dari Excel
     */
    public function collection(Collection $rows)
    {
        // Panggil Action Class dari Container
        $action = app(RegisterStudentAction::class);

        foreach ($rows as $row) {
            try {
                // Skip jika baris kosong atau NIS tidak ada
                if (empty($row['nis']) || empty($row['nama'])) {
                    continue;
                }

                // Mapping Header Excel ke DTO
                // Asumsi Header Excel: nama, nis, password, classroom_id, gender, religion
                $dto = new StudentRegistrationDTO(
                    name: $row['nama'],
                    nis: $row['nis'],
                    password: $row['password'] ?? $row['nis'], // Default pass = NIS jika kosong
                    classroomId: $row['classroom_id'],
                    nisn: $row['nisn'] ?? null,
                    gender: strtoupper($row['gender'] ?? ''), // Pastikan L atau P
                    religion: strtolower($row['religion'] ?? ''),
                );

                // Eksekusi core logic (Membuat User, Profil, dan Role Spatie)
                $action->execute($dto);

            } catch (Exception $e) {
                // Catat ke log jika ada 1 baris yang gagal (misal NIS duplikat), 
                // agar baris lain tetap diproses tanpa menghentikan antrean.
                Log::error("Gagal import siswa NIS {$row['nis']}: " . $e->getMessage());
            }
        }
    }

    /**
     * Memotong data Excel menjadi batch 100 baris agar RAM tidak jebol
     */
    public function chunkSize(): int
    {
        return 100;
    }
}