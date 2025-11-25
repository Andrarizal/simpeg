<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceRealization extends Model
{
    protected $fillable = ['target_id', 'value', 'notes'];

    public function target(): BelongsTo {
        return $this->belongsTo(PerformanceTarget::class);
    }
}
