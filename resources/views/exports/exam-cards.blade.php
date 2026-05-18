<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Kartu Peserta - {{ $classroom->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .page-break {
            page-break-after: always;
        }
        .card-table {
            width: 100%;
            border-collapse: collapse;
        }
        .card-td {
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }
        .card-box {
            border: 2px solid #1e293b;
            border-radius: 8px;
            padding: 12px;
            background-color: #ffffff;
            height: 190px;
            position: relative;
        }
        .card-header {
            border-bottom: 2px double #1e293b;
            padding-bottom: 6px;
            margin-bottom: 8px;
            text-align: center;
        }
        .school-title {
            font-size: 10px;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
        }
        .doc-title {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
        }
        .card-body-table {
            width: 100%;
            margin-top: 5px;
        }
        .card-body-table td {
            padding: 3px 0;
            font-size: 10px;
        }
        .label-cell {
            width: 30%;
            color: #64748b;
        }
        .value-cell {
            font-weight: bold;
            color: #0f172a;
        }
        .credential-box {
            margin-top: 8px;
            background-color: #f1f5f9;
            border: 1px dashed #94a3b8;
            padding: 6px;
            border-radius: 4px;
        }
        .credential-title {
            font-size: 8px;
            color: #475569;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .stamp-area {
            position: absolute;
            bottom: 12px;
            right: 12px;
            text-align: center;
            font-size: 8px;
        }
    </style>
</head>
<body>

    <table class="card-table">
        @foreach($students->chunk(2) as $row)
            <tr>
                @foreach($row as $student)
                    <td class="card-td">
                        <div class="card-box">
                            <!-- Kop Kartu -->
                            <div class="card-header">
                                <span class="school-title">{{ $school_name }}</span><br>
                                <span class="doc-title">KARTU PESERTA CBT {{ $year }}</span>
                            </div>

                            <!-- Detail Data Siswa -->
                            <table class="card-body-table">
                                <tr>
                                    <td class="label-cell">Nama</td>
                                    <td class="value-cell">: {{ strtoupper($student->user->name) }}</td>
                                </tr>
                                <tr>
                                    <td class="label-cell">NIS/NISN</td>
                                    <td class="value-cell">: {{ $student->nis }} / {{ $student->nisn ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="label-cell">Kelas / Ruang</td>
                                    <td class="value-cell">: {{ $classroom->name }} / {{ $student->room?->name ?? 'Lab Utama' }}</td>
                                </tr>
                            </table>

                            <!-- Kredensial Login yang Aman -->
                            <div class="credential-box">
                                <div class="credential-title">Kredensial Akses Ujian (Rahasia)</div>
                                <table style="width: 100%; border: none;">
                                    <tr>
                                        <td style="font-size: 10px; width: 50%;"><strong>Username:</strong> {{ $student->nis }}</td>
                                        <td style="font-size: 10px; width: 50%;"><strong>Password:</strong> {{ $student->nis }}</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Tanda Tangan / Stempel -->
                            <div class="stamp-area">
                                Kepala Sekolah<br>
                                <br><br>
                                <strong>PANITIA CBT</strong>
                            </div>
                        </div>
                    </td>
                @endforeach
                @if(count($row) < 2)
                    <td style="width: 50%;"></td>
                @endif
            </tr>
            
            {{-- Atur halaman baru setiap 8 kartu (4 baris) --}}
            @if($loop->iteration % 4 == 0 && !$loop->last)
                </table>
                <div class="page-break"></div>
                <table class="card-table">
            @endif
        @endforeach
    </table>

</body>
</html>