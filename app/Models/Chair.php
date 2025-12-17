<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chair extends Model
{
    protected $fillable = ['name', 'level', 'head_id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Chair::class, 'head_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Chair::class, 'head_id');
    }

    public function staff() { 
        return $this->hasMany(Staff::class); 
    }

    public function allSubordinateIds()
    {
        $ids = $this->children()->pluck('id')->toArray();
        
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->allSubordinateIds());
        }

        return $ids;
    }

}
