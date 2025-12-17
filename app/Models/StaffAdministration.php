<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class StaffAdministration extends Model
{
    protected $fillable = ['staff_id', 'sip', 'str', 'mcu', 'spk', 'rkk', 'utw', 'is_verified', 'adverb'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    protected $casts = [
        'is_verified' => 'integer'
    ];

    protected static function booted(): void
    {
        static::deleted(function ($model) {
            foreach (['sip', 'str', 'mcu', 'spk', 'rkk', 'utw'] as $field) {
                if ($model->$field && Storage::disk('public')->exists($model->$field)) {
                    Storage::disk('public')->delete($model->$field);
                }
            }
        });
    }
}
