<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StaffContract extends Model
{
    use HasFactory;
    
    protected $fillable = ['staff_id', 'contract_number', 'start_date', 'end_date', 'decree'];

    public function staff(): BelongsTo {
        return $this->belongsTo(Staff::class);
    }
}
