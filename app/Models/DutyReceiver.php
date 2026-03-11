<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DutyReceiver extends Model
{
    protected $fillable = [
        'duty_id',
        'staff_id',
        'outline',
        'is_workhour',
        'image_path',
        'image_verified',
        'content_path',
        'content_verified',
        'letter_path',
        'letter_verified',
    ];

    public function duty(): BelongsTo
    {
        return $this->belongsTo(Duty::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }

    protected $casts = [
        'is_workhour' => 'integer',
        'image_verified' => 'integer',
        'content_verified' => 'integer',
        'letter_verified' => 'integer',
    ];
}
