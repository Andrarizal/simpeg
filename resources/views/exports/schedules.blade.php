<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Unit</title>
    <style>
        body {
            font-family: 'tnr', 'Times New Roman', serif;
            font-size: 12px;
        }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        
        .table-jadwal {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table-jadwal th, .table-jadwal td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
        }
        .table-jadwal th {
            text-align: center;
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

    <h2 class="text-center" style="margin-bottom: 0;">JADWAL UNIT {{ strtoupper($unit->name) }} RSU MITRA PARAMEDIKA</h2>
    <h2 class="text-center" style="margin-top: 0; text-transform: uppercase">BULAN {{ $periodName }}</h2>

    <table class="table-jadwal">
        <thead>
            <tr>
                <td style="font-weight: bold; text-align: right">Tanggal</td>
                @foreach($dates as $date)
                    <th>{{ $date['tanggal'] }}</th>
                @endforeach
                <th rowspan="2" style="width: 50px;">Total<br>Jam</th>
            </tr>
            <tr>
                <td style="font-weight: bold; text-align: right">Hari</td>
                @foreach($dates as $date)
                    <th style="font-size: 11px;">{{ $date['hari'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
          <tr>
              <td colspan="{{ count($dates) + 2 }}" class="text-left" style="font-weight: bold; padding-left: 8px;">
                  {{ strtoupper($unit->name) }}
              </td>
          </tr>
            @foreach($staffs as $staff)
                @php
                    $staffSchedules = $schedules->get($staff->id, collect());
                    $totalHours = 0;
                @endphp
                <tr>
                    <td class="nama-staff">{{ $staff->name }}</td>
                    
                    @foreach($dates as $date)
                        @php
                            $jadwalHariIni = $staffSchedules->firstWhere('schedule_date', $date['full_date']);
                            $shiftKode = '-';

                            if ($jadwalHariIni && $jadwalHariIni->shift) {
                                $shiftKode = $jadwalHariIni->shift->code; 
                                
                                if ($jadwalHariIni->shift->start_time && $jadwalHariIni->shift->end_time) {
                                    $start = \Carbon\Carbon::parse($jadwalHariIni->shift->start_time);
                                    $end = \Carbon\Carbon::parse($jadwalHariIni->shift->end_time);
                                    
                                    if ($end->lessThan($start)) {
                                        $end->addDay();
                                    }
                                    
                                    $totalHours += $start->diffInHours($end);
                                }
                            }
                        @endphp
                        <td class="text-center">{{ $shiftKode }}</td>
                    @endforeach
                    
                    <td class="text-center">{{ $totalHours }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <br>
    <table style="border-collapse: collapse; width: 50%; font-size: 10px; margin-top: 10px;">
        <tr>
          <td rowspan="{{ count($shifts) + 1 }}" style="border: 1px solid #000; padding: 5px; font-weight: bold; vertical-align: middle; width: 25%; text-align: center;">
              Keterangan Jadwal
          </td>
        </tr>
        @foreach($shifts as $shift)
        <tr>
          <td style="border: 1px solid #000; padding: 5px; text-align: center; font-weight: bold;" width="10%">
              {{ $shift->code }}
          </td>
          <td style="border: 1px solid #000; padding: 5px;">
                @php
                    if ($shift->code == 'L') {
                      echo 'Libur / Lepas Jaga';
                    } else {
                      $start = \Carbon\Carbon::parse($shift->start_time)->format('H:i');
                      $end = \Carbon\Carbon::parse($shift->end_time)->format('H:i');
                      echo $start . ' s/d ' . $end . ' WIB';
                    }
                @endphp
          </td>
        </tr>
        @endforeach
    </table>

</body>
</html>