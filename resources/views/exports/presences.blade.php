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
                <th>Metode</th>
                <th>Selisih Masuk</th>
                <th>Selisih Pulang</th>
            </tr>
        </thead>

        <tbody>
            @php
                $total_gap_detik_masuk = 0; 
                $total_gap_detik_pulang = 0; 
            @endphp
            @foreach ($data as $i => $p)
            <tr>
                <td style="text-align: center;">{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($p->presence_date)->translatedFormat('l, d F Y') }}</td>
                <td style="text-align: center;">{{ $p->check_in }}</td>
                <td style="text-align: center;">{{ $p->check_out ?? '-' }}</td>
                <td style="text-align: center;">{{ $p->method === 'network' ? 'Jaringan' : 'Lokasi' }}</td>
                @php
                    $jadwal = \App\Models\Schedule::where('staff_id', $p->staff_id)
                        ->where('schedule_date', $p->presence_date)
                        ->first();

                    $gap_masuk = '-';
                    $gap_pulang = '-';

                    if ($jadwal && $jadwal->shift_id) {
                        $shift = \App\Models\Shift::find($jadwal->shift_id);
                        
                        if ($shift) {
                            $target_masuk = \Carbon\Carbon::parse($shift->start_time);
                            $target_pulang = \Carbon\Carbon::parse($shift->end_time);
                            $real_masuk = \Carbon\Carbon::parse($p->check_in);
                            $real_pulang = \Carbon\Carbon::parse($p->check_out);
                            
                            $gap_masuk = $target_masuk->diff($real_masuk)->format('%H:%I:%S');
                            $gap_pulang = $target_pulang->diff($real_pulang)->format('%H:%I:%S');

                            $total_gap_detik_masuk += $target_masuk->diffInSeconds($real_masuk, false);
                            $total_gap_detik_pulang += $target_pulang->diffInSeconds($real_pulang, false);
                        }
                    }
                @endphp
                <td style="text-align: center;">
                    @if ($real_masuk->lessThan($target_masuk))
                    -
                    @endif
                    {{ $gap_masuk }}
                </td>
                <td style="text-align: center;">
                    @if ($real_pulang->lessThan($target_pulang))
                    -
                    @endif
                    {{ $gap_pulang }}
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f3f4f6;">
                <td colspan="5" style="text-align: center; font-weight: bold; padding: 5px;">
                    TOTAL KETERLAMBATAN
                </td>
                
                <td style="text-align: center; padding: 5px; font-weight: bold;">
                    @php
                        $negatif_masuk = $total_gap_detik_masuk < 0;

                        $absolute_masuk = abs($total_gap_detik_masuk);
                        $jam_masuk = floor($absolute_masuk / 3600);
                        $sisa_masuk = $absolute_masuk % 3600;
                        $menit_masuk = floor($sisa_masuk / 60);
                        $detik_masuk = $sisa_masuk % 60;
                        $tanda_masuk = $negatif_masuk ? '- ' : '';
                        
                        // Format output: 02:15:00
                        // %02d artinya: jika angkanya 5, cetak 05.
                        $total_formatted_masuk = sprintf('%s%02d:%02d:%02d', $tanda_masuk, $jam_masuk, $menit_masuk, $detik_masuk);
                    @endphp
                    
                    {{ $total_formatted_masuk }}
                </td>
                <td style="text-align: center; padding: 5px; font-weight: bold;">
                    @php
                        $negatif_pulang = $total_gap_detik_pulang < 0;

                        $absolute_pulang = abs($total_gap_detik_pulang);
                        // Konversi Detik ke Jam, Menit, Detik
                        $jam_pulang   = floor($absolute_pulang / 3600);
                        $sisa_pulang  = $absolute_pulang % 3600;
                        $menit_pulang = floor($sisa_pulang / 60);
                        $detik_pulang = $sisa_pulang % 60;
                        $tanda_pulang = $negatif_pulang ? '- ' : '';
                        
                        // Format output: 02:15:00
                        // %02d artinya: jika angkanya 5, cetak 05.
                        $total_formatted_pulang = sprintf('%s%02d:%02d:%02d', $tanda_pulang, $jam_pulang, $menit_pulang, $detik_pulang);
                    @endphp
                    
                    {{ $total_formatted_pulang }}
                </td>
            </tr>
        </tfoot>
    </table>

</body>
</html>
