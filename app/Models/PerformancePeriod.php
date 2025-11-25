<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformancePeriod extends Model
{
    protected $fillable = ['year', 'period', 'start_date', 'end_date', 'status'];
}
