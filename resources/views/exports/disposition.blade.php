<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 11pt; }
        .header { 
            text-align: center; font-weight: bold; font-size: 14pt; 
            border-bottom: 3px double black; padding-bottom: 10px; margin-bottom: 20px; 
        }
        .table-data { 
            width: 100%; border-collapse: collapse; border: 1px solid #000; 
        }
        .table-data td { 
            border: 1px solid #000; padding: 8px; vertical-align: top; 
        }
        .label { font-size: 11pt; font-weight: bold; display: block; margin-bottom: 2px; }
        .content { font-size: 12pt; }
        .symbol-check {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 14pt;
            vertical-align: middle;
            color: black;
        }
    </style>
</head>
<body>
    <div class="header">
        LEMBAR DISPOSISI RSU MITRA PARAMEDIKA
    </div>

    <table style="width: 100%; margin-bottom: 10px; vertical-align: middle; border: none;">
        <tr>
            <td style="width: 50%; text-align: center; border: none;">
                Tanggal: {{ \Carbon\Carbon::parse($record->created_at)->translatedFormat('d F Y') }}
            </td>
            
            <td style="width: 50%; text-align: center; border: none;">
                Jam: {{ \Carbon\Carbon::parse($record->created_at)->translatedFormat('H.i') }}
            </td>
        </tr>
    </table>

    <table class="table-data">
        <tr>
            <td width="33%">
                <span class="label">No. Agenda Masuk :</span><br>
                <span class="content">{{ $record->agenda_number }}</span>
            </td>
            <td width="33%">
                <span class="label">Tgl Agenda :</span><br>
                <span class="content">{{ \Carbon\Carbon::parse($record->agenda_date)->translatedFormat('d F Y') }}</span>
            </td>
            <td width="33%">
              <div>
                <span class="symbol-check">
                    {{ in_array('Rahasia', $record->urgency ?? []) ? '☑' : '☐' }}
                </span>
                Rahasia &nbsp;&nbsp;

                <span class="symbol-check">
                    {{ in_array('Penting', $record->urgency ?? []) ? '☑' : '☐' }}
                </span>
                Penting
                
                <br> <span class="symbol-check">
                    {{ in_array('Segera', $record->urgency ?? []) ? '☑' : '☐' }}
                </span>
                Segera &nbsp;&nbsp;

                <span class="symbol-check">
                    {{ in_array('Biasa', $record->urgency ?? []) ? '☑' : '☐' }}
                </span>
                Biasa
            </div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-right: none">
                <p style="list-style-type: none; margin: 0; padding: 0">
                  Nomor Surat     : {{ $record->reference_number }}
                </p>  
                <p style="list-style-type: none; margin: 0; padding: 0">
                  Asal Surat     : {{ $record->sender }}
                </p>  
                <p style="list-style-type: none; margin: 0; padding: 0">
                  Isi Ringkas / Perihal     : <br>{!! nl2br(e($record->title)) !!}
                </p>  
            </td>
            <td style="border-left: none">
                <span class="label">Tgl Surat:</span><br>
                <span class="content">{{ \Carbon\Carbon::parse($record->letter_date)->translatedFormat('d F Y') }}</span>
            </td>
        </tr>
    </table>

    <br>

    <table class="table-data">
        <tr style="background-color: #f0f0f0;">
            <td width="50%" align="center"><strong>DITERUSKAN KEPADA:</strong></td>
            <td width="50%" align="center"><strong>INSTRUKSI / INFORMASI:</strong></td>
        </tr>
        <tr>
            <td>
                <ul style="list-style-type: none; margin: 0; padding: 0 padding-left: 5px; margin: 0;">
                  @php
                      $selectedCount = $record->targetStaffs->count();
                      $totalStaff = \App\Models\Staff::count(); 
                  @endphp

                  @if($selectedCount >= $totalStaff)
                      <p style="margin-bottom: 8px;">
                          <b>Seluruh Karyawan</b>
                      </p>
                  @else
                      @foreach($record->targetStaffs->unique(fn($s) => $s->chair->name ?? 'Staf') as $staff)
                          <p style="margin-bottom: 8px;">
                              <b>{{ $staff->chair->name ?? 'Staf' }}</b> 
                          </p>
                      @endforeach
                  @endif
                </ul>
            </td>
            <td>
                @if($record->classification == 'Disposisi')
                    <div style="margin-bottom: 20px;">
                        {{ $record->instruction }}
                    </div>
                @endif
            </td>
        </tr>
    </table>
    <div style="border-top: 1px dashed #999; padding-top: 10px;">
        <span class="label">Catatan:</span>
        <p style="font-size: 12pt">{{ $record->note ?? '-' }}</p>
    </div>
</body>
</html>