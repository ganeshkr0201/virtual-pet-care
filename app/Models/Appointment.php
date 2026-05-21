<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'pet_id', 'title', 'description', 'type',
        'appointment_datetime', 'vet_name', 'clinic_name',
        'clinic_address', 'clinic_phone', 'status', 'notes',
    ];

    protected $casts = [
        'appointment_datetime' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->appointment_datetime->isFuture() && $this->status === 'scheduled';
    }
}
