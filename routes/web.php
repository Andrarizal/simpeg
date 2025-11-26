<?php

use App\Models\Presence;
use App\Models\StaffAdjustment;
use App\Models\StaffAdministration;
use App\Models\StaffAppointment;
use App\Models\StaffContract;
use App\Models\StaffEntryEducation;
use App\Models\StaffWorkEducation;
use App\Models\StaffWorkExperience;
use Filament\Notifications\Notification;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/storage/profile/{filename}', function ($filename) {
    $path = storage_path('app/private/profile/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->middleware(['auth']);

Route::post('/report-ip', function (Request $request) {
    $ipServer = $request->ip(); // IP publik menurut server
    $ipReported = $request->input('ip'); // IP publik hasil deteksi client

    return response()->json([
        'server_ip' => $ipServer,
        'client_ip' => $ipReported,
    ]);
});

Route::post('/store-device-info', function (Request $request) {
    session([
        'device_info' => [
            'ip' => $request->ip,
            'device_id' => $request->device_id,
            'device_info' => $request->device_info,
            'platform' => $request->platform,
        ]
    ]);

    return response()->json(['status' => 'ok']);
})->name('store.device.info');

Route::post('/check-radius', function (Request $request) {
    $earthRadius = 6371000; // meter

    $dLat = deg2rad($request->lat - setting('lat'));
    $dLon = deg2rad($request->lng - setting('lng'));

    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($request->lat)) * cos(deg2rad(setting('lat'))) *
         sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1-$a));

    return response()->json([
            'user_lat' => $request->lat,
            'user_lng' => $request->lng,
            'sys_lat' => setting('lat'),
            'sys_lng' => setting('lng'),
            'radius' => $earthRadius * $c
        ]);
})->withoutMiddleware(VerifyCsrfToken::class);

Route::post('/check-in-by-gps', function (Request $request){
    if ($request->mode === "check-out"){
        $today = now()->toDateString();
        $presence = Presence::where('staff_id', Auth::user()->staff_id)->whereDate('presence_date', $today)->first();
        $presence->check_out = now()->toTimeString();
        $presence->save();

        Notification::make()
            ->title('Check-out berhasil!')
            ->success()
            ->send();
    } else {
        $device = session('device_info');
        $today = now()->toDateString();

        if (!$device) {
            Notification::make()
                ->title('Data perangkat belum terdeteksi!')
                ->danger()
                ->send();
            return;
        }

        $sameDeviceToday = Presence::where('fingerprint', $device['device_id'])
            ->whereDate('presence_date', $today)
            ->exists();

        if ($sameDeviceToday) {
            Notification::make()
                ->title('Perangkat telah digunakan untuk check-in hari ini!')
                ->danger()
                ->send();
            return;
        }

        $data = [
            'staff_id' => Auth::user()->staff_id,
            'presence_date' => now()->toDateString(),
            'check_in' => now()->toTimeString(),
            'method' => 'coordinate',
            'fingerprint' => $device['device_id'],
            'lattitude' => $request->lat,
            'longitude' => $request->lng,
            'radius' => $request->radius,
        ];

        Presence::create($data);

        Notification::make()
            ->title('Check-in berhasil!')
            ->success()
            ->send();
    }
    return response()->json(['status' => 'ok']);
});

Route::get('/preview-pdf/{token}', function ($token) {
    $path = storage_path("app/private/livewire-tmp/$token.pdf");

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path, [
        'Content-Type' => 'application/pdf',
    ]);
})->name('preview.pdf');

Route::get('/preview-administration/{model}/{id}/{field}', function ($model, $id, $field) {
    $allowedModels = [
        'administration' => StaffAdministration::class,
        'entry_education' => StaffEntryEducation::class,
        'work_education' => StaffWorkEducation::class,
        'experience' => StaffWorkExperience::class,
        'contract' => StaffContract::class,
        'appointment' => StaffAppointment::class,
        'adjustment' => StaffAdjustment::class,
    ];

    if (!array_key_exists($model, $allowedModels)) {
        abort(404);
    }

    $class = $allowedModels[$model];
    $record = '';
    if ($model === 'administration'){
        $record = $class::findOrFail($id);
    } else {
        $record = $class::where('staff_id', $id)->first();
    }

    if (!isset($record->$field)) {
        abort(404);
    }

    $path = storage_path('app/public/' . $record->$field);
    
    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->name('preview.administration')->middleware('auth');
