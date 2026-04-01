<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffContractEvaluation extends Model
{
    protected $fillable = [
        'contract_id',
        'first_score_id',
        'second_score_id',
        'final_score',
        'conclusion',
        'note',
    ];

    public function contract()
    {
        return $this->belongsTo(StaffContract::class, 'contract_id');
    }

    public function firstScore()
    {
        return $this->belongsTo(PerformanceAppraisal::class, 'first_score_id');
    }

    public function secondScore()
    {
        return $this->belongsTo(PerformanceAppraisal::class, 'second_score_id');
    }
}
