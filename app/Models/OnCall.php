<?php

namespace App\Models;

use App\Filament\Resources\OnCalls\OnCallResource;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class OnCall extends Model
{
    protected $fillable = ['staff_id', 'oncall_date', 'start_time', 'end_time', 'hours', 'period_id', 'command', 'command_by', 'is_known', 'known_at', 'note', 'is_verified', 'verified_by', 'verified_at'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    public function period(): BelongsTo {
        return $this->belongsTo(MonthlyPeriod::class, 'period_id');
    }

    public function commander(): BelongsTo {
        return $this->belongsTo(Staff::class, 'command_by');
    }

    public function verifier(): BelongsTo {
        return $this->belongsTo(Staff::class, 'verified_by');
    }

    protected $casts = [
        'is_known' => 'integer',
        'is_verified' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (OnCall $onCall) {
            if ($onCall->oncall_date) {
                $period = MonthlyPeriod::where('start_date', '<=', $onCall->oncall_date)
                    ->where('end_date', '>=', $onCall->oncall_date)
                    ->first();

                if ($period) {
                    $onCall->period_id = $period->id;
                } else {
                    Notification::make()
                        ->warning()
                        ->title('Periode bulanan tidak ditemukan untuk tanggal on call yang dipilih.')
                        ->send();
                }
            }

            $onCall->command_by = Auth::user()->staff_id;
            $onCall->hours = $onCall->getTotalHours();

            Notification::make()
                ->title("Perintah On Call")
                ->body("Anda telah menerima tugas on call dari " . $onCall->commander->name . " pada tanggal " . Carbon::parse($onCall->oncall_date)->translatedFormat('d F Y'))
                ->warning()
                ->icon('heroicon-o-document-text')
                ->iconColor('warning')
                ->actions([
                    Action::make('review')
                        ->label('Tinjau')
                        ->url(OnCallResource::getUrl('index'))
                        ->markAsRead(),
                ])
                ->sendToDatabase($onCall->staff->user);
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
}
