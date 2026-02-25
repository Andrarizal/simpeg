<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LetterTemplate extends Model
{
    protected $fillable = ['name', 'opening_greeting', 'opening_body', 'closing_body'];

    public function letters(): HasMany {
        return $this->hasMany(Letter::class, 'template_id');
    }
}
