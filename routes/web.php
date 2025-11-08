<?php

use Illuminate\Support\Facades\Auth;
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

Route::get('/test-foto', function () {
    $path = storage_path('app/private/profile/01K9EG1T0N8NKASDTW4WR8VSYG.jpg');
    if (!file_exists($path)) {
        return 'File tidak ditemukan';
    }

    return response()->file($path);
});