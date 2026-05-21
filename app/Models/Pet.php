<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'name', 'species', 'breed', 'gender',
        'date_of_birth', 'weight', 'color', 'microchip_id',
        'allergies', 'medical_history', 'emergency_notes',
        'activity_level', 'feeding_schedule', 'vet_name',
        'vet_phone', 'vet_email', 'vet_clinic', 'avatar', 'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'feeding_schedule' => 'array',
        'is_active' => 'boolean',
        'weight' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(PetImage::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function vaccinations()
    {
        return $this->hasMany(Vaccination::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function getAgeAttribute(): string
    {
        if (!$this->date_of_birth) return 'Unknown';
        $dob = Carbon::parse($this->date_of_birth);
        $years = $dob->diffInYears(now());
        $months = $dob->diffInMonths(now()) % 12;
        if ($years > 0) {
            return $years . ' yr' . ($years > 1 ? 's' : '') . ($months > 0 ? ' ' . $months . ' mo' : '');
        }
        return $months . ' month' . ($months !== 1 ? 's' : '');
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        $speciesEmojis = [
            'dog' => '🐕', 'cat' => '🐈', 'bird' => '🐦',
            'rabbit' => '🐇', 'fish' => '🐠', 'hamster' => '🐹',
        ];
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=7C3AED&color=fff&size=128';
    }

    public function getUpcomingVaccinationsAttribute()
    {
        return $this->vaccinations()
            ->whereNotNull('next_due_date')
            ->where('next_due_date', '>=', now())
            ->orderBy('next_due_date')
            ->get();
    }
}
