<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeLeave extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'issue_date',
        'start_time',
        'end_time',
        'status',
        'reasons',
        'avatar',
        'admin_remark',
        'requested_by',
        'request_updated_by',
        'referred_by',
        'branch_id',
        'department_id',
        'company_id',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
