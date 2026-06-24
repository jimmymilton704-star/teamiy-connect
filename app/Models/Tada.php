<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tada extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'title',
        'description',
        'total_expense',
        'status',
        'is_active',
        'is_settled',
        'remark',
        'employee_id',
        'verified_by',
        'created_by',
        'updated_by',
        'branch_id',
        'department_id',
        'approved_date',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_settled' => 'boolean',
            'approved_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
