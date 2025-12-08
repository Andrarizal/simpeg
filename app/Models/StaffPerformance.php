<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class StaffPerformance extends Model
{
    protected $fillable = ['staff_id', 'period_id', 'title', 'description'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    public function period(): BelongsTo {
        return $this->belongsTo(PerformancePeriod::class);
    }

    public function appraisal(): HasOne {
        return $this->hasOne(PerformanceAppraisal::class, 'target_id');
    }
}
