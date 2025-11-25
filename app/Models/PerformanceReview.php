<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceReview extends Model
{
    protected $fillable = ['target_id', 'reviewer_id', 'score', 'notes'];

    public function target(): BelongsTo {
        return $this->belongsTo(PerformanceTarget::class);
    }

    public function reviewer(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }
}
