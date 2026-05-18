<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Hadir - {{ $exam->name }}</title>
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
            color: #666;
            margin-bottom: 15px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 11px;
        }
        .info-table td {
            padding: 2px 0;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .data-table th, .data-table td {
            border: 1px solid #333;
            padding: 8px 6px;
        }
        .data-table th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .center-cell {
            text-align: center;
        }
        .signature-cell-left {
            text-align: left;
            padding-left: 20px;
            font-size: 10px;
        }
        .signature-cell-right {
            text-align: center;
            padding-right: 20px;
            font-size: 10px;
        }
    </style>
</head>
<body>

    <div class="header-title">DAFTAR HADIR PESERTA UJIAN CBT</div>
    <div class="header-sub">{{ strtoupper($school_name) }} | TAHUN AJARAN {{ now()->format('Y') }}/{{ now()->addYear()->format('Y') }}</div>

    <!-- Informasi Pelaksanaan -->
    <table class="info-table">
        <tr>
            <td style="width: 15%;">Mata Pelajaran</td>
            <td style="width: 2%;">:</td>
            <td style="font-weight: bold; width: 45%;">{{ $exam->questionBank->subject->name }}</td>
            <td style="width: 15%;">Waktu Mulai</td>
            <td style="width: 2%;">:</td>
            <td style="font-weight: bold;">{{ $exam->start_time->format('H:i') }} WIB</td>
        </tr>
        <tr>
            <td>Nama Jadwal</td>
            <td>:</td>
            <td>{{ $exam->name }}</td>
            <td>Durasi Ujian</td>
            <td>:</td>
            <td>{{ $exam->duration }} Menit</td>
        </tr>
    </table>

    <!-- Tabel Daftar Hadir Siswa -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">NIS</th>
                <th style="width: 45%;">Nama Lengkap Peserta</th>
                <th style="width: 10%;">Kelas</th>
                <th style="width: 20%;">Tanda Tangan / Sesi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $index => $student)
                <tr>
                    <td class="center-cell">{{ $index + 1 }}</td>
                    <td class="center-cell" style="font-family: monospace;">{{ $student->nis }}</td>
                    <td style="font-weight: bold;">{{ strtoupper($student->user->name) }}</td>
                    <td class="center-cell">{{ $student->classroom->name }}</td>
                    
                    {{-- Format Tanda Tangan Zig-zag --}}
                    @if(($index + 1) % 2 !== 0)
                        <td class="signature-cell-left">
                            {{ $index + 1 }}. .............................
                        </td>
                    @else
                        <td class="signature-cell-right">
                            {{ $index + 1 }}. .............................
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>