<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PerformancePeriod extends Model
{
    protected $fillable = ['year', 'start_date', 'end_date', 'status', 'score'];

    public function performance(): HasMany {
        return $this->hasMany(StaffPerformance::class, 'period_id');
    }
}
