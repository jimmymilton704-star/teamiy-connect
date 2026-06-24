<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignedMember extends Model
{
    protected $fillable = [
        'member_id',
        'assignable_type',
        'assignable_id',
    ];

    public $timestamps = false;
}
