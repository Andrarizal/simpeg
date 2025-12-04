<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffTraining extends Model
{
    protected $fillable = ['staff_id', 'name', 'description', 'training_date', 'duration', 'certificate', 'notes'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }
}
