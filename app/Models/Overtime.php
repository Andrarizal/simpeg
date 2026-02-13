<?php

namespace App\Models;

use Carbon\Carbon;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Overtime extends Model
{
    protected $fillable = ['staff_id', 'overtime_date', 'start_time', 'end_time', 'command', 'hours', 'period_id', 'note', 'is_known', 'known_by', 'known_at', 'is_verified', 'verified_by', 'verified_at'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    public function knowner(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    public function verifier(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    public function period(): BelongsTo {
        return $this->belongsTo(MonthlyPeriod::class, 'period_id');
    }

    protected static function booted()
    {
        static::saving(function (Overtime $overtime) {
            if ($overtime->overtime_date) {
                
                $period = MonthlyPeriod::where('start_date', '<=', $overtime->overtime_date)
                    ->where('end_date', '>=', $overtime->overtime_date)
                    ->first();

                if ($period) {
                    $overtime->period_id = $period->id;
                } else {
                    Notification::make()
                        ->warning()
                        ->title('Periode bulanan tidak ditemukan untuk tanggal on call yang dipilih.')
                        ->send();
                }
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

        if ($end->lessThan($start)) {
            $end->addDay();
        }

        $hours = abs($end->diffInMinutes($start) / 60);

        return round($hours, 2);
    }

    public function calculateTotalHours()
    {
        $this->hours = $this->getTotalHours();
    }

    protected $casts = [
        'is_known' => 'integer',
        'is_verified' => 'integer'
    ];
}
