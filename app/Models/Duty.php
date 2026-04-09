<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Duty extends Model
{
    protected $fillable = [
        'reference_number',
        'duty_date',
        'start_time',
        'end_time',
        'location',
        'duty',
        'transportation',
    ];

    public function receivers(): HasMany
    {
        return $this->hasMany(DutyReceiver::class);
    }

    public function targetStaffs(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'duty_receivers', 'duty_id', 'staff_id')
          ->withPivot(['outline', 'is_workhour', 'image_path', 'image_verified', 'content_path', 'content_verified', 'letter_path', 'letter_verified'])
          ->withTimestamps()
          ->orderBy('unit_id');
    }
}
