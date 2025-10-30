<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAppointment extends Model
{
    use HasFactory;
    
    protected $fillable = ['staff_id', 'decree_number', 'decree_date', 'class'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }
}
