<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffWorkEducation extends Model
{
    use HasFactory;

    protected $fillable = ['staff_id', 'level', 'major', 'institution', 'certificate_number', 'certificate_date', 'certificate'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }
}
