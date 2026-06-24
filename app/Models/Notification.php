<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'notification_publish_date',
        'notification_for_id',
        'is_active',
        'company_id',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'notification_publish_date' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(UserNotification::class, 'notification_id');
    }
}
