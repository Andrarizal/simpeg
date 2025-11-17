<?php

use App\Models\Presence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/storage/profile/{filename}', function ($filename) {
    $path = storage_path('app/private/profile/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->middleware(['auth']);

Route::get('/debug-ip', function () {
    return [
        'client_ip' => request()->getClientIp(),
        'client_all' => request()->all(),
        'user_agent' => request()->userAgent(),
    ];
});

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