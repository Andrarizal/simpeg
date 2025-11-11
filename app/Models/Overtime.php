<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Overtime extends Model
{
    protected $fillable = ['staff_id', 'overtime_date', 'start_time', 'end_time', 'command', 'hours', 'month_year', 'is_known', 'is_verified'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    protected static function booted()
    {
        // static::saving(function ($overtime) {
        //     if ($overtime->start_time && $overtime->end_time) {
        //         $start = strtotime($overtime->start_time);
        //         $end = strtotime($overtime->end_time);
        //         $overtime->hours = round(($end - $start) / 3600, 2);
        //     }
        // });
        static::creating(function ($overtime) {
            if (! $overtime->month_year && $overtime->overtime_date) {
                $overtime->month_year = Carbon::parse($overtime->overtime_date)->format('Y-m');
            }
        });
    }

    public function getTotalHours(): float
    {
        if (!$this->start_time || !$this->end_time) {
            return 0;
        }

        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);

        // Jika waktu selesai lebih kecil dari waktu mulai (melewati tengah malam)
        if ($end->lessThan($start)) {
            $end->addDay();
        }

        // Hitung selisih dalam jam (termasuk menit, hasil desimal)
        $hours = abs($end->diffInMinutes($start) / 60);

        // Format 2 desimal biar rapi
        return round($hours, 2);
    }

    public function calculateTotalHours()
    {
        $this->hours = $this->getTotalHours();
    }

}
