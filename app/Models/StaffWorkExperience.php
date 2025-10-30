<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffWorkExperience extends Model
{
    use HasFactory;

    protected $fillable = ['staff_id', 'institution', 'work_length', 'admission'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }
}
