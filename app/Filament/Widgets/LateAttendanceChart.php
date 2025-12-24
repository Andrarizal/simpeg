<?php

namespace App\Filament\Widgets;

use App\Models\Presence;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class LateAttendanceChart extends ChartWidget
{
    protected static ?int $sort = 7;

    protected ?string $heading = 'Tren Keterlambatan (7 Hari Terakhir)';
    
    protected function getData(): array
    {
        $query = Presence::query()
            ->join('schedules', function ($join) {
                $join->on('presences.staff_id', '=', 'schedules.staff_id')
                     ->on('presences.presence_date', '=', 'schedules.schedule_date');
            })
            ->join('shifts', 'schedules.shift_id', '=', 'shifts.id')
            ->whereColumn('presences.check_in', '>', 'shifts.start_time');

        $data = Trend::query($query)
            ->dateColumn('presence_date')
            ->between(
                start: now()->subDays(6),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Terlambat',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => date('d', strtotime($value->date))),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
