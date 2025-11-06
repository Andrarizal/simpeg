<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffAdministration extends Model
{
    protected $fillable = ['staff_id', 'sip', 'str', 'mcu', 'spk', 'rkk', 'utw', 'is_verified', 'adverb'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }
}
