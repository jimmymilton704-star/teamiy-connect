<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Termination extends Model
{
    protected $fillable = [
        'employee_id',
        'termination_type_id',
        'notice_date',
        'termination_date',
        'reason',
        'admin_remark',
        'document',
        'status',
        'created_by',
        'updated_by',
        'branch_id',
        'department_id',
    ];

    protected function casts(): array
    {
        return [
            'notice_date' => 'date',
            'termination_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
