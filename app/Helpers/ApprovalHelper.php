<?php
use Illuminate\Support\Facades\Auth;

if (!function_exists('shouldShowApprovalButton')) {
  function shouldShowApprovalButton($record): bool
    {
        $user = Auth::user();

        return match ($user->staff->chair->level) {
            3 => $record->status === 'Menunggu' && $record->staff->chair->level === 4,
            2 => 
                ($record->status === 'Menunggu' && $record->staff->chair->level === 3) ||
                $record->status === 'Disetujui Koordinator',
            1 => ($record->status === 'Menunggu' && $record->staff->chair->level === 2) || ($record->status === 'Disetujui Kasi' && $record->status->chair->level != 4),
            default => false,
        };
    }
}