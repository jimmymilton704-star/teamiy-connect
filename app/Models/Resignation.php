<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resignation extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'employee_id',
        'resignation_date',
        'last_working_day',
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
            'resignation_date' => 'date',
            'last_working_day' => 'date',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
