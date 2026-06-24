<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $guarded = [];

    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'night_checkin' => 'datetime',
            'night_checkout' => 'datetime',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function officeTime()
    {
        return $this->belongsTo(OfficeTime::class, 'office_time_id');
    }
}
