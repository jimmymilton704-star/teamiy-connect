<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TeamMeeting extends Model
{
    protected $fillable = [
        'title',
        'description',
        'venue',
        'meeting_date',
        'meeting_start_time',
        'company_id',
        'image',
        'meeting_published_at',
        'created_by',
        'updated_by',
        'branch_id',
        'meeting_link',
        'admin_link',
        'meeting_password',
    ];

    protected function casts(): array
    {
        return [
            'meeting_date' => 'date',
            'meeting_published_at' => 'datetime',
        ];
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_meeting_members', 'team_meeting_id', 'meeting_participator_id');
    }
}
