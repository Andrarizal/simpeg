<?php
use Illuminate\Support\Facades\Auth;

if (!function_exists('shouldShowApprovalButton')) {
  function shouldShowApprovalButton($record): bool
    {
        $user = Auth::user();

        return match ($user->staff->chair->level) {
            4 => 
                $record->status === 'Menunggu' &&
                $user->staff->unit->leader_id === $user->staff->id && $user->role_id != 1,
            3 => (
                $record->status === 'Menunggu' &&
                $record->staff->chair->level === 4 &&
                $record->staff->unit->leader_id === $record->staff->id
                ) || 
                $record->status === 'Diketahui Kepala Unit',
            2 => ((
                $record->status === 'Menunggu' && 
                $record->staff->chair->level === 3
                ) ||
                $record->status === 'Diketahui Koordinator'
                ) && 
                $record->is_verified,
            1 => ((
                $record->status === 'Menunggu' && 
                $record->staff->chair->level === 2
                ) || (
                $record->status === 'Disetujui Kepala Seksi' && 
                $record->status->chair->level != 4
                )) && 
                $record->is_verified,
            default => false,
        };
    }
}