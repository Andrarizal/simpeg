<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chair extends Model
{
    protected $fillable = ['name', 'level', 'head_id'];

    public function allSubordinateIds()
    {
        // Ambil id bawahan dari user login dengan head_id
        $ids = self::where('head_id', $this->id)->pluck('id')->toArray();
        
        foreach ($ids as $subId) {
            // Ambil seluruh data dari id bawahan
            $child = self::find($subId);
            // Gabungkan seluruh data bawahan, lakukan rekursif untuk mengecek apabila data bawahan masih memiliki bawahan dengan memanggil function itu sendiri
            $ids = array_merge($ids, $child->allSubordinateIds());
        }

        // Kembalikan seluruh data hasil penggabungan
        return $ids;
    }

}
