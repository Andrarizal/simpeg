<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MonthlyPeriod extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
    ];

    public function overtime(): HasMany {
        return $this->hasMany(Overtime::class, 'period_id');
    }
}
