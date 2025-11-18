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

    <h3 style="text-align:center; margin:0">Rekap Absensi Bulan {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}</h3>
    <br><br>

    <p style="margin: 0; font-size: 14px;">Nama Pegawai: <span>{{ $data[0]->staff->name }}</span></p>
    <p style="margin: 0; font-size: 14px;">Jabatan: <span>{{ $data[0]->staff->chair->name }}</span></p>
    <p style="margin: 0; font-size: 14px;">Unit: <span>{{ $data[0]->staff->unit->name }}</span></p>
    <br><br>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Hari dan Tanggal</th>
                <th>Masuk</th>
                <th>Pulang</th>
                <th>Metode Absen</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($data as $i => $p)
            <tr>
                <td style="text-align: center;">{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($p->presence_date)->translatedFormat('l, d F Y') }}</td>
                <td style="text-align: center;">{{ $p->check_in }}</td>
                <td style="text-align: center;">{{ $p->check_out ?? '-' }}</td>
                <td style="text-align: center;">{{ $p->method === 'network' ? 'Jaringan' : 'Lokasi' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
