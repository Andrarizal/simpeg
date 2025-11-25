<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Leave extends Model
{
    protected $fillable = ['type', 'subtype', 'staff_id', 'start_date', 'end_date', 'reason', 'remaining', 'evidence', 'is_replaced', 'replacement_id', 'replacement_at', 'status', 'known_by', 'known_at', 'approver_id', 'approve_at', 'is_verified', 'verified_by', 'verified_at', 'adverb'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    public function replacement(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    public function knowner(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    public function approver(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    public function verifier(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }
}
