<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    protected $fillable = ['unit_id', 'name', 'code', 'start_time', 'end_time', 'is_off'];

    public function unit(): BelongsTo {
        return $this->belongsTo(Unit::class);
    }

    public function schedule(): HasMany {
        return $this->hasMany(Schedule::class);
    }
}
