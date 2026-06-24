<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePayslip extends Model
{
    protected $fillable = [
        'employee_id',
        'paid_on',
        'status',
        'remark',
        'created_by',
        'updated_by',
        'salary_group_id',
        'salary_cycle',
        'salary_from',
        'salary_to',
        'gross_salary',
        'tds',
        'advance_salary',
        'tada',
        'net_salary',
        'total_days',
        'present_days',
        'absent_days',
        'leave_days',
        'payment_method_id',
    ];

    protected function casts(): array
    {
        return [
            'paid_on' => 'datetime',
            'salary_from' => 'date',
            'salary_to' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
