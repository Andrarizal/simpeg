<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    protected $fillable = ['type', 'staff_id', 'start_date', 'end_date', 'reason', 'remaining', 'replacement_id', 'status', 'approver_id', 'adverb'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    public function replacement(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    public function approver(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }
}
