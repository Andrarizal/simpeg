<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceBehavior extends Model
{
    protected $fillable = ['period_id', 'staff_id', 'type', 'score', 'reviewer_id', 'notes'];

    public function period(): BelongsTo {
        return $this->belongsTo(PerformancePeriod::class);
    }

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    public function reviewer(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }
}
