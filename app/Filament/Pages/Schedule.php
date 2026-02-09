<?php

namespace App\Filament\Pages;

use App\Livewire\MonthlyScheduleWidget;
use App\Models\Staff;
use Filament\Pages\Page;
use BackedEnum;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use UnitEnum;

class Schedule extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;
    protected static ?string $navigationLabel = 'Kalender Kerja';
    protected static ?string $title = 'Kalender Kerja';
    protected static ?int $navigationSort = 2;
    protected static string|UnitEnum|null $navigationGroup = 'Jadwal';
    
    protected string $view = 'filament.pages.schedule';

    protected function getHeaderWidgets(): array
    {
        return [
            MonthlyScheduleWidget::class,
        ];
    }

    public function getSubheading(): string|Htmlable|null
    {
        $staff = Staff::where('id', Auth::user()->staff_id)->first();

        $nameStaff = "
            <div class='flex items-center gap-1 whitespace-nowrap bg-gray-100 dark:bg-white/5 px-2 py-1 rounded-md border border-gray-200 dark:border-white/10'>
                <span class='font-bold text-primary-600 dark:text-primary-400'>Nama:</span>
                <span class='text-gray-700 dark:text-gray-300'> $staff->name</span>
            </div>
        ";

        return new HtmlString("
            <div class='flex flex-wrap items-center gap-2 mt-2 text-xs'>
                <div class='flex items-center justify-center w-6 h-6 bg-gray-100 dark:bg-gray-800 rounded-full shrink-0'>
                    <svg class='w-4 h-4 text-gray-500' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor'>
                        <path fill-rule='evenodd' clip-rule='evenodd' d='M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z' />
                    </svg>
                </div>
                
                {$nameStaff}
            </div>
        ");
    }
}