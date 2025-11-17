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
                <p style="margin: 0; font-size: 14px;">
                    Jl. Raya Ngemplak, Area Sawah, Widodomartani, Kec. Ngemplak,
                </p>
                <p style="margin: 0; font-size: 14px;">
                    Sleman, Yogyakarta Telp. (0274) 4461098
                </p>
                <p style="margin: 0; font-size: 14px;">
                    Web: rsumipayk.co.id Email: rsumitraparamedika@yahoo.com
                </p>
            </td>
            <td style="width: 20%; text-align: left; border: 0; vertical-align: top;">
                <img src="{{ public_path('img/KARS.jpg') }}" alt="Logo RS" style="width: 110px; height: auto;">
            </td>
        </tr>
    </table>
    <br><br><br>

    <h3 style="text-align:center; margin:0">Rekap Lembur Bulan {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}</h3>
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
                <td style="text-align: center;">{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($p->presence_date)->translatedFormat('l, d F Y') }}</td>
                <td style="text-align: center;">{{ $p->command }}</td>
                <td style="text-align: center;">{{ $p->start_time }}</td>
                <td style="text-align: center;">{{ $p->end_time ?? '-' }}</td>
                <td style="text-align: center;">{{ $p->hours }} Jam</td>
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

    <div style="width: 100%">
        <div align="left" style="width: 50%;float: left;max-height: 130px; text-align: center; vertical-align: top;">
            <div style="position: relative; z-index: 2; width: 100%;">
                <p style="margin: 0; font-size: 14px; z-index: 1">Mengetahui Atasan</p>
                <?php
                $known = true;
                foreach ($data as $i => $p) {
                    $known = $p->is_known === 2 ?? false;
                }
                if ($known){ ?>
                    <img src="{{ public_path('img/rsumpyk.png') }}" style="
                        position: absolute;
                        transform: translateY(-20%);
                        width: 100px;
                        opacity: 0.25;
                        z-index: 10;
                        pointer-events: none;
                        filter: grayscale(1) brightness(0) sepia(1) hue-rotate(180deg) saturate(600%);">
                <?php } ?>
                <p style="margin: 0; font-size: 14px; margin-top: -10%">{{ $head }}</p>
            </div>
        </div>
        <div align="left" style="width: 50%;float: left;max-height: 130px; text-align: center; vertical-align: top;">
            <div style="position: relative; width: 100%;">
                <p style="margin: 0; font-size: 14px; z-index: 1">Verifikasi SDM</p>
                <?php
                $verified = true;
                foreach ($data as $i => $p) {
                    $verified = $p->is_verified ?? false;
                }
                if ($verified){ ?>
                <img src="{{ public_path('img/rsumpyk.png') }}" style="
                    position: absolute;
                    transform: translateY(-20%);
                    width: 100px;
                    opacity: 0.25;
                    z-index: 10;
                    pointer-events: none;
                    filter: grayscale(1) brightness(0) sepia(1) hue-rotate(180deg) saturate(600%);">
                <?php } ?>
                <p style="margin: 0; font-size: 14px; margin-top: -10%">{{ $sdm }}</p>
            </div>
        </div>
    </div>

</body>
</html>
