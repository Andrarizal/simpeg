<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Undangan</title>
    <style>
        body {
            font-size: 12pt;
            line-height: 1.15;
        }
        
        p {
            margin-top: 0;
            margin-bottom: 8px;
            text-align: justify;
        }

        .commander-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            margin-top: 15px;
        }
        .commander-table td {
            line-height: 1;
            vertical-align: middle;
        }

        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            margin-left: 50px;
        }
        .form-table td {
            line-height: 1;
            vertical-align: middle;
        }

        .bordered-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .bordered-table th, .bordered-table td {
            border: 1px solid black;
            padding: 5px 8px;
            text-align: left;
            vertical-align: middle;
        }
        .bordered-table th {
            text-align: center;
            font-weight: bold;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            text-align: center;
        }
        .signature-table td {
            width: 50%;
            vertical-align: bottom;
            padding: 0;
        }

        .footer-note {
            font-size: 10pt;
            margin-top: 20px;
        }
    </style>
</head>
@php
    $pageCount = 0;
    $commander = \App\Models\Staff::with('chair')->where('chair_id', 1)->first();
    $filePath = storage_path('app/public/' . $record->file_path);

    if (!empty($record->file_path) && file_exists($filePath)) {
        try {
            $mpdfCounter = new \Mpdf\Mpdf();
            $pageCount = $mpdfCounter->SetSourceFile($filePath);
        } catch (\Exception $e) {
            $pageCount = 0;
        }
    }
@endphp
<body>

    <table style="width: 100%; border-bottom: 3px double #000000; padding-bottom: 10px;">
        <tr>
            <td style="width: 18%; text-align: left; border: 0; vertical-align: middle;">
                <img src="{{ public_path('img/rsumpyk.png') }}" alt="Logo RS" style="width: 90px; height: 90px;">
            </td>

            <td style="width: 64%; text-align: center; border: 0; vertical-align: top;">
                <h3 style="margin: 0; font-size: 16px; font-weight: normal;">YAYASAN RSU MITRA PARAMEDIKA</h3>
                <h2 style="margin: 0; font-size: 30px; font-weight: bold;"> RSU MITRA PARAMEDIKA</h2>
                <p style="margin: 0; font-size: 14px;">
                    Jl. Raya Ngemplak, Area Sawah, Widodomartani, Kec. Ngemplak,
                </p>
                <p style="margin: 0; font-size: 14px;">
                    Sleman, Yogyakarta Telp. (0274) 4461098
                </p>
                <p style="margin: 0; font-size: 12px;">
                    <b>Web:</b> rsumipayk.co.id <b>Email:</b> rsumitraparamedika@yahoo.com
                </p>
            </td>
            <td style="width: 18%; text-align: right; border: 0; vertical-align: middle;">
                <img src="{{ public_path('img/KARS.jpg') }}" alt="Logo RS" style="width: 100px; height: 90px;">
            </td>
        </tr>
    </table>
    <div class="divider"></div>
    <br>
    <div style="text-align: center; margin-bottom: 20px;">
        <h3 style="margin: 0; text-decoration: underline; font-size: 18pt;">SURAT TUGAS</h3>
        <h6 style="margin: 0; font-weight: bold; font-size: 12pt">NO. {{ $record->reference_number }}</h6>
    </div>

    <p style="margin-bottom: 0">Yang bertanda tangan di bawah ini :</p>
    <table class="commander-table">
        <tr>
            <td width="15%">Nama</td>
            <td width="2%">:</td>
            <td>{{ $commander ? $commander->name : '-' }}</td> </tr>
        <tr>
            <td>NIK</td>
            <td>:</td>
            <td>{{ $commander ? $commander->nip : '-' }}</td>
        </tr>
        <tr>
            <td>Jabatan</td>
            <td>:</td>
            <td>{{ $commander ? $commander->chair->name : '-' }}</td>
        </tr>
    </table>

    <p style="margin-bottom: 15px">Menugaskan kepada :</p>
    <table class="bordered-table">
        <thead>
            <tr>
                <th width="5%">No.</th>
                <th width="45%">Nama</th>
                <th width="20%">NIK</th>
                <th width="30%">Jabatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($record->targetStaffs as $index => $staff)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}.</td>
                <td>{{ $staff->name }}</td>
                <td style="text-align: center">{{ $staff->nip }}</td>
                <td style="text-align: center">{{ $staff->chair->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p>Untuk Menghadiri <b>"{{ $record->duty }}"</b> pada:</p>
    <table class="form-table">
        <tr>
            <td width="25%">Hari</td>
            <td width="2%">:</td>
            <td>{{ \Carbon\Carbon::parse($record->duty_date)->translatedFormat('l') }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($record->duty_date)->translatedFormat('d F Y') }}</td>
        </tr>
        <tr>
            <td>Waktu</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($record->start_time)->translatedFormat('H.i') . ($record->end_time ? ' - ' . \Carbon\Carbon::parse($record->end_time)->translatedFormat('H.i') . ' WIB' : ' WIB - Selesai') }}</td>
        </tr>
        <tr>
            <td>Tempat</td>
            <td>:</td>
            <td>{{ $record->location }}</td>
        </tr>
        <tr>
            <td>Acara</td>
            <td>:</td>
            <td>{{ $record->duty }}</td>
        </tr>
        <tr>
            <td>Alat Transportasi</td>
            <td>:</td>
            <td>{{ $record->transportation }}</td>
        </tr>
    </table>

    <p>Demikian Surat Tugas ini dibuat untuk dilaksanakan dengan penuh tanggung jawab.</p>

    <table class="signature-table">
        <tr>
            <td style="text-align: center;">
                <br>
            </td>
            <td style="text-align: center;">
                Yogyakarta, {{ \Carbon\Carbon::parse($record->letter_date)->translatedFormat('d F Y') }}<br>
                {{ $commander ? $commander->chair->name : '-' }} RSU Mitra Paramedika
                <br>
                <img src="data:image/svg+xml;base64,{{ $known }}" style="width: 84px; "><br>
                <b>{{ $commander ? $commander->name : '-' }}</b><br>
                NIK: {{ $commander ? $commander->nip : '-' }}
            </td>
        </tr>
    </table>

    <div class="footer-note">
        <table style="width: 100%;" class="bordered-table">
            <tr>
                <td style="vertical-align: top; text-align: center;" width="50%">
                    Verifikasi Penyelenggara
                    <br><br><br><br><br><br>
                    (.......................................................)
                </td>
                <td style="vertical-align: middle; padding-left: 10px; text-align:center" width="50%">
                    Notulen Rapat dapat di isikan pada link /<br>
                    barcode:<br>
                    @php
                        $link = url('/duties/' . $record->id . '/outline');
                    @endphp
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ urlencode($link) }}" style="width: 100px; height: 100px;">
                </td>
            </tr>
        </table>
        
        <p style="font-size: 12pt; margin-bottom: 0">Nb: Tanda Tangan dan Stempel</p>
        <p style="font-size: 12pt">
            Mohon surat tugas dikembalikan ke SDM maksimal H+1 hari kerja setelah kegiatan berlangsung, terima kasih atas perhatiannya.
        </p>
    </div>

</body>
</html>