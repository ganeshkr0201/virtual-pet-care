<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reminder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'pet_id', 'title', 'description', 'type',
        'reminder_time', 'start_date', 'end_date', 'repeat',
        'repeat_days', 'is_active', 'email_notify', 'push_notify', 'snooze_minutes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'repeat_days' => 'array',
        'is_active' => 'boolean',
        'email_notify' => 'boolean',
        'push_notify' => 'boolean',
    ];

    public static array $typeIcons = [
        'feeding' => '🍽️',
        'walking' => '🦮',
        'exercise' => '🏃',
        'grooming' => '✂️',
        'medication' => '💊',
        'vet_appointment' => '🏥',
        'vaccination' => '💉',
        'training' => '🎓',
        'water' => '💧',
        'other' => '📌',
    ];

    public static array $typeColors = [
        'feeding' => 'orange',
        'walking' => 'green',
        'exercise' => 'blue',
        'grooming' => 'pink',
        'medication' => 'red',
        'vet_appointment' => 'purple',
        'vaccination' => 'indigo',
        'training' => 'yellow',
        'water' => 'cyan',
        'other' => 'gray',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function logs()
    {
        return $this->hasMany(ReminderLog::class);
    }

    public function todayLog()
    {
        return $this->hasOne(ReminderLog::class)->whereDate('scheduled_date', today());
    }

    public function getTypeIconAttribute(): string
    {
        return self::$typeIcons[$this->type] ?? '📌';
    }

    public function getTypeColorAttribute(): string
    {
        return self::$typeColors[$this->type] ?? 'gray';
    }
}
