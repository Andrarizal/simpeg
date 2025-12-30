<?php

namespace App\Livewire;

use App\Models\Leave;
use App\Models\Schedule;
use Carbon\CarbonPeriod;
use Guava\Calendar\Enums\CalendarViewType;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class MonthlyScheduleWidget extends CalendarWidget
{
    protected CalendarViewType $calendarView = CalendarViewType::DayGridMonth;
    protected string | HtmlString | bool | null $heading = null;

    public function getEvents(FetchInfo $fetchInfo): Collection | array
    {
        $user = Auth::user();
    
        if (! $user->staff_id) return [];

        $leaves = Leave::query()
            ->where('staff_id', $user->staff_id)
            ->where('status', 'Disetujui Kepala Seksi')
            ->orWhere('status', 'Disetujui Direktur')
            ->where(function ($query) use ($fetchInfo) {
                $query->whereBetween('start_date', [$fetchInfo->start, $fetchInfo->end])
                    ->orWhereBetween('end_date', [$fetchInfo->start, $fetchInfo->end])
                    ->orWhere(function ($q) use ($fetchInfo) {
                        $q->where('start_date', '<', $fetchInfo->start)
                            ->where('end_date', '>', $fetchInfo->end);
                    });
            })
            ->get();

        $events = [];
        $blockedDates = [];

        foreach ($leaves as $leave) {
            $isCuti = stripos($leave->type, 'cuti') !== false;
            $color = $isCuti ? '#8B5CF6' : '#06B6D4';

            $events[] = CalendarEvent::make($leave)
                ->title($leave->type . ' - ' . $leave->subtype)
                ->start($leave->start_date)
                ->end($leave->end_date)
                ->backgroundColor($color)
                ->allDay(true);

            $period = CarbonPeriod::create($leave->start_date, $leave->end_date);
            foreach ($period as $date) {
                $blockedDates[] = $date->format('Y-m-d');
            }
        }

        $schedules = Schedule::query()
            ->where('staff_id', $user->staff_id)
            ->where('schedule_date', '>=', $fetchInfo->start)
            ->where('schedule_date', '<=', $fetchInfo->end)
            ->with('shift')
            ->get();

        foreach ($schedules as $schedule) {
            $dateString = $schedule->schedule_date instanceof \DateTime 
                ? $schedule->schedule_date->format('Y-m-d') 
                : $schedule->schedule_date;

            if (in_array($dateString, $blockedDates)) {
                continue; 
            }

            $shiftCode = $schedule->shift->code ?? '';
            
            $color = match ($shiftCode) {
                'P' => '#10B981',
                'R' => '#10B981',
                'S' => '#F59E0B',
                'M' => '#3B82F6',
                'L' => '#EF4444',
                default => '#6B7280', 
            };

            $events[] = CalendarEvent::make($schedule)
                ->title($schedule->shift->name ?? 'Shift')
                ->start($schedule->schedule_date)
                ->end($schedule->schedule_date)
                ->backgroundColor($color)
                ->allDay(true);
        }

        return $events;
    }
}