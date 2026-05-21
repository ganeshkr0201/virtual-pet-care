<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vaccination extends Model
{
    protected $fillable = [
        'pet_id', 'vaccine_name', 'administered_date', 'next_due_date',
        'administered_by', 'batch_number', 'notes', 'certificate_path',
    ];

    protected $casts = [
        'administered_date' => 'date',
        'next_due_date' => 'date',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->next_due_date && $this->next_due_date->isPast();
    }

    public function getDaysUntilDueAttribute(): ?int
    {
        if (!$this->next_due_date) return null;
        return now()->diffInDays($this->next_due_date, false);
    }
}
