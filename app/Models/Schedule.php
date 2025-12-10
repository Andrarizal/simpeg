<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $fillable = ['staff_id', 'shift_id', 'schedule_date', 'is_locked'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    public function shift(): BelongsTo {
        return $this->belongsTo(Shift::class);
    }
}
