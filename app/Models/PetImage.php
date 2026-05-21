<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetImage extends Model
{
    protected $fillable = ['pet_id', 'path', 'caption', 'is_primary'];

    protected $casts = ['is_primary' => 'boolean'];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
