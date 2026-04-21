<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Undangan</title>
    <style>
        body {
            font-size: 12pt;
            line-height: 1.15;
        }
        
        .divider {
            border-top: 3px double #000;
            margin-bottom: 20px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }

        .info-table td {
            line-height: 1; 
            vertical-align: top;
        }

        .event-table {
            width: 100%;
            border-collapse: collapse;
        }

        .event-table td {
            padding-top: 2px;
            padding-bottom: 2px;
            padding-left: 2px;
            
            line-height: 1; 
            
            vertical-align: top; 
        }
        
        .signature-section {
            float: right;
            width: 50%;
            text-align: center;
            margin-top: 30px;
        }

        .notes {
            position: absolute;
            bottom: 64px;
        }
        
        .bold { font-weight: bold; }
        .mr-1 { margin-right: 5px; }
    </style>
</head>
@php
    $pageCount = 0;
    
    $filePath = storage_path('app/public/' . $record->file_path);

    if (!empty($record->file_path) && is_file($filePath)) {
        try {
            $mpdfTempDir = storage_path('app/private/mpdf-tmp');
            if (!\Illuminate\Support\Facades\File::isDirectory($mpdfTempDir)) {
                \Illuminate\Support\Facades\File::makeDirectory($mpdfTempDir, 0755, true, true);
            }

            $mpdfCounter = new \Mpdf\Mpdf(['tempDir' => $mpdfTempDir]);
            $pageCount = $mpdfCounter->SetSourceFile($filePath);
        } catch (\Exception $e) {
            $pageCount = 0;
        }
    }
@endphp
<body>

    <table style="width: 100%; border-bottom: 3px double #000000; padding-bottom: 10px;">
        <tr>
            <td style="width: 18%; text-align: left; border: 0; vertical-align: center;">
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
            <td style="width: 18%; text-align: right; border: 0; vertical-align: center;">
                <img src="{{ public_path('img/KARS.jpg') }}" alt="Logo RS" style="width: 100px; height: 90px;">
            </td>
        </tr>
    </table>
    <div class="divider"></div>

    <div style="text-align: right; margin-bottom: 10px;">
        Sleman, {{ \Carbon\Carbon::parse($record->letter_date)->translatedFormat('d F Y') }}
    </div>

    <table class="info-table">
        <tr>
            <td width="10%">No.</td>
            <td width="2%">:</td>
            <td>{{ $record->reference_number }}</td>
        </tr>
        <tr>
            <td>Lamp.</td>
            <td>:</td>
            <td>
                {{ $pageCount > 0 ? $pageCount . ' Lembar' : '-' }}
            </td>
        </tr>
        <tr>
            <td>Hal</td>
            <td>:</td>
            <td class="bold">Undangan</td>
        </tr>
    </table>

    <div style="margin-bottom: 15px;" class="bold">
        Kepada Yth.<br>
        @if($record->receiver_type === 'Terlampir')
            <span class="bold">Nama Penerima Terlampir</span>
        @else
            <ol style="margin-top: 5px; margin-bottom: 5px; margin-left: 20px;">
                @foreach($record->targetStaffs as $staff)
                    <li>
                        {{ $staff->name }} 
                        @if($staff->chair) 
                            ({{ $staff->chair->name }}) 
                        @endif
                    </li>
                @endforeach
            </ol>
        @endif
        Di Tempat
    </div>

    <span>{{ $record->template->opening_greeting }}</span><br>
    <p style="text-indent: 1.27cm; text-align: justify; margin-bottom: 0; margin-top: 4px;">{{ $record->template->opening_body }}</p>

    <table class="event-table">
        <tr>
            <td width="20%">Hari</td>
            <td width="2%">:</td>
            <td>
                {{ \Carbon\Carbon::parse($record->start_date)->translatedFormat('l') . ($record->end_date ? ' - ' . \Carbon\Carbon::parse($record->end_date)->translatedFormat('l') : '') }}
            </td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td>
                {{ \Carbon\Carbon::parse($record->start_date)->translatedFormat('d F Y') . ($record->end_date ? ' - ' . \Carbon\Carbon::parse($record->end_date)->translatedFormat('d F Y') : '') }}
            </td>
        </tr>
        <tr>
            <td>Pukul</td>
            <td>:</td>
            <td>
                {{ \Carbon\Carbon::parse($record->start_time)->translatedFormat('H.i') }} s.d {{ ($record->end_time ? \Carbon\Carbon::parse($record->end_time)->translatedFormat('H.i') : 'selesai')}}
            </td>
        </tr>
        <tr>
            <td>Tempat</td>
            <td>:</td>
            <td>
                {{ $record->location }}
            </td>
        </tr>
        <tr>
            <td>Acara</td>
            <td>:</td>
            <td>
                <div style="margin-top: -3px;">
                    {!! nl2br(e($record->title)) !!}
                </div>
            </td>
        </tr>
    </table>

    <br>
    <div class="content-body">
        <p style="text-indent: 1.27cm; text-align: justify; margin-bottom: 0; margin-top: 0">{{ $record->template->closing_body }}</p>
    </div>

    <div class="signature-section">
        {{ $record->known->chair->name }}<br>
        RSU Mitra Paramedika
        <br>
        <br>
        <img src="data:image/svg+xml;base64,{{ $known }}" style="width: 96px; ">
        <p class="bold" style="text-decoration: underline; margin-bottom: 0;">
            {{ $record->known->name }}
        </p>
        <p style="margin-top: 2px;">
            NIK: {{ $record->known->nip }}
        </p>
    </div>

    <div class="notes">
        <p>{{ $record->note ?? '' }}</p>
    </div>

</body>
</html>