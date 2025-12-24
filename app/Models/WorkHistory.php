<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkHistory extends Model
{
    protected $fillable = ['staff_id', 'unit_id', 'chair_id', 'staff_status_id', 'start_date', 'end_date', 'decree_number', 'decree_date', 'class', 'decree'];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function chair(): BelongsTo
    {
        return $this->belongsTo(Chair::class);
    }

    public function staffStatus(): BelongsTo
    {
        return $this->belongsTo(StaffStatus::class);
    }
}
