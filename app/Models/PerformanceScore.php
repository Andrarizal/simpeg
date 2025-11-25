<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceScore extends Model
{
    protected $fillable = ['staff_id', 'period_id', 'kpi_score', 'behavior_score', 'total_score'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    public function period(): BelongsTo {
        return $this->belongsTo(PerformancePeriod::class);
    }
}
