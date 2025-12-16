<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chair extends Model
{
    protected $fillable = ['name', 'level', 'head_id'];

    public function allSubordinateIds()
    {
        $ids = self::where('head_id', $this->id)->pluck('id')->toArray();
        
        foreach ($ids as $subId) {
            $child = self::find($subId);
            $ids = array_merge($ids, $child->allSubordinateIds());
        }

        return $ids;
    }

}
