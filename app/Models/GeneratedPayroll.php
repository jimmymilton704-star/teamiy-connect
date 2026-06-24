<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedPayroll extends Model
{
    protected $fillable = [
        'employee_id',
        'payroll_type',
        'payment_type',
        'worked_hours',
        'overtime_hours',
        'undertime_hours',
        'leave_days_by_type',
        'total_unpaid_leave_days',
        'base_salary',
        'overtime_pay',
        'tada_amount',
        'undertime_deduction',
        'unpaid_leave_deduction',
        'tax',
        'net_salary',
        'range',
        'branch_id',
        'department_id',
        'status',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
