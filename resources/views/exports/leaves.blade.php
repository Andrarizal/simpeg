<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px 4px; }
        th { background: #eee; }
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
        <h2 style="text-align:center; margin:0; text-transform: uppercase">Permohonan Cuti / Izin</h1>

        <table>
            <tbody>
                <tr>
                    <td style="width: 25%; text-align:center">Tanggal Permohonan diajukan</td>
                    <td colspan="2">{{  \Carbon\Carbon::parse($record->created_at)->translatedFormat('l, d F Y') }}</td>
                </tr>
                <tr>
                    <th style="width: 25%; text-align: left"><b>1. Nama</b></th>
                    <td colspan="2">{{ $record->staff->name }}</td>
                </tr>
                <tr>
                    <th style="width: 25%; text-align: left"><b>2. Unit/ Bagian</b></th>
                    <td colspan="2">{{ $record->staff->unit->name }}</td>
                </tr>
                <tr>
                    <th style="width: 25%; text-align: left"><b>3. Jabatan</b></th>
                    <td colspan="2">{{ $record->staff->chair->name }}</td>
                </tr>
                <tr>
                    <td colspan="3"><b>4. Keperluan Cuti / Izin: </b><br>{{ $record->reason }}</td>
                </tr>
                <tr>
                    <td style="width: 25%; text-align: left"><b>5. Jumlah Hari Yang Diajukan:</b><br>{{ \Carbon\Carbon::parse($record->start_date)->diffInDays($record->end_date) }} Hari</td>
                    <td colspan="2"><b>Rincian Tanggal Cuti / Izin:</b><br>{{ \Carbon\Carbon::parse($record->start_date)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($record->end_date)->translatedFormat('d F Y') }}</td>
                </tr>
                <tr>
                    <th style="width: 25%; text-align: left"><b>6. Jumlah {{ $record->type }} yang sudah dipakai</b></th>
                    <td colspan="2">
                        @php
                            $usedDays = \App\Models\Leave::where('staff_id', $record->staff_id)
                                ->where('id', '!=', $record->id)
                                ->where('type', $record->type)
                                ->where('subtype', $record->subtype)
                                ->whereYear('start_date', now()->year)
                                ->where(function ($query) {
                                    $query->where('status', '!=', 'Ditolak')
                                        ->orWhere('is_verified', 1);
                                })
                                ->sum(\Illuminate\Support\Facades\DB::raw('DATEDIFF(end_date, start_date) + 1'));
                        @endphp
                        {{ $usedDays }}
                    </td>
                </tr>
                <tr>
                    <th style="width: 25%; text-align: left"><b>7. Sisa {{ $record->type }} yang belum dipakai</b></th>
                    <td colspan="2">{{ $record->remaining }}</td>
                </tr>
                <tr>
                    <th style="width: 25%; text-align: left"><b>8. Nama pengganti</b></th>
                    <td colspan="2">{{ $record->replacement->name ?? '-' }}</td>
                </tr>
                <tr>
                    <th style="width: 25%; text-align: left"><b>9. No HP pengganti</b></th>
                    <td colspan="2">{{ $record->replacement->phone ?? '-' }}</td>
                </tr>
                <tr>
                    <th style="width: 25%; text-align: left"><b>10. Mengetahui pengganti</b></th>
                    <td colspan="2"><?php
                        $replace = $record->is_replaced === 1 ? true : false;
                        if ($replace){ ?>
                            @if (isset($isWord) && $isWord)
                                <p style="margin: 0;"><img src="{{ $qrCode['replace'] }}" width="72" height="72" /></p>
                            @else
                                <p style="margin: 0;"><img src="data:image/svg+xml;base64,{{ $qrCode['replace'] }}" width="72" height="72" /></p>
                            @endif
                        <?php } ?></td>
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
                    <td style="text-align: center">TTD Atasan Langsung</td>
                    <td style="text-align: center">Wadir SDM</td>
                    <td style="text-align: center">{{ $record->staff->chair->level === 4 ? 'Kepala Seksi' : 'Direktur'}}</td>
                </tr>
                <tr>
                    <td style="text-align: center; vertical-align: bottom" width="33%">
                        <?php
                        $known = str_contains($record->status, 'Disetujui') || str_contains($record->status, 'Diketahui') ? true : false;
                        if ($known){ ?>
                            @if (isset($isWord) && $isWord)
                                <p style="margin: 0;"><img src="{{ $qrCode['known'] }}" width="84" height="84" /></p>
                            @else
                                <p style="margin: 0;"><img src="data:image/svg+xml;base64,{{ $qrCode['known'] }}" width="84" height="84" /></p>
                            @endif
                            <p style="margin: 0; font-size: 14px;">{{ $head->name }}</p>
                        <?php } else { ?>
                            <br>
                            <br>
                            <br>
                            <br>
                        <?php } ?>
                    </td>
                    <td style="text-align: center; vertical-align: bottom" width="33%">
                        <?php
                        $verified = $record->is_verified === 1 ? true : false;
                        if ($verified){ ?>
                            @if (isset($isWord) && $isWord)
                                <p style="margin: 0;"><img src="{{ $qrCode['verified'] }}" width="84" height="84" /></p>
                            @else
                                <p style="margin: 0;"><img src="data:image/svg+xml;base64,{{ $qrCode['verified'] }}" width="84" height="84" /></p>
                            @endif
                            <p style="margin: 0; font-size: 14px;">{{ $sdm }}</p>
                        <?php } else { ?>
                            <br>
                            <br>
                            <br>
                            <br>
                        <?php } ?>
                    </td>
                    <td style="text-align: center; vertical-align: bottom" width="33%">
                        <?php
                        $approve = str_contains($record->status, 'Disetujui') ? true : false;
                        if ($approve){ ?>
                            @if (isset($isWord) && $isWord)
                                <p style="margin: 0;"><img src="{{ $qrCode['approve'] }}" width="84" height="84" /></p>
                            @else
                                <p style="margin: 0;"><img src="data:image/svg+xml;base64,{{ $qrCode['approve'] }}" width="84" height="84" /></p>
                            @endif
                            <p style="margin: 0; font-size: 14px;">{{ $approver }}</p>
                        <?php } else { ?>
                            <br>
                            <br>
                            <br>
                            <br>
                        <?php } ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <table>
            <tbody>
                <tr>
                    <th style="width: 25%; text-align:center"><b>Dengan Catatan/Verifikasi SDM</b></th>
                    <td colspan="2">{{ $record->adverb }}</td>
                </tr>
                <tr>
                    <th style="width: 25%; text-align:center" rowspan="2"><b>Dengan ini cuti/izin</b></th>
                    <th>Disetujui</th>
                    <th>Ditolak</th>
                </tr>
                <tr>
                    <?php if (str_contains($record->status, 'Disetujui')) { ?>
                        <td class="symbol-check" style="text-align: center; font-weight: bold; font-size: 30px;">✓</td>
                    <?php } else if (str_contains($record->status, 'Ditolak')) { ?>
                        <td></td>
                    <?php } else { ?>
                        <td style="text-align: center; color: #ccc">(Menunggu)</td>
                    <?php } ?>

                    <?php if (str_contains($record->status, 'Ditolak')) { ?>
                        <td class="symbol-check" style="text-align: center;font-weight: bold; font-size: 30px;">✓</td>
                    <?php } else if (str_contains($record->status, 'Disetujui')) { ?>
                        <td></td>
                    <?php } else { ?>
                        <td style="text-align: center; color: #ccc">(Menunggu)</td>
                    <?php } ?>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>
