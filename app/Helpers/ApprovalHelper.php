<?php
use Illuminate\Support\Facades\Auth;

if (!function_exists('shouldShowApprovalButton')) {
  function shouldShowApprovalButton($record): bool
    {
        $user = Auth::user();

        if (!$user->staff || !$user->staff->chair) {
            return false; 
        }

        if ($record->status == 'Ditolak' || $record->is_replaced == 0) {
            return false;
        }

        if ($record->replacement_id == $user->staff_id && $user->staff->chair_id != $record->staff->unit->leader_id) {
            return false;
        }

        $userLevel = $user->staff->chair->level;

        switch ($userLevel) {
            case 4:
                return $record->status == 'Menunggu' &&
                    $record->staff->unit->leader_id == $user->staff->chair_id &&
                    $user->role_id != 1;
            case 3:
                $isWaitingLevel4 = $record->status == 'Menunggu' &&
                                $record->staff->chair->level == 4 &&
                                $record->staff->unit->leader_id == $record->staff->chair_id;
                $isEscalated = $record->status == 'Diketahui Kepala Unit' || 
                            !$record->staff->unit->leader_id;
                return $isWaitingLevel4 || $isEscalated;
            case 2:
                $isWaitingLevel3 = $record->status == 'Menunggu' && 
                                $record->staff->chair->level == 3;
                $isKnownByCoord = $record->status == 'Diketahui Koordinator';
                return $isWaitingLevel3 || $isKnownByCoord;
            case 1:
                $isWaitingLevel2 = $record->status == 'Menunggu' && 
                                $record->staff->chair->level == 2;
                $isApprovedByKasi = $record->status == 'Disetujui Kepala Seksi' && 
                                    $record->staff->chair->level != 4;
                return $isWaitingLevel2 || $isApprovedByKasi;
            default:
                return false;
        }
    }
}