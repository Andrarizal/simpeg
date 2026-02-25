<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LetterReceiver extends Model
{
    protected $fillable = ['letter_id', 'staff_id', 'is_read', 'read_at'];

    public function letter(): BelongsTo {
        return $this->belongsTo(Letter::class);
    }

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }
}
