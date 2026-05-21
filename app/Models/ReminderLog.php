<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderLog extends Model
{
    protected $fillable = [
        'reminder_id', 'user_id', 'scheduled_date', 'scheduled_time',
        'status', 'completed_at', 'snoozed_until', 'notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'completed_at' => 'datetime',
        'snoozed_until' => 'datetime',
    ];

    public function reminder()
    {
        return $this->belongsTo(Reminder::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
