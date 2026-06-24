<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAccount extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'bank_name',
        'bank_account_no',
        'bank_account_type',
        'salary',
        'salary_cycle',
        'salary_group_id',
        'allow_generate_payroll',
        'account_holder',
    ];

    protected function casts(): array
    {
        return [
            'allow_generate_payroll' => 'boolean',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
