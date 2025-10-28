<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemRule extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type', 'description'];
}
