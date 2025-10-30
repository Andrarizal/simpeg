<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffEntryEducation extends Model
{
    use HasFactory;
    
    protected $fillable = ['staff_id', 'level', 'institution', 'certificate_number', 'certificate_date', 'nonformal_education', 'adverb'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }
}
