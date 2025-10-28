<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Salary extends Model
{
    protected $fillable = ['staff_id', 'month', 'year', 'basic_salary', 'subsidy', 'deduction', 'total', 'file_slip'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }
}
