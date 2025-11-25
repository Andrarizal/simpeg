<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreStaff extends Model
{
    protected $fillable = ['nik', 'nip', 'name', 'birth_date', 'email', 'phone', 'staff_status_id', 'chair_id', 'group_id', 'unit_id', 'token', 'status'];
}
