<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    protected $fillable = [
        'old_branch_id',
        'old_department_id',
        'employee_id',
        'branch_id',
        'department_id',
        'transfer_date',
        'description',
        'status',
        'remark',
        'created_by',
        'updated_by',
        'old_post_id',
        'post_id',
        'old_office_time_id',
        'office_time_id',
        'old_supervisor_id',
        'supervisor_id',
    ];

    protected function casts(): array
    {
        return [
            'transfer_date' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
