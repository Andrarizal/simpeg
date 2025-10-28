<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = ['staff_id', 'date_attendance', 'start_time', 'end_time', 'status'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }
}
