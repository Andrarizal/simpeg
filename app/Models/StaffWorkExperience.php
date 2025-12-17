<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class StaffWorkExperience extends Model
{
    use HasFactory;

    protected $fillable = ['staff_id', 'institution', 'work_length', 'admission', 'certificate'];

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
