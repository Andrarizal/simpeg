<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftExchange extends Model
{
    protected $fillable = ['exchange_date', 'staff_id', 'replacer_id', 'staff_schedule_id', 'replacer_schedule_id', 'reason', 'status', 'approved_by', 'approved_at'];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function replacer(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'replacer_id');
    }

    public function staffSchedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'staff_schedule_id');
    }

    public function replacerSchedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'replacer_schedule_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'approved_by');
    }
}
