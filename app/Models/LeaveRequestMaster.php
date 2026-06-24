<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequestMaster extends Model
{
    protected $table = 'leave_requests_master';

    protected $guarded = [];

    public $incrementing = false;

    protected function casts(): array
    {
        return [
            'leave_requested_date' => 'datetime',
            'leave_from' => 'datetime',
            'leave_to' => 'datetime',
            'early_exit' => 'boolean',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
}
