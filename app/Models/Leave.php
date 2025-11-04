<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    protected $fillable = ['type', 'subtype', 'staff_id', 'start_date', 'end_date', 'reason', 'remaining', 'replacement_id', 'evidence', 'is_replaced', 'status', 'approver_id', 'is_verified','adverb'];

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
