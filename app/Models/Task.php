<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $fillable = [
        'project_id',
        'name',
        'priority',
        'status',
        'start_date',
        'end_date',
        'description',
        'document',
        'is_active',
        'created_by',
        'updated_by',
        'branch_id',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'assigned_members', 'assignable_id', 'member_id')
            ->wherePivot('assignable_type', 'task');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(TaskChecklist::class, 'task_id');
    }
}
