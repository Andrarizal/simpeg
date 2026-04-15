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
</head>

<body>
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

    <h3 style="text-align:center; margin:0">Rekap Absensi Periode {{ $month }}</h3>
    <br /><br />

    <p style="margin: 0; font-size: 14px;">Nama Pegawai: <span>{{ $data[0]->staff->name }}</span></p>
    <p style="margin: 0; font-size: 14px;">Jabatan: <span>{{ $data[0]->staff->chair->name }}</span></p>
    <p style="margin: 0; font-size: 14px;">Unit: <span>{{ $data[0]->staff->unit->name }}</span></p>
    <br /><br />
    
    @php
        $total_target_hours = 0;
        $total_real_hours = 0;

        $dates = [];

        $startDate = \Carbon\Carbon::parse($schedules->first()->schedule_date);
        $endDate = \Carbon\Carbon::parse($schedules->last()->schedule_date);
        
        $current = $startDate->copy();
        
        while ($current->lte($endDate)) {
            $dateString = $current->format('Y-m-d');
            $dates[] = $dateString;

            $schedule = $schedules[$dateString];
            if ($schedule && $schedule->shift) {
                $shift = $schedule->shift;
                
                if ($shift->start_time && $shift->end_time) {
                    $target_masuk = \Carbon\Carbon::parse($shift->start_time);
                    $target_pulang = \Carbon\Carbon::parse($shift->end_time);
                    $total_target_hours += round(abs($target_pulang->diffInMinutes($target_masuk) / 60), 2);
                }
            }

            $current->addDay();
        }
    @endphp

    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th width="5%" style="border: 1px solid black; padding: 5px;" valign="middle">No</th>
                <th width="25%" style="border: 1px solid black; padding: 5px;" valign="middle">Hari dan Tanggal</th>
                <th width="10%" style="border: 1px solid black; padding: 5px;" valign="middle">Masuk</th>
                <th width="10%" style="border: 1px solid black; padding: 5px;" valign="middle">Pulang</th>
                <th width="10%" style="border: 1px solid black; padding: 5px;" valign="middle">Metode</th>
                <th width="20%" style="border: 1px solid black; padding: 5px;" valign="middle">Selisih Masuk</th>
                <th width="20%" style="border: 1px solid black; padding: 5px;" valign="middle">Selisih Pulang</th>
            </tr>
        </thead>

        <tbody>
            @php
                $total_gap_detik_masuk = 0; 
                $total_gap_detik_pulang = 0; 
            @endphp

            @foreach ($data as $i => $p)
            <tr>
                <td style="border: 1px solid black; text-align: center; padding: 5px;">{{ $i + 1 }}</td>
                <td style="border: 1px solid black; padding: 5px;">{{ \Carbon\Carbon::parse($p->presence_date)->translatedFormat('l, d F Y') }}</td>
                <td style="border: 1px solid black; text-align: center; padding: 5px;">{{ $p->check_in }}</td>
                <td style="border: 1px solid black; text-align: center; padding: 5px;">{{ $p->check_out ?? '-' }}</td>
                <td style="border: 1px solid black; text-align: center; padding: 5px;">{{ $p->method === 'network' ? 'Jaringan' : 'Lokasi' }}</td>
                
                @php
                    $gap_masuk = '-';
                    $gap_pulang = '-';
                    $real_masuk = null;
                    $real_pulang = null;
                    $target_masuk = null;
                    $target_pulang = null;

                    $jadwal = $schedules[$p->presence_date] ?? null;

                    if ($jadwal && $jadwal->shift) {
                        $shift = $jadwal->shift;
                        
                        if ($shift->start_time && $shift->end_time) {
                            $target_masuk = \Carbon\Carbon::parse($shift->start_time);
                            $target_pulang = \Carbon\Carbon::parse($shift->end_time);
                            
                            if ($p){
                                if ($p->check_in) {
                                    $real_masuk = \Carbon\Carbon::parse($p->check_in);
                                    if ($real_masuk > $target_masuk) {
                                        $gap_masuk = '+' . $target_masuk->diff($real_masuk)->format('%H:%I:%S');
                                        $total_gap_detik_masuk += $target_masuk->diffInSeconds($real_masuk, false);
                                    }
                                }

                                if ($p->check_out) {
                                    $real_pulang = \Carbon\Carbon::parse($p->check_out);
                                    if ($real_pulang < $target_pulang) {
                                        $gap_pulang = '+' . $target_pulang->diff($real_pulang)->format('%H:%I:%S');
                                        $total_gap_detik_pulang += $target_pulang->diffInSeconds($real_pulang, false); 
                                    }
                                }

                                if (isset($real_masuk) && isset($real_pulang)) {
                                    $total_real_hours += round(abs($real_pulang->diffInMinutes($real_masuk) / 60), 2);
                                }
                            }
                        }
                    }
                @endphp

                <td style="border: 1px solid black; text-align: center; padding: 5px; color: {{ ($real_masuk > $target_masuk) ? 'red' : 'green' }}">
                    {{ $gap_masuk }}
                </td>
                
                <td style="border: 1px solid black; text-align: center; padding: 5px; color: {{ ($real_pulang < $target_pulang) ? 'red' : 'green' }}">
                    {{ $gap_pulang }}
                </td>
            </tr>
            @endforeach
        </tbody>
        
        <tfoot>
            <tr style="background-color: #f3f4f6;">
                <td colspan="5" style="border: 1px solid black; text-align: center; font-weight: bold; padding: 5px;">
                    TOTAL KETERLAMBATAN
                </td>
                
                <td style="border: 1px solid black; text-align: center; padding: 5px; font-weight: bold;">
                    @php
                        $negatif_masuk = $total_gap_detik_masuk < 0;
                        $absolute_masuk = abs($total_gap_detik_masuk);
                        $jam_masuk = floor($absolute_masuk / 3600);
                        $sisa_masuk = $absolute_masuk % 3600;
                        $menit_masuk = floor($sisa_masuk / 60);
                        $detik_masuk = $sisa_masuk % 60;
                        
                        $total_formatted_masuk = sprintf('%s%02d:%02d:%02d', '+', $jam_masuk, $menit_masuk, $detik_masuk);
                    @endphp
                    {{ $total_formatted_masuk }}
                </td>
                
                <td style="border: 1px solid black; text-align: center; padding: 5px; font-weight: bold;">
                    @php
                        $negatif_pulang = $total_gap_detik_pulang < 0;
                        $absolute_pulang = abs($total_gap_detik_pulang);
                        $jam_pulang   = floor($absolute_pulang / 3600);
                        $sisa_pulang  = $absolute_pulang % 3600;
                        $menit_pulang = floor($sisa_pulang / 60);
                        $detik_pulang = $sisa_pulang % 60;
                        
                        $total_formatted_pulang = sprintf('%s%02d:%02d:%02d', '+', $jam_pulang, $menit_pulang, $detik_pulang);
                    @endphp
                    {{ $total_formatted_pulang }}
                </td>
            </tr>
        </tfoot>
    </table>
    <br />

    <p style="margin: 0; font-size: 14px;">Jam Kerja Kontraktual: <span>{{ $total_target_hours }} Jam</span></p>
    <p style="margin: 0; font-size: 14px;">Jam Kerja Aktual: <span>{{ $total_real_hours }} Jam</span></p>

</body>
</html>
