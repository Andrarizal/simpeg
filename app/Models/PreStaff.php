<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreStaff extends Model
{
    protected $fillable = ['nik', 'nip', 'name', 'birth_date', 'email', 'phone', 'staff_status_id', 'chair_id', 'group_id', 'unit_id', 'token', 'status'];

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
}
