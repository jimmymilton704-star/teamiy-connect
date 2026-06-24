<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'name',
        'client_id',
        'start_date',
        'deadline',
        'cost',
        'estimated_hours',
        'status',
        'priority',
        'description',
        'document',
        'cover_pic',
        'is_active',
        'created_by',
        'updated_by',
        'slug',
        'branch_id',
        'department_ids',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'deadline' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    public function leaders(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_team_leaders', 'project_id', 'leader_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'assigned_members', 'assignable_id', 'member_id')
            ->wherePivot('assignable_type', 'project');
    }
}
