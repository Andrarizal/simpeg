<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presence extends Model
{
    protected $fillable = ['staff_id', 'presence_date', 'check-in', 'check-out', 'fingerprint', 'ip'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }
}
