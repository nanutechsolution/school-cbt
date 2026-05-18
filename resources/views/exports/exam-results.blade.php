<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Nilai - {{ $exam->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 20px;
        }
        .header-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        .header-sub {
            text-align: center;
            font-size: 11px;
            color: #555;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
        }
        .info-table td {
            padding: 3px 0;
        }
        .result-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .result-table th, .result-table td {
            border: 1px solid #666;
            padding: 6px 8px;
        }
        .result-table th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-align: center;
        }
        .center-cell {
            text-align: center;
        }
        .score-cell {
            text-align: right;
            font-weight: bold;
            font-size: 12px;
            color: #1e3a8a;
        }
        .sign-area {
            width: 100%;
            margin-top: 40px;
        }
    </style>
</head>
<body>

    <div class="header-title">LAPORAN REKAPITULASI HASIL UJIAN</div>
    <div class="header-sub">{{ strtoupper($school_name) }} | SELESAI PADA {{ now()->format('Y') }}</div>

    <!-- Informasi Detail Ujian -->
    <table class="info-table">
        <tr>
            <td style="width: 18%;">Mata Pelajaran</td>
            <td style="width: 2%;">:</td>
            <td style="font-weight: bold; width: 45%;">{{ $exam->questionBank->subject->name }} ({{ $exam->questionBank->subject->code }})</td>
            <td style="width: 15%;">Tingkat Kelas</td>
            <td style="width: 2%;">:</td>
            <td style="font-weight: bold;">Tingkat {{ $exam->questionBank->level }}</td>
        </tr>
        <tr>
            <td>Nama Paket Soal</td>
            <td>:</td>
            <td>{{ $exam->questionBank->name }}</td>
            <td>Durasi Pelaksanaan</td>
            <td>:</td>
            <td>{{ $exam->duration }} Menit</td>
        </tr>
    </table>

    <!-- Tabel Hasil Ujian Siswa -->
    <table class="result-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 40%;">Nama Lengkap Peserta</th>
                <th style="width: 15%;">Kelas</th>
                <th style="width: 10%;">Benar (PG)</th>
                <th style="width: 10%;">Salah (PG)</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 10%;">Nilai Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse($results as $index => $res)
                <tr>
                    <td class="center-cell">{{ $index + 1 }}</td>
                    <td style="font-weight: bold; text-transform: uppercase;">{{ $res['name'] }}</td>
                    <td class="center-cell">{{ $res['classroom'] }}</td>
                    <td class="center-cell" style="color: #16a34a; font-weight: bold;">{{ $res['correct'] }}</td>
                    <td class="center-cell" style="color: #dc2626;">{{ $res['incorrect'] }}</td>
                    <td class="center-cell">
                        <span style="font-size: 9px; font-weight: bold; text-transform: uppercase;">{{ $res['status'] }}</span>
                    </td>
                    <td class="score-cell">{{ $res['score'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center-cell" style="padding: 20px; color: #888;">Belum ada data nilai peserta yang dikirimkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Area Tanda Tangan Pengesahan -->
    <table class="sign-area" style="border: none;">
        <tr>
            <td style="width: 60%; border: none;"></td>
            <td style="text-align: center; border: none;">
                Jakarta, {{ now()->translatedFormat('d F Y') }}<br>
                Guru Mata Pelajaran,<br>
                <br><br><br><br>
                <strong>( ___________________________ )</strong><br>
                NIP. .........................................
            </td>
        </tr>
    </table>

</body>
</html>
```
eof

---

### 4. Implementasi `ExamResultResource` (Panel Koreksi Essay & Rekap Nilai)

Kita buat sebuah *Resource* baru di Filament khusus untuk Guru. Resource ini akan menampilkan seluruh sesi pengerjaan siswa yang sudah selesai. Guru bisa mengklik **Koreksi Essay** untuk membuka laci samping (*slide-over*) yang memuat daftar soal uraian yang dijawab oleh siswa bersangkutan, membaca jawabannya, dan langsung mengetikkan skor secara asinkron!

Jalankan perintah ini terlebih dahulu di terminal:
```bash