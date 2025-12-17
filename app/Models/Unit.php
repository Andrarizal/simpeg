<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = ['name', 'leader_id', 'work_system'];

    public function leader(): BelongsTo {
        return $this->belongsTo(Chair::class);
    }

    public function shift(): HasMany {
        return $this->hasMany(Shift::class);
    }

    public function staff() { 
        return $this->hasMany(Staff::class); 
    }
}
