<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Letter extends Model
{
    protected $fillable = ['classification', 'agenda_number', 'reference_number', 'start_date', 'end_date', 'letter_date', 'template_id', 'receiver_type', 'urgency', 'sender', 'title', 'start_time', 'end_time', 'location', 'instruction', 'note', 'known_by', 'file_path'];

    public function known(): BelongsTo {
      return $this->belongsTo(Staff::class, 'known_by');
    }

    public function receiver(): HasMany {
      return $this->hasMany(LetterReceiver::class, 'letter_id');
    }

    public function template(): BelongsTo {
      return $this->belongsTo(LetterTemplate::class, 'template_id');
    }

    public function targetStaffs(): BelongsToMany
    {
        return $this->belongsToMany(Staff::class, 'letter_receivers', 'letter_id', 'staff_id')
          ->withPivot(['is_read', 'read_at'])
          ->withTimestamps();
    }

    protected $casts = [
        'urgency' => 'array',
    ];
}
