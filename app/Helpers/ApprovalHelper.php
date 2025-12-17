<?php
use Illuminate\Support\Facades\Auth;

if (!function_exists('shouldShowApprovalButton')) {
  function shouldShowApprovalButton($record): bool
    {
        $user = Auth::user();

        if (!$user->staff || !$user->staff->chair) {
            return false; 
        }

        if ($record->status == 'Ditolak' || !$record->is_replaced){
            return false;
        }
        
        return match ($user->staff->chair->level) {
            4 => 
                $record->status == 'Menunggu' &&
                $record->staff->unit->leader_id == $user->staff->chair_id && $user->role_id != 1,
            3 => (
                $record->status == 'Menunggu' &&
                $record->staff->chair->level == 4 &&
                $record->staff->unit->leader_id == $record->staff->chair_id
                ) || (
                $record->status == 'Diketahui Kepala Unit' ||
                !$record->staff->unit->leader_id
                ),
            2 => ((
                $record->status == 'Menunggu' && 
                $record->staff->chair->level == 3
                ) ||
                $record->status == 'Diketahui Koordinator'
                ) && 
                $record->is_verified,
            1 => ((
                $record->status == 'Menunggu' && 
                $record->staff->chair->level == 2
                ) || (
                $record->status == 'Disetujui Kepala Seksi' && 
                $record->staff->chair->level != 4
                )) && 
                $record->is_verified,
            default => false,
        };
    }
}