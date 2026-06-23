<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequestMaster extends Model
{
    protected $table = 'leave_requests_master';

    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
}