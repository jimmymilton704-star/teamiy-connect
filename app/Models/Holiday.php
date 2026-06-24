<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'event',
        'event_date',
        'note',
        'is_active',
        'company_id',
        'created_by',
        'updated_by',
        'is_public_holiday',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'is_active' => 'boolean',
            'is_public_holiday' => 'boolean',
        ];
    }
}
