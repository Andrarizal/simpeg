<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class StaffEntryEducation extends Model
{
    use HasFactory;
    
    protected $fillable = ['staff_id', 'level', 'institution', 'certificate_number', 'certificate_date', 'nonformal_education', 'adverb', 'certificate'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    protected static function booted(): void
    {
        static::deleted(function ($model) {
            if ($model->certificate && Storage::disk('public')->exists($model->certificate)) {
                Storage::disk('public')->delete($model->certificate);
            }
        });
    }
}
