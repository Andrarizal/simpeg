<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = ['name', 'leader_id'];

    public function leader(): HasMany {
        return $this->hasMany(Staff::class);
    }
}
