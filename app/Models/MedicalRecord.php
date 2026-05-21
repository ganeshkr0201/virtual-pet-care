<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $fillable = [
        'pet_id', 'type', 'title', 'description', 'record_date',
        'weight', 'vet_name', 'diagnosis', 'treatment', 'medications', 'attachment_path',
    ];

    protected $casts = [
        'record_date' => 'date',
        'weight' => 'decimal:2',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path ? asset('storage/' . $this->attachment_path) : null;
    }
}
