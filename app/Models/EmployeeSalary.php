<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeSalary extends Model
{
    protected $fillable = [
        'employee_id',
        'annual_salary',
        'basic_salary_type',
        'basic_salary_value',
        'monthly_basic_salary',
        'annual_basic_salary',
        'monthly_fixed_allowance',
        'annual_fixed_allowance',
        'salary_group_id',
        'hour_rate',
        'weekly_basic_salary',
        'weekly_fixed_allowance',
        'tax',
        'is_overtime',
        'weekly_working_hours',
        'payroll_type',
        'payment_type',
    ];

    protected function casts(): array
    {
        return [
            'is_overtime' => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
