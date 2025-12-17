<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceAppraisal extends Model
{
    protected $fillable = ['target_id', 'appraiser_id', 'score', 'notes'];

    public function target(): BelongsTo {
        return $this->belongsTo(StaffPerformance::class);
    }

    public function appraiser(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }

    protected static function booted(): void
    {
        static::saved(function (PerformanceAppraisal $appraisal) {
            static::updatePeriodAverage($appraisal);
        });

        static::deleted(function (PerformanceAppraisal $appraisal) {
            static::updatePeriodAverage($appraisal);
        });
    }

    protected static function updatePeriodAverage(PerformanceAppraisal $appraisal)
    {
        $period = $appraisal->target->period;

        if (!$period) return;
        
        $averageScore = PerformanceAppraisal::whereHas('target', function ($q) use ($period) {
            $q->where('period_id', $period->id);
        })->avg('score');

        $period->updateQuietly([
            'score' => round($averageScore, 2) // Bulatkan 2 desimal
        ]);
    }
}
