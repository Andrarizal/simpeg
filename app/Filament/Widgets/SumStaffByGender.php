<?php
namespace App\Filament\Widgets;

use App\Models\Staff;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SumStaffByGender extends ChartWidget
{
    protected ?string $heading = 'Jenis Kelamin Pegawai';
    protected static ?int $sort = 8; 
    protected ?string $maxHeight = '115px';

    protected int | string | array $columnSpan = 2; 

    protected function getData(): array
    {
        $staffByGender = Staff::select('sex', DB::raw('COUNT(*) as total'))
            ->groupBy('sex')
            ->get();

        // Mengubah kode 'L' dan 'P' menjadi teks utuh
        $labels = $staffByGender->map(function ($item) {
            if ($item->sex === 'L') return 'Laki-laki';
            if ($item->sex === 'P') return 'Perempuan';
            return 'Lainnya';
        })->toArray();

        $data = $staffByGender->pluck('total')->toArray();
        
        // Warna statis agar biru untuk Laki-laki dan merah muda untuk Perempuan
        $colors = $staffByGender->map(function ($item) {
            return $item->sex === 'L' ? '#3b82f6' : '#ec4899'; 
        })->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie'; // Tipe diubah ke pie
    }

    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false,
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'display' => true, // Ditampilkan untuk pie chart
                    'position' => 'bottom',
                ],
            ],
            // Sumbu X dan Y dihapus karena tidak berlaku untuk Pie Chart
        ];
    }

    public static function canView(): bool
    {
        return Auth::user()?->role_id == 1;
    }
}