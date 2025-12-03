<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Staff extends Model
{

    use HasFactory;

    protected $fillable = ['pas', 'nik', 'nip', 'name', 'birth_place', 'birth_date', 'sex', 'marital', 'origin', 'domicile', 'email', 'phone', 'other_phone', 'other_phone_adverb', 'entry_date', 'retirement_date', 'staff_status_id', 'chair_id', 'group_id', 'unit_id'];

    protected static function booted()
    {
        static::created(function ($staff) {
            StaffAdministration::create([
                'staff_id' => $staff->id,
            ]);
        });
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public function staffStatus(): BelongsTo {
        return $this->belongsTo(StaffStatus::class);
    }

    public function chair(): BelongsTo {
        return $this->belongsTo(Chair::class);
    }

    public function group(): BelongsTo {
        return $this->belongsTo(Group::class);
    }

    public function unit(): BelongsTo {
        return $this->belongsTo(Unit::class);
    }

    public function contract(): HasOne { 
        return $this->hasOne(StaffContract::class); 
    }

    public function appointment(): HasOne { 
        return $this->hasOne(StaffAppointment::class); 
    }
    
    public function adjustment(): HasOne { 
        return $this->hasOne(StaffAdjustment::class); 
    }

    public function entryEducation(): HasOne {
        return $this->hasOne(StaffEntryEducation::class); 
    }

    public function workEducation(): HasOne {
        return $this->hasOne(StaffWorkEducation::class); 
    }

    public function workExperience(): HasOne {
        return $this->hasOne(StaffWorkExperience::class); 
    }

    public function administration(): HasOne
    {   
        return $this->hasOne(StaffAdministration::class);
    }

    public function overtimes(): HasMany
    {
        return $this->hasMany(Overtime::class);
    }
}
