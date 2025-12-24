<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class StaffAdjustment extends Model
{
    use HasFactory;
    
    protected $fillable = ['staff_id', 'decree_number', 'decree_date', 'class', 'decree'];

    protected $touches = ['staff'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    protected static function booted(): void
    {
        static::deleted(function ($model) {
            if ($model->decree && Storage::disk('public')->exists($model->decree)) {
                Storage::disk('public')->delete($model->decree);
            }
        });
    }
}
