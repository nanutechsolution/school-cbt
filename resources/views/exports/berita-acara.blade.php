<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara - {{ $exam->name }}</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12px;
            line-height: 1.6;
            margin: 30px;
        }
        .header-section {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
        }
        .school-header-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .school-subtitle {
            font-size: 12px;
            margin-top: 2px;
        }
        .doc-title {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 15px;
            text-transform: uppercase;
        }
        .doc-number {
            font-size: 11px;
            margin-top: 2px;
        }
        .paragraf-isi {
            text-align: justify;
            text-indent: 30px;
            margin-bottom: 15px;
        }
        .meta-table {
            width: 100%;
            margin-left: 30px;
            margin-bottom: 20px;
        }
        .meta-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        .sign-table {
            width: 100%;
            margin-top: 40px;
            border-collapse: collapse;
        }
        .sign-td {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
    </style>
</head>
<body>

    <!-- KOP SURAT SEKOLAH -->
    <div class="header-section">
        <span class="school-header-title">YAYASAN PENDIDIKAN NASIONAL</span><br>
        <span class="school-header-title" style="font-size: 18px;">{{ $school_name }}</span><br>
        <span class="school-subtitle">Jl. Raya Pendidikan No. 45, Jakarta Selatan | Telp: (021) 777-8888</span>
    </div>

    <!-- JUDUL DOKUMEN -->
    <div style="text-align: center; margin-bottom: 25px;">
        <span class="doc-title">BERITA ACARA PELAKSANAAN</span><br>
        <span class="doc-title" style="font-size:13px; text-decoration: none; margin-top: 0;">UJIAN COMPUTER BASED TEST (CBT)</span><br>
        <span class="doc-number">Nomor: B/{{ now()->format('Y/m') }}/CBT-SMK/01</span>
    </div>

    <!-- ISI DOKUMEN -->
    <p class="paragraf-isi">
        Pada hari ini, <strong>{{ $date_now }}</strong>, telah diselenggarakan Ujian Computer Based Test (CBT) Tingkat Sekolah untuk mata pelajaran yang tertera di bawah ini:
    </p>

    <table class="meta-table">
        <tr>
            <td style="width: 25%;">Nama Jadwal Ujian</td>
            <td style="width: 3%;">:</td>
            <td style="font-weight: bold;">{{ $exam->name }}</td>
        </tr>
        <tr>
            <td>Mata Pelajaran</td>
            <td>:</td>
            <td>{{ $exam->questionBank->subject->name }} ({{ $exam->questionBank->subject->code }})</td>
        </tr>
        <tr>
            <td>Waktu Mulai / Durasi</td>
            <td>:</td>
            <td>{{ $exam->start_time->format('H:i') }} WIB / {{ $exam->duration }} Menit</td>
        </tr>
        <tr>
            <td>Sasaran Kelas</td>
            <td>:</td>
            <td>
                @foreach($exam->classrooms as $c)
                    {{ $c->name }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
            </td>
        </tr>
        <tr>
            <td>Jumlah Peserta Terdaftar</td>
            <td>:</td>
            <td>{{ $total_students }} Peserta</td>
        </tr>
    </table>

    <p class="paragraf-isi">
        Pelaksanaan ujian terpantau berjalan secara kondusif dan aman. Seluruh riwayat aktivitas login dan potensi kecurangan siswa telah dicatat secara otomatis oleh sistem server CBT.
    </p>

    <!-- CATATAN KEJADIAN KHUSUS -->
    <p style="font-weight: bold; margin-bottom: 5px;">Catatan Selama Pelaksanaan Ujian / Kejadian Khusus:</p>
    <div style="border: 1px solid #000; height: 100px; padding: 10px; font-style: italic; color: #555;">
        Tidak ada kejadian khusus. Pelaksanaan berjalan dengan tertib dan lancar.
    </div>

    <!-- TANDA TANGAN -->
    <table class="sign-table">
        <tr>
            <td class="sign-td">
                Mengetahui,<br>
                Kepala Sekolah SMK<br>
                <br><br><br><br>
                ( ___________________________ )<br>
                NIP. .........................................
            </td>
            <td class="sign-td">
                Jakarta, {{ now()->translatedFormat('d F Y') }}<br>
                Pengawas Ruangan CBT<br>
                <br><br><br><br>
                ( ___________________________ )<br>
                NIP. .........................................
            </td>
        </tr>
    </table>

</body>
</html>