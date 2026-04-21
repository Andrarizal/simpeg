<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px 4px; }
        th { background: #eee; }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .signature-table td {
            vertical-align: bottom;
            padding: 0;
            border: none;
        }
        .symbol-check {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 14pt;
            vertical-align: middle;
            color: black;
        }
        p, td, th {
            margin-top: 0pt;
            margin-bottom: 0pt;
        }
        .MsoNormal {
            margin: 0pt;
        }
    </style>
    @if (isset($isWord) && $isWord)
    <style>
        @page Section1 {
            size: 21cm 33cm; 
            
            margin: 2cm 2cm 2cm 2cm; 
        }

        div.Section1 {
            page: Section1;
        }
    </style>
    @endif
</head>

<body>
    <div class="Section1">
        <table style="width: 100%; padding-bottom: 10px;">
            <tr>
                <td style="width: 18%; text-align: left; border: 0; vertical-align: center;">
                    <img src="{{ public_path('img/rsumpyk.png') }}" alt="Logo RS" width="90" height="90" />
                </td>

                <td style="width: 64%; text-align: center; border: 0; vertical-align: top;">
                    <h3 style="margin: 0; font-size: 16px; font-weight: normal;">YAYASAN RSU MITRA PARAMEDIKA</h3>
                    <h2 style="margin: 0; font-size: 30px; font-weight: bold;">RSU MITRA PARAMEDIKA</h2>
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
                <td style="width: 18%; text-align: right; border: 0; vertical-align: center;">
                    <img src="{{ public_path('img/KARS.jpg') }}" alt="Logo RS" width="100" height="90" />
                </td>
            </tr>
            <tr>
                <td colspan="3" style="border-top: 3px solid black; border-bottom: 1px solid black; padding: 0; font-size: 2px; line-height: 2px;">&nbsp;</td>
            </tr>
        </table>
        <br />
        <h2 style="text-align:center; margin:0; text-transform: uppercase">Jawaban Permohonan Cuti / Izin</h1>

        <table>
            <tbody>
                <tr>
                    <td colspan="3">Dengan ini pengajuan cuti/izin kepada:</td>
                </tr>
                <tr>
                    <td style="width: 25%; text-align: left">Nama</td>
                    <td colspan="2">{{ $record->staff->name }}</td>
                </tr>
                <tr>
                    <td style="width: 25%; text-align: left">Unit</td>
                    <td colspan="2">{{ $record->staff->unit->name }}</td>
                </tr>
                <tr>
                    <td style="width: 25%; text-align: left">Jabatan</td>
                    <td colspan="2">{{ $record->staff->chair->name }}</td>
                </tr>
                <tr>
                    <td style="width: 25%; text-align: left">Tanggal</td>
                    <td colspan="2">{{ \Carbon\Carbon::parse($record->start_date)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($record->end_date)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <td style="width: 25%; text-align: left">Untuk Keperluan</td>
                    <td colspan="2">{{ $record->reason }}</td>
                </tr>
                <tr>
                    <td style="width: 25%; text-align: left">Kami</td>
                    <td style="text-align: center;{{ str_contains($record->status, 'Ditolak') ? 'text-decoration: line-through' : '' }};"><b>Diberikan</b></td>
                    <td style="text-align: center;{{ str_contains($record->status, 'Disetujui') ? 'text-decoration: line-through' : '' }};"><b>Tidak diberikan</b></td>
                </tr>
                <tr>
                    <td style="width: 25%; text-align: left">Dengan Rincian</td>
                    <td colspan="2" style="vertical-align: middle">{!! nl2br(e($record->adverb)) !!}</td>
                </tr>
                <tr>
                    <td colspan="3">Demikian surat balasan cuti/izin ini kami buat untuk dipergunakan sebagaimana mestinya.</td>
                </tr>
            </tbody>
        </table>

        <br>
        <table class="signature-table">
            <tr>
                <td width="60%" style="text-align: center;">
                    <br>
                </td>
                <td width="40%" style="text-align: left;padding-left: 10px;">
                    <div align="left">Sleman, {{ \Carbon\Carbon::parse($record->verified_at)->translatedFormat('d F Y') }}<br>
                    Menyetujui,
                    <br>
                    Direktur RSU Mitra Paramedika
                    <br></div>
                    <?php
                    $approve = $record->status == 'Disetujui Direktur' ? true : false;
                    $verified = $record->is_verified === 1 ? true : false;
                    if ($record->staff->chair->level == 4){
                        $approve = str_contains($record->status, 'Disetujui') ? true : false;
                    }
                    if ($approve && $verified){ ?>
                        @if (isset($isWord) && $isWord)
                            <center><img src="{{ $qrCode }}" width="84" height="84" /></center>
                        @else
                            <center><img src="data:image/svg+xml;base64,{{ $qrCode }}" width="84" height="84" /></center>
                        @endif
                    <?php } else { ?>
                        <br>
                        <br>
                        <br>
                        <br>
                    <?php } ?>
                    <div align="left">{{ $approver ? $approver : '-' }}</div><br>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
