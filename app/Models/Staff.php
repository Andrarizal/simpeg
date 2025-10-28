<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Staff extends Model
{

    use HasFactory;

    protected $fillable = ['nik', 'name', 'birth_place', 'birth_date', 'sex', 'address', 'phone', 'personal_email', 'office_email', 'last_education', 'work_entry_date', 'unit_id', 'chair_id',];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function chair(): BelongsTo {
        return $this->belongsTo(Chair::class);
    }

    public function unit(): BelongsTo {
        return $this->belongsTo(Unit::class);
    }
}
