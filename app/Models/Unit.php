<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unit extends Model
{
    protected $fillable = ['name', 'leader_id'];

    public function leader(): BelongsTo {
        return $this->belongsTo(Chair::class);
    }
}
