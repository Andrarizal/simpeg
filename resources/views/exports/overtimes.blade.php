<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px 4px; }
        th { background: #eee; }
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
            size: 29.7cm 21.0cm; 
            
            mso-page-orientation: landscape; 
            
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
        <br /><br />

        <h3 style="text-align:center; margin:0">Rekap Lembur Periode {{ $month }}</h3>
        <br><br>

        <p style="margin: 0; font-size: 14px;">Nama Pegawai: <span>{{ $data[0]->staff->name }}</span></p>
        <p style="margin: 0; font-size: 14px;">Jabatan: <span>{{ $data[0]->staff->chair->name }}</span></p>
        <p style="margin: 0; font-size: 14px;">Unit: <span>{{ $data[0]->staff->unit->name }}</span></p>
        <br>

        <table>
            <thead>
                <tr>
                    <th rowspan="2">No</th>
                    <th rowspan="2">Tanggal</th>
                    <th rowspan="2">Perintah</th>
                    <th colspan="2">Jam Lembur</th>
                    <th rowspan="2">Jumlah Jam</th>
                </tr>
                <tr>
                    <th>Masuk Lembur</th>
                    <th>Pulang Lembur</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($data as $i => $p)
                <tr>
                    <td style="width: 3%;text-align: center;">{{ $i + 1 }}</td>
                    <td style="width: 14%">{{ \Carbon\Carbon::parse($p->overtime_date)->translatedFormat('l, d F Y') }}</td>
                    <td>{{ $p->command }}</td>
                    <td style="width: 10%;text-align: center;">{{ $p->start_time }}</td>
                    <td style="width: 10%;text-align: center;">{{ $p->end_time ?? '-' }}</td>
                    <td style="width: 10%;text-align: center;">{{ $p->hours ? $p->hours . ' Jam' : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: center; font-weight: bold">Total Jam Lembur</td>
                    <td style="text-align: center; font-weight: bold">
                        <?php
                        $total = 0;
                        foreach ($data as $i => $p) {
                            $total += $p->hours;
                        }
                        echo $total;
                        ?> Jam
                    </td>
                </tr>
            </tfoot>
        </table>
        <br><br>

        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: top; border: 0;">
                    <p style="margin: 0; font-size: 14px;">Mengetahui Atasan</p>
                    @if ($known)
                        @if (isset($isWord) && $isWord)
                            <p style="margin: 0;"><img src="{{ $qrCode['known'] }}" width="96" height="96" /></p>
                        @else
                            <p style="margin: 0;"><img src="data:image/svg+xml;base64,{{ $qrCode['known'] }}" width="96" height="96" /></p>
                        @endif
                        <p style="margin: 0; font-size: 14px;">{{ $head }}</p>
                    @else
                        <p style="margin: 0; line-height: 96px;">&nbsp;</p>
                    @endif
                    
                </td>

                <td style="width: 50%; text-align: center; vertical-align: top; border: 0;">
                    <p style="margin: 0; font-size: 14px;">Verifikasi SDM</p>
                    
                    @if ($verified)
                        @if (isset($isWord) && $isWord)
                            <p style="margin: 0;"><img src="{{ $qrCode['verified'] }}" width="96" height="96" /></p>
                        @else
                            <p style="margin: 0;"><img src="data:image/svg+xml;base64,{{ $qrCode['verified'] }}" width="96" height="96" /></p>
                        @endif
                        <p style="margin: 0; font-size: 14px;">{{ $sdm }}</p>
                    @else
                        <p style="margin: 0; line-height: 96px;">&nbsp;</p>
                    @endif
                    
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
