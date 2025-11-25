<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px 4px; }
        th { background: #eee; }
    </style>
</head>

<body>
    <table style="width: 100%; border-bottom: 3px double #000000; padding-bottom: 10px;">
        <tr>
            <td style="width: 20%; text-align: right; border: 0; vertical-align: top;">
                <img src="{{ public_path('img/rsumpyk.png') }}" alt="Logo RS" style="width: 80px; height: auto;">
            </td>

            <td style="width: 60%; text-align: center; border: 0; vertical-align: top;">
                <h3 style="margin: 0; font-size: 16px; font-weight: normal;">YAYASAN RSU MITRA PARAMEDIKA</h3>
                <h2 style="margin: 0; font-size: 28px; font-weight: bold;"> RSU MITRA PARAMEDIKA</h2>
                <p style="margin: 0; font-size: 12px;">
                    Jl. Raya Ngemplak, Area Sawah, Widodomartani, Kec. Ngemplak,
                </p>
                <p style="margin: 0; font-size: 12px;">
                    Sleman, Yogyakarta Telp. (0274) 4461098
                </p>
                <p style="margin: 0; font-size: 12px;">
                    Web: rsumipayk.co.id Email: rsumitraparamedika@yahoo.com
                </p>
            </td>
            <td style="width: 20%; text-align: left; border: 0; vertical-align: top;">
                <img src="{{ public_path('img/KARS.jpg') }}" alt="Logo RS" style="width: 110px; height: auto;">
            </td>
        </tr>
    </table>
    <br>
    <h3 style="text-align:center; margin:0">Permohonan {{ $record->type }}</h3>
    <br>

    <table>
        <tbody>
            <tr>
                <th style="width: 25%">Tanggal Pengajuan</th>
                <td colspan="2">{{  \Carbon\Carbon::parse($record->created_at)->translatedFormat('l, d F Y') }}</td>
            </tr>
            <tr>
                <th style="width: 25%">Nama</th>
                <td colspan="2">{{ $record->staff->name }}</td>
            </tr>
            <tr>
                <th style="width: 25%">Unit/ Bagian</th>
                <td colspan="2">{{ $record->staff->unit->name }}</td>
            </tr>
            <tr>
                <th style="width: 25%">Jabatan</th>
                <td colspan="2">{{ $record->staff->chair->name }}</td>
            </tr>
            <tr>
                <th style="width: 25%">NIK</th>
                <td colspan="2">{{ $record->staff->nik }}</td>
            </tr>
            <tr>
                <td colspan="3"><b>Mengajukan Untuk Keperluan: </b><br>{{ $record->reason }}</td>
            </tr>
            <tr>
                <td style="width: 25%"><b>Jumlah Hari {{ $record->type }}:</b><br>{{ \Carbon\Carbon::parse($record->start_date)->diffInDays($record->end_date) }} Hari</td>
                <td colspan="2"><b>Rincian Tanggal:</b><br>{{ \Carbon\Carbon::parse($record->start_date)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($record->end_date)->translatedFormat('d F Y') }}</td>
            </tr>
            <tr>
                <th style="width: 25%">Sisa {{ $record->type }} Saat ini</th>
                <td colspan="2">{{ $record->remaining }}</td>
            </tr>
            <tr>
                <th style="width: 25%">Sisa {{ $record->type }} Jika Disetujui</th>
                <td colspan="2">{{ ($record->remaining - \Carbon\Carbon::parse($record->start_date)->diffInDays($record->end_date)) }}</td>
            </tr>
        </tbody>
    </table>

    <table>
        <tbody>
            <tr>
                <th>Nama Pengganti</th>
                <th>TTD Pengganti</th>
                <th>No Telp Pengganti</th>
            </tr>
            <tr>
                <td style="width: 40%; text-align: center">{{ $record->replacement->name }}</td>
                <td align="center" style="width: 25%">
                    <?php
                    $replace = $record->is_replaced === 1 ? true : false;
                    if ($replace){ ?>
                        <img src="data:image/svg+xml;base64,{{ $qrCode['replace'] }}" style="width: 72px">
                    <?php } ?>
                </td>
                <td style="width: 35%; text-align: center">{{ $record->replacement->phone }}</td>
            </tr>
        </tbody>
    </table>

    <table>
        <tbody>
            <tr>
                <th rowspan="2" colspan="2">Pengajuan {{ $record->type }}</th>
                <th style="width: 66%">Dengan Catatan</th>
            </tr>
            <tr>
                <td rowspan="3" style="width: 66%">{{ $record->adverb }}</td>
            </tr>
            <tr>
                <th>Disetujui</th>
                <th>Ditolak</th>
            </tr>
            <tr>
                <?php if (str_contains($record->status, 'Disetujui')) { ?>
                    <td style="text-align: center; font-weight: bold; font-size: 30px;">✓</td>
                <?php } else if (str_contains($record->status, 'Ditolak')) { ?>
                    <td></td>
                <?php } else { ?>
                    <td style="text-align: center; color: #ccc">(Menunggu)</td>
                <?php } ?>

                <?php if (str_contains($record->status, 'Ditolak')) { ?>
                    <td style="text-align: center;font-weight: bold; font-size: 30px;">✓</td>
                <?php } else if (str_contains($record->status, 'Disetujui')) { ?>
                    <td></td>
                <?php } else { ?>
                    <td style="text-align: center; color: #ccc">(Menunggu)</td>
                <?php } ?>
            </tr>
        </tbody>
    </table>

    <table>
        <tbody>
            <tr>
                <th><b>Mengetahui</b></th>
                <th><b>Verifikasi</b></th>
                <th><b>Menyetujui</b></th>
            </tr>
            <tr>
                <td style="text-align: center">Atasan Langsung</td>
                <td style="text-align: center">Wadir SDM</td>
                <td style="text-align: center">{{ $record->staff->chair->level === 4 ? 'Kepala Seksi' : 'Direktur'}}</td>
            </tr>
            <tr>
                <td style="text-align: center; vertical-align: bottom">
                    <?php
                    $known = str_contains($record->status, 'Disetujui') || str_contains($record->status, 'Diketahui') ? true : false;
                    if ($known){ ?>
                        <img src="data:image/svg+xml;base64,{{ $qrCode['known'] }}" style="width: 84px">
                    <?php } else { ?>
                        <br>
                        <br>
                        <br>
                        <br>
                    <?php } ?>
                    <p style="margin: 0; font-size: 14px;">{{ $head->name }}</p>
                </td>
                <td style="text-align: center; vertical-align: bottom">
                    <?php
                    $verified = $record->is_verified === 1 ? true : false;
                    if ($verified){ ?>
                        <img src="data:image/svg+xml;base64,{{ $qrCode['verified'] }}" style="width: 84px">
                    <?php } else { ?>
                        <br>
                        <br>
                        <br>
                        <br>
                    <?php } ?>
                    <p style="margin: 0; font-size: 14px;">{{ $sdm }}</p>
                </td>
                <td style="text-align: center; vertical-align: bottom">
                    <?php
                    $approve = str_contains($record->status, 'Disetujui') ? true : false;
                    if ($approve){ ?>
                        <img src="data:image/svg+xml;base64,{{ $qrCode['approve'] }}" style="width: 84px">
                    <?php } else { ?>
                        <br>
                        <br>
                        <br>
                        <br>
                    <?php } ?>
                    <p style="margin: 0; font-size: 14px;">{{ $approver }}</p>
                </td>
            </tr>
        </tbody>
    </table>
</body>
</html>
