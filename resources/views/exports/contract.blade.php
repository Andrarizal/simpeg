<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Evaluasi Kontrak</title>
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

        .signature-section {
            float: right;
            width: 40%;
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
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
        <h3 style="margin: 0; font-size: 16pt;">LAPORAN EVALUASI HABIS KONTRAK</h3>
        <h3 style="margin: 0; font-size: 16pt; text-transform: uppercase">BULAN {{ \Carbon\Carbon::today()->translatedFormat('F Y') }}</h3>
        <h6 style="margin: 0; font-weight: bold; font-size: 14pt">Nomor: {{ $contract->contract->contract_number }}</h6>
    </div>

    <br><br>
    <p style="margin-bottom: 15px">Sehubungan dengan berakhirnya kontrak karyawan:</p>
    <table class="bordered-table">
        <tr>
            <td width="25%">Nama</td>
            <td>{{ $contract->contract->staff->name }}</td> </tr>
        <tr>
            <td>Jabatan</td>
            <td>{{ $contract->contract->staff->chair->name }}</td>
        </tr>
        <tr>
            <td>Unit</td>
            <td>{{ $contract->contract->staff->unit->name }}</td>
        </tr>
        <tr>
            <td>Periode Kontrak</td>
            <td>{{ \Carbon\Carbon::parse($contract->contract->start_date)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($contract->contract->end_date)->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <p style="margin-bottom: 15px">Dengan ini kami melaporkan :</p>
    <ol>
        <li>Hasil Penilaian oleh Atasan Langsung</li>
    </ol>
    <table class="bordered-table">
        <thead>
            <tr>
                <td style="text-align: center;" rowspan="2" width="19%">Nilai Indikator Kinerja Individu Semester 1</td>
                <td style="text-align: center;" rowspan="2" width="19%">Nilai Indikator Kinerja Individu Semester 2</td>
                <td style="text-align: center;" rowspan="2" width="14%">Nilai Final</td>
                <td style="text-align: center;" colspan="2" width="22%">Kriteria Kelulusan</td>
                <td style="text-align: center;" rowspan="2" width="26%">Kesimpulan</td>
            </tr>
            <tr>
                <td style="text-align: center;" width="11%">≥ {{ setting('minimum_passing_grade') }}%</td>
                <td style="text-align: center;" width="11%">< {{ setting('minimum_passing_grade') }}%</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">{{ (int)$contract->firstScore->score }}%</td>
                <td style="text-align: center;">{{ (int)$contract->secondScore->score }}%</td>
                <td style="text-align: center">{{ (int)$contract->final_score }}%</td>
                <td style="text-align: center">Lulus</td>
                <td style="text-align: center">Tidak Lulus</td>
                <td style="text-align: center">Berdasarkan penilaian yang dilakukan oleh penilai, maka dengan ini karyawan yang bersangkutan dinyatakan <b style="text-decoration: underline">{{ $contract->conclusion }}</b></td>
            </tr>
        </tbody>
    </table>
    <ol start="2">
        <li style="margin-bottom: 2px">Catatan Kepala Sub Bagian Tata Usaha :</li>
        <p style="line-height: 1.5rem">{{ $contract->note }}</p>
    </ol>

    <div class="signature-section">
        Sleman, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}<br>
        Melaporkan,
        <br>
        Kepala Sub Bagian Tata Usaha
        <br>
        <img src="data:image/svg+xml;base64,{{ $known }}" style="width: 96px; ">
        <p class="bold" style="text-decoration: underline; margin-bottom: 0; text-align:center">
            {{ $administrator->name }}
        </p>
        <p style="margin-top: 2px; text-align: center;">
            NIK: {{ $administrator->nip }}
        </p>
    </div>

</body>
</html>