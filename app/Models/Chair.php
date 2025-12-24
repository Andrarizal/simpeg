<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chair extends Model
{
    protected $fillable = ['name', 'level', 'head_id', 'unit_id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Chair::class, 'head_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Chair::class, 'head_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function ledUnit(): HasOne
    {
        return $this->hasOne(Unit::class, 'leader_id');
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

    // function khusus struktur organisasi
    public function getSubordinatesAttribute()
    {
        // Berdasarkan head_id
        $structuralSubordinates = $this->children->filter(function ($child) {
            
            // level 3/4 ikut head_id
            if ($child->ledUnit) return true;

            // pegawai satu unit tampil
            if ($child->unit_id == $this->unit_id) return true;

            // level 4
            // jika punya kepala unit, sembunyikan level 5.
            // hanya tampilkan level 4 dan level 5 yang tidak memiliki kepala unit.
            $childUnit = $child->unit;
            if ($childUnit && $childUnit->leader_id && $childUnit->leader_id != $this->id) {
                return false; 
            }

            return true;
        });

        // cek anggota unit
        $functionalSubordinates = collect();

        if ($this->ledUnit) {
            // ambil anggota unit yang dipimpin
            $members = Chair::where('unit_id', $this->ledUnit->id)
                ->where('id', '!=', $this->id)
                ->with('parent')
                ->get();

            $functionalSubordinates = $members->filter(function ($member) {
                
                // jika punya unit yang ada pimpinannya, sembunyikan
                if ($member->ledUnit) return false;

                // cek apakah atasan langsungnya adalah dia sendiri
                if ($member->head_id && $member->head_id != $this->id) {
                    
                    // cek apakah ada atasan di unit yang sama
                    $bossUnitId = $member->parent ? $member->parent->unit_id : null;

                    // jika atasan di unit yang sama
                    if ($bossUnitId == $this->ledUnit->id) {
                        // sembunyikan karena sudah ditampilkan di filter atas
                        return false; 
                    }
                }

                return true;
            });
        }

        // 3. GABUNGKAN & UNIQUE
        return $structuralSubordinates
            ->merge($functionalSubordinates)
            ->unique('id')
            ->values();
    }
}
