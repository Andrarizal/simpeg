<?php

use App\Models\SystemRule;
use Illuminate\Support\Facades\Cache;

if (!function_exists('setting')) {
    // helper untuk membuat function yang bisa diakses dimana saja
    function setting(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = SystemRule::where('key', $key)->first();
            if (!$setting) {
                return $default;
            }

            return match ($setting->type) {
                'integer' => (int) $setting->value,
                'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
                'json' => json_decode($setting->value, true),
                default => $setting->value,
            };
        });
    }
}
