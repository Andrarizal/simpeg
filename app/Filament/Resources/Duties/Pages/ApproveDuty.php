<?php

namespace App\Filament\Resources\Duties\Pages;

use App\Filament\Resources\Duties\DutyResource;
use App\Filament\Resources\Duties\Tables\ApproveTable;
use App\Models\Staff;
use Filament\Resources\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ApproveDuty extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = DutyResource::class;

    protected string $view = 'filament.resources.duties.pages.approve-duty';

    public ?Staff $staff = null;

    public function getTitle(): string
    {
        return 'Riwayat Tugas: ' . $this->staff->name;
    }

    public function mount(int|string $record): void
    {
        $this->staff = Staff::findOrFail($record);
    }

    public function table(Table $table): Table
    {
        return ApproveTable::configure($table, $this->staff);
    }

    public static function canAccess(array $parameters = []): bool
    {
        $user = Auth::user();
        if (!$user || !$user->staff || !$user->staff->chair) {
            return false; 
        }

        return $user->role_id == 1;
    }
}
